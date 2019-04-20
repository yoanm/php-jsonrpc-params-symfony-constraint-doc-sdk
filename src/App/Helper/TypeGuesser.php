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
    ];
    const BOOLEAN_CONSTRAINT_CLASS_LIST = [
        Assert\IsTrue::class,
        Assert\IsFalse::class,
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
        if (null !== ($type = $this->guessPrimaryTypeFromConstraint($constraint))) {
            return $type;
        }

        // If primary type is still not defined
        $constraintClass = get_class($constraint);
        if (Assert\Count::class == $constraintClass) {
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
     * @param Constraint $constraint
     *
     * @return null|ArrayDoc|BooleanDoc|ObjectDoc|ScalarDoc|StringDoc
     */
    private function guessPrimaryTypeFromConstraint(Constraint $constraint) : ?TypeDoc
    {
        // Try to guess primary types
        if (null !== $this->getMatchingClassNameIn($constraint, self::STRING_CONSTRAINT_CLASS_LIST)) {
            return new StringDoc();
        } elseif (null !== $this->getMatchingClassNameIn($constraint, self::BOOLEAN_CONSTRAINT_CLASS_LIST)) {
            return new BooleanDoc();
        } elseif ($constraint instanceof Assert\DateTime) {
            return $this->guessDateTimeType($constraint);
        } elseif ($constraint instanceof Assert\Collection) {
            return $this->guestCollectionType($constraint);
        } elseif ($constraint instanceof Assert\Regex) {
            return new ScalarDoc();
        } elseif ($constraint instanceof Assert\All // << Applied only on array
            || ($constraint instanceof Assert\Choice
                && true === $constraint->multiple // << expect an array multiple choices
            )
        ) {
            return new ArrayDoc();
        }

        return null;
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
    private function guestCollectionType(Assert\Collection $constraint) : TypeDoc
    {
        // If only integer => array, else object
        $integerKeyList = array_filter(array_keys($constraint->fields), 'is_int');
        if (count($constraint->fields) === count($integerKeyList)) {
            return new ArrayDoc();
        }

        return new ObjectDoc();
    }
}
