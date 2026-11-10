# YouWillLikeIT Elements

Basic, layout-free content blocks for [silverstripe/elemental](https://github.com/silverstripe/silverstripe-elemental) 6.

The module owns **content, CMS editing, validation, translations, Fluent and Schema.org integration**.
Projects own **layout, extra fields and styles** (see [docs/extending.md](docs/extending.md)).

## Requirements

- PHP 8.3+, Silverstripe CMS 6.1+
- `dnadesign/silverstripe-elemental` ^6.2, `silverstripe/linkfield` ^5.2,
  `symbiote/silverstripe-gridfieldextensions` ^5.2, `bummzack/sortablefile` ^3.0

Optional:

- `tractorcow/silverstripe-fluent` ^8.2, for multilingual sites;
- `dorsetdigital/silverstripe-schema-manager` ^2.2, for structured data;
- `unclecheese/display-logic` ^4.0, for conditional fields in `SectionStyleExtension`.

## Installation

Add the GitHub repository as a VCS repository and require a tagged version:

```json
"repositories": [
    { "type": "vcs", "url": "https://github.com/you-will-like-it/silverstripe-elements" }
],
"require": {
    "youwilllikeit/silverstripe-elements": "^2.0"
}
```

```bash
composer update youwilllikeit/silverstripe-elements
vendor/bin/sake db:build --flush
```

Then allow the elements on your page types, for example:

```yaml
Page:
  extensions:
    - DNADesign\Elemental\Extensions\ElementalPageExtension
  allowed_elements:
    - YouWillLikeIT\Elements\Elements\ElementHero
    - YouWillLikeIT\Elements\Elements\ElementTextMedia
    # …
```

## Elements

| Element | Fields | Items | Schema |
|---|---|---|---|
| `ElementHero` | Title (H1/H2), Lead, Image + ImageAlt, buttons | – | – |
| `ElementTextMedia` | Title, Content (HTML), Image + ImageAlt, buttons | – | – |
| `ElementCallToAction` | Title, Content, buttons | – | – |
| `ElementFeatureGrid` | Title, Intro, buttons | `FeatureItem`: Title, Text, Image + ImageAlt, Link | – |
| `ElementFaq` | Title, Intro (HTML), buttons | `FaqItem`: Question, Answer (HTML) | FAQPage (all FAQ blocks of a page merge) |
| `ElementTestimonials` | Title | `TestimonialItem`: Quote, Author, Context, Rating, Source, Date, IncludeInSchema | Review (only with IncludeInSchema + Source) |
| `ElementTeam` | Title, Intro (HTML), buttons | `TeamMember`: Name, Role, Bio, Image + ImageAlt | Person |
| `ElementLogoWall` | Title | `LogoItem`: Name (= alt), Image, Link | – |
| `ElementGallery` | Title, Intro | sortable images (title = alt) | ImageGallery |

Shared by all, via `ElementBase`:

- `HeadingLevel`, limited by the `heading_levels` config;
- `CTALinks` (LinkField, multiple), disabled with `has_cta_links: false`;
- template helpers `$Heading('classes')`, `$ChildHeadingTag`, `$IsFirstElement` and `$SortedCTALinks`.

Templates live in `templates/YouWillLikeIT/Elements/Elements/`. They contain semantic HTML with
`ywli-*` BEM classes and no framework classes. Override them in your theme.

## Fluent (multilingual sites only)

The module applies Fluent to its item models when Fluent is installed. The project applies it to the
base classes:

```yaml
---
Name: app-fluent
After: ['#fluentextensions', '#youwilllikeit-elements-fluent']
---
DNADesign\Elemental\Models\BaseElement:
  extensions:
    - TractorCow\Fluent\Extension\FluentVersionedExtension
SilverStripe\LinkField\Models\Link:
  extensions:
    - TractorCow\Fluent\Extension\FluentVersionedExtension
```

Translatable fields are declared in each class's `$translate`.

## Section styles (opt-in)

```yaml
DNADesign\Elemental\Models\BaseElement:
  extensions:
    - YouWillLikeIT\Elements\Extensions\SectionStyleExtension

# Bootstrap mapping example
YouWillLikeIT\Elements\Extensions\SectionStyleExtension:
  background_classes:
    light: 'bg-light'
    dark: 'bg-dark text-bg-dark'
    primary: 'bg-primary text-bg-primary'
    image: 'text-bg-dark'
  spacing_classes:
    sm: 'py-4 py-lg-5'
    md: 'py-5 py-lg-6'
    lg: 'py-6 py-lg-7'
  width_classes:
    default: 'container'
    narrow: 'container container-narrow'
    full: 'container-fluid'
```

Render it in the theme's `templates/DNADesign/Elemental/Layout/ElementHolder.ss`:

```ss
<section class="element $SimpleClassName.LowerCase $SectionClasses<% if $StyleVariant %> $StyleVariant<% end_if %>" id="$Anchor"<% if $IsDarkSection %> data-bs-theme="dark"<% end_if %>>
    <% if $HasBackgroundImage %>
        <% with $BackgroundImage.ScaleMaxWidth(1920) %>
            <img class="section__bg" src="$URL" width="$Width" height="$Height" alt="" loading="lazy" decoding="async">
        <% end_with %>
    <% end_if %>
    <div class="$ContainerClass">$Element</div>
</section>
```

## Schema.org

When `dorsetdigital/silverstripe-schema-manager` is installed, `ElementalSchemaExtension` (on `SiteTree`)
collects entities from all elements that implement `ProvidesSchemaEntities`. Projects adjust them with
the element hook `updateSchemaEntities(array &$entities, SiteTree $page)`. The reviewed item of testimonials defaults to the
site organisation. Change it with the `schema_item_reviewed_id` config or the hook
`updateSchemaItemReviewedID(string &$id)`.

## Versioning

Semantic versioning. A new minor version adds elements or nullable fields, a patch fixes bugs, and a
major version is needed for anything that changes or removes fields, tables or template variables.
See [CHANGELOG.md](CHANGELOG.md).
