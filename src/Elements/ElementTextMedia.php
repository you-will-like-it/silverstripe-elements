<?php

declare(strict_types=1);

namespace YouWillLikeIT\Elements\Elements;

use SilverStripe\Assets\Image;
use SilverStripe\Core\Validation\ValidationResult;
use SilverStripe\Forms\FieldList;
use SilverStripe\Forms\HTMLEditor\HTMLEditorField;
use SilverStripe\ORM\FieldType\DBField;
use YouWillLikeIT\Elements\Traits\HasAccessibleImage;

/**
 * Rich text with an optional image.
 *
 * @property string $Content
 * @property string $ImageAlt
 * @property int $ImageID
 * @method Image Image()
 */
class ElementTextMedia extends ElementBase
{
    use HasAccessibleImage;

    private static string $table_name = 'YWLI_ElementTextMedia';

    private static string $icon = 'font-icon-block-promo-3';

    private static string $singular_name = 'Text with image';

    private static string $plural_name = 'Texts with image';

    private static string $class_description = 'Rich text with an optional image';

    private static array $db = [
        'Content' => 'HTMLText',
        'ImageAlt' => 'Varchar(255)',
    ];

    private static array $has_one = [
        'Image' => Image::class,
    ];

    private static array $owns = [
        'Image',
    ];

    private static array $translate = [
        'Content',
        'ImageAlt',
    ];

    public function fieldLabels($includerelations = true): array
    {
        $labels = parent::fieldLabels($includerelations);
        $labels['Content'] = _t(self::class . '.Content', 'Content');
        $labels['Image'] = _t(self::class . '.Image', 'Image');
        $labels['ImageAlt'] = _t(self::class . '.ImageAlt', 'Image alternative text');

        return $labels;
    }

    public function getCMSFields(): FieldList
    {
        $this->beforeUpdateCMSFields(function (FieldList $fields): void {
            $fields->removeByName(['Image', 'ImageAlt']);
            $fields->replaceField('Content', HTMLEditorField::create('Content', $this->fieldLabel('Content'))->setRows(12));
            $fields->addFieldsToTab(
                'Root.Main',
                $this->imageFields('Elements/TextMedia', $this->fieldLabel('Image'), $this->fieldLabel('ImageAlt'))
            );
        });

        return parent::getCMSFields();
    }

    public function validate(): ValidationResult
    {
        $result = parent::validate();
        $this->validateImageAlt($result);

        return $result;
    }

    public function getSummary(): string
    {
        return (string) DBField::create_field('HTMLText', $this->Content)->Summary(20);
    }
}
