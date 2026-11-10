<?php

declare(strict_types=1);

namespace YouWillLikeIT\Elements\Elements;

use Bummzack\SortableFile\Forms\SortableUploadField;
use DorsetDigital\SchemaManager\Model\Schema\ImageGallerySchema;
use SilverStripe\Assets\Image;
use SilverStripe\CMS\Model\SiteTree;
use SilverStripe\Forms\FieldList;
use SilverStripe\Forms\TextareaField;
use SilverStripe\ORM\ManyManyList;
use YouWillLikeIT\Elements\Schema\ProvidesSchemaEntities;

/**
 * Sortable image collection. The image title (edited in Files) is its alternative text.
 *
 * @property string $Intro
 * @method ManyManyList<Image> Images()
 */
class ElementGallery extends ElementBase implements ProvidesSchemaEntities
{
    private static string $table_name = 'YWLI_ElementGallery';

    private static string $icon = 'font-icon-block-gallery';

    private static string $singular_name = 'Gallery';

    private static string $plural_name = 'Galleries';

    private static string $class_description = 'Sortable images (ImageGallery schema)';

    private static array $db = [
        'Intro' => 'Text',
    ];

    private static array $many_many = [
        'Images' => Image::class,
    ];

    private static array $many_many_extraFields = [
        'Images' => ['SortOrder' => 'Int'],
    ];

    private static array $owns = [
        'Images',
    ];

    private static bool $has_cta_links = false;

    private static array $translate = [
        'Intro',
    ];

    public function fieldLabels($includerelations = true): array
    {
        $labels = parent::fieldLabels($includerelations);
        $labels['Intro'] = _t(self::class . '.Intro', 'Introduction');
        $labels['Images'] = _t(self::class . '.Images', 'Images');

        return $labels;
    }

    public function getCMSFields(): FieldList
    {
        $this->beforeUpdateCMSFields(function (FieldList $fields): void {
            $fields->removeByName(['Images']);
            $fields->replaceField('Intro', TextareaField::create('Intro', $this->fieldLabel('Intro'))->setRows(3));
            $fields->addFieldToTab(
                'Root.Main',
                SortableUploadField::create('Images', $this->fieldLabel('Images'))
                    ->setFolderName('Elements/Gallery')
                    ->setAllowedFileCategories('image/supported')
                    ->setDescription(_t(
                        self::class . '.ImagesHint',
                        'Sort by drag and drop. Each image title is used as alternative text, so give every image a descriptive title.'
                    ))
            );
        });

        return parent::getCMSFields();
    }

    public function getSummary(): string
    {
        return _t(self::class . '.Summary', '{count} images', ['count' => $this->Images()->count()]);
    }

    public function getSortedImages(): ManyManyList
    {
        return $this->Images()->sort('SortOrder');
    }

    public function schemaEntities(SiteTree $page): array
    {
        $images = $this->getSortedImages();
        if (!$images->exists() || !class_exists(ImageGallerySchema::class)) {
            return [];
        }

        $gallery = ImageGallerySchema::create(
            'gallery-' . $this->ID,
            ((string) $this->Title) ?: (string) $page->Title,
            ((string) $this->Intro) ?: null
        )->setIsPartOfPage($page->AbsoluteLink());

        foreach ($images as $image) {
            $width = (int) $image->getWidth();
            $height = (int) $image->getHeight();
            $gallery->addImage(
                $image->getAbsoluteURL(),
                ((string) $image->Title) ?: null,
                null,
                $width > 0 ? $width : null,
                $height > 0 ? $height : null
            );
        }

        return [$gallery];
    }
}
