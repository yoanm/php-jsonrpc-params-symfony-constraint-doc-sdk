<?php
namespace Yoanm\JsonRpcParamsSymfonyConstraintDoc\App\Helper;

use Symfony\Component\Validator\Constraint;
use Yoanm\JsonRpcServerDoc\Domain\Model\Type\TypeDoc;

/**
 * Class ConstraintPayloadDocHelper
 */
class ConstraintPayloadDocHelper
{
    /** Use this key in constraint payload to define parameter documentation */
    const PAYLOAD_DOCUMENTATION_KEY = 'documentation';
    const PAYLOAD_DOCUMENTATION_TYPE_KEY = 'type';
    const PAYLOAD_DOCUMENTATION_REQUIRED_KEY = 'required';
    const PAYLOAD_DOCUMENTATION_NULLABLE_KEY = 'nullable';
    const PAYLOAD_DOCUMENTATION_DESCRIPTION_KEY = 'description';
    const PAYLOAD_DOCUMENTATION_DEFAULT_KEY = 'default';
    const PAYLOAD_DOCUMENTATION_EXAMPLE_KEY = 'example';

    /**
     * @param TypeDoc $doc
     * @param Constraint $constraint
     */
    public function appendPayloadDoc(TypeDoc $doc, Constraint $constraint)
    {
        if (!$this->hasPayloadDoc($constraint)) {
            return;
        }

        // /!\ Do not override value if payload have nothing defined for a key ! /!\

        $doc->setRequired(
            $this->getPayloadDocValue($constraint, self::PAYLOAD_DOCUMENTATION_REQUIRED_KEY) ?? $doc->isRequired()
        );
        $doc->setNullable(
            $this->getPayloadDocValue($constraint, self::PAYLOAD_DOCUMENTATION_NULLABLE_KEY) ?? $doc->isNullable()
        );

        $doc->setExample(
            $this->getPayloadDocValue($constraint, self::PAYLOAD_DOCUMENTATION_EXAMPLE_KEY) ?? $doc->getExample()
        );
        $doc->setDefault(
            $this->getPayloadDocValue($constraint, self::PAYLOAD_DOCUMENTATION_DEFAULT_KEY) ?? $doc->getDefault()
        );

        $description = $this->getPayloadDocValue($constraint, self::PAYLOAD_DOCUMENTATION_DESCRIPTION_KEY);
        if (null !== $description) {
            $doc->setDescription($description);
        }
    }

    /**
     * @param Constraint $constraint
     *
     * @return mixed|null Null in case type does not exist
     */
    public function getTypeIfExist(Constraint $constraint)
    {
        return $this->hasPayloadDoc($constraint)
            ? $this->getPayloadDocValue($constraint, self::PAYLOAD_DOCUMENTATION_TYPE_KEY)
            : null
        ;
    }

    /**
     * @param Constraint $constraint
     *
     * @return bool
     */
    protected function hasPayloadDoc(Constraint $constraint) : bool
    {
        return is_array($constraint->payload)
            && array_key_exists(self::PAYLOAD_DOCUMENTATION_KEY, $constraint->payload);
    }

    /**
     * @param Constraint $constraint
     * @param string     $documentationKey
     *
     * @return mixed|null Return null if value does not exist
     */
    protected function getPayloadDocValue(Constraint $constraint, string $documentationKey)
    {
        return $this->hasPayloadDocKey($constraint, $documentationKey)
            ? $constraint->payload[self::PAYLOAD_DOCUMENTATION_KEY][$documentationKey]
            : null
        ;
    }

    /**
     * @param Constraint $constraint
     * @param string     $documentationKey
     *
     * @return bool
     */
    protected function hasPayloadDocKey(Constraint $constraint, string $documentationKey) : bool
    {
        return array_key_exists($documentationKey, $constraint->payload[self::PAYLOAD_DOCUMENTATION_KEY]);
    }
}
