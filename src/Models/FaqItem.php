<?php

declare(strict_types=1);

namespace YouWillLikeIT\Elements\Models;

use SilverStripe\Core\Validation\ValidationResult;
use SilverStripe\Forms\FieldList;
use SilverStripe\Forms\HTMLEditor\HTMLEditorField;
use SilverStripe\Forms\TextField;
use SilverStripe\ORM\DataObject;
use SilverStripe\Versioned\Versioned;
use YouWillLikeIT\Elements\Elements\ElementFaq;

/**
 * @property string $Question
 * @property string $Answer
 * @property int $Sort
 * @method ElementFaq Parent()
 */
class FaqItem extends DataObject
{
    use ItemPermissions;

    private static string $table_name = 'YWLI_FaqItem';

    private static string $singular_name = 'Question';

    private static string $plural_name = 'Questions';

    private static array $db = [
        'Question' => 'Varchar(255)',
        'Answer' => 'HTMLText',
        'Sort' => 'Int',
    ];

    private static array $has_one = [
        'Parent' => ElementFaq::class,
    ];

    private static array $extensions = [
        Versioned::class,
    ];

    private static string $default_sort = 'Sort ASC';

    private static array $summary_fields = [
        'Question',
        'Answer.Summary',
    ];

    private static array $translate = [
        'Question',
        'Answer',
    ];

    public function fieldLabels($includerelations = true): array
    {
        $labels = parent::fieldLabels($includerelations);
        $labels['Question'] = _t(self::class . '.Question', 'Question');
        $labels['Answer'] = _t(self::class . '.Answer', 'Answer');
        $labels['Answer.Summary'] = $labels['Answer'];

        return $labels;
    }

    public function getCMSFields(): FieldList
    {
        $this->beforeUpdateCMSFields(function (FieldList $fields): void {
            $fields->removeByName(['ParentID', 'Sort']);
            $fields->replaceField('Question', TextField::create('Question', $this->fieldLabel('Question')));
            $fields->replaceField('Answer', HTMLEditorField::create('Answer', $this->fieldLabel('Answer'))->setRows(8));
        });

        return parent::getCMSFields();
    }

    public function getTitle(): string
    {
        return (string) $this->Question;
    }

    public function validate(): ValidationResult
    {
        $result = parent::validate();
        if (trim((string) $this->Question) === '') {
            $result->addFieldError('Question', _t(self::class . '.QuestionRequired', 'Please enter the question.'));
        }

        return $result;
    }
}
