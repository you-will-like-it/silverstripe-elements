<?php

declare(strict_types=1);

namespace YouWillLikeIT\Elements\Extensions;

use SilverStripe\AssetAdmin\Forms\UploadField;
use SilverStripe\Assets\Image;
use SilverStripe\Core\Extension;
use SilverStripe\Forms\DropdownField;
use SilverStripe\Forms\FieldList;
use UncleCheese\DisplayLogic\Forms\Wrapper;

/**
 * OPT-IN section presentation (background, spacing, width) for any element, including third-party ones.
 * Not applied by the module. Enable it on BaseElement in the project and render it in the theme's
 * ElementHolder.ss. Class names are mapped in YAML so the module stays framework-agnostic:
 *
 *   YouWillLikeIT\Elements\Extensions\SectionStyleExtension:
 *     background_classes:
 *       light: 'bg-light'
 *       dark: 'bg-dark text-bg-dark'
 *     spacing_classes:
 *       md: 'py-5 py-lg-6'
 *     width_classes:
 *       default: 'container'
 *
 * @property string $Background
 * @property string $Spacing
 * @property string $ContentWidth
 * @property int $BackgroundImageID
 * @method Image BackgroundImage()
 */
class SectionStyleExtension extends Extension
{
    private static array $db = [
        'Background' => "Enum('none,light,dark,primary,image','none')",
        'Spacing' => "Enum('none,sm,md,lg','md')",
        'ContentWidth' => "Enum('default,narrow,full','default')",
    ];

    private static array $has_one = [
        'BackgroundImage' => Image::class,
    ];

    private static array $owns = [
        'BackgroundImage',
    ];

    private static array $defaults = [
        'Background' => 'none',
        'Spacing' => 'md',
        'ContentWidth' => 'default',
    ];

    /**
     * Extra classes per background value (project YAML). Modifier classes are always added.
     */
    private static array $background_classes = [];

    /**
     * Extra classes per spacing value (project YAML).
     */
    private static array $spacing_classes = [];

    /**
     * Container classes per width value (project YAML).
     */
    private static array $width_classes = [
        'default' => 'ywli-container',
        'narrow' => 'ywli-container ywli-container--narrow',
        'full' => 'ywli-container ywli-container--full',
    ];

    /**
     * Backgrounds on which text must be light.
     */
    private static array $dark_backgrounds = ['dark', 'primary', 'image'];

    protected function updateCMSFields(FieldList $fields): void
    {
        $fields->removeByName(['Background', 'Spacing', 'ContentWidth', 'BackgroundImage']);

        $upload = UploadField::create('BackgroundImage', _t(self::class . '.BackgroundImage', 'Background image'))
            ->setFolderName('Elements/Backgrounds')
            ->setAllowedFileCategories('image/supported')
            ->setDescription(_t(self::class . '.BackgroundImageHint', 'At least 1920 × 900 px. Decorative only: put important content in text.'));

        $imageField = class_exists(Wrapper::class)
            ? Wrapper::create($upload)->displayIf('Background')->isEqualTo('image')->end()
            : $upload;

        $fields->addFieldsToTab('Root.Settings', [
            DropdownField::create('Background', _t(self::class . '.Background', 'Background'), [
                'none' => _t(self::class . '.BgNone', 'None'),
                'light' => _t(self::class . '.BgLight', 'Light'),
                'dark' => _t(self::class . '.BgDark', 'Dark'),
                'primary' => _t(self::class . '.BgPrimary', 'Brand colour'),
                'image' => _t(self::class . '.BgImage', 'Image'),
            ]),
            $imageField,
            DropdownField::create('Spacing', _t(self::class . '.Spacing', 'Vertical spacing'), [
                'none' => _t(self::class . '.SpacingNone', 'None'),
                'sm' => _t(self::class . '.SpacingSm', 'Small'),
                'md' => _t(self::class . '.SpacingMd', 'Medium'),
                'lg' => _t(self::class . '.SpacingLg', 'Large'),
            ]),
            DropdownField::create('ContentWidth', _t(self::class . '.ContentWidth', 'Content width'), [
                'default' => _t(self::class . '.WidthDefault', 'Default'),
                'narrow' => _t(self::class . '.WidthNarrow', 'Narrow'),
                'full' => _t(self::class . '.WidthFull', 'Full width'),
            ]),
        ]);
    }

    /**
     * Classes for the section wrapper rendered by ElementHolder.ss.
     */
    public function getSectionClasses(): string
    {
        $owner = $this->getOwner();
        $background = $owner->Background ?: 'none';
        $spacing = $owner->Spacing ?: 'md';

        $classes = [
            'ywli-section',
            'ywli-section--bg-' . $background,
            'ywli-section--space-' . $spacing,
            (string) ($owner->config()->get('background_classes')[$background] ?? ''),
            (string) ($owner->config()->get('spacing_classes')[$spacing] ?? ''),
        ];

        return implode(' ', array_filter($classes));
    }

    public function getContainerClass(): string
    {
        $width = $this->getOwner()->ContentWidth ?: 'default';

        return (string) ($this->getOwner()->config()->get('width_classes')[$width] ?? 'ywli-container');
    }

    public function getIsDarkSection(): bool
    {
        return in_array(
            $this->getOwner()->Background,
            (array) $this->getOwner()->config()->get('dark_backgrounds'),
            true
        );
    }

    public function getHasBackgroundImage(): bool
    {
        $owner = $this->getOwner();

        return $owner->Background === 'image' && $owner->BackgroundImage()->exists();
    }
}
