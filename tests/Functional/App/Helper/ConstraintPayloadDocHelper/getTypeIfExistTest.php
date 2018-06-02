<?php
namespace Tests\Functional\App\Helper\ConstraintPayloadDocHelper;

use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Prophecy\Prophecy\ObjectProphecy;
use Symfony\Component\Validator\Constraints as Assert;
use Yoanm\JsonRpcParamsSymfonyConstraintDoc\App\Helper\ConstraintPayloadDocHelper;
use Yoanm\JsonRpcServerDoc\Domain\Model\Type\TypeDoc;

/**
 * @covers Yoanm\JsonRpcParamsSymfonyConstraintDoc\App\Helper\ConstraintPayloadDocHelper
 *
 * @group Helper
 */
class GetTypeIfExistTest extends TestCase
{

    /** @var ConstraintPayloadDocHelper */
    private $helper;

    public function setUp()
    {
        $this->helper = new ConstraintPayloadDocHelper();
    }

    public function testShouldReturnNullIfNotDefined()
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
