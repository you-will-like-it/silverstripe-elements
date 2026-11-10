<?php

declare(strict_types=1);

namespace YouWillLikeIT\Elements\Models;

use SilverStripe\Assets\Image;
use SilverStripe\Core\Validation\ValidationResult;
use SilverStripe\Forms\FieldList;
use SilverStripe\Forms\TextareaField;
use SilverStripe\LinkField\Form\LinkField;
use SilverStripe\LinkField\Models\Link;
use SilverStripe\ORM\DataObject;
use SilverStripe\Versioned\Versioned;
use YouWillLikeIT\Elements\Elements\ElementFeatureGrid;
use YouWillLikeIT\Elements\Traits\HasAccessibleImage;

/**
 * @property string $Title
 * @property string $Text
 * @property string $ImageAlt
 * @property int $Sort
 * @property int $ImageID
 * @method ElementFeatureGrid Parent()
 * @method Image Image()
 * @method Link Link()
 */
class FeatureItem extends DataObject
{
    use ItemPermissions;
    use HasAccessibleImage;

    private static string $table_name = 'YWLI_FeatureItem';

    private static string $singular_name = 'Entry';

    private static string $plural_name = 'Entries';

    private static array $db = [
        'Title' => 'Varchar(150)',
        'Text' => 'Text',
        'ImageAlt' => 'Varchar(255)',
        'Sort' => 'Int',
    ];

    private static array $has_one = [
        'Parent' => ElementFeatureGrid::class,
        'Image' => Image::class,
        'Link' => Link::class,
    ];

    private static array $owns = [
        'Image',
        'Link',
    ];

    private static array $cascade_deletes = [
        'Link',
    ];

    private static array $cascade_duplicates = [
        'Link',
    ];

    private static array $extensions = [
        Versioned::class,
    ];

    private static string $default_sort = 'Sort ASC';

    private static array $summary_fields = [
        'Image.CMSThumbnail',
        'Title',
        'Text.Summary',
    ];

    private static array $translate = [
        'Title',
        'Text',
        'ImageAlt',
    ];

    public function fieldLabels($includerelations = true): array
    {
        $labels = parent::fieldLabels($includerelations);
        $labels['Title'] = _t(self::class . '.Title', 'Title');
        $labels['Text'] = _t(self::class . '.Text', 'Text');
        $labels['Text.Summary'] = $labels['Text'];
        $labels['Image'] = _t(self::class . '.Image', 'Image');
        $labels['Image.CMSThumbnail'] = '';
        $labels['ImageAlt'] = _t(self::class . '.ImageAlt', 'Image alternative text');
        $labels['Link'] = _t(self::class . '.Link', 'Link');

        return $labels;
    }

    public function getCMSFields(): FieldList
    {
        $this->beforeUpdateCMSFields(function (FieldList $fields): void {
            $fields->removeByName(['ParentID', 'Sort', 'Image', 'ImageAlt', 'Link', 'LinkID']);
            $fields->replaceField('Text', TextareaField::create('Text', $this->fieldLabel('Text'))->setRows(4));
            $fields->addFieldsToTab(
                'Root.Main',
                $this->imageFields('Elements/FeatureList', $this->fieldLabel('Image'), $this->fieldLabel('ImageAlt'))
            );
            $fields->addFieldToTab('Root.Main', LinkField::create('Link', $this->fieldLabel('Link')));
        });

        return parent::getCMSFields();
    }

    public function validate(): ValidationResult
    {
        $result = parent::validate();
        if (trim((string) $this->Title) === '') {
            $result->addFieldError('Title', _t(self::class . '.TitleRequired', 'Please enter a title.'));
        }
        $this->validateImageAlt($result);

        return $result;
    }
}
