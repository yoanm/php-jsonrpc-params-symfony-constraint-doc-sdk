Feature: ConstraintToParamsDocTransformer - All constraint

  Scenario: Simple All constraint
    Given I have the following Constraint:
    """
    use Symfony\Component\Validator\Constraints as ConstraintNS;
    return new ConstraintNS\All([new ConstraintNS\Type('string')]);
    """
    When I transform constraint
    Then I should have a constraint doc of class "Yoanm\JsonRpcServerDoc\Domain\Model\Type\ArrayDoc"
    And constraint doc item validation should be of type "Yoanm\JsonRpcServerDoc\Domain\Model\Type\StringDoc"
    ## Check others properties
    And constraint doc "getName" should return null
    And constraint doc "getDescription" should return null
    And constraint doc "getDefault" should return null
    And constraint doc "getExample" should return null
    And constraint doc "isNullable" should return true
    And constraint doc "isRequired" should return false
    And constraint doc "getAllowedValueList" should return an empty array
    And constraint doc "getSiblingList" should return an empty array
    And constraint doc "getMinItem" should return null
    And constraint doc "getMaxItem" should return null
    And constraint doc "isAllowExtraSibling" should return false
    And constraint doc "isAllowMissingSibling" should return false

  Scenario: Fully configured All constraint
    Given I have the following Constraint:
    """
    use Symfony\Component\Validator\Constraints as ConstraintNS;
    return new ConstraintNS\All([
      'constraints' => [new ConstraintNS\Type('string')],
      'payload' => [
        'documentation' => [
          'description' => 'description',
          'default' => 'default',
          'example' => 'example',
          'required' => true,
          'nullable' => false
        ]
      ]
    ]);
    """
    When I transform constraint
    Then I should have a constraint doc of class "Yoanm\JsonRpcServerDoc\Domain\Model\Type\ArrayDoc"
    And constraint doc item validation should be of type "Yoanm\JsonRpcServerDoc\Domain\Model\Type\StringDoc"
    And constraint doc "getDescription" should return the value "description"
    And constraint doc "getDefault" should return the value "default"
    And constraint doc "getExample" should return the value "example"
    And constraint doc "isNullable" should return false
    And constraint doc "isRequired" should return true
    ## Check others properties
    And constraint doc "getName" should return null
    And constraint doc "getAllowedValueList" should return an empty array
    And constraint doc "getSiblingList" should return an empty array
    And constraint doc "getMinItem" should return null
    And constraint doc "getMaxItem" should return null
    And constraint doc "isAllowExtraSibling" should return false
    And constraint doc "isAllowMissingSibling" should return false
