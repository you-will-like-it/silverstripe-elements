<?php

declare(strict_types=1);

namespace YouWillLikeIT\Elements\Schema;

use SilverStripe\CMS\Model\SiteTree;

/**
 * Implemented by elements that contribute Schema.org entities to their page.
 * No hard dependency on the schema module: implementations must return [] when it is absent.
 */
interface ProvidesSchemaEntities
{
    /**
     * @return array<int, \DorsetDigital\SchemaManager\Model\Schema\Schema>
     */
    public function schemaEntities(SiteTree $page): array;
}
