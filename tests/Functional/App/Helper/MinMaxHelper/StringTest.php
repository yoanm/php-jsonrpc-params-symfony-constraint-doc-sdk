<?php
namespace Tests\Functional\App\Helper\MinMaxHelper;

use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Constraints as Assert;
use Yoanm\JsonRpcParamsSymfonyConstraintDoc\App\Helper\MinMaxHelper;
use Yoanm\JsonRpcServerDoc\Domain\Model\Type\StringDoc;

/**
 * @covers Yoanm\JsonRpcParamsSymfonyConstraintDoc\App\Helper\MinMaxHelper
 *
 * @group Helper
 */
class StringTest extends TestCase
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
    public function testShouldHandle(Constraint $constraint, $expectedMin, $expectedMax)
    {
        $doc = new StringDoc();

        $this->helper->append($doc, $constraint);

        $this->assertSame($expectedMin, $doc->getMinLength());
        $this->assertSame($expectedMax, $doc->getMaxLength());
    }

    public function provideConstraintsClass()
    {
        return [
            'Length constraint' => [
                'constraintClass' => new Assert\Length(['min' => 2, 'max' => 4]),
                'expectedMin' => 2,
                'expectedMax' => 4,
            ],
            'Length constraint with only min' => [
                'constraintClass' => new Assert\Length(['min' => 2]),
                'expectedMin' => 2,
                'expectedMax' => null,
            ],
            'Length constraint with only max' => [
                'constraintClass' => new Assert\Length(['max' => 4]),
                'expectedMin' => null,
                'expectedMax' => 4,
            ],
        ];
    }

    public function testShouldSetMinLengthIfNotNullWithNotBlankConstraint()
    {
        $doc = new StringDoc();

        $this->helper->append($doc, new Assert\NotBlank());

        $this->assertSame(1, $doc->getMinLength());
    }

    public function testShouldSetMaxLengthIfNotNullWithBlankConstraint()
    {
        $doc = new StringDoc();

        $this->helper->append($doc, new Assert\Blank());

        $this->assertSame(0, $doc->getMaxLength());
    }
}
