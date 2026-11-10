<?php

declare(strict_types=1);

namespace YouWillLikeIT\Elements\Elements;

use DorsetDigital\SchemaManager\Model\Schema\FAQPageSchema;
use SilverStripe\CMS\Model\SiteTree;
use SilverStripe\Forms\FieldList;
use SilverStripe\Forms\HTMLEditor\HTMLEditorField;
use SilverStripe\ORM\HasManyList;
use YouWillLikeIT\Elements\Models\FaqItem;
use YouWillLikeIT\Elements\Schema\ProvidesSchemaEntities;

/**
 * Questions and answers. All FAQ blocks of a page form one FAQPage entity.
 *
 * @property string $Intro
 * @method HasManyList<FaqItem> Items()
 */
class ElementFaq extends ElementBase implements ProvidesSchemaEntities
{
    private static string $table_name = 'YWLI_ElementFaq';

    private static string $icon = 'font-icon-block-content';

    private static string $singular_name = 'FAQ';

    private static string $plural_name = 'FAQs';

    private static string $class_description = 'Questions and answers (FAQPage schema)';

    private static array $db = [
        'Intro' => 'HTMLText',
    ];

    private static array $has_many = [
        'Items' => FaqItem::class,
    ];

    private static array $owns = [
        'Items',
    ];

    private static array $cascade_deletes = [
        'Items',
    ];

    private static array $cascade_duplicates = [
        'Items',
    ];

    private static array $translate = [
        'Intro',
    ];

    public function fieldLabels($includerelations = true): array
    {
        $labels = parent::fieldLabels($includerelations);
        $labels['Intro'] = _t(self::class . '.Intro', 'Introduction');
        $labels['Items'] = _t(self::class . '.Items', 'Questions');

        return $labels;
    }

    public function getCMSFields(): FieldList
    {
        $this->beforeUpdateCMSFields(function (FieldList $fields): void {
            $fields->removeByName(['Items']);
            $fields->replaceField('Intro', HTMLEditorField::create('Intro', $this->fieldLabel('Intro'))->setRows(5));
            $fields->addFieldToTab('Root.Main', $this->itemsField('Items', $this->fieldLabel('Items')));
        });

        return parent::getCMSFields();
    }

    public function getSummary(): string
    {
        return _t(self::class . '.Summary', '{count} questions', ['count' => $this->Items()->count()]);
    }

    public function getSortedItems(): HasManyList
    {
        return $this->Items()->sort('Sort');
    }

    public function schemaEntities(SiteTree $page): array
    {
        $items = $this->getSortedItems();
        if (!$items->exists() || !class_exists(FAQPageSchema::class)) {
            return [];
        }

        $schema = FAQPageSchema::create('page-' . $page->ID)->setIsPartOfPage($page->AbsoluteLink());
        $added = 0;

        foreach ($items as $item) {
            $answer = trim(html_entity_decode(strip_tags((string) $item->Answer), ENT_QUOTES | ENT_HTML5));
            if (trim((string) $item->Question) !== '' && $answer !== '') {
                $schema->addQuestion((string) $item->Question, $answer);
                $added++;
            }
        }

        return $added > 0 ? [$schema] : [];
    }
}
