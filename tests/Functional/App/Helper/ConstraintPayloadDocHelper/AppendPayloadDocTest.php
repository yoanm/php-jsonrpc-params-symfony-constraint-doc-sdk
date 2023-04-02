<?php
namespace Tests\Functional\App\Helper\ConstraintPayloadDocHelper;

use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Prophecy\PhpUnit\ProphecyTrait;
use Prophecy\Prophecy\ObjectProphecy;
use Symfony\Component\Validator\Constraints as Assert;
use Yoanm\JsonRpcParamsSymfonyConstraintDoc\App\Helper\ConstraintPayloadDocHelper;
use Yoanm\JsonRpcServerDoc\Domain\Model\Type\TypeDoc;

/**
 * @covers Yoanm\JsonRpcParamsSymfonyConstraintDoc\App\Helper\ConstraintPayloadDocHelper
 *
 * @group Helper
 */
class AppendPayloadDocTest extends TestCase
{
    use ProphecyTrait;

    /** @var ConstraintPayloadDocHelper */
    private $helper;

    public function setUp(): void
    {
        $this->helper = new ConstraintPayloadDocHelper();
    }

    public function testShouldDoNothingIfNoPayloadDocDefined()
    {
        $constraint = new Assert\Valid();
        /** @var TypeDoc|ObjectProphecy $doc */
        $doc = $this->prophesize(TypeDoc::class);

        //Define one prophecy to check that nothing else is called
        $doc->setRequired(Argument::cetera())->shouldNotBeCalled();

        $this->helper->appendPayloadDoc($doc->reveal(), $constraint);
    }

    public function testShouldAppendIsRequiredIfDefined()
    {
        $constraint = new Assert\Valid();
        $doc = new TypeDoc();

        $initialValue = $doc->isRequired();
        $constraint->payload = ['documentation' => ['required' => !$initialValue]];

        $this->helper->appendPayloadDoc($doc, $constraint);

        $this->assertSame(!$initialValue, $doc->isRequired());
    }

    public function testShouldAppendIsNullableIfDefined()
    {
        $constraint = new Assert\Valid();
        $doc = new TypeDoc();

        $initialValue = $doc->isNullable();
        $constraint->payload = ['documentation' => ['nullable' => !$initialValue]];

        $this->helper->appendPayloadDoc($doc, $constraint);

        $this->assertSame(!$initialValue, $doc->isNullable());
    }

    public function testShouldAppendExampleIfDefined()
    {
        $constraint = new Assert\Valid();
        $doc = new TypeDoc();

        $docValue = 'my-example';
        $constraint->payload = ['documentation' => ['example' => $docValue]];

        $this->helper->appendPayloadDoc($doc, $constraint);

        $this->assertSame($docValue, $doc->getExample());
    }

    public function testShouldAppendDefaultIfDefined()
    {
        $constraint = new Assert\Valid();
        $doc = new TypeDoc();

        $docValue = 'my-default';
        $constraint->payload = ['documentation' => ['default' => $docValue]];

        $this->helper->appendPayloadDoc($doc, $constraint);

        $this->assertSame($docValue, $doc->getDefault());
    }

    public function testShouldAppendDescriptionIfDefined()
    {
        $constraint = new Assert\Valid();
        $doc = new TypeDoc();

        $docValue = 'my-description';
        $constraint->payload = ['documentation' => ['description' => $docValue]];

        $this->helper->appendPayloadDoc($doc, $constraint);

        $this->assertSame($docValue, $doc->getDescription());
    }
}
