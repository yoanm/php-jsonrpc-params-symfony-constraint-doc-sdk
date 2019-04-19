Feature: ConstraintToParamsDocTransformer - IsTrue & IsFalse constraints

  Scenario: Simple IsTrue constraint
    Given I have the following Constraint:
    """
    use Symfony\Component\Validator\Constraints as ConstraintNS;
    return new ConstraintNS\IsTrue();
    """
    When I transform constraint
    Then I should have a constraint doc of class "Yoanm\JsonRpcServerDoc\Domain\Model\Type\BooleanDoc"
    And constraint doc "getAllowedValueList" should return:
    """
    [true, 1, "1"]
    """
    And constraint doc "isNullable" should return false
    And constraint doc "getDefault" should return true
    And constraint doc "getExample" should return true
    ## Check others properties
    And constraint doc "getName" should return null
    And constraint doc "getDescription" should return null
    And constraint doc "isRequired" should return false

  Scenario: Simple IsFalse constraint
    Given I have the following Constraint:
    """
    use Symfony\Component\Validator\Constraints as ConstraintNS;
    return new ConstraintNS\IsFalse();
    """
    When I transform constraint
    Then I should have a constraint doc of class "Yoanm\JsonRpcServerDoc\Domain\Model\Type\BooleanDoc"
    And constraint doc "getAllowedValueList" should return:
    """
    [false, 0, "0"]
    """
    And constraint doc "isNullable" should return false
    And constraint doc "getDefault" should return false
    And constraint doc "getExample" should return false
    ## Check others properties
    And constraint doc "getName" should return null
    And constraint doc "getDescription" should return null
    And constraint doc "isRequired" should return false
