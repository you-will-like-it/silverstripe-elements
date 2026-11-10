<?php

declare(strict_types=1);

namespace YouWillLikeIT\Elements\Elements;

use SilverStripe\Forms\FieldList;
use SilverStripe\Forms\TextareaField;
use SilverStripe\ORM\HasManyList;
use YouWillLikeIT\Elements\Models\FeatureItem;

/**
 * List of entries (title, text, image, link): services, benefits, steps.
 *
 * @property string $Intro
 * @method HasManyList<FeatureItem> Items()
 */
class ElementFeatureGrid extends ElementBase
{
    private static string $table_name = 'YWLI_ElementFeatureGrid';

    private static string $icon = 'font-icon-block-layout';

    private static string $singular_name = 'Feature list';

    private static string $plural_name = 'Feature lists';

    private static string $class_description = 'Entries with title, text, image and link';

    private static array $db = [
        'Intro' => 'Text',
    ];

    private static array $has_many = [
        'Items' => FeatureItem::class,
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
        $labels['Items'] = _t(self::class . '.Items', 'Entries');

        return $labels;
    }

    public function getCMSFields(): FieldList
    {
        $this->beforeUpdateCMSFields(function (FieldList $fields): void {
            $fields->removeByName(['Items']);
            $fields->replaceField('Intro', TextareaField::create('Intro', $this->fieldLabel('Intro'))->setRows(3));
            $fields->addFieldToTab('Root.Main', $this->itemsField('Items', $this->fieldLabel('Items')));
        });

        return parent::getCMSFields();
    }

    public function getSummary(): string
    {
        return _t(self::class . '.Summary', '{count} entries', ['count' => $this->Items()->count()]);
    }

    public function getSortedItems(): HasManyList
    {
        return $this->Items()->sort('Sort');
    }
}
