Feature: ConstraintToParamsDocTransformer - Range constraint

  Scenario: Simple Range constraint
    Given I have the following Constraint:
    """
    use Symfony\Component\Validator\Constraints as ConstraintNS;
    return new ConstraintNS\Range([
      'min' => 2,
      'max' => 3
    ]);
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

  Scenario: Simple Range constraint with integer type specified
    Given I have the following Constraint:
    """
    use Symfony\Component\Validator\Constraints as ConstraintNS;
    return new ConstraintNS\Range([
      'min' => 2,
      'max' => 3,
      'payload' => [
        'documentation' => [
          'type' => 'integer'
        ]
      ]
    ]);
    """
    When I transform constraint
    Then I should have a constraint doc of class "Yoanm\JsonRpcServerDoc\Domain\Model\Type\IntegerDoc"
    And constraint doc "getMin" should return the number 2
    And constraint doc "getMax" should return the number 3
    And constraint doc "isInclusiveMin" should return true
    And constraint doc "isInclusiveMax" should return true
    ## Check others properties
    And constraint doc "getName" should return null
    And constraint doc "getDescription" should return null
    And constraint doc "getDefault" should return null
    And constraint doc "getExample" should return null
    And constraint doc "isNullable" should return true
    And constraint doc "isRequired" should return false
    And constraint doc "getAllowedValueList" should return an empty array

  Scenario: Simple Range constraint with float type specified
    Given I have the following Constraint:
    """
    use Symfony\Component\Validator\Constraints as ConstraintNS;
    return new ConstraintNS\Range([
      'min' => 2.4,
      'max' => 3.3,
      'payload' => [
        'documentation' => [
          'type' => 'float'
        ]
      ]
    ]);
    """
    When I transform constraint
    Then I should have a constraint doc of class "Yoanm\JsonRpcServerDoc\Domain\Model\Type\FloatDoc"
    And constraint doc "getMin" should return the number 2.4
    And constraint doc "getMax" should return the number 3.3
    And constraint doc "isInclusiveMin" should return true
    And constraint doc "isInclusiveMax" should return true
    ## Check others properties
    And constraint doc "getName" should return null
    And constraint doc "getDescription" should return null
    And constraint doc "getDefault" should return null
    And constraint doc "getExample" should return null
    And constraint doc "isNullable" should return true
    And constraint doc "isRequired" should return false
    And constraint doc "getAllowedValueList" should return an empty array

  Scenario: Simple Range constraint with string type specified (date interval check)
    Given I have the following Constraint:
    """
    use Symfony\Component\Validator\Constraints as ConstraintNS;
    return new ConstraintNS\Range([
      'min' => '2018-11-01',
      'max' => '2018-11-15',
      'payload' => [
        'documentation' => [
          'type' => 'string'
        ]
      ]
    ]);
    """
    When I transform constraint
    Then I should have a constraint doc of class "Yoanm\JsonRpcServerDoc\Domain\Model\Type\StringDoc"
    And constraint doc "getFormat" should return the value "date"
    ## Check others properties
    And constraint doc "getName" should return null
    And constraint doc "getDescription" should return null
    And constraint doc "getDefault" should return null
    And constraint doc "getExample" should return null
    And constraint doc "isNullable" should return true
    And constraint doc "isRequired" should return false
    And constraint doc "getAllowedValueList" should return an empty array
    And constraint doc "getMinLength" should return null
    And constraint doc "getMaxLength" should return null


