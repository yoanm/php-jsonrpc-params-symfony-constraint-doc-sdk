<?php
namespace Yoanm\JsonRpcParamsSymfonyConstraintDoc\App\Helper;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Constraints as Assert;
use Yoanm\JsonRpcServerDoc\Domain\Model\Type\ArrayDoc;
use Yoanm\JsonRpcServerDoc\Domain\Model\Type\BooleanDoc;
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
    /** @var TypeGuesser */
    private $typeGuesser;

    const MANAGED_TYPE_CLASS_LIST = [
        'scalar' => ScalarDoc::class,
        'string' => StringDoc::class,
        'bool' => BooleanDoc::class,
        'boolean' => BooleanDoc::class,
        'int' => IntegerDoc::class,
        'integer' => IntegerDoc::class,
        'float' => FloatDoc::class,
        'long' => FloatDoc::class,
        'double' => FloatDoc::class,
        'real' => FloatDoc::class,
        'numeric' => NumberDoc::class,
        'array' => ArrayDoc::class,
        'object' => ObjectDoc::class,
    ];

    /**
     * @param ConstraintPayloadDocHelper $constraintPayloadDocHelper
     * @param TypeGuesser                $typeGuesser
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
    protected function getDocFromTypeConstraintOrPayloadDocIfExist(array $constraintList) : ?TypeDoc
    {
        $doc = null;
        // Check if a Type constraint exist or if a constraint have a type documentation
        foreach ($constraintList as $constraint) {
            $doc = $this->createDocFromConstraint($constraint);

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
    private function normalizeType(string $type) : ?TypeDoc
    {
        if (array_key_exists($type, self::MANAGED_TYPE_CLASS_LIST)) {
            $class = self::MANAGED_TYPE_CLASS_LIST[$type];

            return new $class();
        }

        return null;
    }

    /**
     * @param Constraint $constraint
     *
     * @return TypeDoc|null
     */
    private function createDocFromConstraint(Constraint $constraint) : ?TypeDoc
    {
        $doc = null;

        if (null !== ($stringType = $this->getStringType($constraint))) {
            $doc = $this->normalizeType($stringType);
        } elseif ($constraint instanceof Assert\Callback) {
            $doc = $this->getTypeFromCallbackConstraint($constraint);
        } elseif ($constraint instanceof Assert\Existence && count($constraint->constraints) > 0) {
            $doc = $this->guess($constraint->constraints);
        }

        return $doc;
    }

    /**
     * @param Assert\Callback $constraint
     *
     * @return TypeDoc
     */
    private function getTypeFromCallbackConstraint(Assert\Callback $constraint): TypeDoc
    {
        $callbackResult = call_user_func($constraint->callback);
        $doc = $this->guess(
            is_array($callbackResult)
                ? $callbackResult
                : [$callbackResult]
        );
        return $doc;
    }

    /**
     * @param Constraint $constraint
     *
     * @return string|null
     */
    private function getStringType(Constraint $constraint) : ?string
    {
        $stringType = null;
        if (null !== ($typeFromPayload = $this->constraintPayloadDocHelper->getTypeIfExist($constraint))) {
            $stringType = $typeFromPayload;
        } elseif ($constraint instanceof Assert\Type) {
            $stringType = strtolower($constraint->type);
        } elseif ($constraint instanceof Assert\IdenticalTo) {// Strict comparison so value define the type
            $stringType = gettype($constraint->value);
        }

        return $stringType;
    }
}
