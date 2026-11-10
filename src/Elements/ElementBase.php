<?php

declare(strict_types=1);

namespace YouWillLikeIT\Elements\Elements;

use DNADesign\Elemental\Models\BaseElement;
use SilverStripe\Forms\DropdownField;
use SilverStripe\Forms\FieldList;
use SilverStripe\Forms\FormField;
use SilverStripe\Forms\GridField\GridField;
use SilverStripe\Forms\GridField\GridFieldConfig_RecordEditor;
use SilverStripe\Forms\LiteralField;
use SilverStripe\LinkField\Form\MultiLinkField;
use SilverStripe\LinkField\Models\Link;
use SilverStripe\Model\ModelData;
use SilverStripe\ORM\FieldType\DBField;
use SilverStripe\ORM\FieldType\DBHTMLText;
use SilverStripe\ORM\HasManyList;
use Symbiote\GridFieldExtensions\GridFieldOrderableRows;

/**
 * Shared basics for all YouWillLikeIT elements: semantic heading level and call-to-action links.
 *
 * The module ships no layout. Projects add fields with an Extension on the concrete element class
 * and override the element template in their theme (see docs/extending.md).
 *
 * @property string $HeadingLevel
 * @method HasManyList<Link> CTALinks()
 */
abstract class ElementBase extends BaseElement
{
    private static string $table_name = 'YWLI_ElementBase';

    private static array $db = [
        'HeadingLevel' => "Enum('h1,h2,h3','h2')",
    ];

    private static array $has_many = [
        'CTALinks' => Link::class . '.Owner',
    ];

    private static array $owns = [
        'CTALinks',
    ];

    private static array $cascade_deletes = [
        'CTALinks',
    ];

    private static array $cascade_duplicates = [
        'CTALinks',
    ];

    private static array $defaults = [
        'ShowTitle' => true,
        'HeadingLevel' => 'h2',
    ];

    /**
     * Whether the element offers call-to-action links in the CMS.
     */
    private static bool $has_cta_links = true;

    /**
     * Heading levels an editor may choose. Only the page header may use h1.
     */
    private static array $heading_levels = ['h2', 'h3'];

    private static bool $inline_editable = false;

    public function fieldLabels($includerelations = true): array
    {
        $labels = parent::fieldLabels($includerelations);
        $labels['HeadingLevel'] = _t(self::class . '.HeadingLevel', 'Heading level');
        $labels['CTALinks'] = _t(self::class . '.CTALinks', 'Buttons');

        return $labels;
    }

    public function getCMSFields(): FieldList
    {
        $this->beforeUpdateCMSFields(function (FieldList $fields): void {
            $fields->removeByName(['HeadingLevel', 'CTALinks']);

            $levels = $this->getAllowedHeadingLevels();
            if (count($levels) > 1) {
                $fields->addFieldToTab(
                    'Root.Settings',
                    DropdownField::create(
                        'HeadingLevel',
                        $this->fieldLabel('HeadingLevel'),
                        array_combine($levels, array_map('strtoupper', $levels))
                    )->setDescription(_t(
                        self::class . '.HeadingLevelHint',
                        'Use H1 only once per page (page header). Following blocks use H2, nested ones H3.'
                    ))
                );
            }

            if ($this->config()->get('has_cta_links')) {
                $fields->addFieldToTab(
                    'Root.Main',
                    MultiLinkField::create('CTALinks', $this->fieldLabel('CTALinks'))
                        ->setDescription(_t(
                            self::class . '.CTALinksHint',
                            'The first button is the primary action. Use descriptive link texts, not "more".'
                        ))
                );
            }
        });

        return parent::getCMSFields();
    }

    /**
     * Sortable GridField for a has_many item relation, or a notice until the element is saved.
     */
    protected function itemsField(string $relation, string $label): FormField
    {
        if (!$this->isInDB()) {
            return LiteralField::create(
                $relation,
                '<p class="alert alert-info">'
                . htmlspecialchars(_t(self::class . '.SaveFirst', 'Save the block first, then add entries here.'), ENT_QUOTES)
                . '</p>'
            );
        }

        $config = GridFieldConfig_RecordEditor::create(50)
            ->addComponent(GridFieldOrderableRows::create('Sort'));

        return GridField::create($relation, $label, $this->getComponents($relation), $config);
    }

    /**
     * @return array<int, string>
     */
    public function getAllowedHeadingLevels(): array
    {
        $levels = array_values(array_intersect(
            (array) $this->config()->get('heading_levels'),
            ['h1', 'h2', 'h3']
        ));

        return $levels ?: ['h2'];
    }

    public function getHeadingTag(): string
    {
        $levels = $this->getAllowedHeadingLevels();

        return in_array($this->HeadingLevel, $levels, true) ? $this->HeadingLevel : $levels[0];
    }

    /**
     * Heading tag for entries inside this element (one level below the element heading).
     */
    public function getChildHeadingTag(): string
    {
        return match ($this->getHeadingTag()) {
            'h1' => 'h2',
            'h2' => 'h3',
            default => 'h4',
        };
    }

    /**
     * Element title as heading with the configured level, or null when hidden/empty.
     */
    public function Heading(string $classes = ''): ?DBHTMLText
    {
        if (!$this->ShowTitle || trim((string) $this->Title) === '') {
            return null;
        }

        $tag = $this->getHeadingTag();
        $class = $classes !== '' ? ' class="' . htmlspecialchars($classes, ENT_QUOTES) . '"' : '';

        /** @var DBHTMLText $field */
        $field = DBField::create_field(
            'HTMLFragment',
            sprintf('<%1$s%2$s>%3$s</%1$s>', $tag, $class, htmlspecialchars((string) $this->Title, ENT_QUOTES))
        );

        return $field;
    }

    /**
     * True when this element is the first block of its area (eager-load its image for LCP).
     */
    public function getIsFirstElement(): bool
    {
        $first = $this->Parent()?->Elements()->first();

        return $first instanceof ModelData && (int) $first->ID === (int) $this->ID;
    }

    public function getSortedCTALinks(): HasManyList
    {
        return $this->CTALinks()->sort('Sort');
    }
}
