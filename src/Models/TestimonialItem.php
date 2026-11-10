<?php

declare(strict_types=1);

namespace YouWillLikeIT\Elements\Models;

use SilverStripe\Core\Validation\ValidationResult;
use SilverStripe\Forms\CheckboxField;
use SilverStripe\Forms\DropdownField;
use SilverStripe\Forms\FieldList;
use SilverStripe\Forms\TextareaField;
use SilverStripe\ORM\DataObject;
use SilverStripe\Versioned\Versioned;
use YouWillLikeIT\Elements\Elements\ElementTestimonials;

/**
 * @property string $Quote
 * @property string $Author
 * @property string $Context
 * @property int $Rating
 * @property string $Source
 * @property string $Date
 * @property bool $IncludeInSchema
 * @property int $Sort
 * @method ElementTestimonials Parent()
 */
class TestimonialItem extends DataObject
{
    use ItemPermissions;

    private static string $table_name = 'YWLI_TestimonialItem';

    private static string $singular_name = 'Quote';

    private static string $plural_name = 'Quotes';

    private static array $db = [
        'Quote' => 'Text',
        'Author' => 'Varchar(120)',
        'Context' => 'Varchar(160)',
        'Rating' => 'Int',
        'Source' => 'Varchar(120)',
        'Date' => 'Date',
        'IncludeInSchema' => 'Boolean',
        'Sort' => 'Int',
    ];

    private static array $has_one = [
        'Parent' => ElementTestimonials::class,
    ];

    private static array $extensions = [
        Versioned::class,
    ];

    private static string $default_sort = 'Sort ASC';

    private static array $summary_fields = [
        'Author',
        'Quote.Summary',
        'Rating',
        'IncludeInSchema.Nice',
    ];

    private static array $translate = [
        'Quote',
        'Context',
    ];

    public function fieldLabels($includerelations = true): array
    {
        $labels = parent::fieldLabels($includerelations);
        $labels['Quote'] = _t(self::class . '.Quote', 'Quote');
        $labels['Quote.Summary'] = $labels['Quote'];
        $labels['Author'] = _t(self::class . '.Author', 'Author');
        $labels['Context'] = _t(self::class . '.Context', 'Context (e.g. vehicle, service, city)');
        $labels['Rating'] = _t(self::class . '.Rating', 'Stars');
        $labels['Source'] = _t(self::class . '.Source', 'Source');
        $labels['Date'] = _t(self::class . '.Date', 'Date');
        $labels['IncludeInSchema'] = _t(self::class . '.IncludeInSchema', 'Publish as review in structured data');
        $labels['IncludeInSchema.Nice'] = _t(self::class . '.Schema', 'Schema');

        return $labels;
    }

    public function getCMSFields(): FieldList
    {
        $this->beforeUpdateCMSFields(function (FieldList $fields): void {
            $fields->removeByName(['ParentID', 'Sort']);
            $fields->replaceField('Quote', TextareaField::create('Quote', $this->fieldLabel('Quote'))->setRows(5));
            $fields->replaceField('Rating', DropdownField::create('Rating', $this->fieldLabel('Rating'), [
                0 => _t(self::class . '.NoRating', 'No rating'),
                1 => '1', 2 => '2', 3 => '3', 4 => '4', 5 => '5',
            ]));
            $fields->dataFieldByName('Source')?->setDescription(_t(
                self::class . '.SourceHint',
                'Where the review was published, e.g. "Google". Required for structured data.'
            ));
            $fields->replaceField(
                'IncludeInSchema',
                CheckboxField::create('IncludeInSchema', $this->fieldLabel('IncludeInSchema'))
                    ->setDescription(_t(
                        self::class . '.IncludeInSchemaHint',
                        'Only for real, verifiable reviews with author and source.'
                    ))
            );
        });

        return parent::getCMSFields();
    }

    public function getTitle(): string
    {
        return (string) $this->Author;
    }

    public function getIsSchemaEligible(): bool
    {
        return $this->IncludeInSchema
            && trim((string) $this->Quote) !== ''
            && trim((string) $this->Author) !== ''
            && trim((string) $this->Source) !== '';
    }

    /**
     * Accessible label, e.g. "4 of 5 stars".
     */
    public function getRatingLabel(): string
    {
        return _t(self::class . '.RatingLabel', '{rating} of 5 stars', ['rating' => $this->getClampedRating()]);
    }

    /**
     * Visual stars (render aria-hidden next to RatingLabel).
     */
    public function getStars(): string
    {
        $rating = $this->getClampedRating();

        return str_repeat('★', $rating) . str_repeat('☆', 5 - $rating);
    }

    private function getClampedRating(): int
    {
        return max(0, min(5, (int) $this->Rating));
    }

    public function validate(): ValidationResult
    {
        $result = parent::validate();
        if (trim((string) $this->Quote) === '') {
            $result->addFieldError('Quote', _t(self::class . '.QuoteRequired', 'Please enter the quote.'));
        }
        if (trim((string) $this->Author) === '') {
            $result->addFieldError('Author', _t(self::class . '.AuthorRequired', 'Please enter the author.'));
        }
        if ($this->IncludeInSchema && trim((string) $this->Source) === '') {
            $result->addFieldError('Source', _t(self::class . '.SourceRequired', 'A source is required for structured data.'));
        }

        return $result;
    }
}
