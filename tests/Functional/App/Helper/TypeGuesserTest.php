<?php
namespace Tests\Functional\App\Helper;

use PHPUnit\Framework\TestCase;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Constraints as Assert;
use Yoanm\JsonRpcParamsSymfonyConstraintDoc\App\Helper\TypeGuesser;
use Yoanm\JsonRpcServerDoc\Domain\Model\Type\ArrayDoc;
use Yoanm\JsonRpcServerDoc\Domain\Model\Type\BooleanDoc;
use Yoanm\JsonRpcServerDoc\Domain\Model\Type\CollectionDoc;
use Yoanm\JsonRpcServerDoc\Domain\Model\Type\ObjectDoc;
use Yoanm\JsonRpcServerDoc\Domain\Model\Type\ScalarDoc;
use Yoanm\JsonRpcServerDoc\Domain\Model\Type\StringDoc;

/**
 * @covers Yoanm\JsonRpcParamsSymfonyConstraintDoc\App\Helper\TypeGuesser
 *
 * @group Helper
 */
class TypeGuesserTest extends TestCase
{
    /** @var TypeGuesser */
    private $guesser;

    public function setUp()
    {
        $this->guesser = new TypeGuesser();
    }

    /**
     * @dataProvider provideConstraints
     *
     * @param Constraint $constraint
     * @param $expectedClass
     */
    public function testShouldHandleConstraint(Constraint $constraint, $expectedClass)
    {
        // use get_class to avoid inheritance issue
        $this->assertSame(
            $expectedClass,
            get_class($this->guesser->guessTypeFromConstraintList([$constraint]))
        );
    }

    public function provideConstraints()
    {
        return [
            'Length to String' => [
                'constraint' => new Assert\Length(3),
                'expectedClass' => StringDoc::class,
            ],
            'Date to String' => [
                'constraint' => new Assert\Date(),
                'expectedClass' => StringDoc::class,
            ],
            'Time to String' => [
                'constraint' => new Assert\Time(),
                'expectedClass' => StringDoc::class,
            ],
            'Bic to String' => [
                'constraint' => new Assert\Bic(),
                'expectedClass' => StringDoc::class,
            ],
            'CardScheme to String' => [
                'constraint' => new Assert\CardScheme('AMEX'),
                'expectedClass' => StringDoc::class,
            ],
            'Country to String' => [
                'constraint' => new Assert\Country(),
                'expectedClass' => StringDoc::class,
            ],
            'Currency to String' => [
                'constraint' => new Assert\Currency(),
                'expectedClass' => StringDoc::class,
            ],
            'Email to String' => [
                'constraint' => new Assert\Email(),
                'expectedClass' => StringDoc::class,
            ],
            'File to String' => [
                'constraint' => new Assert\File(),
                'expectedClass' => StringDoc::class,
            ],
            'Iban to String' => [
                'constraint' => new Assert\Iban(),
                'expectedClass' => StringDoc::class,
            ],
            'Ip to String' => [
                'constraint' => new Assert\Ip(),
                'expectedClass' => StringDoc::class,
            ],
            'Isbn to String' => [
                'constraint' => new Assert\Isbn(),
                'expectedClass' => StringDoc::class,
            ],
            'Issn to String' => [
                'constraint' => new Assert\Issn(),
                'expectedClass' => StringDoc::class,
            ],
            'Language to String' => [
                'constraint' => new Assert\Language(),
                'expectedClass' => StringDoc::class,
            ],
            'Locale to String' => [
                'constraint' => new Assert\Locale(),
                'expectedClass' => StringDoc::class,
            ],
            'Luhn to String' => [
                'constraint' => new Assert\Luhn(),
                'expectedClass' => StringDoc::class,
            ],
            'Regex to String' => [
                'constraint' => new Assert\Regex('/.{2}/'),
                'expectedClass' => ScalarDoc::class,
            ],
            'Url to String' => [
                'constraint' => new Assert\Url(),
                'expectedClass' => StringDoc::class,
            ],
            'Uuid to String' => [
                'constraint' => new Assert\Uuid(),
                'expectedClass' => StringDoc::class,
            ],
            'DateTime to Scalar' => [
                'constraint' => new Assert\DateTime(['format' => 'U']),
                'expectedClass' => ScalarDoc::class,
            ],
            'DateTime to String' => [
                'constraint' => new Assert\DateTime(),
                'expectedClass' => StringDoc::class,
            ],
            'IsTrue to Boolean' => [
                'constraint' => new Assert\IsTrue(),
                'expectedClass' => BooleanDoc::class,
            ],
            'IsFalse to Boolean' => [
                'constraint' => new Assert\IsFalse(),
                'expectedClass' => BooleanDoc::class,
            ],
            'Collection to Array' => [
                'constraint' => new Assert\Collection(['fields' => [0 => [], 1 => []]]),
                'expectedClass' => ArrayDoc::class,
            ],
            'Collection to Object' => [
                'constraint' => new Assert\Collection(['fields' => ['a' => [], 'b' => []]]),
                'expectedClass' => ObjectDoc::class,
            ],
            'Choice (multiple) to Array' => [
                'constraint' => new Assert\Choice(['multiple' => true]),
                'expectedClass' => ArrayDoc::class,
            ],
            'All to Array' => [
                'constraint' => new Assert\All(['constraints' => []]),
                'expectedClass' => ArrayDoc::class,
            ],
            'Count to Collection' => [
                'constraint' => new Assert\Count(['min' => 2]),
                'expectedClass' => CollectionDoc::class,
            ],
        ];
    }
}
