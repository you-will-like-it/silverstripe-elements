<?php

declare(strict_types=1);

namespace YouWillLikeIT\Elements\Traits;

use SilverStripe\AssetAdmin\Forms\UploadField;
use SilverStripe\Core\Validation\ValidationResult;
use SilverStripe\Forms\TextField;

/**
 * Image + required alternative text, shared by elements and items with a single image.
 * Expects a has_one "Image" and a db field "ImageAlt" on the using class.
 */
trait HasAccessibleImage
{
    /**
     * @return array{0: UploadField, 1: TextField}
     */
    protected function imageFields(string $folder, string $label, string $altLabel): array
    {
        return [
            UploadField::create('Image', $label)
                ->setFolderName($folder)
                ->setAllowedFileCategories('image/supported'),
            TextField::create('ImageAlt', $altLabel)
                ->setDescription(_t(
                    __TRAIT__ . '.ImageAltHint',
                    'Describes the image for screen readers and search engines. Required when an image is set.'
                )),
        ];
    }

    protected function validateImageAlt(ValidationResult $result): void
    {
        if ((int) $this->ImageID > 0 && trim((string) $this->ImageAlt) === '') {
            $result->addFieldError(
                'ImageAlt',
                _t(__TRAIT__ . '.ImageAltRequired', 'Please enter an alternative text for the image.')
            );
        }
    }

    public function getImageAltText(): string
    {
        return trim((string) $this->ImageAlt);
    }
}
