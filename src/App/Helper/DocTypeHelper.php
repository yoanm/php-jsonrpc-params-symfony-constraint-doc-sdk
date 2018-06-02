<?php
namespace Yoanm\JsonRpcParamsSymfonyConstraintDoc\App\Helper;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Constraints as Assert;
use Yoanm\JsonRpcServerDoc\Domain\Model\Type\ArrayDoc;
use Yoanm\JsonRpcServerDoc\Domain\Model\Type\BooleanDoc;
use Yoanm\JsonRpcServerDoc\Domain\Model\Type\FloatDoc;
use Yoanm\JsonRpcServerDoc\Domain\Model\Type\IntegerDoc;
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
    /** @var TypeGuesser */
    private $typeGuesser;

    /**
     * @param ConstraintPayloadDocHelper $constraintPayloadDocHelper
     */
    public function __construct(ConstraintPayloadDocHelper $constraintPayloadDocHelper, TypeGuesser $typeGuesser)
    {
        $this->constraintPayloadDocHelper = $constraintPayloadDocHelper;
        $this->typeGuesser = $typeGuesser;
    }

    /**
     * @param Constraint[] $constraintList
     *
     * @return TypeDoc
     */
    public function guess(array $constraintList) : TypeDoc
    {
        return $this->getDocFromTypeConstraintOrPayloadDocIfExist($constraintList)
            ?? $this->typeGuesser->guessTypeFromConstraintList($constraintList)
            ?? new TypeDoc()
        ;
    }

    /**
     * @param Constraint[] $constraintList
     *
     * @return TypeDoc|null
     */
    protected function getDocFromTypeConstraintOrPayloadDocIfExist(array $constraintList)
    {
        $doc = null;
        // Check if a Type constraint exist or if a constraint have a type documentation
        foreach ($constraintList as $constraint) {
            if (null !== ($typeFromPayload = $this->constraintPayloadDocHelper->getTypeIfExist($constraint))) {
                $doc = $this->normalizeType($typeFromPayload);
            } elseif ($constraint instanceof Assert\Type) {
                $doc = $this->normalizeType(strtolower($constraint->type));
            }

            if (null !== $doc) {
                break;
            }
        }

        return $doc;
    }

    /**
     * @param string $type
     *
     * @return TypeDoc|null
     */
    private function normalizeType(string $type)
    {
        if ('scalar' === $type) {
            return new ScalarDoc();
        } elseif ('string' === $type) {
            return new StringDoc();
        } elseif ('bool' === $type || 'boolean' === $type) {
            return new BooleanDoc();
        } elseif ('int' === $type || 'integer' === $type) {
            return new IntegerDoc();
        } elseif (in_array($type, ['float', 'long', 'double', 'real', 'numeric'])) {
            return new FloatDoc();
        } elseif ('array' === $type) {
            return new ArrayDoc();
        } elseif ('object' === $type) {
            return new ObjectDoc();
        }

        return null;
    }
}
