Feature: ConstraintToParamsDocTransformer - Fully configured Type constraint

  Scenario: Simple boolean Type constraint
    Given I have the following Constraint:
    """
    return new Symfony\Component\Validator\Constraints\Type([
      'type' => 'boolean',
      'payload' => [
        'documentation' => [
          'default' => true,
          'example' => false,
          'description' => 'desc',
          'nullable' => false,
          'required' => true
        ]
      ]
    ]);
    """
    When I transform constraint
    Then I should have a constraint doc of class "Yoanm\JsonRpcServerDoc\Domain\Model\Type\BooleanDoc"
    And constraint doc "getDescription" should return the value "desc"
    And constraint doc "getDefault" should return true
    And constraint doc "getExample" should return false
    And constraint doc "isRequired" should return true
    And constraint doc "isNullable" should return false
    ## Check others properties
    And constraint doc "getName" should return null
    And constraint doc "getAllowedValueList" should return an empty array

  Scenario: Simple string Type constraint
    Given I have the following Constraint:
    """
    return new Symfony\Component\Validator\Constraints\Type([
      'type' => 'string',
      'payload' => [
        'documentation' => [
          'default' => 'default_value',
          'example' => 'example',
          'description' => 'desc',
          'nullable' => false,
          'required' => true
        ]
      ]
    ]);
    """
    When I transform constraint
    Then I should have a constraint doc of class "Yoanm\JsonRpcServerDoc\Domain\Model\Type\StringDoc"
    And constraint doc "getDescription" should return the value "desc"
    And constraint doc "getDefault" should return the value "default_value"
    And constraint doc "getExample" should return the value "example"
    And constraint doc "isRequired" should return true
    And constraint doc "isNullable" should return false
    ## Check others properties
    And constraint doc "getName" should return null
    And constraint doc "getFormat" should return null
    And constraint doc "getAllowedValueList" should return an empty array
    And constraint doc "getMinLength" should return null
    And constraint doc "getMaxLength" should return null

  Scenario: Simple integer Type constraint
    Given I have the following Constraint:
    """
    return new Symfony\Component\Validator\Constraints\Type([
      'type' => 'integer',
      'payload' => [
        'documentation' => [
          'default' => 3,
          'example' => 4,
          'description' => 'desc',
          'nullable' => false,
          'required' => true
        ]
      ]
    ]);
    """
    When I transform constraint
    Then I should have a constraint doc of class "Yoanm\JsonRpcServerDoc\Domain\Model\Type\IntegerDoc"
    And constraint doc "getDescription" should return the value "desc"
    And constraint doc "getDefault" should return the number 3
    And constraint doc "getExample" should return the number 4
    And constraint doc "isRequired" should return true
    And constraint doc "isNullable" should return false
    ## Check others properties
    And constraint doc "getName" should return null
    And constraint doc "getAllowedValueList" should return an empty array
    And constraint doc "getMin" should return null
    And constraint doc "isInclusiveMin" should return true
    And constraint doc "getMax" should return null
    And constraint doc "isInclusiveMax" should return true

  Scenario: Simple float Type constraint
    Given I have the following Constraint:
    """
    return new Symfony\Component\Validator\Constraints\Type([
      'type' => 'float',
      'payload' => [
        'documentation' => [
          'default' => 3.2,
          'example' => 4.5,
          'description' => 'desc',
          'nullable' => false,
          'required' => true
        ]
      ]
    ]);
    """
    When I transform constraint
    Then I should have a constraint doc of class "Yoanm\JsonRpcServerDoc\Domain\Model\Type\FloatDoc"
    And constraint doc "getDescription" should return the value "desc"
    And constraint doc "getDefault" should return the number 3.2
    And constraint doc "getExample" should return the number 4.5
    And constraint doc "isRequired" should return true
    And constraint doc "isNullable" should return false
    ## Check others properties
    And constraint doc "getName" should return null
    And constraint doc "getAllowedValueList" should return an empty array
    And constraint doc "getMin" should return null
    And constraint doc "isInclusiveMin" should return true
    And constraint doc "getMax" should return null
    And constraint doc "isInclusiveMax" should return true

  Scenario: Simple long Type constraint
    Given I have the following Constraint:
    """
    return new Symfony\Component\Validator\Constraints\Type([
      'type' => 'long',
      'payload' => [
        'documentation' => [
          'default' => 3.2,
          'example' => 4.5,
          'description' => 'desc',
          'nullable' => false,
          'required' => true
        ]
      ]
    ]);
    """
    When I transform constraint
    Then I should have a constraint doc of class "Yoanm\JsonRpcServerDoc\Domain\Model\Type\FloatDoc"
    And constraint doc "getDescription" should return the value "desc"
    And constraint doc "getDefault" should return the number 3.2
    And constraint doc "getExample" should return the number 4.5
    And constraint doc "isRequired" should return true
    And constraint doc "isNullable" should return false
    ## Check others properties
    And constraint doc "getName" should return null
    And constraint doc "getAllowedValueList" should return an empty array
    And constraint doc "getMin" should return null
    And constraint doc "isInclusiveMin" should return true
    And constraint doc "getMax" should return null
    And constraint doc "isInclusiveMax" should return true

  Scenario: Simple double Type constraint
    Given I have the following Constraint:
    """
    return new Symfony\Component\Validator\Constraints\Type([
      'type' => 'double',
      'payload' => [
        'documentation' => [
          'default' => 3.2,
          'example' => 4.5,
          'description' => 'desc',
          'nullable' => false,
          'required' => true
        ]
      ]
    ]);
    """
    When I transform constraint
    Then I should have a constraint doc of class "Yoanm\JsonRpcServerDoc\Domain\Model\Type\FloatDoc"
    And constraint doc "getDescription" should return the value "desc"
    And constraint doc "getDefault" should return the number 3.2
    And constraint doc "getExample" should return the number 4.5
    And constraint doc "isRequired" should return true
    And constraint doc "isNullable" should return false
    ## Check others properties
    And constraint doc "getName" should return null
    And constraint doc "getAllowedValueList" should return an empty array
    And constraint doc "getMin" should return null
    And constraint doc "isInclusiveMin" should return true
    And constraint doc "getMax" should return null
    And constraint doc "isInclusiveMax" should return true

  Scenario: Simple real Type constraint
    Given I have the following Constraint:
    """
    return new Symfony\Component\Validator\Constraints\Type([
      'type' => 'real',
      'payload' => [
        'documentation' => [
          'default' => 3.2,
          'example' => 4.5,
          'description' => 'desc',
          'nullable' => false,
          'required' => true
        ]
      ]
    ]);
    """
    When I transform constraint
    Then I should have a constraint doc of class "Yoanm\JsonRpcServerDoc\Domain\Model\Type\FloatDoc"
    And constraint doc "getDescription" should return the value "desc"
    And constraint doc "getDefault" should return the number 3.2
    And constraint doc "getExample" should return the number 4.5
    And constraint doc "isRequired" should return true
    And constraint doc "isNullable" should return false
    ## Check others properties
    And constraint doc "getName" should return null
    And constraint doc "getAllowedValueList" should return an empty array
    And constraint doc "getMin" should return null
    And constraint doc "isInclusiveMin" should return true
    And constraint doc "getMax" should return null
    And constraint doc "isInclusiveMax" should return true

  Scenario: Simple numeric Type constraint
    Given I have the following Constraint:
    """
    return new Symfony\Component\Validator\Constraints\Type([
      'type' => 'numeric',
      'payload' => [
        'documentation' => [
          'default' => 3.2,
          'example' => 4.5,
          'description' => 'desc',
          'nullable' => false,
          'required' => true
        ]
      ]
    ]);
    """
    When I transform constraint
    Then I should have a constraint doc of class "Yoanm\JsonRpcServerDoc\Domain\Model\Type\NumberDoc"
    And constraint doc "getDescription" should return the value "desc"
    And constraint doc "getDefault" should return the number 3.2
    And constraint doc "getExample" should return the number 4.5
    And constraint doc "isRequired" should return true
    And constraint doc "isNullable" should return false
    ## Check others properties
    And constraint doc "getName" should return null
    And constraint doc "getAllowedValueList" should return an empty array
    And constraint doc "getMin" should return null
    And constraint doc "isInclusiveMin" should return true
    And constraint doc "getMax" should return null
    And constraint doc "isInclusiveMax" should return true


  Scenario: Simple scalar (wide type) Type constraint
    Given I have the following Constraint:
    """
    return new Symfony\Component\Validator\Constraints\Type([
      'type' => 'scalar',
      'payload' => [
        'documentation' => [
          'default' => 'default',
          'example' => 'example',
          'description' => 'desc',
          'nullable' => false,
          'required' => true
        ]
      ]
    ]);
    """
    When I transform constraint
    Then I should have a constraint doc of class "Yoanm\JsonRpcServerDoc\Domain\Model\Type\ScalarDoc"
    And constraint doc "getDescription" should return the value "desc"
    And constraint doc "getDefault" should return the value "default"
    And constraint doc "getExample" should return the value "example"
    And constraint doc "isRequired" should return true
    And constraint doc "isNullable" should return false
    ## Check others properties
    And constraint doc "getName" should return null
    And constraint doc "getAllowedValueList" should return an empty array
