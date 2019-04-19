<?php
namespace Tests\Functional\Infra;

use PHPUnit\Framework\TestCase;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Constraints as Assert;
use Yoanm\JsonRpcParamsSymfonyConstraintDoc\App\Helper\StringDocHelper;
use Yoanm\JsonRpcServerDoc\Domain\Model\Type\StringDoc;
use Yoanm\JsonRpcServerDoc\Domain\Model\Type\TypeDoc;

/**
 * @covers Yoanm\JsonRpcParamsSymfonyConstraintDoc\App\Helper\StringDocHelper
 *
 * @group Helper
 */
class StringDocHelperTest extends TestCase
{

    /** @var StringDocHelper */
    private $helper;

    public function setUp()
    {
        $this->helper = new StringDocHelper();
    }

    public function testShouldDoNothingIfNotAStringDoc()
    {
        $doc = new TypeDoc();
        $constraint = $this->prophesize(Assert\Date::class);

        $this->helper->append($doc, $constraint->reveal());

        // there only to avoid "This test did not perform any assertions" issue
        $this->assertNull(null);
    }

    /**
     * @dataProvider provideConstraintsClassWithFormat
     *
     * @param Constraint $constraint
     * @param string     $expectedFormat
     */
    public function testShouldHandle(Constraint $constraint, $expectedFormat)
    {
        $doc = new StringDoc();

        $this->helper->append($doc, $constraint);

        $this->assertSame($expectedFormat, $doc->getFormat());
    }

    public function provideConstraintsClassWithFormat()
    {
        return [
            'BIC format' => [
                'constraintClass' => new Assert\Bic(),
                'expectedFormat' => 'bic'
            ],
            'Card scheme format' => [
                'constraintClass' => new Assert\CardScheme('AMEX'),
                'expectedFormat' => 'cardScheme'
            ],
            'country format' => [
                'constraintClass' => new Assert\Country(),
                'expectedFormat' => 'country'
            ],
            'currency format' => [
                'constraintClass' => new Assert\Currency(),
                'expectedFormat' => 'currency'
            ],
            'date format' => [
                'constraintClass' => new Assert\Date(),
                'expectedFormat' => 'date'
            ],
            'date  formattime' => [
                'constraintClass' => new Assert\DateTime(),
                'expectedFormat' => 'datetime'
            ],
            'email format' => [
                'constraintClass' => new Assert\Email(),
                'expectedFormat' => 'email'
            ],
            'file format' => [
                'constraintClass' => new Assert\File(),
                'expectedFormat' => 'file'
            ],
            'IBAN format' => [
                'constraintClass' => new Assert\Iban(),
                'expectedFormat' => 'iban'
            ],
            'IP format' => [
                'constraintClass' => new Assert\Ip(),
                'expectedFormat' => 'ip'
            ],
            'isbn format' => [
                'constraintClass' => new Assert\Isbn(),
                'expectedFormat' => 'isbn'
            ],
            'issn format' => [
                'constraintClass' => new Assert\Issn(),
                'expectedFormat' => 'issn'
            ],
            'language format' => [
                'constraintClass' => new Assert\Language(),
                'expectedFormat' => 'language'
            ],
            'locale format' => [
                'constraintClass' => new Assert\Locale(),
                'expectedFormat' => 'locale'
            ],
            'luhn format' => [
                'constraintClass' => new Assert\Luhn(),
                'expectedFormat' => 'luhn'
            ],
            'regex format' => [
                'constraintClass' => new Assert\Regex('/.*/'),
                'expectedFormat' => '/.*/'
            ],
            'time format' => [
                'constraintClass' => new Assert\Time(),
                'expectedFormat' => 'time'
            ],
            'url format' => [
                'constraintClass' => new Assert\Url(),
                'expectedFormat' => 'url'
            ],
            'uuid format' => [
                'constraintClass' => new Assert\Uuid(),
                'expectedFormat' => 'uuid'
            ],
            'Range constraint (with string it must be date conparaison)' => [
                'constraintClass' => new Assert\Range(['min' => '2018-10-10', 'max' => '2018-11-10']),
                'expectedFormat' => 'datetime'
            ],
            'Expression constraint' => [
                'constraintClass' => new Assert\Expression('strlen(this) == 2'),
                'expectedFormat' => 'strlen(this) == 2'
            ],
        ];
    }
}
