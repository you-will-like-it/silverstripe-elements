<?php

declare(strict_types=1);

namespace YouWillLikeIT\Elements\Models;

use SilverStripe\AssetAdmin\Forms\UploadField;
use SilverStripe\Assets\Image;
use SilverStripe\Core\Validation\ValidationResult;
use SilverStripe\Forms\FieldList;
use SilverStripe\LinkField\Form\LinkField;
use SilverStripe\LinkField\Models\Link;
use SilverStripe\ORM\DataObject;
use SilverStripe\Versioned\Versioned;
use YouWillLikeIT\Elements\Elements\ElementLogoWall;

/**
 * Logo; the name is its alternative text.
 *
 * @property string $Name
 * @property int $Sort
 * @property int $ImageID
 * @method ElementLogoWall Parent()
 * @method Image Image()
 * @method Link Link()
 */
class LogoItem extends DataObject
{
    use ItemPermissions;

    private static string $table_name = 'YWLI_LogoItem';

    private static string $singular_name = 'Logo';

    private static string $plural_name = 'Logos';

    private static array $db = [
        'Name' => 'Varchar(120)',
        'Sort' => 'Int',
    ];

    private static array $has_one = [
        'Parent' => ElementLogoWall::class,
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
        'Name',
    ];

    private static array $translate = [
        'Name',
    ];

    public function fieldLabels($includerelations = true): array
    {
        $labels = parent::fieldLabels($includerelations);
        $labels['Name'] = _t(self::class . '.Name', 'Name (alternative text)');
        $labels['Image'] = _t(self::class . '.Image', 'Logo');
        $labels['Image.CMSThumbnail'] = '';
        $labels['Link'] = _t(self::class . '.Link', 'Link');

        return $labels;
    }

    public function getCMSFields(): FieldList
    {
        $this->beforeUpdateCMSFields(function (FieldList $fields): void {
            $fields->removeByName(['ParentID', 'Sort', 'Image', 'Link', 'LinkID']);
            $fields->addFieldsToTab('Root.Main', [
                UploadField::create('Image', $this->fieldLabel('Image'))
                    ->setFolderName('Elements/Logos')
                    ->setAllowedFileCategories('image/supported')
                    ->setDescription(_t(self::class . '.ImageHint', 'SVG or PNG with transparent background.')),
                LinkField::create('Link', $this->fieldLabel('Link')),
            ]);
        });

        return parent::getCMSFields();
    }

    public function getTitle(): string
    {
        return (string) $this->Name;
    }

    public function validate(): ValidationResult
    {
        $result = parent::validate();
        if (trim((string) $this->Name) === '') {
            $result->addFieldError('Name', _t(self::class . '.NameRequired', 'Please enter the name; it is the alternative text.'));
        }

        return $result;
    }
}
