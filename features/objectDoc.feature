Feature: ConstraintToParamsDocTransformer - Collection constraint

  Scenario: Simple Collection constraint
    Given I have the following Constraint:
    """
    use Symfony\Component\Validator\Constraints as ConstraintNS;
    return new ConstraintNS\Collection([
      'fields' => ['a' => new ConstraintNS\Optional(new ConstraintNS\Type('string'))]
    ]);
    """
    When I transform constraint
    Then I should have a constraint doc of class "Yoanm\JsonRpcServerDoc\Domain\Model\Type\ObjectDoc"
    And constraint doc should have a sibling "a" of class "Yoanm\JsonRpcServerDoc\Domain\Model\Type\StringDoc"
    And constraint doc sibling "a" "isRequired" should return false
    ## Check others properties
    And constraint doc "getName" should return null
    And constraint doc "getDescription" should return null
    And constraint doc "getDefault" should return null
    And constraint doc "getExample" should return null
    And constraint doc "isRequired" should return false
    And constraint doc "isNullable" should return true
    And constraint doc "getAllowedValueList" should return an empty array
    And constraint doc "getMinItem" should return null
    And constraint doc "getMaxItem" should return null
    And constraint doc "isAllowExtraSibling" should return false
    And constraint doc "isAllowMissingSibling" should return false

  Scenario: Fully configured Collection constraint
    Given I have the following Constraint:
    """
    use Symfony\Component\Validator\Constraints as ConstraintNS;
    return [
      new ConstraintNS\Collection([
        'fields' => [
          'a' => new ConstraintNS\Optional(new ConstraintNS\Type('string')),
          'b' => new ConstraintNS\Type('string'),
        ],
        'allowExtraFields' => true,
        'allowMissingFields' => true,
        'payload' => [
            'documentation' => [
              'description' => 'description',
              'default' => 'default',
              'example' => 'example',
              'required' => true,
              'nullable' => false
            ]
          ]
      ]),
      new ConstraintNS\Count(['min' => 3, 'max' => 5])
    ];
    """
    When I transform constraint
    Then I should have a constraint doc of class "Yoanm\JsonRpcServerDoc\Domain\Model\Type\ObjectDoc"
    And constraint doc should have a sibling "a" of class "Yoanm\JsonRpcServerDoc\Domain\Model\Type\StringDoc"
    And constraint doc should have 2 siblings
    And constraint doc sibling "a" "isRequired" should return false
    And constraint doc should have a sibling "b" of class "Yoanm\JsonRpcServerDoc\Domain\Model\Type\StringDoc"
    And constraint doc sibling "b" "isRequired" should return true
    And constraint doc "getMinItem" should return the number 3
    And constraint doc "getMaxItem" should return the number 5
    And constraint doc "isAllowExtraSibling" should return true
    And constraint doc "isAllowMissingSibling" should return true
    And constraint doc "getDescription" should return the value "description"
    And constraint doc "getDefault" should return the value "default"
    And constraint doc "getExample" should return the value "example"
    And constraint doc "isNullable" should return false
    And constraint doc "isRequired" should return true
    ## Check others properties
    And constraint doc "getName" should return null
    And constraint doc "getAllowedValueList" should return an empty array
