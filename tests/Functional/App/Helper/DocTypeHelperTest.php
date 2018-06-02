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
use Yoanm\JsonRpcServerDoc\Domain\Model\Type\CollectionDoc;
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
    public function testShouldHandleType($type, $expectedClass)
    {
        // use get_class to avoid inheritance issue
        $this->assertSame(
            $expectedClass,
            get_class($this->helper->guess([new Assert\Type($type)]))
        );
    }

    /**
     * @dataProvider provideTypeAliases
     *
     * @param string $type
     * @param string $expectedClass
     */
    public function testShouldHandleTypeAlias($type, $expectedClass)
    {
        // use get_class to avoid inheritance issue
        $this->assertSame(
            $expectedClass,
            get_class($this->helper->guess([new Assert\Type($type)]))
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
            'numeric to float' => [
                'typeAlias' => 'numeric',
                'expectedClass' => FloatDoc::class
            ],
        ];
    }
}
