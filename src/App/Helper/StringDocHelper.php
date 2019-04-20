<?php
namespace Yoanm\JsonRpcParamsSymfonyConstraintDoc\App\Helper;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Constraints;
use Yoanm\JsonRpcServerDoc\Domain\Model\Type\StringDoc;
use Yoanm\JsonRpcServerDoc\Domain\Model\Type\TypeDoc;

/**
 * Class StringDocHelper
 */
class StringDocHelper
{
    const CONSTRAINT_WITH_FORMAT_FROM_CLASSNAME = [
        Constraints\Bic::class => true,
        Constraints\CardScheme::class => true,
        Constraints\Country::class => true,
        Constraints\Currency::class => true,
        Constraints\Date::class => true,
        Constraints\DateTime::class => true,
        Constraints\Range::class => true,
        Constraints\Email::class => true,
        Constraints\File::class => true,
        Constraints\Iban::class => true,
        Constraints\Ip::class => true,
        Constraints\Isbn::class => true,
        Constraints\Issn::class => true,
        Constraints\Language::class => true,
        Constraints\Locale::class => true,
        Constraints\Luhn::class => true,
        Constraints\Time::class => true,
        Constraints\Url::class => true,
        Constraints\Uuid::class => true,
    ];

    const CONSTRAINT_WITH_FORMAT_FROM_PROPERTY = [
        Constraints\Regex::class => 'pattern',
        Constraints\Expression::class => 'expression',
    ];

    /**
     * @param TypeDoc    $doc
     * @param Constraint $constraint
     *
     * @throws \ReflectionException
     */
    public function append(TypeDoc $doc, Constraint $constraint) : void
    {
        // If not a string nor scalar => give up
        if (!$doc instanceof StringDoc) {
            return;
        }

        $constraintClass = get_class($constraint);

        if (array_key_exists($constraintClass, self::CONSTRAINT_WITH_FORMAT_FROM_CLASSNAME)) {
            $this->enhanceFromClassName($doc, $constraint);
        } elseif (array_key_exists($constraintClass, self::CONSTRAINT_WITH_FORMAT_FROM_PROPERTY)) {
            $propertyName = self::CONSTRAINT_WITH_FORMAT_FROM_PROPERTY[$constraintClass];
            $doc->setFormat($constraint->{$propertyName});
        }
    }

    /**
     * @param StringDoc    $doc
     * @param Constraint $constraint
     *
     * @throws \ReflectionException
     */
    private function enhanceFromClassName(StringDoc $doc, Constraint $constraint): void
    {
        if ($constraint instanceof Constraints\DateTime || $constraint instanceof Constraints\Range) {
            // If it's a string range it must be a date range check (either it must be an integer or float value)
            $format = 'datetime';
        } else {
            $format = lcfirst((new \ReflectionClass($constraint))->getShortName());
        }
        $doc->setFormat($format);

        if ($constraint instanceof Constraints\Uuid) {
            $formatDescription = sprintf(
                '%s (%s)',
                ucfirst($format),
                implode(
                    ', ',
                    array_map(
                        function ($version) {
                            return sprintf('v%s', $version);
                        },
                        $constraint->versions
                    )
                )
            );
            $doc->setDescription(
                sprintf(
                    '%s%s%s',
                    $doc->getDescription(),
                    strlen($doc->getDescription()) ? ' ' : '',
                    $formatDescription
                )
            );
        }
    }
}
