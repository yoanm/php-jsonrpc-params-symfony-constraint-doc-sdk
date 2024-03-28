Feature: ConstraintToParamsDocTransformer - Callback constraint

  Scenario: Simple Callback constraint
    Given I have the following Constraint:
    """
    use Symfony\Component\Validator\Constraints as ConstraintNS;
    return new ConstraintNS\Callback(function () {
      return new ConstraintNS\Required(
        new ConstraintNS\Type('string')
      );
    });
    """
    When I transform constraint
    Then I should have a constraint doc of class "Yoanm\JsonRpcServerDoc\Domain\Model\Type\StringDoc"
    And constraint doc "isRequired" should return true
    ## Check others properties
    And constraint doc "getName" should return null
    And constraint doc "getDescription" should return null
    And constraint doc "getDefault" should return null
    And constraint doc "getExample" should return null
    And constraint doc "isNullable" should return true
    And constraint doc "getAllowedValueList" should return an empty array
    And constraint doc "getFormat" should return null
    And constraint doc "getMinLength" should return null
    And constraint doc "getMaxLength" should return null

  Scenario: Fully configured Callback constraint
    Given I have the following Constraint:
    """
    use Symfony\Component\Validator\Constraints as ConstraintNS;
    return new ConstraintNS\Callback([
      'callback' => function () {
        return new ConstraintNS\Required(
          new ConstraintNS\Type('string')
        );
      },
      'payload' => [
        'documentation' => [
          'description' => 'description',
          'default' => 'default',
          'example' => 'example',
          'nullable' => false
        ]
      ]
    ]);
    """
    When I transform constraint
    Then I should have a constraint doc of class "Yoanm\JsonRpcServerDoc\Domain\Model\Type\StringDoc"
    And constraint doc "isRequired" should return true
    And constraint doc "getDescription" should return the value "description"
    And constraint doc "getDefault" should return the value "default"
    And constraint doc "getExample" should return the value "example"
    And constraint doc "isNullable" should return false
    ## Check others properties
    And constraint doc "getName" should return null
    And constraint doc "getAllowedValueList" should return an empty array
    And constraint doc "getFormat" should return null
    And constraint doc "getMinLength" should return null
    And constraint doc "getMaxLength" should return null
