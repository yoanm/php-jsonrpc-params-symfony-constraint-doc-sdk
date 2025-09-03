<?php
namespace Tests\Functional\App\Helper\MinMaxHelper;

use PHPUnit\Framework\TestCase;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Constraints as Assert;
use Yoanm\JsonRpcParamsSymfonyConstraintDoc\App\Helper\MinMaxHelper;
use Yoanm\JsonRpcServerDoc\Domain\Model\Type\CollectionDoc;

/**
 * @covers Yoanm\JsonRpcParamsSymfonyConstraintDoc\App\Helper\MinMaxHelper
 *
 * @group Helper
 */
class CollectionTest extends TestCase
{
    /** @var MinMaxHelper */
    private $helper;

    public function setUp(): void
    {
        $this->helper = new MinMaxHelper();
    }

    /**
     * @dataProvider provideConstraintsClass
     *
     * @param Constraint $constraint
     * @param int|null   $expectedMin
     * @param int|null   $expectedMax
     */
    public function testShouldHandle(Constraint $constraint, $expectedMin, $expectedMax)
    {
        $doc = new CollectionDoc();

        $this->helper->append($doc, $constraint);

        $this->assertSame($expectedMin, $doc->getMinItem());
        $this->assertSame($expectedMax, $doc->getMaxItem());
    }

    public function provideConstraintsClass()
    {
        return [
            'Count constraint' => [
                'constraint' => new Assert\Count(['min' => 2, 'max' => 4]),
                'expectedMin' => 2,
                'expectedMax' => 4,
            ],
            'Count constraint with only min' => [
                'constraint' => new Assert\Count(['min' => 2]),
                'expectedMin' => 2,
                'expectedMax' => null,
            ],
            'Count constraint with only max' => [
                'constraint' => new Assert\Count(['max' => 4]),
                'expectedMin' => null,
                'expectedMax' => 4,
            ],
            'Choice constraint' => [
                'constraint' => new Assert\Choice([
                    'choices' => [1, 2, 3, 4],
                    'multiple' => true,
                    'min' => 2,
                    'max' => 4
                ]),
                'expectedMin' => 2,
                'expectedMax' => 4,
            ],
            'Choice constraint with only min' => [
                'constraint' => new Assert\Choice([
                    'choices' => [1, 2, 3, 4],
                    'multiple' => true,
                    'min' => 2
                ]),
                'expectedMin' => 2,
                'expectedMax' => null,
            ],
            'Choice constraint with only max' => [
                'constraint' => new Assert\Choice([
                    'choices' => [1, 2, 3, 4],
                    'multiple' => true,
                    'max' => 4
                ]),
                'expectedMin' => null,
                'expectedMax' => 4,
            ],
            'NotBlank constraint with array type' => [
                'constraint' => new Assert\NotBlank(['payload' => ['documentation' => ['type' => 'array']]]),
                'expectedMin' => 1,
                'expectedMax' => null,
            ],
            'GreaterThan constraint with array type' => [
                'constraint' => new Assert\GreaterThan(
                    2,
                    null,
                    null,
                    null,
                    ['documentation' => ['type' => 'array']]
                ),
                'expectedMin' => 3,
                'expectedMax' => null,
            ],
            'GreaterThanOrEqual constraint with array type' => [
                'constraint' => new Assert\GreaterThanOrEqual(
                    2,
                    null,
                    null,
                    null,
                    ['documentation' => ['type' => 'array']]
                ),
                'expectedMin' => 2,
                'expectedMax' => null,
            ],
            'LessThan constraint with array type' => [
                'constraint' => new Assert\LessThan(
                    3,
                    null,
                    null,
                    null,
                    ['documentation' => ['type' => 'array']]
                ),
                'expectedMin' => null,
                'expectedMax' => 2,
            ],
            'LessThanOrEqual constraint with array type' => [
                'constraint' => new Assert\LessThanOrEqual(
                    3,
                    null,
                    null,
                    null,
                    ['documentation' => ['type' => 'array']]
                ),
                'expectedMin' => null,
                'expectedMax' => 3,
            ],
        ];
    }
}
