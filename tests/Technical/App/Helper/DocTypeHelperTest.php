<?php
namespace Tests\Technical\App\Helper;

use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;
use Prophecy\Prophecy\ObjectProphecy;
use Symfony\Component\Validator\Constraints as Assert;
use Yoanm\JsonRpcParamsSymfonyConstraintDoc\App\Helper\ConstraintPayloadDocHelper;
use Yoanm\JsonRpcParamsSymfonyConstraintDoc\App\Helper\DocTypeHelper;
use Yoanm\JsonRpcParamsSymfonyConstraintDoc\App\Helper\TypeGuesser;
use Yoanm\JsonRpcServerDoc\Domain\Model\Type\ArrayDoc;
use Yoanm\JsonRpcServerDoc\Domain\Model\Type\IntegerDoc;
use Yoanm\JsonRpcServerDoc\Domain\Model\Type\TypeDoc;

/**
 * @covers Yoanm\JsonRpcParamsSymfonyConstraintDoc\App\Helper\DocTypeHelper
 *
 * @group Helper
 */
class DocTypeHelperTest extends TestCase
{
    use ProphecyTrait;

    /** @var ConstraintPayloadDocHelper|ObjectProphecy */
    private $constraintPayloadDocHelper;
    /** @var TypeGuesser|ObjectProphecy */
    private $typeGuesser;

    /** @var DocTypeHelper */
    private $helper;

    public function setUp(): void
    {
        $this->constraintPayloadDocHelper = $this->prophesize(ConstraintPayloadDocHelper::class);
        $this->typeGuesser = $this->prophesize(TypeGuesser::class);

        $this->helper = new DocTypeHelper(
            $this->constraintPayloadDocHelper->reveal(),
            $this->typeGuesser->reveal()
        );
    }

    public function testShouldPrioritizePayloadDocOverAnythingElse()
    {
        $realType = 'array';
        $constraint = new Assert\Type('integer');
        $constraint->payload = ['documentation' => ['type' => $realType]];
        $this->constraintPayloadDocHelper->getTypeIfExist($constraint)
            ->willReturn($realType)
            ->shouldBeCalled()
        ;

        $this->assertInstanceOf(ArrayDoc::class, $this->helper->guess([$constraint]));
    }

    public function testShouldNormalizeGivenType()
    {
        $constraint = new Assert\Type('plop');

        // use get_class to avoid inheritance issue
        $this->assertSame(TypeDoc::class, get_class($this->helper->guess([$constraint])));
    }

    public function testShouldPrioritizeTypeGuesserAfterPayloadDoc()
    {
        $constraint = new Assert\Required();
        $constraint->payload = ['documentation' => []];
        $constraintList = [$constraint];

        $this->typeGuesser->guessTypeFromConstraintList($constraintList)
            ->willReturn(new IntegerDoc())
            ->shouldBeCalled()
        ;

        $this->assertInstanceOf(IntegerDoc::class, $this->helper->guess($constraintList));
    }

    public function testShouldReturnBasicTypeDocIfRealTypeNotEstablished()
    {
        // use get_class to avoid inheritance issue
        $this->assertSame(TypeDoc::class, get_class($this->helper->guess([])));
    }
}
