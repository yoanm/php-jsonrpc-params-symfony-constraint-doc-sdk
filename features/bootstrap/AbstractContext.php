<?php
namespace Tests\Functional\BehatContext;

use Behat\Behat\Context\Context;
use Behat\Gherkin\Node\PyStringNode;
use PHPUnit\Framework\Assert;
use Yoanm\JsonRpcServerDoc\Domain\Model\Type\TypeDoc;
use Yoanm\JsonRpcServerDoc\Infra\Normalizer\TypeDocNormalizer;

class AbstractContext implements Context
{
    public function jsonDecode(string $encodedData)
    {
        $decoded = json_decode($encodedData, true);

        if (JSON_ERROR_NONE != json_last_error()) {
            throw new \Exception(
                json_last_error_msg(),
                json_last_error()
            );
        }

        return $decoded;
    }
}
