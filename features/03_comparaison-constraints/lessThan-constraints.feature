Feature: ConstraintToParamsDocTransformer - LessThan & LessThanOrEqual constraint

  Scenario: Simple LessThan constraint
    Given I have the following Constraint:
    """
    use Symfony\Component\Validator\Constraints as ConstraintNS;
    return new ConstraintNS\LessThan(2);
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

  Scenario: Simple LessThan constraint with integer type specified
    Given I have the following Constraint:
    """
    use Symfony\Component\Validator\Constraints as ConstraintNS;
    return new ConstraintNS\LessThan([
      'value' => 2,
      'payload' => [
        'documentation' => [
          'type' => 'integer'
        ]
      ]
    ]);
    """
    When I transform constraint
    Then I should have a constraint doc of class "Yoanm\JsonRpcServerDoc\Domain\Model\Type\IntegerDoc"
    And constraint doc "getMax" should return the number 2
    And constraint doc "isInclusiveMax" should return false
    ## Check others properties
    And constraint doc "getName" should return null
    And constraint doc "getDescription" should return null
    And constraint doc "getDefault" should return null
    And constraint doc "getExample" should return null
    And constraint doc "isNullable" should return true
    And constraint doc "isRequired" should return false
    And constraint doc "getAllowedValueList" should return an empty array
    And constraint doc "getMin" should return null
    And constraint doc "isInclusiveMin" should return true

  Scenario: Simple LessThan constraint with float type specified
    Given I have the following Constraint:
    """
    use Symfony\Component\Validator\Constraints as ConstraintNS;
    return new ConstraintNS\LessThan([
      'value' => 2.4,
      'payload' => [
        'documentation' => [
          'type' => 'float'
        ]
      ]
    ]);
    """
    When I transform constraint
    Then I should have a constraint doc of class "Yoanm\JsonRpcServerDoc\Domain\Model\Type\FloatDoc"
    And constraint doc "getMax" should return the number 2.4
    And constraint doc "isInclusiveMax" should return false
    ## Check others properties
    And constraint doc "getName" should return null
    And constraint doc "getDescription" should return null
    And constraint doc "getDefault" should return null
    And constraint doc "getExample" should return null
    And constraint doc "isNullable" should return true
    And constraint doc "isRequired" should return false
    And constraint doc "getAllowedValueList" should return an empty array
    And constraint doc "getMin" should return null
    And constraint doc "isInclusiveMin" should return true

  Scenario: Simple LessThan constraint with array type specified
    Given I have the following Constraint:
    """
    use Symfony\Component\Validator\Constraints as ConstraintNS;
    return new ConstraintNS\LessThan([
      'value' => 4,
      'payload' => [
        'documentation' => [
          'type' => 'array'
        ]
      ]
    ]);
    """
    When I transform constraint
    Then I should have a constraint doc of class "Yoanm\JsonRpcServerDoc\Domain\Model\Type\ArrayDoc"
    And constraint doc "getMaxItem" should return the number 3
    ## Check others properties
    And constraint doc "getName" should return null
    And constraint doc "getDescription" should return null
    And constraint doc "getDefault" should return null
    And constraint doc "getExample" should return null
    And constraint doc "isNullable" should return true
    And constraint doc "isRequired" should return false
    And constraint doc "getAllowedValueList" should return an empty array
    And constraint doc "getMinItem" should return null
    And constraint doc "isAllowExtraSibling" should return false
    And constraint doc "isAllowMissingSibling" should return false
    And constraint doc "getItemValidation" should return null

  Scenario: Simple LessThanOrEqual constraint
    Given I have the following Constraint:
    """
    use Symfony\Component\Validator\Constraints as ConstraintNS;
    return new ConstraintNS\LessThanOrEqual(2);
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

  Scenario: Simple LessThanOrEqual constraint with integer type specified
    Given I have the following Constraint:
    """
    use Symfony\Component\Validator\Constraints as ConstraintNS;
    return new ConstraintNS\LessThanOrEqual([
      'value' => 2,
      'payload' => [
        'documentation' => [
          'type' => 'integer'
        ]
      ]
    ]);
    """
    When I transform constraint
    Then I should have a constraint doc of class "Yoanm\JsonRpcServerDoc\Domain\Model\Type\IntegerDoc"
    And constraint doc "getMax" should return the number 2
    And constraint doc "isInclusiveMax" should return true
    ## Check others properties
    And constraint doc "getName" should return null
    And constraint doc "getDescription" should return null
    And constraint doc "getDefault" should return null
    And constraint doc "getExample" should return null
    And constraint doc "isNullable" should return true
    And constraint doc "isRequired" should return false
    And constraint doc "getAllowedValueList" should return an empty array
    And constraint doc "getMin" should return null
    And constraint doc "isInclusiveMin" should return true

  Scenario: Simple LessThanOrEqual constraint with float type specified
    Given I have the following Constraint:
    """
    use Symfony\Component\Validator\Constraints as ConstraintNS;
    return new ConstraintNS\LessThanOrEqual([
      'value' => 2.4,
      'payload' => [
        'documentation' => [
          'type' => 'float'
        ]
      ]
    ]);
    """
    When I transform constraint
    Then I should have a constraint doc of class "Yoanm\JsonRpcServerDoc\Domain\Model\Type\FloatDoc"
    And constraint doc "getMax" should return the number 2.4
    And constraint doc "isInclusiveMax" should return true
    ## Check others properties
    And constraint doc "getName" should return null
    And constraint doc "getDescription" should return null
    And constraint doc "getDefault" should return null
    And constraint doc "getExample" should return null
    And constraint doc "isNullable" should return true
    And constraint doc "isRequired" should return false
    And constraint doc "getAllowedValueList" should return an empty array
    And constraint doc "getMin" should return null
    And constraint doc "isInclusiveMin" should return true

  Scenario: Simple LessThanOrEqual constraint with array type specified
    Given I have the following Constraint:
    """
    use Symfony\Component\Validator\Constraints as ConstraintNS;
    return new ConstraintNS\LessThanOrEqual([
      'value' => 4,
      'payload' => [
        'documentation' => [
          'type' => 'array'
        ]
      ]
    ]);
    """
    When I transform constraint
    Then I should have a constraint doc of class "Yoanm\JsonRpcServerDoc\Domain\Model\Type\ArrayDoc"
    And constraint doc "getMaxItem" should return the number 4
    ## Check others properties
    And constraint doc "getName" should return null
    And constraint doc "getDescription" should return null
    And constraint doc "getDefault" should return null
    And constraint doc "getExample" should return null
    And constraint doc "isNullable" should return true
    And constraint doc "isRequired" should return false
    And constraint doc "getAllowedValueList" should return an empty array
    And constraint doc "getMinItem" should return null
    And constraint doc "isAllowExtraSibling" should return false
    And constraint doc "isAllowMissingSibling" should return false
    And constraint doc "getItemValidation" should return null
