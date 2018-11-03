Feature: ConstraintToParamsDocTransformer - Uuid constraint

  Scenario: Simple Uuid constraint
    Given I have the following Constraint:
    """
    use Symfony\Component\Validator\Constraints as ConstraintNS;
    return new ConstraintNS\Uuid();
    """
    When I transform constraint
    Then I should have a constraint doc of class "Yoanm\JsonRpcServerDoc\Domain\Model\Type\StringDoc"
    And constraint doc "getFormat" should return the value "uuid"
    And constraint doc "getDescription" should return the value "Uuid (v1, v2, v3, v4, v5)"
    ## Check others properties
    And constraint doc "getName" should return null
    And constraint doc "getDefault" should return null
    And constraint doc "getExample" should return null
    And constraint doc "isNullable" should return true
    And constraint doc "isRequired" should return false
    And constraint doc "getAllowedValueList" should return an empty array
    And constraint doc "getMinLength" should return null
    And constraint doc "getMaxLength" should return null

  Scenario: Fully configured Uuid constraint
    Given I have the following Constraint:
    """
    use Symfony\Component\Validator\Constraints as ConstraintNS;
    return new ConstraintNS\Uuid([
      'versions' => [3, 4, 5],
      'payload' => [
        'documentation' => [
          'type' => 'string',
          'default' => 'default',
          'example' => 'example',
          'required' => true,
          'nullable' => false
        ]
      ]
    ]);
    """
    When I transform constraint
    Then I should have a constraint doc of class "Yoanm\JsonRpcServerDoc\Domain\Model\Type\StringDoc"
    And constraint doc "getFormat" should return the value "uuid"
    And constraint doc "getDescription" should return the value "Uuid (v3, v4, v5)"
    And constraint doc "getDefault" should return the value "default"
    And constraint doc "getExample" should return the value "example"
    And constraint doc "isNullable" should return false
    And constraint doc "isRequired" should return true
    ## Check others properties
    And constraint doc "getName" should return null
    And constraint doc "getAllowedValueList" should return an empty array
    And constraint doc "getMinLength" should return null
    And constraint doc "getMaxLength" should return null
