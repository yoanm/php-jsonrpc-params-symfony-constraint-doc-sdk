<?php
namespace Yoanm\JsonRpcParamsSymfonyConstraintDoc\App\Helper;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Constraints as Assert;
use Yoanm\JsonRpcServerDoc\Domain\Model\Type\ArrayDoc;
use Yoanm\JsonRpcServerDoc\Domain\Model\Type\BooleanDoc;
use Yoanm\JsonRpcServerDoc\Domain\Model\Type\CollectionDoc;
use Yoanm\JsonRpcServerDoc\Domain\Model\Type\FloatDoc;
use Yoanm\JsonRpcServerDoc\Domain\Model\Type\IntegerDoc;
use Yoanm\JsonRpcServerDoc\Domain\Model\Type\NumberDoc;
use Yoanm\JsonRpcServerDoc\Domain\Model\Type\ObjectDoc;
use Yoanm\JsonRpcServerDoc\Domain\Model\Type\ScalarDoc;
use Yoanm\JsonRpcServerDoc\Domain\Model\Type\StringDoc;
use Yoanm\JsonRpcServerDoc\Domain\Model\Type\TypeDoc;

/**
 * Class DocTypeHelper
 */
class DocTypeHelper
{
    /** @var ConstraintPayloadDocHelper */
    private $constraintPayloadDocHelper;

    /**
     * @param ConstraintPayloadDocHelper $constraintPayloadDocHelper
     */
    public function __construct(ConstraintPayloadDocHelper $constraintPayloadDocHelper)
    {
        $this->constraintPayloadDocHelper = $constraintPayloadDocHelper;
    }

    /**
     * @param Constraint[] $constraintList
     *
     * @return TypeDoc
     */
    public function guess(array $constraintList) : TypeDoc
    {
        return $this->getDocFromTypeConstraintOrPayloadDocIfExist($constraintList)
        ?? $this->guessTypeFromConstraintList($constraintList)
        ?? new TypeDoc();
    }

    /**
     * @param Constraint[] $constraintList
     *
     * @return TypeDoc|null
     */
    private function getDocFromTypeConstraintOrPayloadDocIfExist(array $constraintList)
    {
        $doc = null;
        // Check if a Type constraint exist or if a constraint have a type documentation
        foreach ($constraintList as $constraint) {
            if (null !== ($typeFromPayload = $this->constraintPayloadDocHelper->getTypeIfExist($constraint))) {
                $doc = $this->getDocFromType($typeFromPayload);
            } elseif ($constraint instanceof Assert\Type) {
                $doc = $this->getDocFromType(strtolower($constraint->type));
            }

            if (null !== $doc) {
                break;
            }
        }

        return $doc;
    }

    /**
     * @param array $constraintList
     *
     * @return TypeDoc|null
     */
    private function guessTypeFromConstraintList(array $constraintList)
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
    private function guessTypeFromConstraint(Constraint $constraint)
    {
        // Try to guess primary types
        switch (true) {
            case $constraint instanceof Assert\Existence:
                return $this->guess($constraint->constraints);
            case $constraint instanceof Assert\Length:// << Applied on string only
            case $constraint instanceof Assert\Date: // << validator expect a string with specific format
            case $constraint instanceof Assert\Time: // << validator expect a string with specific format
            case $constraint instanceof Assert\Bic:
            case $constraint instanceof Assert\CardScheme:
            case $constraint instanceof Assert\Country:
            case $constraint instanceof Assert\Currency:
            case $constraint instanceof Assert\Email:
            case $constraint instanceof Assert\File:
            case $constraint instanceof Assert\Iban:
            case $constraint instanceof Assert\Ip:
            case $constraint instanceof Assert\Isbn:
            case $constraint instanceof Assert\Issn:
            case $constraint instanceof Assert\Language:
            case $constraint instanceof Assert\Locale:
            case $constraint instanceof Assert\Luhn:
            case $constraint instanceof Assert\Regex:
            case $constraint instanceof Assert\Url:
            case $constraint instanceof Assert\Uuid:
                return new StringDoc();
            case $constraint instanceof Assert\DateTime:
                if ('U' === $constraint->format) {
                    return new ScalarDoc();// Don't know if value will be an number as string or as integer
                }

                return new StringDoc();
            case $constraint instanceof Assert\IsTrue:
            case $constraint instanceof Assert\IsFalse:
                return new BooleanDoc();
            case $constraint instanceof Assert\Collection:
                // If only integer => array, else object
                $integerKeyList = array_filter(array_keys($constraint->fields), 'is_int');
                if (count($constraint->fields) === count($integerKeyList)) {
                    return new ArrayDoc();
                }

                return new ObjectDoc();
            case $constraint instanceof Assert\Choice
                && true === $constraint->multiple: // << expect an array multiple choices
            case $constraint instanceof Assert\All: // << Applied only on array
                return new ArrayDoc();
        }

        // If primary type is still not defined
        switch (true) {
            case $constraint instanceof Assert\Range:
                if ((null !== $constraint->min && is_float($constraint->min))
                    || (null !== $constraint->max && is_float($constraint->max))
                ) {
                    return new FloatDoc();
                }

                return new NumberDoc();
            case $constraint instanceof Assert\GreaterThan:
            case $constraint instanceof Assert\GreaterThanOrEqual:
            case $constraint instanceof Assert\LessThan:
            case $constraint instanceof Assert\LessThanOrEqual:
                if (null !== $constraint->value && is_float($constraint->value)) {
                    return new FloatDoc();
                }

                return new NumberDoc();
            case $constraint instanceof Assert\Count:
                return new CollectionDoc();
        }

        return null;
    }

    /**
     * @param string $type
     *
     * @return TypeDoc|null
     */
    private function getDocFromType(string $type)
    {
        switch (true) {
            case 'scalar' === $type:
                return new ScalarDoc();
            case 'string' === $type:
                return new StringDoc();
            case 'bool' === $type || 'boolean' === $type:
                return new BooleanDoc();
            case 'int' === $type || 'integer' === $type:
                return new IntegerDoc();
            case in_array($type, ['float', 'long', 'double', 'real', 'numeric']):
                return new FloatDoc();
            case 'array' === $type:
                return new ArrayDoc();
            case 'object' === $type:
                return new ObjectDoc();
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
}
