Feature: ConstraintToParamsDocTransformer - Fully configured NotNull & IsNull constraints

  Scenario: Fully configured NotNull constraint with string type specified
    Given I have the following Constraint:
    """
    use Symfony\Component\Validator\Constraints as ConstraintNS;
    return new ConstraintNS\NotNull([
      'payload' => [
        'documentation' => [
          'type' => 'string',
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
    Then I should have a constraint doc of class "Yoanm\JsonRpcServerDoc\Domain\Model\Type\StringDoc"
    And constraint doc "getDescription" should return the value "description"
    And constraint doc "getDefault" should return the value "default"
    And constraint doc "getExample" should return the value "example"
    And constraint doc "isRequired" should return true
    And constraint doc "isNullable" should return false
    ## Check others properties
    And constraint doc "getName" should return null
    And constraint doc "getAllowedValueList" should return an empty array
    And constraint doc "getFormat" should return null
    And constraint doc "getMinLength" should return null
    And constraint doc "getMaxLength" should return null
