Feature: ConstraintToParamsDocTransformer - DivisibleBy constraint

  Scenario: Simple DivisibleBy constraint
    Given I have the following Constraint:
    """
    use Symfony\Component\Validator\Constraints as ConstraintNS;
    return new ConstraintNS\DivisibleBy(0.25);
    """
    When I transform constraint
    Then I should have a constraint doc of class "Yoanm\JsonRpcServerDoc\Domain\Model\Type\NumberDoc"
    ## Check others properties
    And constraint doc "getName" should return null
    And constraint doc "getDescription" should return null
    And constraint doc "getDefault" should return null
    And constraint doc "getExample" should return null
    And constraint doc "isNullable" should return true
    And constraint doc "isRequired" should return false

  Scenario: Fully configured DivisibleBy constraint
    Given I have the following Constraint:
    """
    use Symfony\Component\Validator\Constraints as ConstraintNS;
    return new ConstraintNS\DivisibleBy([
      'value' => 5,
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
    Then I should have a constraint doc of class "Yoanm\JsonRpcServerDoc\Domain\Model\Type\NumberDoc"
    And constraint doc "getDescription" should return the value "description"
    And constraint doc "getDefault" should return the value "default"
    And constraint doc "getExample" should return the value "example"
    And constraint doc "isNullable" should return false
    And constraint doc "isRequired" should return true
    ## Check others properties
    And constraint doc "getName" should return null
