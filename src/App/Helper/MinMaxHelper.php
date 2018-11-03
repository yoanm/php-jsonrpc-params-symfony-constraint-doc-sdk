<?php
namespace Yoanm\JsonRpcParamsSymfonyConstraintDoc\App\Helper;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Constraints as Assert;
use Yoanm\JsonRpcServerDoc\Domain\Model\Type\ArrayDoc;
use Yoanm\JsonRpcServerDoc\Domain\Model\Type\CollectionDoc;
use Yoanm\JsonRpcServerDoc\Domain\Model\Type\NumberDoc;
use Yoanm\JsonRpcServerDoc\Domain\Model\Type\StringDoc;
use Yoanm\JsonRpcServerDoc\Domain\Model\Type\TypeDoc;

/**
 * Class MinMaxHelper
 */
class MinMaxHelper
{
    /**
     * @param TypeDoc    $doc
     * @param Constraint $constraint
     */
    public function append(TypeDoc $doc, Constraint $constraint)
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
    private function appendStringDoc(StringDoc $doc, Constraint $constraint)
    {
        if ($constraint instanceof Assert\Length) {
            if (null !== $constraint->min) {
                $doc->setMinLength($constraint->min);
            }
            if (null !== $constraint->max) {
                $doc->setMaxLength($constraint->max);
            }
        } elseif ($constraint instanceof Assert\NotBlank && null === $doc->getMinLength()) {
            // Not blank so minimum 1 character
            $doc->setMinLength(1);
        } elseif ($constraint instanceof Assert\Blank && null === $doc->getMaxLength()) {
            // Blank so maximum 0 character
            $doc->setMaxLength(0);
        }
    }

    /**
     * @param NumberDoc  $doc
     * @param Constraint $constraint
     */
    private function appendNumberDoc(NumberDoc $doc, Constraint $constraint)
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
    private function appendCollectionDoc(CollectionDoc $doc, Constraint $constraint)
    {
        if ($constraint instanceof Assert\Choice) {
            if (null !== $constraint->min) {
                $doc->setMinItem($constraint->min);
            }
            if (null !== $constraint->max) {
                $doc->setMaxItem($constraint->max);
            }
        } elseif ($constraint instanceof Assert\Count) {
            if (null !== $constraint->min) {
                $doc->setMinItem($constraint->min);
            }
            if (null !== $constraint->max) {
                $doc->setMaxItem($constraint->max);
            }
        } elseif ($constraint instanceof Assert\NotBlank && null === $doc->getMinItem()) {
            // Not blank so minimum 1 item
            $doc->setMinItem(1);
        }/* // Documentation does not mention array, counter to NotBlank constraint
         elseif ($constraint instanceof Assert\Blank && null === $doc->getMaxItem()) {
            // Blank so maximum 0 item
            $doc->setMaxItem(0);
        }*/
        if ($constraint instanceof Assert\GreaterThan || $constraint instanceof Assert\GreaterThanOrEqual) {
            $doc->setMinItem(
                $constraint instanceof Assert\GreaterThanOrEqual
                    ? $constraint->value
                    : $constraint->value + 1
            );
        } elseif ($constraint instanceof Assert\LessThan || $constraint instanceof Assert\LessThanOrEqual) {
            $doc->setMaxItem(
                $constraint instanceof Assert\LessThanOrEqual
                    ? $constraint->value
                    : $constraint->value - 1
            );
        }
    }

    /**
     * @param NumberDoc $doc
     * @param Constraint $constraint
     */
    private function appendNumberMinMax(NumberDoc $doc, Constraint $constraint)
    {
        if ($constraint instanceof Assert\Range) {
            if (null !== $constraint->min) {
                $doc->setMin($constraint->min);
            }
            if (null !== $constraint->max) {
                $doc->setMax($constraint->max);
            }
        } elseif ($constraint instanceof Assert\LessThanOrEqual
            || $constraint instanceof Assert\LessThan
        ) {
            $doc->setMax($constraint->value);
        } elseif ($constraint instanceof Assert\GreaterThanOrEqual
            || $constraint instanceof Assert\GreaterThan
        ) {
            $doc->setMin($constraint->value);
        }
    }
}
