<?php

declare(strict_types=1);

namespace YouWillLikeIT\Elements\Elements;

use DorsetDigital\SchemaManager\Control\SchemaIDs;
use DorsetDigital\SchemaManager\Model\Schema\PersonSchema;
use SilverStripe\CMS\Model\SiteTree;
use SilverStripe\Forms\FieldList;
use SilverStripe\Forms\HTMLEditor\HTMLEditorField;
use SilverStripe\ORM\HasManyList;
use YouWillLikeIT\Elements\Models\TeamMember;
use YouWillLikeIT\Elements\Schema\ProvidesSchemaEntities;

/**
 * People with photo, role and short bio (Person schema).
 *
 * @property string $Intro
 * @method HasManyList<TeamMember> Members()
 */
class ElementTeam extends ElementBase implements ProvidesSchemaEntities
{
    private static string $table_name = 'YWLI_ElementTeam';

    private static string $icon = 'font-icon-torsos-all';

    private static string $singular_name = 'Team';

    private static string $plural_name = 'Teams';

    private static string $class_description = 'People with photo, role and bio (Person schema)';

    private static array $db = [
        'Intro' => 'HTMLText',
    ];

    private static array $has_many = [
        'Members' => TeamMember::class,
    ];

    private static array $owns = [
        'Members',
    ];

    private static array $cascade_deletes = [
        'Members',
    ];

    private static array $cascade_duplicates = [
        'Members',
    ];

    private static array $translate = [
        'Intro',
    ];

    public function fieldLabels($includerelations = true): array
    {
        $labels = parent::fieldLabels($includerelations);
        $labels['Intro'] = _t(self::class . '.Intro', 'Introduction');
        $labels['Members'] = _t(self::class . '.Members', 'People');

        return $labels;
    }

    public function getCMSFields(): FieldList
    {
        $this->beforeUpdateCMSFields(function (FieldList $fields): void {
            $fields->removeByName(['Members']);
            $fields->replaceField('Intro', HTMLEditorField::create('Intro', $this->fieldLabel('Intro'))->setRows(5));
            $fields->addFieldToTab('Root.Main', $this->itemsField('Members', $this->fieldLabel('Members')));
        });

        return parent::getCMSFields();
    }

    public function getSummary(): string
    {
        return implode(', ', $this->Members()->column('Name'));
    }

    public function getSortedMembers(): HasManyList
    {
        return $this->Members()->sort('Sort');
    }

    public function schemaEntities(SiteTree $page): array
    {
        if (!class_exists(PersonSchema::class)) {
            return [];
        }

        $entities = [];
        foreach ($this->getSortedMembers() as $member) {
            if (trim((string) $member->Name) === '') {
                continue;
            }

            $person = PersonSchema::create('team-' . $member->ID, (string) $member->Name)
                ->setWorksFor(SchemaIDs::organisation())
                ->setIsPartOfPage($page->AbsoluteLink());

            if ($member->Role) {
                $person->setJobTitle((string) $member->Role);
            }
            if ($member->Image()->exists()) {
                $person->setImage($member->Image()->getAbsoluteURL());
            }

            $entities[] = $person;
        }

        return $entities;
    }
}
