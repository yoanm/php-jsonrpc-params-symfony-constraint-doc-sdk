<?php
namespace Tests\Functional\App\Helper\MinMaxHelper;

use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Constraints as Assert;
use Yoanm\JsonRpcParamsSymfonyConstraintDoc\App\Helper\MinMaxHelper;
use Yoanm\JsonRpcServerDoc\Domain\Model\Type\NumberDoc;

/**
 * @covers Yoanm\JsonRpcParamsSymfonyConstraintDoc\App\Helper\MinMaxHelper
 *
 * @group Helper
 */
class NumberTest extends TestCase
{
    use ProphecyTrait;

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
    public function testShouldHandle(
        Constraint $constraint,
        $expectedMin,
        $expectedMax,
        $expectedInclusiveMin,
        $expectedInclusiveMax
    ) {
        $doc = new NumberDoc();

        $this->helper->append($doc, $constraint);

        $this->assertSame($expectedMin, $doc->getMin());
        $this->assertSame($expectedMax, $doc->getMax());
        $this->assertSame($expectedInclusiveMin, $doc->isInclusiveMin());
        $this->assertSame($expectedInclusiveMax, $doc->isInclusiveMax());
    }

    public function provideConstraintsClass()
    {
        return [
            'Range constraint' => [
                'constraintClass' => new Assert\Range(['min' => 2, 'max' => 4]),
                'expectedMin' => 2,
                'expectedMax' => 4,
                'expectedIncludeMin' => true,
                'expectedIncludeMax' => true,
            ],
            'LessThan constraint' => [
                'constraintClass' => new Assert\LessThan(4),
                'expectedMin' => null,
                'expectedMax' => 4,
                'expectedIncludeMin' => true,
                'expectedIncludeMax' => false,
            ],
            'LessThanOrEqual constraint' => [
                'constraintClass' => new Assert\LessThanOrEqual(4),
                'expectedMin' => null,
                'expectedMax' => 4,
                'expectedIncludeMin' => true,
                'expectedIncludeMax' => true,
            ],
            'GreaterThan constraint' => [
                'constraintClass' => new Assert\GreaterThan(2),
                'expectedMin' => 2,
                'expectedMax' => null,
                'expectedIncludeMin' => false,
                'expectedIncludeMax' => true,
            ],
            'GreaterThanOrEqual constraint' => [
                'constraintClass' => new Assert\GreaterThanOrEqual(2),
                'expectedMin' => 2,
                'expectedMax' => null,
                'expectedIncludeMin' => true,
                'expectedIncludeMax' => true,
            ],
            'Range constraint with only min' => [
                'constraintClass' => new Assert\Range(['min' => 2]),
                'expectedMin' => 2,
                'expectedMax' => null,
                'expectedIncludeMin' => true,
                'expectedIncludeMax' => true,
            ],
            'Range constraint with only max' => [
                'constraintClass' => new Assert\Range(['max' => 4]),
                'expectedMin' => null,
                'expectedMax' => 4,
                'expectedIncludeMin' => true,
                'expectedIncludeMax' => true,
            ],
        ];
    }
}
