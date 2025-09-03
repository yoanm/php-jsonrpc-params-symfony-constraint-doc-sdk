Feature: ConstraintToParamsDocTransformer - EqualTo & NotEqualTo constraint

  Scenario: Simple EqualTo constraint
    Given I have the following Constraint:
    """
    use Symfony\Component\Validator\Constraints as ConstraintNS;
    return new ConstraintNS\EqualTo('expected-text');
    """
    When I transform constraint
    Then I should have a constraint doc of class "Yoanm\JsonRpcServerDoc\Domain\Model\Type\TypeDoc"
    And constraint doc "getAllowedValueList" should return:
    """
    ["expected-text"]
    """
    ## Check others properties
    And constraint doc "getName" should return null
    And constraint doc "getDescription" should return null
    And constraint doc "getDefault" should return null
    And constraint doc "getExample" should return null
    And constraint doc "isNullable" should return true
    And constraint doc "isRequired" should return false

  Scenario: Simple NotEqualTo constraint
    Given I have the following Constraint:
    """
    use Symfony\Component\Validator\Constraints as ConstraintNS;
    return new ConstraintNS\NotEqualTo('expected-text');
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


  Scenario: Fully configured EqualTo constraint
    Given I have the following Constraint:
    """
    use Symfony\Component\Validator\Constraints as ConstraintNS;
    return new ConstraintNS\EqualTo(
      'expected-text',
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
    And constraint doc "getAllowedValueList" should return:
    """
    ["expected-text"]
    """
    And constraint doc "getDescription" should return the value "description"
    And constraint doc "getDefault" should return the value "default"
    And constraint doc "getExample" should return the value "example"
    And constraint doc "isNullable" should return false
    And constraint doc "isRequired" should return true
    ## Check others properties
    And constraint doc "getName" should return null

  Scenario: Fully configured NotEqualTo constraint
    Given I have the following Constraint:
    """
    use Symfony\Component\Validator\Constraints as ConstraintNS;
    return new ConstraintNS\NotEqualTo(
      'expected-text',
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
