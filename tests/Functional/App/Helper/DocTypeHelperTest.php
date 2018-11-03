<?php
namespace Tests\Functional\App\Helper;

use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Prophecy\Prophecy\ObjectProphecy;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Constraints as Assert;
use Yoanm\JsonRpcParamsSymfonyConstraintDoc\App\Helper\ConstraintPayloadDocHelper;
use Yoanm\JsonRpcParamsSymfonyConstraintDoc\App\Helper\DocTypeHelper;
use Yoanm\JsonRpcParamsSymfonyConstraintDoc\App\Helper\TypeGuesser;
use Yoanm\JsonRpcServerDoc\Domain\Model\Type\ArrayDoc;
use Yoanm\JsonRpcServerDoc\Domain\Model\Type\BooleanDoc;
use Yoanm\JsonRpcServerDoc\Domain\Model\Type\FloatDoc;
use Yoanm\JsonRpcServerDoc\Domain\Model\Type\IntegerDoc;
use Yoanm\JsonRpcServerDoc\Domain\Model\Type\NumberDoc;
use Yoanm\JsonRpcServerDoc\Domain\Model\Type\ObjectDoc;
use Yoanm\JsonRpcServerDoc\Domain\Model\Type\ScalarDoc;
use Yoanm\JsonRpcServerDoc\Domain\Model\Type\StringDoc;

/**
 * @covers Yoanm\JsonRpcParamsSymfonyConstraintDoc\App\Helper\DocTypeHelper
 *
 * @group Helper
 */
class DocTypeHelperTest extends TestCase
{
    /** @var ConstraintPayloadDocHelper|ObjectProphecy */
    private $constraintPayloadDocHelper;
    /** @var TypeGuesser|ObjectProphecy */
    private $typeGuesser;

    /** @var DocTypeHelper */
    private $helper;

    public function setUp()
    {
        $this->constraintPayloadDocHelper = $this->prophesize(ConstraintPayloadDocHelper::class);
        $this->typeGuesser = $this->prophesize(TypeGuesser::class);

        $this->helper = new DocTypeHelper(
            $this->constraintPayloadDocHelper->reveal(),
            $this->typeGuesser->reveal()
        );
    }

    /**
     * @dataProvider provideTypes
     *
     * @param string $type
     * @param string $expectedClass
     */
    public function testShouldHandleType(string $type, string $expectedClass)
    {
        // use get_class to avoid inheritance issue
        $this->assertSame(
            $expectedClass,
            get_class($this->helper->guess([new Assert\Type($type)]))
        );
    }

    /**
     * @dataProvider provideDerivedTypes
     *
     * @param string $type
     * @param string $expectedClass
     */
    public function testShouldHandleDerivedType(Constraint $constraint, string $expectedClass)
    {
        // use get_class to avoid inheritance issue
        $this->assertSame(
            $expectedClass,
            get_class($this->helper->guess([$constraint]))
        );
    }

    /**
     * @dataProvider provideTypeAliases
     *
     * @param string $type
     * @param string $expectedClass
     */
    public function testShouldHandleTypeAlias(string $type, string $expectedClass)
    {
        // use get_class to avoid inheritance issue
        $this->assertSame(
            $expectedClass,
            get_class($this->helper->guess([new Assert\Type($type)]))
        );
    }



    /**
     * @dataProvider provideExistenceConstraints
     *
     * @param Assert\Existence $existenceConstraint
     * @param string           $expectedClass
     *
     * @group yo
     */
    public function testShouldHandleTypeInsideExistenceType(Assert\Existence $existenceConstraint, $expectedClass)
    {
        // use get_class to avoid inheritance issue
        $this->assertSame(
            $expectedClass,
            get_class($this->helper->guess([$existenceConstraint]))
        );
    }

    public function provideTypes()
    {
        return [
            'scalar' => [
                'typeAlias' => 'scalar',
                'expectedClass' => ScalarDoc::class
            ],
            'string' => [
                'typeAlias' => 'string',
                'expectedClass' => StringDoc::class
            ],
            'integer' => [
                'typeAlias' => 'integer',
                'expectedClass' => IntegerDoc::class
            ],
            'float' => [
                'typeAlias' => 'float',
                'expectedClass' => FloatDoc::class
            ],
            'array' => [
                'typeAlias' => 'array',
                'expectedClass' => ArrayDoc::class
            ],
            'object' => [
                'typeAlias' => 'object',
                'expectedClass' => ObjectDoc::class
            ],
        ];
    }

    public function provideDerivedTypes()
    {
        return [
            'From IdenticalTo float' => [
                'constraint' => new Assert\IdenticalTo(2.3),
                'expectedClass' => FloatDoc::class,
            ],
            'From IdenticalTo integer' => [
                'constraint' => new Assert\IdenticalTo(2),
                'expectedClass' => IntegerDoc::class,
            ],
            'From IdenticalTo bool' => [
                'constraint' => new Assert\IdenticalTo(true),
                'expectedClass' => BooleanDoc::class,
            ],
            'From IdenticalTo string' => [
                'constraint' => new Assert\IdenticalTo('a'),
                'expectedClass' => StringDoc::class,
            ],
            'From callback' => [
                'constraint' => new Assert\Callback(function () { return new Assert\Type('string');}),
                'expectedClass' => StringDoc::class,
            ],
            'From callback array result' => [
                'constraint' => new Assert\Callback(function () { return [new Assert\Type('string')];}),
                'expectedClass' => StringDoc::class,
            ]
        ];
    }

    public function provideTypeAliases()
    {
        return [
            'int to integer' => [
                'typeAlias' => 'int',
                'expectedClass' => IntegerDoc::class
            ],
            'boolean' => [
                'typeAlias' => 'boolean',
                'expectedClass' => BooleanDoc::class
            ],
            'bool to boolean' => [
                'typeAlias' => 'bool',
                'expectedClass' => BooleanDoc::class
            ],
            'long to float' => [
                'typeAlias' => 'long',
                'expectedClass' => FloatDoc::class
            ],
            'double to float' => [
                'typeAlias' => 'double',
                'expectedClass' => FloatDoc::class
            ],
            'real to float' => [
                'typeAlias' => 'real',
                'expectedClass' => FloatDoc::class
            ],
            'numeric to number' => [
                'typeAlias' => 'numeric',
                'expectedClass' => NumberDoc::class
            ],
        ];
    }


    public function provideExistenceConstraints()
    {
        return [
            'required' => [
                'existenceConstraint' => new Assert\Required(new Assert\Type('float')),
                'expectedClass' => FloatDoc::class
            ],
            'optional' => [
                'existenceConstraint' => new Assert\Optional(new Assert\Type('float')),
                'expectedClass' => FloatDoc::class
            ],
        ];
    }
}
