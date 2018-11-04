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
    /**
     * @param TypeDoc    $doc
     * @param Constraint $constraint
     *
     * @throws \ReflectionException
     */
    public function append(TypeDoc $doc, Constraint $constraint)
    {
        // If format already defined or type is defined and is not a string nor scalar => give up
        if (!$doc instanceof StringDoc) {
            return;
        }

        $constraintClass = get_class($constraint);
        $constraintForFormatList = [
            Assert\Bic::class,
            Assert\CardScheme::class,
            Assert\Country::class,
            Assert\Currency::class,
            Assert\Date::class,
            Assert\DateTime::class,
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

        if (in_array($constraintClass, $constraintForFormatList)) {
            if (Assert\DateTime::class === $constraintClass) {
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
        } elseif ($constraint instanceof Assert\Regex) {
            $doc->setFormat($constraint->pattern);
        } elseif ($constraint instanceof Assert\Range) {
            // If it's a string range it must be a date range check (either it must be an integer or float value)
            $doc->setFormat('datetime');
        } elseif ($constraint instanceof Assert\Expression) {
            // If it's a string range it must be a date range check (either it must be an integer or float value)
            $doc->setFormat($constraint->expression);
        }
    }
}
