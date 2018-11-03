Feature: ConstraintToParamsDocTransformer - Collection constraint

  Scenario: Simple Collection constraint
    Given I have the following Constraint:
    """
    use Symfony\Component\Validator\Constraints as ConstraintNS;
    return new ConstraintNS\Collection([
      'a' => new ConstraintNS\Type('string'),
      'b' => new ConstraintNS\Type('integer'),
      'c' => new ConstraintNS\Type('bool')
    ]);
    """
    When I transform constraint
    Then I should have a constraint doc of class "Yoanm\JsonRpcServerDoc\Domain\Model\Type\ObjectDoc"
    ## Check others properties
    And constraint doc "getName" should return null
    And constraint doc "getDescription" should return null
    And constraint doc "getDefault" should return null
    And constraint doc "getExample" should return null
    And constraint doc "isNullable" should return true
    And constraint doc "isRequired" should return false
    And constraint doc "getAllowedValueList" should return an empty array
    And constraint doc "getMinItem" should return null
    And constraint doc "getMaxItem" should return null
    And constraint doc "isAllowExtraSibling" should return false
    And constraint doc "isAllowMissingSibling" should return false
    ## Check siblings
    And constraint doc should have 3 siblings
    And constraint doc should have a sibling "a" of class "Yoanm\JsonRpcServerDoc\Domain\Model\Type\StringDoc"
    And constraint doc sibling "a" "getName" should return the value "a"
    And constraint doc sibling "a" "isRequired" should return true
    ## Check others "a" sibling properties
    And constraint doc sibling "a" "getDescription" should return null
    And constraint doc sibling "a" "getDefault" should return null
    And constraint doc sibling "a" "getExample" should return null
    And constraint doc sibling "a" "isNullable" should return true
    And constraint doc sibling "a" "getAllowedValueList" should return an empty array
    And constraint doc sibling "a" "getFormat" should return null
    And constraint doc sibling "a" "getMinLength" should return null
    And constraint doc sibling "a" "getMaxLength" should return null
    And constraint doc should have a sibling "b" of class "Yoanm\JsonRpcServerDoc\Domain\Model\Type\IntegerDoc"
    And constraint doc sibling "b" "getName" should return the value "b"
    And constraint doc sibling "b" "isRequired" should return true
    ## Check others "b" sibling properties
    And constraint doc sibling "b" "getDescription" should return null
    And constraint doc sibling "b" "getDefault" should return null
    And constraint doc sibling "b" "getExample" should return null
    And constraint doc sibling "b" "isNullable" should return true
    And constraint doc sibling "b" "getAllowedValueList" should return an empty array
    And constraint doc sibling "b" "getMin" should return null
    And constraint doc sibling "b" "isInclusiveMin" should return true
    And constraint doc sibling "b" "getMax" should return null
    And constraint doc sibling "b" "isInclusiveMax" should return true
    And constraint doc should have a sibling "c" of class "Yoanm\JsonRpcServerDoc\Domain\Model\Type\BooleanDoc"
    And constraint doc sibling "c" "getName" should return the value "c"
    And constraint doc sibling "c" "isRequired" should return true
    ## Check others "c" sibling properties
    And constraint doc sibling "c" "getDescription" should return null
    And constraint doc sibling "c" "getDefault" should return null
    And constraint doc sibling "c" "getExample" should return null
    And constraint doc sibling "c" "isNullable" should return true
    And constraint doc sibling "c" "getAllowedValueList" should return an empty array

  Scenario: Fully configured Collection constraint
    Given I have the following Constraint:
    """
    use Symfony\Component\Validator\Constraints as ConstraintNS;
    return new ConstraintNS\Collection([
      'fields' => [
        'a' => new ConstraintNS\Type('string'),
        'b' => new ConstraintNS\Type('integer'),
        'c' => new ConstraintNS\Type('bool')
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
    ]);
    """
    When I transform constraint
    Then I should have a constraint doc of class "Yoanm\JsonRpcServerDoc\Domain\Model\Type\ObjectDoc"
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
    And constraint doc "getMinItem" should return null
    And constraint doc "getMaxItem" should return null
    ## Check siblings
    And constraint doc should have 3 siblings
    And constraint doc should have a sibling "a" of class "Yoanm\JsonRpcServerDoc\Domain\Model\Type\StringDoc"
    And constraint doc sibling "a" "getName" should return the value "a"
    And constraint doc sibling "a" "isRequired" should return true
    ## Check others "a" sibling properties
    And constraint doc sibling "a" "getDescription" should return null
    And constraint doc sibling "a" "getDefault" should return null
    And constraint doc sibling "a" "getExample" should return null
    And constraint doc sibling "a" "isNullable" should return true
    And constraint doc sibling "a" "getAllowedValueList" should return an empty array
    And constraint doc sibling "a" "getFormat" should return null
    And constraint doc sibling "a" "getMinLength" should return null
    And constraint doc sibling "a" "getMaxLength" should return null
    And constraint doc should have a sibling "b" of class "Yoanm\JsonRpcServerDoc\Domain\Model\Type\IntegerDoc"
    And constraint doc sibling "b" "getName" should return the value "b"
    And constraint doc sibling "b" "isRequired" should return true
    ## Check others "b" sibling properties
    And constraint doc sibling "b" "getDescription" should return null
    And constraint doc sibling "b" "getDefault" should return null
    And constraint doc sibling "b" "getExample" should return null
    And constraint doc sibling "b" "isNullable" should return true
    And constraint doc sibling "b" "getAllowedValueList" should return an empty array
    And constraint doc sibling "b" "getMin" should return null
    And constraint doc sibling "b" "isInclusiveMin" should return true
    And constraint doc sibling "b" "getMax" should return null
    And constraint doc sibling "b" "isInclusiveMax" should return true
    And constraint doc should have a sibling "c" of class "Yoanm\JsonRpcServerDoc\Domain\Model\Type\BooleanDoc"
    And constraint doc sibling "c" "getName" should return the value "c"
    And constraint doc sibling "c" "isRequired" should return true
    ## Check others "c" sibling properties
    And constraint doc sibling "c" "getDescription" should return null
    And constraint doc sibling "c" "getDefault" should return null
    And constraint doc sibling "c" "getExample" should return null
    And constraint doc sibling "c" "isNullable" should return true
    And constraint doc sibling "c" "getAllowedValueList" should return an empty array
