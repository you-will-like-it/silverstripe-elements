<?php

declare(strict_types=1);

namespace YouWillLikeIT\Elements\Schema;

use DNADesign\Elemental\Models\ElementalArea;
use DorsetDigital\SchemaManager\Model\Schema\Schema;
use SilverStripe\CMS\Model\SiteTree;
use SilverStripe\Core\Extension;

/**
 * Bridge to dorsetdigital/silverstripe-schema-manager: collects entities from all
 * elements in every elemental area of the page. Applied only when the module exists.
 *
 * Elements implement ProvidesSchemaEntities. Projects adjust an element's entities through
 * the extension hook updateSchemaEntities(array &$entities, SiteTree $page) on the element.
 *
 * @extends Extension<SiteTree>
 */
class ElementalSchemaExtension extends Extension
{
    /**
     * @param array<int, Schema> $entities
     */
    protected function updateSchemaManagerEntities(array &$entities): void
    {
        $page = $this->getOwner();
        if (!$page instanceof SiteTree) {
            return;
        }

        /** @var array<string, Schema> $byId */
        $byId = [];

        foreach ($this->getAreas($page) as $area) {
            foreach ($area->Elements() as $element) {
                if (!$element instanceof ProvidesSchemaEntities || !$element->canView()) {
                    continue;
                }
                $elementEntities = $element->schemaEntities($page);
                // Projects map their own extension fields here.
                $element->extend('updateSchemaEntities', $elementEntities, $page);

                foreach ($elementEntities as $schema) {
                    if (!$schema instanceof Schema) {
                        continue;
                    }
                    $id = $schema->getEntityID();
                    if (isset($byId[$id])) {
                        $this->merge($byId[$id], $schema);
                        continue;
                    }
                    $byId[$id] = $schema;
                }
            }
        }

        foreach ($byId as $schema) {
            $entities[] = $schema;
        }
    }

    /**
     * Several FAQ elements on one page produce one FAQPage: questions are appended.
     */
    private function merge(Schema $existing, Schema $incoming): void
    {
        $current = $existing->toArray();
        $new = $incoming->toArray();

        if (isset($current['mainEntity'], $new['mainEntity']) && is_array($current['mainEntity'])) {
            $existing->update(['mainEntity' => array_merge($current['mainEntity'], $new['mainEntity'])]);
            return;
        }

        $existing->update($new);
    }

    /**
     * @return array<int, ElementalArea>
     */
    private function getAreas(SiteTree $page): array
    {
        $areas = [];
        foreach ((array) $page->config()->get('has_one') as $relation => $class) {
            if (is_string($class) && is_a($class, ElementalArea::class, true)) {
                $area = $page->getComponent($relation);
                if ($area && $area->exists()) {
                    $areas[] = $area;
                }
            }
        }

        return $areas;
    }
}
