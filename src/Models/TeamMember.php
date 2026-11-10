<?php

declare(strict_types=1);

namespace YouWillLikeIT\Elements\Models;

use SilverStripe\Assets\Image;
use SilverStripe\Core\Validation\ValidationResult;
use SilverStripe\Forms\FieldList;
use SilverStripe\Forms\TextareaField;
use SilverStripe\ORM\DataObject;
use SilverStripe\Versioned\Versioned;
use YouWillLikeIT\Elements\Elements\ElementTeam;
use YouWillLikeIT\Elements\Traits\HasAccessibleImage;

/**
 * @property string $Name
 * @property string $Role
 * @property string $Bio
 * @property string $ImageAlt
 * @property int $Sort
 * @property int $ImageID
 * @method ElementTeam Parent()
 * @method Image Image()
 */
class TeamMember extends DataObject
{
    use ItemPermissions;
    use HasAccessibleImage;

    private static string $table_name = 'YWLI_TeamMember';

    private static string $singular_name = 'Person';

    private static string $plural_name = 'People';

    private static array $db = [
        'Name' => 'Varchar(120)',
        'Role' => 'Varchar(120)',
        'Bio' => 'Text',
        'ImageAlt' => 'Varchar(255)',
        'Sort' => 'Int',
    ];

    private static array $has_one = [
        'Parent' => ElementTeam::class,
        'Image' => Image::class,
    ];

    private static array $owns = [
        'Image',
    ];

    private static array $extensions = [
        Versioned::class,
    ];

    private static string $default_sort = 'Sort ASC';

    private static array $summary_fields = [
        'Image.CMSThumbnail',
        'Name',
        'Role',
    ];

    private static array $translate = [
        'Role',
        'Bio',
        'ImageAlt',
    ];

    public function fieldLabels($includerelations = true): array
    {
        $labels = parent::fieldLabels($includerelations);
        $labels['Name'] = _t(self::class . '.Name', 'Name');
        $labels['Role'] = _t(self::class . '.Role', 'Role');
        $labels['Bio'] = _t(self::class . '.Bio', 'Short bio');
        $labels['Image'] = _t(self::class . '.Image', 'Photo');
        $labels['Image.CMSThumbnail'] = '';
        $labels['ImageAlt'] = _t(self::class . '.ImageAlt', 'Photo alternative text');

        return $labels;
    }

    public function getCMSFields(): FieldList
    {
        $this->beforeUpdateCMSFields(function (FieldList $fields): void {
            $fields->removeByName(['ParentID', 'Sort', 'Image', 'ImageAlt']);
            $fields->replaceField('Bio', TextareaField::create('Bio', $this->fieldLabel('Bio'))->setRows(5));
            $fields->addFieldsToTab(
                'Root.Main',
                $this->imageFields('Elements/Team', $this->fieldLabel('Image'), $this->fieldLabel('ImageAlt'))
            );
        });

        return parent::getCMSFields();
    }

    public function getTitle(): string
    {
        return (string) $this->Name;
    }

    /**
     * Alt text falls back to the person's name; a portrait without alt is never empty.
     */
    public function getImageAltText(): string
    {
        return trim((string) $this->ImageAlt) ?: (string) $this->Name;
    }

    public function validate(): ValidationResult
    {
        $result = parent::validate();
        if (trim((string) $this->Name) === '') {
            $result->addFieldError('Name', _t(self::class . '.NameRequired', 'Please enter the name.'));
        }

        return $result;
    }
}
