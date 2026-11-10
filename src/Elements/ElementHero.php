<?php

declare(strict_types=1);

namespace YouWillLikeIT\Elements\Elements;

use SilverStripe\Assets\Image;
use SilverStripe\Core\Validation\ValidationResult;
use SilverStripe\Forms\FieldList;
use SilverStripe\Forms\TextareaField;
use YouWillLikeIT\Elements\Traits\HasAccessibleImage;

/**
 * Page header: title (usually the page H1), lead text, image and buttons.
 *
 * @property string $Lead
 * @property string $ImageAlt
 * @property int $ImageID
 * @method Image Image()
 */
class ElementHero extends ElementBase
{
    use HasAccessibleImage;

    private static string $table_name = 'YWLI_ElementHero';

    private static string $icon = 'font-icon-block-banner';

    private static string $singular_name = 'Hero';

    private static string $plural_name = 'Heroes';

    private static string $class_description = 'Page header with heading, lead text, image and buttons';

    private static array $db = [
        'Lead' => 'Text',
        'ImageAlt' => 'Varchar(255)',
    ];

    private static array $has_one = [
        'Image' => Image::class,
    ];

    private static array $owns = [
        'Image',
    ];

    private static array $defaults = [
        'HeadingLevel' => 'h1',
    ];

    private static array $heading_levels = ['h1', 'h2'];

    private static array $translate = [
        'Lead',
        'ImageAlt',
    ];

    public function fieldLabels($includerelations = true): array
    {
        $labels = parent::fieldLabels($includerelations);
        $labels['Lead'] = _t(self::class . '.Lead', 'Lead text');
        $labels['Image'] = _t(self::class . '.Image', 'Image');
        $labels['ImageAlt'] = _t(self::class . '.ImageAlt', 'Image alternative text');

        return $labels;
    }

    public function getCMSFields(): FieldList
    {
        $this->beforeUpdateCMSFields(function (FieldList $fields): void {
            $fields->removeByName(['Image', 'ImageAlt']);
            $fields->replaceField('Lead', TextareaField::create('Lead', $this->fieldLabel('Lead'))->setRows(3));
            $fields->addFieldsToTab(
                'Root.Main',
                $this->imageFields('Elements/Hero', $this->fieldLabel('Image'), $this->fieldLabel('ImageAlt'))
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
        return (string) ($this->Lead ?: $this->Title);
    }
}
