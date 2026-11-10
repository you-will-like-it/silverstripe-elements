<?php

declare(strict_types=1);

namespace YouWillLikeIT\Elements\Elements;

use SilverStripe\Forms\FieldList;
use SilverStripe\ORM\HasManyList;
use YouWillLikeIT\Elements\Models\LogoItem;

/**
 * Partner, brand or certification logos, optionally linked.
 *
 * @method HasManyList<LogoItem> Logos()
 */
class ElementLogoWall extends ElementBase
{
    private static string $table_name = 'YWLI_ElementLogoWall';

    private static string $icon = 'font-icon-block-carousel';

    private static string $singular_name = 'Logo wall';

    private static string $plural_name = 'Logo walls';

    private static string $class_description = 'Partner, brand or certification logos';

    private static array $has_many = [
        'Logos' => LogoItem::class,
    ];

    private static array $owns = [
        'Logos',
    ];

    private static array $cascade_deletes = [
        'Logos',
    ];

    private static array $cascade_duplicates = [
        'Logos',
    ];

    private static bool $has_cta_links = false;

    public function fieldLabels($includerelations = true): array
    {
        $labels = parent::fieldLabels($includerelations);
        $labels['Logos'] = _t(self::class . '.Logos', 'Logos');

        return $labels;
    }

    public function getCMSFields(): FieldList
    {
        $this->beforeUpdateCMSFields(function (FieldList $fields): void {
            $fields->removeByName(['Logos']);
            $fields->addFieldToTab('Root.Main', $this->itemsField('Logos', $this->fieldLabel('Logos')));
        });

        return parent::getCMSFields();
    }

    public function getSummary(): string
    {
        return implode(', ', $this->Logos()->column('Name'));
    }

    public function getSortedLogos(): HasManyList
    {
        return $this->Logos()->sort('Sort');
    }
}
