Feature: ConstraintToParamsDocTransformer - Type constraint

  Scenario: Simple boolean Type constraint
    Given I have the following Constraint:
    """
    return new Symfony\Component\Validator\Constraints\Type('boolean');
    """
    When I transform constraint
    Then I should have a constraint doc of class "Yoanm\JsonRpcServerDoc\Domain\Model\Type\BooleanDoc"
    ## Check others properties
    And constraint doc "getName" should return null
    And constraint doc "getDescription" should return null
    And constraint doc "getDefault" should return null
    And constraint doc "getExample" should return null
    And constraint doc "isRequired" should return false
    And constraint doc "isNullable" should return true
    And constraint doc "getAllowedValueList" should return an empty array

  Scenario: Simple string Type constraint
    Given I have the following Constraint:
    """
    return new Symfony\Component\Validator\Constraints\Type('string');
    """
    When I transform constraint
    Then I should have a constraint doc of class "Yoanm\JsonRpcServerDoc\Domain\Model\Type\StringDoc"
    ## Check others properties
    And constraint doc "getName" should return null
    And constraint doc "getDescription" should return null
    And constraint doc "getDefault" should return null
    And constraint doc "getExample" should return null
    And constraint doc "isRequired" should return false
    And constraint doc "isNullable" should return true
    And constraint doc "getAllowedValueList" should return an empty array
    And constraint doc "getFormat" should return null
    And constraint doc "getMinLength" should return null
    And constraint doc "getMaxLength" should return null

  Scenario: Simple integer Type constraint
    Given I have the following Constraint:
    """
    return new Symfony\Component\Validator\Constraints\Type('integer');
    """
    When I transform constraint
    Then I should have a constraint doc of class "Yoanm\JsonRpcServerDoc\Domain\Model\Type\IntegerDoc"
    ## Check others properties
    And constraint doc "getName" should return null
    And constraint doc "getDescription" should return null
    And constraint doc "getDefault" should return null
    And constraint doc "getExample" should return null
    And constraint doc "isRequired" should return false
    And constraint doc "isNullable" should return true
    And constraint doc "getAllowedValueList" should return an empty array
    And constraint doc "getMin" should return null
    And constraint doc "isInclusiveMin" should return true
    And constraint doc "getMax" should return null
    And constraint doc "isInclusiveMax" should return true

  Scenario: Simple float Type constraint
    Given I have the following Constraint:
    """
    return new Symfony\Component\Validator\Constraints\Type('float');
    """
    When I transform constraint
    Then I should have a constraint doc of class "Yoanm\JsonRpcServerDoc\Domain\Model\Type\FloatDoc"
    ## Check others properties
    And constraint doc "getName" should return null
    And constraint doc "getDescription" should return null
    And constraint doc "getDefault" should return null
    And constraint doc "getExample" should return null
    And constraint doc "isRequired" should return false
    And constraint doc "isNullable" should return true
    And constraint doc "getAllowedValueList" should return an empty array
    And constraint doc "getMin" should return null
    And constraint doc "isInclusiveMin" should return true
    And constraint doc "getMax" should return null
    And constraint doc "isInclusiveMax" should return true

  Scenario: Simple long Type constraint
    Given I have the following Constraint:
    """
    return new Symfony\Component\Validator\Constraints\Type('long');
    """
    When I transform constraint
    Then I should have a constraint doc of class "Yoanm\JsonRpcServerDoc\Domain\Model\Type\FloatDoc"
    ## Check others properties
    And constraint doc "getName" should return null
    And constraint doc "getDescription" should return null
    And constraint doc "getDefault" should return null
    And constraint doc "getExample" should return null
    And constraint doc "isRequired" should return false
    And constraint doc "isNullable" should return true
    And constraint doc "getAllowedValueList" should return an empty array
    And constraint doc "getMin" should return null
    And constraint doc "isInclusiveMin" should return true
    And constraint doc "getMax" should return null
    And constraint doc "isInclusiveMax" should return true

  Scenario: Simple double Type constraint
    Given I have the following Constraint:
    """
    return new Symfony\Component\Validator\Constraints\Type('double');
    """
    When I transform constraint
    Then I should have a constraint doc of class "Yoanm\JsonRpcServerDoc\Domain\Model\Type\FloatDoc"
    ## Check others properties
    And constraint doc "getName" should return null
    And constraint doc "getDescription" should return null
    And constraint doc "getDefault" should return null
    And constraint doc "getExample" should return null
    And constraint doc "isRequired" should return false
    And constraint doc "isNullable" should return true
    And constraint doc "getAllowedValueList" should return an empty array
    And constraint doc "getMin" should return null
    And constraint doc "isInclusiveMin" should return true
    And constraint doc "getMax" should return null
    And constraint doc "isInclusiveMax" should return true

  Scenario: Simple real Type constraint
    Given I have the following Constraint:
    """
    return new Symfony\Component\Validator\Constraints\Type('real');
    """
    When I transform constraint
    Then I should have a constraint doc of class "Yoanm\JsonRpcServerDoc\Domain\Model\Type\FloatDoc"
    ## Check others properties
    And constraint doc "getName" should return null
    And constraint doc "getDescription" should return null
    And constraint doc "getDefault" should return null
    And constraint doc "getExample" should return null
    And constraint doc "isRequired" should return false
    And constraint doc "isNullable" should return true
    And constraint doc "getAllowedValueList" should return an empty array
    And constraint doc "getMin" should return null
    And constraint doc "isInclusiveMin" should return true
    And constraint doc "getMax" should return null
    And constraint doc "isInclusiveMax" should return true

  Scenario: Simple numeric Type constraint
    Given I have the following Constraint:
    """
    return new Symfony\Component\Validator\Constraints\Type('numeric');
    """
    When I transform constraint
    Then I should have a constraint doc of class "Yoanm\JsonRpcServerDoc\Domain\Model\Type\NumberDoc"
    ## Check others properties
    And constraint doc "getName" should return null
    And constraint doc "getDescription" should return null
    And constraint doc "getDefault" should return null
    And constraint doc "getExample" should return null
    And constraint doc "isRequired" should return false
    And constraint doc "isNullable" should return true
    And constraint doc "getAllowedValueList" should return an empty array
    And constraint doc "getMin" should return null
    And constraint doc "isInclusiveMin" should return true
    And constraint doc "getMax" should return null
    And constraint doc "isInclusiveMax" should return true

  Scenario: Simple scalar (wide type) Type constraint
    Given I have the following Constraint:
    """
    return new Symfony\Component\Validator\Constraints\Type('scalar');
    """
    When I transform constraint
    Then I should have a constraint doc of class "Yoanm\JsonRpcServerDoc\Domain\Model\Type\ScalarDoc"
    ## Check others properties
    And constraint doc "getName" should return null
    And constraint doc "getDescription" should return null
    And constraint doc "getDefault" should return null
    And constraint doc "getExample" should return null
    And constraint doc "isRequired" should return false
    And constraint doc "isNullable" should return true
    And constraint doc "getAllowedValueList" should return an empty array
