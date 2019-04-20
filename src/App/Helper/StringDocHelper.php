<?php
namespace Yoanm\JsonRpcParamsSymfonyConstraintDoc\App\Helper;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Constraints as Assert;
use Yoanm\JsonRpcServerDoc\Domain\Model\Type\StringDoc;
use Yoanm\JsonRpcServerDoc\Domain\Model\Type\TypeDoc;

/**
 * Class StringDocHelper
 */
class StringDocHelper
{
    const CONSTRAINT_WITH_FORMAT_FROM_CLASSNAME = [
        Assert\Bic::class,
        Assert\CardScheme::class,
        Assert\Country::class,
        Assert\Currency::class,
        Assert\Date::class,
        Assert\DateTime::class,
        Assert\Range::class,
        Assert\Email::class,
        Assert\File::class,
        Assert\Iban::class,
        Assert\Ip::class,
        Assert\Isbn::class,
        Assert\Issn::class,
        Assert\Language::class,
        Assert\Locale::class,
        Assert\Luhn::class,
        Assert\Time::class,
        Assert\Url::class,
        Assert\Uuid::class,
    ];

    const CONSTRAINT_WITH_FORMAT_FROM_PROPERTY = [
        Assert\Regex::class => 'pattern',
        Assert\Expression::class => 'expression',
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

        if (in_array($constraintClass, self::CONSTRAINT_WITH_FORMAT_FROM_CLASSNAME)) {
            $this->enhanceFromClassName($doc, $constraint);
        } elseif (in_array($constraintClass, self::CONSTRAINT_WITH_FORMAT_FROM_PROPERTY)) {
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
        if ($constraint instanceof Assert\DateTime || $constraint instanceof Assert\Range) {
            // If it's a string range it must be a date range check (either it must be an integer or float value)
            $format = 'datetime';
        } else {
            $format = lcfirst((new \ReflectionClass($constraint))->getShortName());
        }
        $doc->setFormat($format);

        if ($constraint instanceof Assert\Uuid) {
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
