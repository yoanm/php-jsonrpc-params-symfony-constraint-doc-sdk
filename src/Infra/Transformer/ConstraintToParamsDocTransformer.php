<?php
namespace Yoanm\JsonRpcParamsSymfonyConstraintDoc\Infra\Transformer;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Constraints as Assert;
use Yoanm\JsonRpcParamsSymfonyConstraintDoc\App\Helper\ConstraintPayloadDocHelper;
use Yoanm\JsonRpcParamsSymfonyConstraintDoc\App\Helper\DocTypeHelper;
use Yoanm\JsonRpcParamsSymfonyConstraintDoc\App\Helper\MinMaxHelper;
use Yoanm\JsonRpcParamsSymfonyConstraintDoc\App\Helper\StringDocHelper;
use Yoanm\JsonRpcServerDoc\Domain\Model\Type\ArrayDoc;
use Yoanm\JsonRpcServerDoc\Domain\Model\Type\CollectionDoc;
use Yoanm\JsonRpcServerDoc\Domain\Model\Type\TypeDoc;

/**
 * Class ConstraintToParamsDocTransformer
 */
class ConstraintToParamsDocTransformer
{
    /** @var DocTypeHelper */
    private $docTypeHelper;
    /** @var StringDocHelper */
    private $stringDocHelper;
    /** @var MinMaxHelper */
    private $minMaxHelper;
    /** @var ConstraintPayloadDocHelper */
    private $constraintPayloadDocHelper;

    /**
     * @param DocTypeHelper              $docTypeHelper
     * @param ConstraintPayloadDocHelper $constraintPayloadDocHelper
     */
    public function __construct(
        DocTypeHelper $docTypeHelper,
        StringDocHelper $stringDocHelper,
        MinMaxHelper $minMaxHelper,
        ConstraintPayloadDocHelper $constraintPayloadDocHelper
    ) {
        $this->docTypeHelper = $docTypeHelper;
        $this->minMaxHelper = $minMaxHelper;
        $this->stringDocHelper = $stringDocHelper;
        $this->constraintPayloadDocHelper = $constraintPayloadDocHelper;
    }

    /**
     * @param Constraint $constraint
     *
     * @return TypeDoc
     */
    public function transform(Constraint $constraint) : TypeDoc
    {
        return $this->transformList([$constraint]);
    }

    /**
     * @param Constraint[] $constraintList
     *
     * @return TypeDoc
     */
    public function transformList(array $constraintList) : TypeDoc
    {
        $constraintDoc = $this->docTypeHelper->guess($constraintList);

        foreach ($constraintList as $constraint) {
            $this->appendToDoc($constraintDoc, $constraint);
        }

        return $constraintDoc;
    }

    /**
     * @param TypeDoc $doc
     * @param Constraint   $constraint
     */
    private function appendToDoc(TypeDoc $doc, Constraint $constraint) : void
    {
        static $notNullConstraintList = [
            Assert\NotNull::class,
            Assert\IsTrue::class, // If it is true, it cannot be null ...
            Assert\IsFalse::class, // If it is false, it cannot be null ...
            // If should be identical to something, it cannot be null (but can be identical to null)
            Assert\IdenticalTo::class,
        ];
        if ($constraint instanceof Assert\Callback) {
            $callbackResult = call_user_func($constraint->callback);
            $callbackResultList = is_array($callbackResult) ? $callbackResult : [$callbackResult];
            foreach ($callbackResultList as $subConstraint) {
                $this->appendToDoc($doc, $subConstraint);
            }
        } elseif ($doc instanceof ArrayDoc && $constraint instanceof Assert\All) {
            $this->appendAllConstraintToDoc($doc, $constraint);
        } else {
            $this->stringDocHelper->append($doc, $constraint);
            $this->appendCollectionDoc($doc, $constraint);

            $this->minMaxHelper->append($doc, $constraint);
            $this->appendValidItemListDoc($doc, $constraint);

            if ($constraint instanceof Assert\Existence) {
                $doc->setRequired($constraint instanceof Assert\Required);
                foreach ($constraint->constraints as $subConstraint) {
                    $this->appendToDoc($doc, $subConstraint);
                }
            } elseif ($this->isInstanceOfOneClassIn($constraint, $notNullConstraintList)) {
                $doc->setNullable(
                    ($constraint instanceof Assert\IdenticalTo)
                        ? is_null($constraint->value)
                        : false
                );
                $defaultValue = $exampleValue = null;
                switch (true) {
                    case $constraint instanceof Assert\IsTrue:
                        $defaultValue = $exampleValue = true;
                        break;
                    case $constraint instanceof Assert\IsFalse:
                        $defaultValue = $exampleValue = false;
                        break;
                    case $constraint instanceof Assert\IdenticalTo:
                        $defaultValue = $exampleValue = $constraint->value;
                        break;
                }
                $doc->setDefault($doc->getDefault() ?? $defaultValue);
                $doc->setExample($doc->getExample() ?? $exampleValue);
            }
        }
        // /!\ Payload doc will override values even if already defined
        $this->constraintPayloadDocHelper->appendPayloadDoc($doc, $constraint);
    }

    /**
     * @param TypeDoc $doc
     * @param Constraint $constraint
     */
    private function appendCollectionDoc(TypeDoc $doc, Constraint $constraint) : void
    {
        // If not a collection => give up
        if (!$doc instanceof CollectionDoc) {
            return;
        }

        if ($constraint instanceof Assert\Collection) {
            foreach ($constraint->fields as $fieldName => $subConstraint) {
                $sibling = $this->transform($subConstraint);
                $doc->addSibling(
                    $sibling->setName($fieldName)
                );
            }

            $doc->setAllowExtraSibling($constraint->allowExtraFields === true);
            $doc->setAllowMissingSibling($constraint->allowMissingFields === true);
        }
    }

    /**
     * @param TypeDoc $doc
     * @param Constraint $constraint
     */
    private function appendValidItemListDoc(TypeDoc $doc, Constraint $constraint) : void
    {
        if ($constraint instanceof Assert\Choice) {
            if ($constraint->callback && is_callable($constraint->callback)) {
                $choiceList = call_user_func($constraint->callback);
            } else {
                $choiceList = $constraint->choices ?? [];
            }
            foreach ($choiceList as $choice) {
                $this->addToAllowedValueListIfNotExist($doc, $choice);
            }
        } elseif ($constraint instanceof Assert\IsNull) {
            $this->addToAllowedValueListIfNotExist($doc, null);
        } elseif ($constraint instanceof Assert\IdenticalTo) {
            $this->addToAllowedValueListIfNotExist($doc, $constraint->value);
        } elseif ($constraint instanceof Assert\IsTrue) {
            $this->addToAllowedValueListIfNotExist($doc, true);
            $this->addToAllowedValueListIfNotExist($doc, 1);
            $this->addToAllowedValueListIfNotExist($doc, '1');
        } elseif ($constraint instanceof Assert\IsFalse) {
            $this->addToAllowedValueListIfNotExist($doc, false);
            $this->addToAllowedValueListIfNotExist($doc, 0);
            $this->addToAllowedValueListIfNotExist($doc, '0');
        } elseif ($constraint instanceof Assert\EqualTo) {
            $this->addToAllowedValueListIfNotExist($doc, $constraint->value);
        }
    }

    /**
     * @param ArrayDoc   $doc
     * @param Assert\All $constraint
     */
    private function appendAllConstraintToDoc(ArrayDoc $doc, Assert\All $constraint) : void
    {
        $itemDoc = $this->docTypeHelper->guess($constraint->constraints);
        foreach ($constraint->constraints as $subConstraint) {
            $this->appendToDoc($itemDoc, $subConstraint);
        }

        $doc->setItemValidation($itemDoc);
    }

    /**
     * @param       $object
     * @param array $classList
     *
     * @return bool
     */
    private function isInstanceOfOneClassIn($object, array $classList) : bool
    {
        $actualClassList = array_merge(
            [get_class($object)],
            class_implements($object),
            class_uses($object)
        );
        $parentClass = get_parent_class($object);
        while (false !== $parentClass) {
            $actualClassList[] = $parentClass;
            $parentClass = get_parent_class($parentClass);
        }

        return count(array_intersect($actualClassList, $classList)) > 0;
    }

    private function addToAllowedValueListIfNotExist(TypeDoc $doc, $value)
    {
        if (!in_array($value, $doc->getAllowedValueList(), true)) {
            $doc->addAllowedValue($value);
        }
    }
}
