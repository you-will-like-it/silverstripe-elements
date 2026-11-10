# Extending elements in a project

The module provides **basics**: content fields, CMS editing, validation, translations, Fluent and
schema integration, and plain semantic templates. It ships **no layout and no CSS**.

Everything design-specific lives in the project:

| Need | Where | How |
|---|---|---|
| Extra field (eyebrow, variant, columns, icon, …) | `app/src/Extensions/Elements/<Element>Extension.php` | `Extension` with `$db` / `$has_one` / `$owns`, `updateCMSFields()` |
| Translatable extra field | same extension | `private static array $translate = [...]` (merged into the element's config) |
| Layout / markup | `themes/<slug>/templates/YouWillLikeIT/Elements/Elements/<Element>.ss` | Copy the module template, then change it |
| Styles | `app/client/src/scss/elements/_<element>.scss` | Style the project's markup; BEM classes from the module are optional hooks |
| Section background/spacing/width | YAML | Enable `SectionStyleExtension` and map classes (see README) |
| Schema for extra fields | same extension | `updateSchemaEntities(array &$entities, SiteTree $page)` |

## Example: hero with eyebrow and two layout variants

```php
<?php

declare(strict_types=1);

namespace App\Extensions\Elements;

use SilverStripe\Core\Extension;
use SilverStripe\Forms\DropdownField;
use SilverStripe\Forms\FieldList;
use SilverStripe\Forms\TextField;
use YouWillLikeIT\Elements\Elements\ElementHero;

/**
 * @extends Extension<ElementHero>
 * @property string $Eyebrow
 * @property string $Variant
 */
class HeroExtension extends Extension
{
    private static array $db = [
        'Eyebrow' => 'Varchar(120)',
        'Variant' => "Enum('default,compact','default')",
    ];

    // Fluent: merged into ElementHero's translate config.
    private static array $translate = [
        'Eyebrow',
    ];

    protected function updateCMSFields(FieldList $fields): void
    {
        $fields->removeByName(['Eyebrow', 'Variant']);
        $fields->insertBefore('Title', TextField::create('Eyebrow', _t(self::class . '.Eyebrow', 'Eyebrow')));
        $fields->addFieldToTab('Root.Settings', DropdownField::create('Variant', _t(self::class . '.Variant', 'Variant'), [
            'default' => _t(self::class . '.VariantDefault', 'Large (home)'),
            'compact' => _t(self::class . '.VariantCompact', 'Compact (subpages)'),
        ]));
    }
}
```

```yaml
# app/_config/elements.yml
YouWillLikeIT\Elements\Elements\ElementHero:
  extensions:
    - App\Extensions\Elements\HeroExtension
```

Then copy `templates/YouWillLikeIT/Elements/Elements/ElementHero.ss` into the theme and add `$Eyebrow`
and the variant classes there. Run `sake db:build --flush`.

## Rules

- **Never edit the module for one project.** If a field is useful on most sites, propose it for the module
  (new minor version) and remove it from the project afterwards.
- Extra fields are additive: nullable or with defaults, so existing content keeps working.
- Keep accessibility and SEO intact when overriding templates:
  - one `h1` per page (`$Heading` honours the editor's level; use `$ChildHeadingTag` for entries);
  - `alt="$ImageAltText.ATT"` on images, `width` / `height` from the scaled image;
  - `loading="lazy"`, except for the first block's image (`$IsFirstElement` → `fetchpriority="high"`);
  - descriptive link texts (`$Title` of the Link).
- Keep the include `YouWillLikeIT\\Elements\\CTALinks` or reproduce its markup with the project's button classes.
- Project-specific translations go into the project's `app/lang/*.yml`.
