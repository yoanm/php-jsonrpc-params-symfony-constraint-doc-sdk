Feature: ConstraintToParamsDocTransformer - Fully configured IdenticalTo & NotIdenticalTo constraint

  Scenario: Fully configured string IdenticalTo constraint
    Given I have the following Constraint:
    """
    use Symfony\Component\Validator\Constraints as ConstraintNS;
    return new ConstraintNS\IdenticalTo(
      'expected-text',
      null,
      null,
      null,
      [
        'documentation' => [
          'description' => 'description',
          'required' => true,
          'nullable' => false
        ]
      ]
    );
    """
    When I transform constraint
    Then I should have a constraint doc of class "Yoanm\JsonRpcServerDoc\Domain\Model\Type\StringDoc"
    And constraint doc "getAllowedValueList" should return:
    """
    ["expected-text"]
    """
    And constraint doc "getDefault" should return the value "expected-text"
    And constraint doc "getExample" should return the value "expected-text"
    And constraint doc "getDescription" should return the value "description"
    And constraint doc "isRequired" should return true
    And constraint doc "isNullable" should return false
    ## Check others properties
    And constraint doc "getName" should return null
    And constraint doc "getFormat" should return null
    And constraint doc "getMinLength" should return null
    And constraint doc "getMaxLength" should return null

  Scenario: Fully configured boolean IdenticalTo constraint
    Given I have the following Constraint:
    """
    use Symfony\Component\Validator\Constraints as ConstraintNS;
    return new ConstraintNS\IdenticalTo(
      true,
      null,
      null,
      null,
      [
        'documentation' => [
          'description' => 'description',
          'required' => true,
          'nullable' => false
        ]
      ]
    );
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
    And constraint doc "getDescription" should return the value "description"
    And constraint doc "isRequired" should return true
    ## Check others properties
    And constraint doc "getName" should return null

  Scenario: Fully configured integer IdenticalTo constraint
    Given I have the following Constraint:
    """
    use Symfony\Component\Validator\Constraints as ConstraintNS;
    return new ConstraintNS\IdenticalTo(
      2,
      null,
      null,
      null,
      [
        'documentation' => [
          'description' => 'description',
          'required' => true,
          'nullable' => false
        ]
      ]
    );
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
    And constraint doc "getDescription" should return the value "description"
    And constraint doc "isRequired" should return true
    ## Check others properties
    And constraint doc "getName" should return null
    And constraint doc "getMin" should return null
    And constraint doc "isInclusiveMin" should return true
    And constraint doc "getMax" should return null
    And constraint doc "isInclusiveMax" should return true

  Scenario: Fully configured float IdenticalTo constraint
    Given I have the following Constraint:
    """
    use Symfony\Component\Validator\Constraints as ConstraintNS;
    return new ConstraintNS\IdenticalTo(
      2.3,
      null,
      null,
      null,
      [
        'documentation' => [
          'description' => 'description',
          'required' => true,
          'nullable' => false
        ]
      ]
    );
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
    And constraint doc "getDescription" should return the value "description"
    And constraint doc "isRequired" should return true
    ## Check others properties
    And constraint doc "getName" should return null
    And constraint doc "getMin" should return null
    And constraint doc "isInclusiveMin" should return true
    And constraint doc "getMax" should return null
    And constraint doc "isInclusiveMax" should return true

  Scenario: Fully configured NotIdenticalTo constraint
    Given I have the following Constraint:
    """
    use Symfony\Component\Validator\Constraints as ConstraintNS;
    return new ConstraintNS\NotIdenticalTo(
      'not-expected-value',
      null,
      null,
      null,
      [
        'documentation' => [
          'description' => 'description',
          'default' => 'default',
          'example' => 'example',
          'required' => true,
          'nullable' => false
        ]
      ]
    );
    """
    When I transform constraint
    Then I should have a constraint doc of class "Yoanm\JsonRpcServerDoc\Domain\Model\Type\TypeDoc"
    And constraint doc "getDescription" should return the value "description"
    And constraint doc "getDefault" should return the value "default"
    And constraint doc "getExample" should return the value "example"
    And constraint doc "isNullable" should return false
    And constraint doc "isRequired" should return true
    ## Check others properties
    And constraint doc "getName" should return null
    And constraint doc "getAllowedValueList" should return an empty array
