<?php
namespace Tests\Functional\BehatContext;

use Behat\Gherkin\Node\PyStringNode;
use PHPUnit\Framework\Assert;
use Symfony\Component\Validator\Constraint;
use Yoanm\JsonRpcParamsSymfonyConstraintDoc\App\Helper\ConstraintPayloadDocHelper;
use Yoanm\JsonRpcParamsSymfonyConstraintDoc\App\Helper\DocTypeHelper;
use Yoanm\JsonRpcParamsSymfonyConstraintDoc\App\Helper\MinMaxHelper;
use Yoanm\JsonRpcParamsSymfonyConstraintDoc\App\Helper\StringDocHelper;
use Yoanm\JsonRpcParamsSymfonyConstraintDoc\App\Helper\TypeGuesser;
use Yoanm\JsonRpcParamsSymfonyConstraintDoc\Infra\Transformer\ConstraintToParamsDocTransformer;
use Yoanm\JsonRpcServerDoc\Domain\Model\Type\CollectionDoc;
use Yoanm\JsonRpcServerDoc\Domain\Model\Type\TypeDoc;

class ConstraintTransformerContext extends AbstractContext
{
    /** @var null|TypeDoc */
    private $lastDocumenation = null;
    /** @var Constraint|null */
    private $lastConstraint = null;

    /**
     * @Given I have the following Constraint:
     */
    public function givenIHaveTheFollowingConstraint(PyStringNode $phpCode)
    {
        $this->lastConstraint = eval($phpCode->getRaw());
    }

    /**
     * @When I transform constraint
     */
    public function whenINormalizeConstraint()
    {
        $constraintPayloadDocHelper = new ConstraintPayloadDocHelper();
        $transformer = new ConstraintToParamsDocTransformer(
            new DocTypeHelper(
                $constraintPayloadDocHelper,
                new TypeGuesser()
            ),
            new StringDocHelper(),
            new MinMaxHelper(),
            $constraintPayloadDocHelper
        );
        if (is_array($this->lastConstraint)) {
            $this->lastDocumenation = $transformer->transformList($this->lastConstraint);
        } else {
            $this->lastDocumenation = $transformer->transform($this->lastConstraint);
        }
    }

    /**
     * @Then I should have a constraint doc of class :class
     */
    public function thenIShouldHaveFollowingConstraintDoc($class)
    {
        // use ::class notation to avoid issue with inheritance
        Assert::assertSame($class, get_class($this->lastDocumenation));
    }

    /**
     * @Then constraint doc item validation should be of type :class
     */
    public function thenConstraintDocItemValidationShouldBeOfType($class)
    {
        $itemValidation = $this->lastDocumenation->getItemValidation();
        Assert::assertSame($class, get_class($itemValidation));
    }

    /**
     * @Then constraint doc should have :count sibling
     * @Then constraint doc should have :count siblings
     */
    public function thenConstraintDocShouldHaveXSiblings($count)
    {
        Assert::assertCount((int) $count, $this->lastDocumenation->getSiblingList());
    }

    /**
     * @Then constraint doc should have a sibling :siblingName of class :class
     */
    public function thenConstraintDocShouldHaveASiblingName($siblingName, $class = null)
    {
        $sibling = $this->findSiblingNamed($siblingName);
        if (null !== $class) {
            Assert::assertInstanceOf($class, $sibling);
        }
    }

    /**
     * @Then constraint doc sibling :siblingName :methodName should return the value :result
     */
    public function thenConstraintDocSiblingMethodShouldReturn($siblingName, $methodName, $result)
    {
        $sibling = $this->findSiblingNamed($siblingName);
        $this->assertMethodCallResult($sibling, $methodName, $result);
    }

    /**
     * @Then constraint doc sibling :siblingName :methodName should return the number :result
     */
    public function thenConstraintDocSiblingMethodShouldReturnInteger($methodName, $result)
    {
        $this->assertMethodCallResult(
            $this->lastDocumenation,
            $methodName,
            (int) $result == $result ? (int) $result : (float) $result
        );
    }

    /**
     * @Then constraint doc sibling :siblingName :methodName should return null
     */
    public function thenConstraintDocSiblingMethodShouldReturnNull($siblingName, $methodName)
    {
        $sibling = $this->findSiblingNamed($siblingName);
        $this->assertMethodCallResult($sibling, $methodName, null);
    }

    /**
     * @Then constraint doc sibling :siblingName :methodName should return true
     */
    public function thenConstraintDocSiblingMethodShouldReturnTrue($siblingName, $methodName)
    {
        $sibling = $this->findSiblingNamed($siblingName);
        $this->assertMethodCallResult($sibling, $methodName, true);
    }

    /**
     * @Then constraint doc sibling :siblingName :methodName should return false
     */
    public function thenConstraintDocSiblingMethodShouldReturnFalse($siblingName, $methodName)
    {
        $sibling = $this->findSiblingNamed($siblingName);
        $this->assertMethodCallResult($sibling, $methodName, false);
    }

    /**
     * @Then constraint doc sibling :siblingName :methodName should return an empty array
     */
    public function thenConstraintDocSiblingMethodShouldReturnEmptyArray($siblingName, $methodName)
    {
        $sibling = $this->findSiblingNamed($siblingName);
        $this->assertMethodCallResult($sibling, $methodName, []);
    }

    /**
     * @Then constraint doc :methodName should return:
     */
    public function thenIShouldHaveAConstraintDocWhichReturnsPyStringNode($methodName, PyStringNode $result)
    {
        $this->thenConstraintDocMethodShouldReturn(
            $methodName,
            $this->jsonDecode($result->getRaw())
        );
    }

    /**
     * @Then constraint doc :methodName should return the value :result
     */
    public function thenConstraintDocMethodShouldReturn($methodName, $result)
    {
        $this->assertMethodCallResult($this->lastDocumenation, $methodName, $result);
    }

    /**
     * @Then constraint doc :methodName should contain the value :result
     */
    public function thenConstraintDocMethodShouldContain($methodName, $result)
    {
        $this->assertMethodCallContains($this->lastDocumenation, $methodName, $result);
    }

    /**
     * @Then constraint doc :methodName should return the number :result
     */
    public function thenConstraintDocMethodShouldReturnInteger($methodName, $result)
    {
        $this->assertMethodCallResult(
            $this->lastDocumenation,
            $methodName,
            (int) $result == $result ? (int) $result : (float) $result
        );
    }

    /**
     * @Then constraint doc :methodName should return null
     */
    public function thenConstraintDocMethodShouldReturnNull($methodName)
    {
        $this->assertMethodCallResult($this->lastDocumenation, $methodName, null);
    }

    /**
     * @Then constraint doc :methodName should return true
     */
    public function thenConstraintDocMethodShouldReturnTrue($methodName)
    {
        $this->assertMethodCallResult($this->lastDocumenation, $methodName, true);
    }

    /**
     * @Then constraint doc :methodName should return false
     */
    public function thenConstraintDocMethodShouldReturnFalse($methodName)
    {
        $this->assertMethodCallResult($this->lastDocumenation, $methodName, false);
    }

    /**
     * @Then constraint doc :methodName should return an empty array
     */
    public function thenConstraintDocMethodShouldReturnEmptyArray($methodName)
    {
        $this->assertMethodCallResult($this->lastDocumenation, $methodName, []);
    }

    /**
     * @param string $siblingName
     *
     * @return mixed
     * @throws \Exception
     */
    private function findSiblingNamed($siblingName)
    {
        Assert::assertInstanceOf(CollectionDoc::class, $this->lastDocumenation);

        foreach ($this->lastDocumenation->getSiblingList() as $sibling) {
            if ($siblingName === $sibling->getName()) {
                return $sibling;
            }
        }

        throw new \Exception(sprintf('Unable to find sibling named "%s"', $siblingName));
    }

    /**
     * @param $object
     * @param $methodName
     * @param $result
     */
    private function assertMethodCallResult($object, $methodName, $result): void
    {
        Assert::assertSame($result, call_user_func_array([$object, $methodName], []));
    }

    /**
     * @param $object
     * @param $methodName
     * @param $result
     */
    private function assertMethodCallContains($object, $methodName, $result): void
    {
        Assert::assertStringContainsString($result, call_user_func_array([$object, $methodName], []));
    }
}
