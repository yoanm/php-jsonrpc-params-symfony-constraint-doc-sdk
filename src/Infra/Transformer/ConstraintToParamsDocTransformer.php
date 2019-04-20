<?php
namespace Yoanm\JsonRpcParamsSymfonyConstraintDoc\Infra\Transformer;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Constraints as Assert;
use Yoanm\JsonRpcParamsSymfonyConstraintDoc\App\Helper\ClassComparatorTrait;
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
    use ClassComparatorTrait;

    /** @var DocTypeHelper */
    private $docTypeHelper;
    /** @var StringDocHelper */
    private $stringDocHelper;
    /** @var MinMaxHelper */
    private $minMaxHelper;
    /** @var ConstraintPayloadDocHelper */
    private $constraintPayloadDocHelper;

    const CONSTRAINT_WITH_ALLOWED_VALUE_LIST = [
        Assert\IsTrue::class => [true, 1, '1'],
        Assert\IsFalse::class => [false, 0, '0'],
        Assert\IsNull::class => [null],
    ];

    const CONSTRAINT_WITH_ALLOWED_VALUE_LIST_FROM_PROPERTY = [
        Assert\IdenticalTo::class => 'value',
        Assert\EqualTo::class => 'value',
    ];

    const NULL_NOT_NULL_CONSTRAINT_LIST = [
        Assert\NotNull::class,
        Assert\IsTrue::class, // If it is true, it cannot be null ...
        Assert\IsFalse::class, // If it is false, it cannot be null ...
        // If should be identical to something, it cannot be null (but can be identical to null)
        Assert\IdenticalTo::class,
    ];



    /**
     * @param DocTypeHelper              $docTypeHelper
     * @param StringDocHelper            $stringDocHelper
     * @param MinMaxHelper               $minMaxHelper
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
     *
     * @throws \ReflectionException
     */
    public function transform(Constraint $constraint) : TypeDoc
    {
        return $this->transformList([$constraint]);
    }

    /**
     * @param Constraint[] $constraintList
     *
     * @return TypeDoc
     *
     * @throws \ReflectionException
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
     * @param TypeDoc    $doc
     * @param Constraint $constraint
     *
     * @throws \ReflectionException
     */
    private function appendToDoc(TypeDoc $doc, Constraint $constraint) : void
    {
        if ($constraint instanceof Assert\Callback) {
            $callbackResult = call_user_func($constraint->callback);
            $callbackResultList = is_array($callbackResult) ? $callbackResult : [$callbackResult];
            foreach ($callbackResultList as $subConstraint) {
                $this->appendToDoc($doc, $subConstraint);
            }
        } elseif ($doc instanceof ArrayDoc && $constraint instanceof Assert\All) {
            $this->appendAllConstraintToDoc($doc, $constraint);
        } else {
            $this->basicAppendToDoc($doc, $constraint);
        }
        // /!\ Payload doc will override values even if already defined
        $this->constraintPayloadDocHelper->appendPayloadDoc($doc, $constraint);
    }

    /**
     * @param TypeDoc    $doc
     * @param Constraint $constraint
     *
     * @throws \ReflectionException
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
            $this->appendChoiceAllowedValue($doc, $constraint);
        } elseif (null !== ($match = $this->getMatchingClassNameIn(
            $constraint,
            array_keys(self::CONSTRAINT_WITH_ALLOWED_VALUE_LIST_FROM_PROPERTY)
        ))) {
            $this->addToAllowedValueListIfNotExist(
                $doc,
                $constraint->{self::CONSTRAINT_WITH_ALLOWED_VALUE_LIST_FROM_PROPERTY[$match]}
            );
        } elseif (null !== ($match = $this->getMatchingClassNameIn(
            $constraint,
            array_keys(self::CONSTRAINT_WITH_ALLOWED_VALUE_LIST)
        ))) {
            $this->addListToAllowedValueListIfNotExist($doc, self::CONSTRAINT_WITH_ALLOWED_VALUE_LIST[$match]);
        }
    }

    /**
     * @param ArrayDoc   $doc
     * @param Assert\All $constraint
     *
     * @throws \ReflectionException
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
     * @param TypeDoc $doc
     * @param mixed[] $valueList
     */
    private function addListToAllowedValueListIfNotExist(TypeDoc $doc, array $valueList) : void
    {
        foreach ($valueList as $value) {
            $this->addToAllowedValueListIfNotExist($doc, $value);
        }
    }

    /**
     * @param TypeDoc $doc
     * @param mixed   $value
     */
    private function addToAllowedValueListIfNotExist(TypeDoc $doc, $value) : void
    {
        if (!in_array($value, $doc->getAllowedValueList(), true)) {
            $doc->addAllowedValue($value);
        }
    }

    /**
     * @param TypeDoc    $doc
     * @param Constraint $constraint
     *
     * @throws \ReflectionException
     */
    private function basicAppendToDoc(TypeDoc $doc, Constraint $constraint): void
    {
        $this->stringDocHelper->append($doc, $constraint);
        $this->appendCollectionDoc($doc, $constraint);

        $this->minMaxHelper->append($doc, $constraint);
        $this->appendValidItemListDoc($doc, $constraint);

        if ($constraint instanceof Assert\Existence) {
            $this->appendExistenceConstraintData($doc, $constraint);
        } elseif (null !== ($match = $this->getMatchingClassNameIn($constraint, self::NULL_NOT_NULL_CONSTRAINT_LIST))) {
            $this->setNulNotNullConstraintData($doc, $constraint, $match);
        }
    }

    /**
     * @param TypeDoc       $doc
     * @param Assert\Choice $constraint
     */
    private function appendChoiceAllowedValue(TypeDoc $doc, Assert\Choice $constraint): void
    {
        if ($constraint->callback && is_callable($constraint->callback)) {
            $choiceList = call_user_func($constraint->callback);
        } else {
            $choiceList = $constraint->choices ?? [];
        }
        $this->addListToAllowedValueListIfNotExist($doc, $choiceList);
    }

    /**
     * @param TypeDoc    $doc
     * @param Constraint $constraint
     * @param string     $sanitizedClass
     */
    private function setNulNotNullConstraintData(TypeDoc $doc, Constraint $constraint, string $sanitizedClass): void
    {
        $isIdenticalTo = $sanitizedClass === Assert\IdenticalTo::class;
        $doc->setNullable($isIdenticalTo ? is_null($constraint->value) : false);
        $defaultValue = $exampleValue = null;
        switch (true) {
            case $sanitizedClass === Assert\IsTrue::class:
                $defaultValue = $exampleValue = true;
                break;
            case $sanitizedClass === Assert\IsFalse::class:
                $defaultValue = $exampleValue = false;
                break;
            case $isIdenticalTo:
                $defaultValue = $exampleValue = $constraint->value;
                break;
        }
        $doc->setDefault($doc->getDefault() ?? $defaultValue);
        $doc->setExample($doc->getExample() ?? $exampleValue);
    }

    /**
     * @param TypeDoc          $doc
     * @param Assert\Existence $constraint
     *
     * @throws \ReflectionException
     */
    private function appendExistenceConstraintData(TypeDoc $doc, Assert\Existence $constraint): void
    {
        $doc->setRequired($constraint instanceof Assert\Required);
        foreach ($constraint->constraints as $subConstraint) {
            $this->appendToDoc($doc, $subConstraint);
        }
    }
}
