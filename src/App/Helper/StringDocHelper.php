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

        switch (true) {
            case $constraint instanceof Assert\Bic:
            case $constraint instanceof Assert\CardScheme:
            case $constraint instanceof Assert\Country:
            case $constraint instanceof Assert\Currency:
            case $constraint instanceof Assert\Date:
            case $constraint instanceof Assert\DateTime:
            case $constraint instanceof Assert\Email:
            case $constraint instanceof Assert\File:
            case $constraint instanceof Assert\Iban:
            case $constraint instanceof Assert\Ip:
            case $constraint instanceof Assert\Isbn:
            case $constraint instanceof Assert\Issn:
            case $constraint instanceof Assert\Language:
            case $constraint instanceof Assert\Locale:
            case $constraint instanceof Assert\Luhn:
            case $constraint instanceof Assert\Regex:
            case $constraint instanceof Assert\Time:
            case $constraint instanceof Assert\Url:
            case $constraint instanceof Assert\Uuid:
                $doc->setFormat(lcfirst((new \ReflectionClass($constraint))->getShortName()));
                break;
        }
    }
}
