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
    use ClassComparatorTrait;

    const CONSTRAINT_WITH_FORMAT_FROM_CLASSNAME = [
        Constraints\Bic::class,
        Constraints\CardScheme::class,
        Constraints\Country::class,
        Constraints\Currency::class,
        Constraints\Date::class,
        Constraints\DateTime::class,
        Constraints\Range::class,
        Constraints\Email::class,
        Constraints\File::class,
        Constraints\Iban::class,
        Constraints\Ip::class,
        Constraints\Isbn::class,
        Constraints\Issn::class,
        Constraints\Language::class,
        Constraints\Locale::class,
        Constraints\Luhn::class,
        Constraints\Time::class,
        Constraints\Url::class,
        Constraints\Uuid::class,
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

        if (null !== $this->getMatchingClassNameIn($constraint, self::CONSTRAINT_WITH_FORMAT_FROM_CLASSNAME)) {
            $this->enhanceFromClassName($doc, $constraint);
        } elseif (null !== ($match = $this->getMatchingClassNameIn(
            $constraint,
            array_keys(self::CONSTRAINT_WITH_FORMAT_FROM_PROPERTY)
        ))) {
            $doc->setFormat($constraint->{self::CONSTRAINT_WITH_FORMAT_FROM_PROPERTY[$match]});
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
        static $dateTimeClassList = [Constraints\DateTime::class, Constraints\Range::class];
        if (null !== $this->getMatchingClassNameIn($constraint, $dateTimeClassList)) {
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
                    strlen($doc->getDescription() ?? '') ? ' ' : '',
                    $formatDescription
                )
            );
        }
    }
}
