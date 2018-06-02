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
     * @param TypeDoc $doc
     * @param Constraint $constraint
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
            Assert\Regex::class,
            Assert\Time::class,
            Assert\Url::class,
            Assert\Uuid::class,
        ];

        if (in_array($constraintClass, $constraintForFormatList)) {
            $doc->setFormat(lcfirst((new \ReflectionClass($constraint))->getShortName()));
        }
    }
}
