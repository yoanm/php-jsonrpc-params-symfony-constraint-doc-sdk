<?php
namespace Tests\Functional\App\Helper\MinMaxHelper;

use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
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

    public function setUp()
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
                'constraintClass' => new Assert\Count(['min' => 2, 'max' => 4]),
                'expectedMin' => 2,
                'expectedMax' => 4,
            ],
            'Count constraint with only min' => [
                'constraintClass' => new Assert\Count(['min' => 2]),
                'expectedMin' => 2,
                'expectedMax' => null,
            ],
            'Count constraint with only max' => [
                'constraintClass' => new Assert\Count(['max' => 4]),
                'expectedMin' => null,
                'expectedMax' => 4,
            ],
        ];
    }
}
