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
    public function transform(Constraint $constraint)
    {
        return $this->docFromConstraint($constraint);
    }

    /**
     * @param Constraint      $constraintOrConstraintList
     * @param string|int|null $paramNameOrIndex
     *
     * @return TypeDoc
     */
    private function docFromConstraint(Constraint $constraint, $paramNameOrIndex = null)
    {
        $constraintList = [$constraint];
        $constraintDoc = $this->docTypeHelper->guess($constraintList);
        if (null !== $paramNameOrIndex) {
            $constraintDoc->setName($paramNameOrIndex);
        }

        foreach ($constraintList as $constraint) {
            $this->appendToDoc($constraintDoc, $constraint);
        }

        return $constraintDoc;
    }

    /**
     * @param TypeDoc $doc
     * @param Constraint   $constraint
     */
    private function appendToDoc(TypeDoc $doc, Constraint $constraint)
    {
        if ($doc instanceof ArrayDoc && $constraint instanceof Assert\All) {
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
            } elseif ($constraint instanceof Assert\NotNull) {
                $doc->setNullable(false);
            }

            // /!\ Payload doc will override values even if already defined
            $this->constraintPayloadDocHelper->appendPayloadDoc($doc, $constraint);
        }
    }

    /**
     * @param TypeDoc $doc
     * @param Constraint $constraint
     */
    private function appendCollectionDoc(TypeDoc $doc, Constraint $constraint)
    {
        // If not a collection => give up
        if (!$doc instanceof CollectionDoc) {
            return;
        }

        if ($constraint instanceof Assert\Collection) {
            foreach ($constraint->fields as $fieldName => $constraintOrConstrainList) {
                $doc->addSibling(
                    $this->docFromConstraint($constraintOrConstrainList, $fieldName)
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
    private function appendValidItemListDoc(TypeDoc $doc, Constraint $constraint)
    {
        if ($constraint instanceof Assert\Choice) {
            if ($constraint->callback && is_callable($constraint->callback)) {
                $choiceList = call_user_func($constraint->callback);
            } else {
                $choiceList = $constraint->choices ?? [];
            }
            foreach ($choiceList as $fieldName => $choice) {
                $doc->addAllowedValue($choice);
            }
        }
    }

    /**
     * @param ArrayDoc   $doc
     * @param Assert\All $constraint
     */
    private function appendAllConstraintToDoc(ArrayDoc $doc, Assert\All $constraint)
    {
        $itemDoc = $this->docTypeHelper->guess($constraint->constraints);
        foreach ($constraint->constraints as $subConstraint) {
            $this->appendToDoc($itemDoc, $subConstraint);
        }

        $doc->setItemValidation($itemDoc);
    }
}
