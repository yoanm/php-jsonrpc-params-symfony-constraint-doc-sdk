Feature: ConstraintToParamsDocTransformer - Choice constraint

  Scenario: Simple Choice constraint
    Given I have the following Constraint:
    """
    use Symfony\Component\Validator\Constraints as ConstraintNS;
    return new ConstraintNS\Choice([1, 3, 5]);
    """
    When I transform constraint
    Then I should have a constraint doc of class "Yoanm\JsonRpcServerDoc\Domain\Model\Type\TypeDoc"
    And constraint doc "getAllowedValueList" should return:
    """
    [1, 3, 5]
    """
    ## Check others properties
    And constraint doc "getName" should return null
    And constraint doc "getDescription" should return null
    And constraint doc "getDefault" should return null
    And constraint doc "getExample" should return null
    And constraint doc "isNullable" should return true
    And constraint doc "isRequired" should return false

  Scenario: Simple Multiple Choice constraint
    Given I have the following Constraint:
    """
    use Symfony\Component\Validator\Constraints as ConstraintNS;
    return new ConstraintNS\Choice([
      'choices' => [1, 3, 5],
      'multiple' => true
    ]);
    """
    When I transform constraint
    Then I should have a constraint doc of class "Yoanm\JsonRpcServerDoc\Domain\Model\Type\ArrayDoc"
    And constraint doc "getAllowedValueList" should return:
    """
    [1, 3, 5]
    """
    ## Check others properties
    And constraint doc "getName" should return null
    And constraint doc "getDescription" should return null
    And constraint doc "getDefault" should return null
    And constraint doc "getExample" should return null
    And constraint doc "isNullable" should return true
    And constraint doc "isRequired" should return false
    And constraint doc "getMinItem" should return null
    And constraint doc "getMaxItem" should return null
    And constraint doc "isAllowExtraSibling" should return false
    And constraint doc "isAllowMissingSibling" should return false
    And constraint doc "getItemValidation" should return null


  Scenario: Fully configured Choice constraint
    Given I have the following Constraint:
    """
    use Symfony\Component\Validator\Constraints as ConstraintNS;
    return new ConstraintNS\Choice([
      'choices' => [1, 3, 5],
      'strict' => true,
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
    Then I should have a constraint doc of class "Yoanm\JsonRpcServerDoc\Domain\Model\Type\TypeDoc"
    And constraint doc "getAllowedValueList" should return:
    """
    [1, 3, 5]
    """
    And constraint doc "getDescription" should return the value "description"
    And constraint doc "getDefault" should return the value "default"
    And constraint doc "getExample" should return the value "example"
    And constraint doc "isNullable" should return false
    And constraint doc "isRequired" should return true
    ## Check others properties
    And constraint doc "getName" should return null

  Scenario: Fully configured Multiple Choice constraint
    Given I have the following Constraint:
    """
    use Symfony\Component\Validator\Constraints as ConstraintNS;
    return new ConstraintNS\Choice([
      'choices' => [1, 3, 4, 5],
      'multiple' => true,
      'strict' => true,
      'min' => 2,
      'max' => 3,
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
    And constraint doc "getAllowedValueList" should return:
    """
    [1, 3, 4, 5]
    """
    And constraint doc "getMinItem" should return the number 2
    And constraint doc "getMaxItem" should return the number 3
    And constraint doc "getDescription" should return the value "description"
    And constraint doc "getDefault" should return the value "default"
    And constraint doc "getExample" should return the value "example"
    And constraint doc "isNullable" should return false
    And constraint doc "isRequired" should return true
    ## Check others properties
    And constraint doc "getName" should return null
    And constraint doc "isAllowExtraSibling" should return false
    And constraint doc "isAllowMissingSibling" should return false
    And constraint doc "getItemValidation" should return null

