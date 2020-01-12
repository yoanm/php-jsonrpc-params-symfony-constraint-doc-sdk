<?php
namespace Tests\Functional\App\Helper\ConstraintPayloadDocHelper;

use PHPUnit\Framework\TestCase;
use Symfony\Component\Validator\Constraints as Assert;
use Yoanm\JsonRpcParamsSymfonyConstraintDoc\App\Helper\ConstraintPayloadDocHelper;

/**
 * @covers Yoanm\JsonRpcParamsSymfonyConstraintDoc\App\Helper\ConstraintPayloadDocHelper
 *
 * @group Helper
 */
class GetTypeIfExistTest extends TestCase
{

    /** @var ConstraintPayloadDocHelper */
    private $helper;

    public function setUp(): void
    {
        $this->helper = new ConstraintPayloadDocHelper();
    }

    public function testShouldReturnNullIfNoPayload()
    {
        $constraint = new Assert\Valid();

        $this->assertNull($this->helper->getTypeIfExist($constraint));
    }

    public function testShouldReturnNullIfTypeIsNotDefined()
    {
        $constraint = new Assert\Valid();

        $constraint->payload = ['documentation' => []];

        $this->assertNull($this->helper->getTypeIfExist($constraint));
    }

    public function testShouldReturnTypeIfDefined()
    {
        $constraint = new Assert\Valid();
        $type = 'my-type';

        $constraint->payload = ['documentation' => ['type' => $type]];

        $this->assertSame($type, $this->helper->getTypeIfExist($constraint));
    }
}
