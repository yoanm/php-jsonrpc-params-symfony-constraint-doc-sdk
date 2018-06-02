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
        }
    }

    /**
     * @param NumberDoc  $doc
     * @param Constraint $constraint
     */
    private function appendNumberDoc(NumberDoc $doc, Constraint $constraint)
    {
        switch (true) {
            case $constraint instanceof Assert\Range:
                if (null !== $constraint->min) {
                    $doc->setMin($constraint->min);
                }
                if (null !== $constraint->max) {
                    $doc->setMax($constraint->max);
                }
                break;
            case $constraint instanceof Assert\LessThanOrEqual:
            case $constraint instanceof Assert\LessThan:
                $doc->setMax($constraint->value);
                break;
            case $constraint instanceof Assert\GreaterThanOrEqual:
            case $constraint instanceof Assert\GreaterThan:
                $doc->setMin($constraint->value);
                break;
        }
        switch (true) {
            case $constraint instanceof Assert\GreaterThan:
                $doc->setInclusiveMin(false);
                break;
            case $constraint instanceof Assert\LessThan:
                $doc->setInclusiveMax(false);
                break;
        }
    }

    /**
     * @param CollectionDoc $doc
     * @param Constraint    $constraint
     */
    private function appendCollectionDoc(CollectionDoc $doc, Constraint $constraint)
    {
        if ($constraint instanceof Assert\Count) {
            if (null !== $constraint->min) {
                $doc->setMinItem($constraint->min);
            }
            if (null !== $constraint->max) {
                $doc->setMaxItem($constraint->max);
            }
        }
    }
}
