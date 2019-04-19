Feature: ConstraintToParamsDocTransformer - IdenticalTo & NotIdenticalTo constraint

  Scenario: Simple string IdenticalTo constraint
    Given I have the following Constraint:
    """
    use Symfony\Component\Validator\Constraints as ConstraintNS;
    return new ConstraintNS\IdenticalTo('expected-text');
    """
    When I transform constraint
    Then I should have a constraint doc of class "Yoanm\JsonRpcServerDoc\Domain\Model\Type\StringDoc"
    And constraint doc "getAllowedValueList" should return:
    """
    ["expected-text"]
    """
    And constraint doc "getDefault" should return the value "expected-text"
    And constraint doc "getExample" should return the value "expected-text"
    And constraint doc "isNullable" should return false
    ## Check others properties
    And constraint doc "getName" should return null
    And constraint doc "getDescription" should return null
    And constraint doc "isRequired" should return false
    And constraint doc "getFormat" should return null
    And constraint doc "getMinLength" should return null
    And constraint doc "getMaxLength" should return null

  Scenario: Simple boolean IdenticalTo constraint
    Given I have the following Constraint:
    """
    use Symfony\Component\Validator\Constraints as ConstraintNS;
    return new ConstraintNS\IdenticalTo(true);
    """
    When I transform constraint
    Then I should have a constraint doc of class "Yoanm\JsonRpcServerDoc\Domain\Model\Type\BooleanDoc"
    And constraint doc "getAllowedValueList" should return:
    """
    [true]
    """
    And constraint doc "getDefault" should return true
    And constraint doc "getExample" should return true
    And constraint doc "isNullable" should return false
    ## Check others properties
    And constraint doc "getName" should return null
    And constraint doc "getDescription" should return null
    And constraint doc "isRequired" should return false

  Scenario: Simple integer IdenticalTo constraint
    Given I have the following Constraint:
    """
    use Symfony\Component\Validator\Constraints as ConstraintNS;
    return new ConstraintNS\IdenticalTo(2);
    """
    When I transform constraint
    Then I should have a constraint doc of class "Yoanm\JsonRpcServerDoc\Domain\Model\Type\IntegerDoc"
    And constraint doc "getAllowedValueList" should return:
    """
    [2]
    """
    And constraint doc "getDefault" should return the number 2
    And constraint doc "getExample" should return the number 2
    And constraint doc "isNullable" should return false
    ## Check others properties
    And constraint doc "getName" should return null
    And constraint doc "getDescription" should return null
    And constraint doc "isRequired" should return false
    And constraint doc "getMin" should return null
    And constraint doc "isInclusiveMin" should return true
    And constraint doc "getMax" should return null
    And constraint doc "isInclusiveMax" should return true

  Scenario: Simple float IdenticalTo constraint
    Given I have the following Constraint:
    """
    use Symfony\Component\Validator\Constraints as ConstraintNS;
    return new ConstraintNS\IdenticalTo(2.3);
    """
    When I transform constraint
    Then I should have a constraint doc of class "Yoanm\JsonRpcServerDoc\Domain\Model\Type\FloatDoc"
    And constraint doc "getAllowedValueList" should return:
    """
    [2.3]
    """
    And constraint doc "getDefault" should return the number 2.3
    And constraint doc "getExample" should return the number 2.3
    And constraint doc "isNullable" should return false
    ## Check others properties
    And constraint doc "getName" should return null
    And constraint doc "getDescription" should return null
    And constraint doc "isRequired" should return false
    And constraint doc "getMin" should return null
    And constraint doc "isInclusiveMin" should return true
    And constraint doc "getMax" should return null
    And constraint doc "isInclusiveMax" should return true

  Scenario: Simple NotIdenticalTo constraint
    Given I have the following Constraint:
    """
    use Symfony\Component\Validator\Constraints as ConstraintNS;
    return new ConstraintNS\NotIdenticalTo('not-expected-text');
    """
    When I transform constraint
    Then I should have a constraint doc of class "Yoanm\JsonRpcServerDoc\Domain\Model\Type\TypeDoc"
    ## Check others properties
    And constraint doc "getName" should return null
    And constraint doc "getDescription" should return null
    And constraint doc "getDefault" should return null
    And constraint doc "getExample" should return null
    And constraint doc "isNullable" should return true
    And constraint doc "isRequired" should return false
    And constraint doc "getAllowedValueList" should return an empty array
