# Changelog

## 2.0.0 — 2026-11-10

First release as a standalone repository. It is a breaking change compared to 1.0.0, which was embedded in
`silverstripe-autohaus-bulic`. Sites on 1.x keep their embedded copy.

### Changed
- Basics only, with no layout. Removed fields that existed only for layout: `Eyebrow`; `Variant`, `ShowBreadcrumbs` and `TrustItems` (Hero); `ImagePosition` and `SecondImage` (TextMedia); `Variant`, `Columns` and `Icon` (FeatureGrid); `Layout` (FAQ); `Variant` (Testimonials); `Columns` (Gallery). Projects add these via extensions (docs/extending.md).
- Templates are plain semantic HTML with `ywli-*` classes and no Bootstrap. The FAQ uses native `<details>`.
- `SectionStyleExtension` is opt-in. `ContainerWidth` was renamed to `ContentWidth` (`default`, `narrow`, `full`), and CSS classes are mapped in YAML.
- `HeadingLevel` enum now includes `h1`. In 1.0 the Hero default of `h1` was outside the enum.
- Element descriptions use Elemental 6's `class_description`. The 1.0 `description` was never shown.
- CMS labels default to English, with translations in `lang/de.yml` and `lang/bs.yml`.
- `display-logic` became optional (suggest).

### Added
- `HasAccessibleImage` trait: required alt text whenever an image is set.
- `$ChildHeadingTag` for correct heading order of entries.
- Element schema hook `updateSchemaEntities(array &$entities, SiteTree $page)`.
- Validation for required item fields. Reviews go into the schema only with author and source.

## 1.0.0 — 2026-11-07

Initial version inside `you-will-like-it/silverstripe-autohaus-bulic`.
