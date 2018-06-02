<?php
namespace Tests\Technical\App\Helper;

use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Prophecy\Prophecy\ObjectProphecy;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Constraints as Assert;
use Yoanm\JsonRpcParamsSymfonyConstraintDoc\App\Helper\ConstraintPayloadDocHelper;
use Yoanm\JsonRpcParamsSymfonyConstraintDoc\App\Helper\DocTypeHelper;
use Yoanm\JsonRpcServerDoc\Domain\Model\Type\ArrayDoc;
use Yoanm\JsonRpcServerDoc\Domain\Model\Type\BooleanDoc;
use Yoanm\JsonRpcServerDoc\Domain\Model\Type\CollectionDoc;
use Yoanm\JsonRpcServerDoc\Domain\Model\Type\FloatDoc;
use Yoanm\JsonRpcServerDoc\Domain\Model\Type\IntegerDoc;
use Yoanm\JsonRpcServerDoc\Domain\Model\Type\NumberDoc;
use Yoanm\JsonRpcServerDoc\Domain\Model\Type\ObjectDoc;
use Yoanm\JsonRpcServerDoc\Domain\Model\Type\ScalarDoc;
use Yoanm\JsonRpcServerDoc\Domain\Model\Type\StringDoc;
use Yoanm\JsonRpcServerDoc\Domain\Model\Type\TypeDoc;

/**
 * @covers Yoanm\JsonRpcParamsSymfonyConstraintDoc\App\Helper\DocTypeHelper
 *
 * @group Helper
 */
class DocTypeHelperTest extends TestCase
{
    /** @var ConstraintPayloadDocHelper|ObjectProphecy */
    private $constraintPayloadDocHelper;
    /** @var DocTypeHelper */
    private $helper;

    public function setUp()
    {
        $this->constraintPayloadDocHelper = $this->prophesize(ConstraintPayloadDocHelper::class);
        $this->helper = new DocTypeHelper(
            $this->constraintPayloadDocHelper->reveal()
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

    public function testShouldPrioritizeTypeConstraintAfterPayloadDoc()
    {
        $constraint = new Assert\Type('integer');
        $constraint->payload = ['documentation' => []];

        $this->assertInstanceOf(IntegerDoc::class, $this->helper->guess([$constraint]));
    }

    public function testShouldReturnBasicTypeDocIfRealTypeNotEstablished()
    {
        // use get_class to avoid inheritance issue
        $this->assertSame(TypeDoc::class, get_class($this->helper->guess([])));
    }

    public function testShouldReturnBasicTypeDocIfTypeConstraintTypeIsAmbigous()
    {
        // use get_class to avoid inheritance issue
        $this->assertSame(TypeDoc::class, get_class($this->helper->guess([new Assert\Type('plop')])));
    }

    /**
     * @dataProvider provideAbstractAndRealType
     *
     * @param Constraint[] $constraintList
     * @param string       $expectedClass
     */
    public function testShouldHandleTemporaryAbstractType(array $constraintList, $expectedClass)
    {
        // use get_class to avoid inheritance issue
        $this->assertSame(
            $expectedClass,
            get_class($this->helper->guess($constraintList))
        );
    }

    /**
     * @return array
     */
    public function provideAbstractAndRealType()
    {
        /** /!\ Do not use Type constraint as it will override anything /!\ **/

        // Define a constraint that will be resolved as ScalarDoc
        $constraintScalarType = new Assert\DateTime(['format' => 'U']);
        // Define a constraint that will be resolved as NumberDoc
        $constraintNumberType = new Assert\Range(['min' => 2]);
        // Define a constraint that will be resolved as CollectionDoc
        $constraintCollectionType = new Assert\Count(['min' => 2]);

        $constraintStringType = new Assert\Length(['min' => 2]);
        $constraintBooleanType = new Assert\IsTrue();
        $constraintFloatType = new Assert\GreaterThan(['value' => 1.2]);
        //$constraintIntegerType = ????


        return [
            'scalar' => [
                'constraintList' => [$constraintScalarType],
                'expectedClass' => ScalarDoc::class
            ],
            'number' => [
                'constraintList' => [$constraintNumberType],
                'expectedClass' => NumberDoc::class
            ],
            'collection' => [
                'constraintList' => [$constraintCollectionType],
                'expectedClass' => CollectionDoc::class
            ],
            'scalar to string' => [
                'constraintList' => [
                    $constraintScalarType,
                    $constraintStringType,
                ],
                'expectedClass' => StringDoc::class
            ],
            'scalar to boolean' => [
                'constraintList' => [
                    $constraintScalarType,
                    $constraintBooleanType,
                ],
                'expectedClass' => BooleanDoc::class
            ],
            /**'number to integer' => [ No case exist for the moment
                'constraintList' => [
                    $constraintNumberType,
                    $constraintIntegerType
                ],
                'expectedClass' => IntegerDoc::class
            ],*/
            'number to float' => [
                'constraintList' => [
                    $constraintNumberType,
                    $constraintFloatType,
                ],
                'expectedClass' => FloatDoc::class
            ],
            'collection to array' => [
                'constraintList' => [
                    $constraintCollectionType,
                    new Assert\Type('array'),
                ],
                'expectedClass' => ArrayDoc::class
            ],
            'collection to object' => [
                'constraintList' => [
                    $constraintCollectionType,
                    new Assert\Type('object'),
                ],
                'expectedClass' => ObjectDoc::class
            ],
            /**'scalar to number to integer' => [ No case exist for the moment
                'constraintList' => [
                        $constraintScalarType,
                        $constraintNumberType,
                        $constraintIntegerType,
                ],
                'expectedClass' => IntegerDoc::class
            ],*/
            'scalar to number to float' => [
                'constraintList' => [
                    $constraintScalarType,
                    $constraintNumberType,
                    $constraintFloatType,
                ],
                'expectedClass' => FloatDoc::class
            ],
        ];
    }
}
