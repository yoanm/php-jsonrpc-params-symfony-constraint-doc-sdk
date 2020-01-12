<?php
namespace Tests\Technical\App\Helper;

use PHPUnit\Framework\TestCase;
use Symfony\Component\Validator\Constraints as Assert;
use Yoanm\JsonRpcParamsSymfonyConstraintDoc\App\Helper\TypeGuesser;

/**
 * @covers Yoanm\JsonRpcParamsSymfonyConstraintDoc\App\Helper\TypeGuesser
 *
 * @group Helper
 */
class TypeGuesserTest extends TestCase
{
    /** @var TypeGuesser */
    private $guesser;

    public function setUp(): void
    {
        $this->guesser = new TypeGuesser();
    }

    public function testShouldReturnNullIfRealTypeNotEstablished()
    {
        // use get_class to avoid inheritance issue
        $this->assertNull($this->guesser->guessTypeFromConstraintList([new Assert\Type('plop')]));
    }
}
