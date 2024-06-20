<?php
namespace Yoanm\JsonRpcParamsSymfonyConstraintDoc\App\Helper;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Constraints as Assert;
use Yoanm\JsonRpcServerDoc\Domain\Model\Type\ArrayDoc;
use Yoanm\JsonRpcServerDoc\Domain\Model\Type\BooleanDoc;
use Yoanm\JsonRpcServerDoc\Domain\Model\Type\CollectionDoc;
use Yoanm\JsonRpcServerDoc\Domain\Model\Type\NumberDoc;
use Yoanm\JsonRpcServerDoc\Domain\Model\Type\ObjectDoc;
use Yoanm\JsonRpcServerDoc\Domain\Model\Type\ScalarDoc;
use Yoanm\JsonRpcServerDoc\Domain\Model\Type\StringDoc;
use Yoanm\JsonRpcServerDoc\Domain\Model\Type\TypeDoc;

/**
 * Class TypeGuesser
 */
class TypeGuesser
{
    use ClassComparatorTrait;

    const STRING_CONSTRAINT_CLASS_LIST = [
        Assert\Length::class, // << Applied on string only
        Assert\Date::class,  // << validator expect a string with specific format
        Assert\Time::class,  // << validator expect a string with specific format
        Assert\Bic::class,
        Assert\CardScheme::class,
        Assert\Country::class,
        Assert\Currency::class,
        Assert\Email::class,
        Assert\File::class,
        Assert\Iban::class,
        Assert\Ip::class,
        Assert\Isbn::class,
        Assert\Issn::class,
        Assert\Language::class,
        Assert\Locale::class,
        Assert\Luhn::class,
        Assert\Url::class,
        Assert\Uuid::class,
        Assert\ExpressionSyntax::class,
        Assert\ExpressionLanguageSyntax::class,
        Assert\Hostname::class,
        Assert\Cidr::class,
        Assert\Json::class,
        Assert\Ulid::class,
        Assert\CssColor::class,
        Assert\Timezone::class,
        Assert\Isin::class,
        Assert\Blank::class,
    ];
    const NUMBER_CONSTRAINT_CLASS_LIST = [
        Assert\DivisibleBy::class,
        Assert\Positive::class,
        Assert\PositiveOrZero::class,
        Assert\Negative::class,
        Assert\NegativeOrZero::class,
    ];
    const BOOLEAN_CONSTRAINT_CLASS_LIST = [
        Assert\IsTrue::class,
        Assert\IsFalse::class,
    ];
    const OBJECT_CONSTRAINT_CLASS_LIST = [
        Assert\Valid::class,
        Assert\Cascade::class,
        Assert\Traverse::class,
    ];
    const COLLECTION_CONSTRAINT_CLASS_LIST = [
        // Count is primarily applied on array, but can also be used with an object implementing Countable
        Assert\Count::class,
        // All is primarily applied on array, but can also be used with an object implementing Traversable
        Assert\All::class,
        // Unique is primarily applied on array, but can also be used with an object implementing Traversable
        Assert\Unique::class,
    ];

    /**
     * @param array $constraintList
     *
     * @return TypeDoc|null
     */
    public function guessTypeFromConstraintList(array $constraintList) : ?TypeDoc
    {
        $doc = $abstractTypeFound = null;
        foreach ($constraintList as $constraint) {
            $doc = $this->guessTypeFromConstraint($constraint);
            if (null !== $doc) {
                if ($this->isAbstractType($doc)) {
                     // Abstract type => continue to see if better type can be found
                     $abstractTypeFound = $doc;
                     $doc = null;
                } else {
                     break;
                }
            }
        }
        // Try to fallback on abstractType if found
        if (null === $doc && null !== $abstractTypeFound) {
            $doc = $abstractTypeFound;
        }

        return $doc;
    }

    /**
     * @param Constraint $constraint
     *
     * @return TypeDoc|null
     */
    protected function guessTypeFromConstraint(Constraint $constraint) : ?TypeDoc
    {
        // Try to guess primary types
        $type = $this->guessSimplePrimaryTypeFromConstraint($constraint);
        if (null !== $type) {
            return $type;
        } elseif ($constraint instanceof Assert\DateTime) {
            return $this->guessDateTimeType($constraint);
        } elseif ($constraint instanceof Assert\Collection) {
            return $this->guessCollectionType($constraint);
        } elseif ($this->isCollectionType($constraint)) {
            return new CollectionDoc();
        }

        return null;
    }

    /**
     * @param TypeDoc $doc
     *
     * @return bool
     */
    private function isAbstractType(TypeDoc $doc) : bool
    {
        // use get_class to avoid inheritance issue
        $class = get_class($doc);

        return CollectionDoc::class === $class
            || NumberDoc::class === $class
            || ScalarDoc::class === $class
        ;
    }

    /**
     * @param Assert\DateTime $constraint
     *
     * @return ScalarDoc|StringDoc
     */
    private function guessDateTimeType(Assert\DateTime $constraint) : TypeDoc
    {
        if ('U' === $constraint->format) {
            return new ScalarDoc();// Don't know if value will be an number as string or as integer
        }

        return new StringDoc();
    }

    /**
     * @param Assert\Collection $constraint
     *
     * @return ArrayDoc|ObjectDoc
     */
    private function guessCollectionType(Assert\Collection $constraint) : TypeDoc
    {
        // If only integer keys (strict check) => array, else object
        $integerKeyList = array_filter(array_keys($constraint->fields), fn ($v) => (int)$v === $v);
        if (count($constraint->fields) === count($integerKeyList)) {
            return new ArrayDoc();
        }

        return new ObjectDoc();
    }

    private function isStringConstraint(Constraint $constraint): bool
    {
        return null !== $this->getMatchingClassNameIn($constraint, self::STRING_CONSTRAINT_CLASS_LIST);
    }

    private function isBooleanConstraint(Constraint $constraint): bool
    {
        return null !== $this->getMatchingClassNameIn($constraint, self::BOOLEAN_CONSTRAINT_CLASS_LIST);
    }

    private function isNumberConstraint(Constraint $constraint): bool
    {
        return null !== $this->getMatchingClassNameIn($constraint, self::NUMBER_CONSTRAINT_CLASS_LIST);
    }

    /**
     * @param Constraint $constraint
     *
     * @return bool
     */
    private function isArrayConstraint(Constraint $constraint): bool
    {
        return ($constraint instanceof Assert\Choice
                && true === $constraint->multiple // << expect an array multiple choices
            );
    }

    private function isObjectType(Constraint $constraint): bool
    {
        return null !== $this->getMatchingClassNameIn($constraint, self::OBJECT_CONSTRAINT_CLASS_LIST);
    }

    /**
     * Must be called at the end in case all others checks failed !
     */
    private function isCollectionType(Constraint $constraint): bool
    {
        return null !== $this->getMatchingClassNameIn($constraint, self::COLLECTION_CONSTRAINT_CLASS_LIST);
    }

    /**
     * @param Constraint $constraint
     *
     * @return TypeDoc|null
     */
    private function guessSimplePrimaryTypeFromConstraint(Constraint $constraint) : ?TypeDoc
    {
        if ($this->isStringConstraint($constraint)) {
            return new StringDoc();
        } elseif ($this->isBooleanConstraint($constraint)) {
            return new BooleanDoc();
        } elseif ($this->isNumberConstraint($constraint)) {
            return new NumberDoc();
        } elseif ($constraint instanceof Assert\Regex) {
            return new ScalarDoc();
        } elseif ($this->isArrayConstraint($constraint)) {
            return new ArrayDoc();
        } elseif ($this->isObjectType($constraint)) {
            return new ObjectDoc();
        }

        return null;
    }
}
