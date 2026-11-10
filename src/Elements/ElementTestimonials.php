<?php

declare(strict_types=1);

namespace YouWillLikeIT\Elements\Elements;

use DorsetDigital\SchemaManager\Control\SchemaIDs;
use DorsetDigital\SchemaManager\Model\Schema\RatingSchema;
use DorsetDigital\SchemaManager\Model\Schema\ReviewSchema;
use SilverStripe\CMS\Model\SiteTree;
use SilverStripe\Forms\FieldList;
use SilverStripe\ORM\HasManyList;
use YouWillLikeIT\Elements\Models\TestimonialItem;
use YouWillLikeIT\Elements\Schema\ProvidesSchemaEntities;

/**
 * Customer quotes. Only entries marked "IncludeInSchema" with a source become Review entities.
 *
 * @method HasManyList<TestimonialItem> Items()
 */
class ElementTestimonials extends ElementBase implements ProvidesSchemaEntities
{
    private static string $table_name = 'YWLI_ElementTestimonials';

    private static string $icon = 'font-icon-chat';

    private static string $singular_name = 'Testimonials';

    private static string $plural_name = 'Testimonials';

    private static string $class_description = 'Customer quotes and reviews (Review schema for sourced reviews)';

    private static array $has_many = [
        'Items' => TestimonialItem::class,
    ];

    private static array $owns = [
        'Items',
    ];

    private static array $cascade_deletes = [
        'Items',
    ];

    private static array $cascade_duplicates = [
        'Items',
    ];

    private static bool $has_cta_links = false;

    /**
     * Schema @id of the reviewed entity. Null = site organisation.
     * Projects may also use the extension hook updateSchemaItemReviewedID(string &$id).
     */
    private static ?string $schema_item_reviewed_id = null;

    public function fieldLabels($includerelations = true): array
    {
        $labels = parent::fieldLabels($includerelations);
        $labels['Items'] = _t(self::class . '.Items', 'Quotes');

        return $labels;
    }

    public function getCMSFields(): FieldList
    {
        $this->beforeUpdateCMSFields(function (FieldList $fields): void {
            $fields->removeByName(['Items']);
            $fields->addFieldToTab('Root.Main', $this->itemsField('Items', $this->fieldLabel('Items')));
        });

        return parent::getCMSFields();
    }

    public function getSummary(): string
    {
        return _t(self::class . '.Summary', '{count} quotes', ['count' => $this->Items()->count()]);
    }

    public function getSortedItems(): HasManyList
    {
        return $this->Items()->sort('Sort');
    }

    public function schemaEntities(SiteTree $page): array
    {
        if (!class_exists(ReviewSchema::class)) {
            return [];
        }

        $itemReviewed = (string) ($this->config()->get('schema_item_reviewed_id') ?: SchemaIDs::organisation());
        $this->extend('updateSchemaItemReviewedID', $itemReviewed);

        $entities = [];
        foreach ($this->getSortedItems() as $item) {
            if (!$item->getIsSchemaEligible()) {
                continue;
            }

            $review = ReviewSchema::create(
                'testimonial-' . $item->ID,
                (string) $item->Quote,
                (string) $item->Author,
                $itemReviewed
            )->setIsPartOfPage($page->AbsoluteLink());

            if ($item->Date) {
                $review->setDatePublished((string) $item->Date);
            }
            if ((int) $item->Rating > 0) {
                $review->setRating(RatingSchema::create((int) $item->Rating, 5, 1));
            }

            $entities[] = $review;
        }

        return $entities;
    }
}
