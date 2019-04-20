<?php
namespace Yoanm\JsonRpcParamsSymfonyConstraintDoc\App\Helper;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Constraints as Assert;
use Yoanm\JsonRpcServerDoc\Domain\Model\Type\CollectionDoc;
use Yoanm\JsonRpcServerDoc\Domain\Model\Type\NumberDoc;
use Yoanm\JsonRpcServerDoc\Domain\Model\Type\StringDoc;
use Yoanm\JsonRpcServerDoc\Domain\Model\Type\TypeDoc;

/**
 * Class MinMaxHelper
 */
class MinMaxHelper
{
    use ClassComparatorTrait;

    /**
     * @param TypeDoc    $doc
     * @param Constraint $constraint
     */
    public function append(TypeDoc $doc, Constraint $constraint) : void
    {
        if ($doc instanceof StringDoc) {
            $this->appendStringDoc($doc, $constraint);
        } elseif ($doc instanceof NumberDoc) {
            $this->appendNumberDoc($doc, $constraint);
        } elseif ($doc instanceof CollectionDoc) {
            $this->appendCollectionDoc($doc, $constraint);
        }
    }

    /**
     * @param StringDoc  $doc
     * @param Constraint $constraint
     */
    private function appendStringDoc(StringDoc $doc, Constraint $constraint) : void
    {
        $min = $max = null;
        if ($constraint instanceof Assert\Length) {
            $min = $constraint->min;
            $max = $constraint->max;
        } elseif ($constraint instanceof Assert\NotBlank && null === $doc->getMinLength()) {
            // Not blank so minimum 1 character
            $min = 1;
        } elseif ($constraint instanceof Assert\Blank && null === $doc->getMaxLength()) {
            // Blank so maximum 0 character
            $max = 0;
        }

        $this->setMinMaxLengthIfNotNull($doc, $min, $max);
    }

    /**
     * @param NumberDoc  $doc
     * @param Constraint $constraint
     */
    private function appendNumberDoc(NumberDoc $doc, Constraint $constraint) : void
    {
        $this->appendNumberMinMax($doc, $constraint);

        if ($constraint instanceof Assert\LessThan) {
            $doc->setInclusiveMax(false);
        } elseif ($constraint instanceof Assert\GreaterThan) {
            $doc->setInclusiveMin(false);
        }
    }

    /**
     * @param CollectionDoc $doc
     * @param Constraint    $constraint
     */
    private function appendCollectionDoc(CollectionDoc $doc, Constraint $constraint) : void
    {
        $min = $max = null;
        if ($constraint instanceof Assert\Choice || $constraint instanceof Assert\Count) {
            $min = $constraint->min;
            $max = $constraint->max;
        } elseif ($constraint instanceof Assert\NotBlank && null === $doc->getMinItem()) {
            // Not blank so minimum 1 item
            $min = 1;
        } /* Documentation does not mention array, counter to NotBlank constraint
         elseif ($constraint instanceof Assert\Blank && null === $doc->getMaxItem()) {
            // Blank so maximum 0 item
            $max = 0;
        }*/
        $this->setMinMaxItemIfNotNull($doc, $min, $max);
        $this->appendLessGreaterThanMinMaxItem($doc, $constraint);
    }

    /**
     * @param NumberDoc $doc
     * @param Constraint $constraint
     */
    private function appendNumberMinMax(NumberDoc $doc, Constraint $constraint) : void
    {
        $min = $max = null;
        if ($constraint instanceof Assert\Range) {
            $min = $constraint->min;
            $max = $constraint->max;
        } elseif ($constraint instanceof Assert\LessThanOrEqual
            || $constraint instanceof Assert\LessThan
        ) {
            $max = $constraint->value;
        } elseif ($constraint instanceof Assert\GreaterThanOrEqual
            || $constraint instanceof Assert\GreaterThan
        ) {
            $min = $constraint->value;
        }

        $this->setMinMaxIfNotNull($doc, $min, $max);
    }

    /**
     * @param CollectionDoc $doc
     * @param Constraint    $constraint
     */
    private function appendLessGreaterThanMinMaxItem(CollectionDoc $doc, Constraint $constraint): void
    {
        $min = $max = null;
        $gtConstraintList = [Assert\GreaterThan::class, Assert\GreaterThanOrEqual::class];
        $ltConstraintList = [Assert\LessThan::class, Assert\LessThanOrEqual::class];
        if (null !== ($match = $this->getMatchingClassNameIn($constraint, $gtConstraintList))) {
            $min = ($match === Assert\GreaterThanOrEqual::class)
                ? $constraint->value
                : $constraint->value + 1;
        } elseif (null !== ($match = $this->getMatchingClassNameIn($constraint, $ltConstraintList))) {
            $max = ($match === Assert\LessThanOrEqual::class)
                ? $constraint->value
                : $constraint->value - 1
            ;
        }

        $this->setMinMaxItemIfNotNull($doc, $min, $max);
    }

    /**
     * @param StringDoc      $doc
     * @param null|int|mixed $min
     * @param null|int|mixed $max
     */
    private function setMinMaxLengthIfNotNull(StringDoc $doc, $min, $max): void
    {
        if (null !== $min) {
            $doc->setMinLength((int)$min);
        }
        if (null !== $max) {
            $doc->setMaxLength((int)$max);
        }
    }

    /**
     * @param CollectionDoc  $doc
     * @param null|int|mixed $min
     * @param null|int|mixed $max
     */
    private function setMinMaxItemIfNotNull(CollectionDoc $doc, $min, $max): void
    {
        if (null !== $min) {
            $doc->setMinItem((int) $min);
        }
        if (null !== $max) {
            $doc->setMaxItem((int) $max);
        }
    }

    /**
     * @param NumberDoc      $doc
     * @param null|int|mixed $min
     * @param null|int|mixed $max
     */
    private function setMinMaxIfNotNull(NumberDoc $doc, $min, $max): void
    {
        if (null !== $min) {
            $doc->setMin($min);
        }
        if (null !== $max) {
            $doc->setMax($max);
        }
    }
}
