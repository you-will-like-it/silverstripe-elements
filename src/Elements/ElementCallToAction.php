<?php

declare(strict_types=1);

namespace YouWillLikeIT\Elements\Elements;

use SilverStripe\Forms\FieldList;
use SilverStripe\Forms\TextareaField;

/**
 * Short call to action: heading, text and buttons.
 *
 * @property string $Content
 */
class ElementCallToAction extends ElementBase
{
    private static string $table_name = 'YWLI_ElementCallToAction';

    private static string $icon = 'font-icon-block-promo-2';

    private static string $singular_name = 'Call to action';

    private static string $plural_name = 'Calls to action';

    private static string $class_description = 'Heading, short text and buttons';

    private static array $db = [
        'Content' => 'Text',
    ];

    private static array $translate = [
        'Content',
    ];

    public function fieldLabels($includerelations = true): array
    {
        $labels = parent::fieldLabels($includerelations);
        $labels['Content'] = _t(self::class . '.Content', 'Text');

        return $labels;
    }

    public function getCMSFields(): FieldList
    {
        $this->beforeUpdateCMSFields(function (FieldList $fields): void {
            $fields->replaceField('Content', TextareaField::create('Content', $this->fieldLabel('Content'))->setRows(3));
        });

        return parent::getCMSFields();
    }

    public function getSummary(): string
    {
        return (string) ($this->Content ?: $this->Title);
    }
}
