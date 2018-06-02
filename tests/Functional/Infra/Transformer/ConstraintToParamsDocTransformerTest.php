<?php
namespace Tests\Functional\Infra\Transformer;

use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Prophecy\Prophecy\ObjectProphecy;
use Symfony\Component\Validator\Constraints as Assert;
use Yoanm\JsonRpcParamsSymfonyConstraintDoc\App\Helper\ConstraintPayloadDocHelper;
use Yoanm\JsonRpcParamsSymfonyConstraintDoc\App\Helper\DocTypeHelper;
use Yoanm\JsonRpcParamsSymfonyConstraintDoc\App\Helper\MinMaxHelper;
use Yoanm\JsonRpcParamsSymfonyConstraintDoc\App\Helper\StringDocHelper;
use Yoanm\JsonRpcParamsSymfonyConstraintDoc\Infra\Transformer\ConstraintToParamsDocTransformer;
use Yoanm\JsonRpcServerDoc\Domain\Model\Type\ArrayDoc;
use Yoanm\JsonRpcServerDoc\Domain\Model\Type\IntegerDoc;
use Yoanm\JsonRpcServerDoc\Domain\Model\Type\ObjectDoc;
use Yoanm\JsonRpcServerDoc\Domain\Model\Type\StringDoc;
use Yoanm\JsonRpcServerDoc\Domain\Model\Type\TypeDoc;

/**
 * @covers Yoanm\JsonRpcParamsSymfonyConstraintDoc\Infra\Transformer\ConstraintToParamsDocTransformer
 *
 * @group Helper
 */
class ConstraintToParamsDocTransformerTest extends TestCase
{
    /** @var DocTypeHelper|ObjectProphecy */
    private $docTypeHelper;
    /** @var StringDocHelper|ObjectProphecy */
    private $stringDocHelper;
    /** @var MinMaxHelper|ObjectProphecy */
    private $minMaxHelper;
    /** @var ConstraintPayloadDocHelper|ObjectProphecy */
    private $constraintPayloadDocHelper;

    /** @var ConstraintToParamsDocTransformer */
    private $transformer;

    public function setUp()
    {
        $this->docTypeHelper = $this->prophesize(DocTypeHelper::class);
        $this->stringDocHelper = $this->prophesize(StringDocHelper::class);
        $this->minMaxHelper = $this->prophesize(MinMaxHelper::class);
        $this->constraintPayloadDocHelper = $this->prophesize(ConstraintPayloadDocHelper::class);

        $this->transformer = new ConstraintToParamsDocTransformer(
            $this->docTypeHelper->reveal(),
            $this->stringDocHelper->reveal(),
            $this->minMaxHelper->reveal(),
            $this->constraintPayloadDocHelper->reveal()
        );
    }

    public function testShouldHandleNotNullConstraint()
    {
        $constraint = new Assert\NotNull();
        $doc = new TypeDoc();

        $this->assertTrue($doc->isNullable());

        $this->docTypeHelper->guess([$constraint])
            ->willReturn($doc)
            ->shouldBeCalled()
        ;

        $this->assertFalse($this->transformer->transform($constraint)->isNullable());
    }

    public function testShouldHandleChoiceConstraint()
    {
        $choiceList = ['a', 'b'];
        $constraint = new Assert\Choice($choiceList);
        $doc = new TypeDoc();

        $this->docTypeHelper->guess([$constraint])
            ->willReturn($doc)
            ->shouldBeCalled()
        ;

        $this->assertSame($choiceList, $this->transformer->transform($constraint)->getAllowedValueList());
    }

    public function testShouldHandleChoiceConstraintWithCallback()
    {
        $choiceList = ['a', 'b'];
        $choiceCallback = function () use ($choiceList) {
            return $choiceList;
        };

        $constraint = new Assert\Choice(['callback' => $choiceCallback]);
        $doc = new TypeDoc();

        $this->docTypeHelper->guess([$constraint])
            ->willReturn($doc)
            ->shouldBeCalled()
        ;

        $this->assertSame($choiceList, $this->transformer->transform($constraint)->getAllowedValueList());
    }

    public function testShouldHandleAllConstraint()
    {
        $subConstraintList = [new Assert\Type('string')];
        $constraint = new Assert\All($subConstraintList);
        $doc = new ArrayDoc();
        $subDoc = new StringDoc();

        $this->docTypeHelper->guess([$constraint])
            ->willReturn($doc)
            ->shouldBeCalled()
        ;
        $this->docTypeHelper->guess($subConstraintList)
            ->willReturn($subDoc)
            ->shouldBeCalled()
        ;

        $newDoc = $this->transformer->transform($constraint);

        $this->assertSame($doc, $newDoc);
        $this->assertSame($subDoc, $doc->getItemValidation());
    }

    public function testShouldHandleCollectionConstraintAsArrayDoc()
    {
        $fieldsConstraintList = [0 => new Assert\Type('string'), 1 => new Assert\Type('integer')];
        $constraint = new Assert\Collection([
            'fields' => $fieldsConstraintList,
            'allowExtraFields' => true,
            'allowMissingFields' => true,
        ]);
        $doc = new ArrayDoc();
        $subDoc = new StringDoc();
        $subDoc2 = new IntegerDoc();

        $this->docTypeHelper->guess([$constraint])
            ->willReturn($doc)
            ->shouldBeCalled()
        ;
        $this->docTypeHelper->guess(Argument::cetera())
            ->willReturn($subDoc, $subDoc2)
            ->shouldBeCalled()
        ;

        $newDoc = $this->transformer->transform($constraint);

        $this->assertSame($doc, $newDoc);
        $this->assertSame([$subDoc, $subDoc2], $doc->getSiblingList());
        $this->assertSame(0, $subDoc->getName());
        $this->assertSame(1, $subDoc2->getName());
        $this->assertTrue($doc->isAllowMissingSibling());
        $this->assertTrue($doc->isAllowExtraSibling());
    }

    public function testShouldHandleCollectionConstraintAsObjectDoc()
    {
        $fieldsConstraintList = ['key1' => new Assert\Type('string'), 'key2' => new Assert\Type('integer')];
        $constraint = new Assert\Collection([
            'fields' => $fieldsConstraintList,
            'allowExtraFields' => true,
            'allowMissingFields' => true,
        ]);
        $doc = new ObjectDoc();
        $subDoc = new StringDoc();
        $subDoc2 = new IntegerDoc();

        $this->docTypeHelper->guess([$constraint])
            ->willReturn($doc)
            ->shouldBeCalled()
        ;
        $this->docTypeHelper->guess(Argument::cetera())
            ->willReturn($subDoc, $subDoc2)
            ->shouldBeCalled()
        ;

        $newDoc = $this->transformer->transform($constraint);

        $this->assertSame($doc, $newDoc);
        $this->assertSame([$subDoc, $subDoc2], $doc->getSiblingList());
        $this->assertSame('key1', $subDoc->getName());
        $this->assertSame('key2', $subDoc2->getName());
        $this->assertTrue($doc->isAllowMissingSibling());
        $this->assertTrue($doc->isAllowExtraSibling());
    }



    public function testPayloadDocShouldOverrideAnything()
    {
        $constraint = new Assert\NotNull();

        $doc = new IntegerDoc();
        $this->assertTrue($doc->isNullable());

        $this->docTypeHelper->guess([$constraint])
            ->willReturn($doc)
            ->shouldBeCalled()
        ;
        $self = $this;
        $this->constraintPayloadDocHelper->appendPayloadDoc($doc, $constraint)
            ->will(function ($args) use ($self) {
                /** @var IntegerDoc $doc */
                $doc = $args[0];
                $self->assertFalse($doc->isNullable());

                // Set it back to true
                $doc->setNullable(true);
            })
            ->shouldBeCalled()
        ;

        $newDoc = $this->transformer->transform($constraint);

        $this->assertSame($doc, $newDoc);
        // Check is still true even if constraint is NotNul
        $this->assertTrue($doc->isNullable());
    }
}
