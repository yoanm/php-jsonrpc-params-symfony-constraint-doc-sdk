# JSON-RPC params symfony constraint doc

[![License](https://img.shields.io/github/license/yoanm/php-jsonrpc-params-symfony-constraint-doc-sdk.svg)](https://github.com/yoanm/php-jsonrpc-params-symfony-constraint-doc-sdk)
[![Code size](https://img.shields.io/github/languages/code-size/yoanm/php-jsonrpc-params-symfony-constraint-doc-sdk.svg)](https://github.com/yoanm/php-jsonrpc-params-symfony-constraint-doc-sdk)
[![Dependabot Status](https://api.dependabot.com/badges/status?host=github\&repo=yoanm/php-jsonrpc-params-symfony-constraint-doc-sdk)](https://dependabot.com)

[![Scrutinizer Build Status](https://img.shields.io/scrutinizer/build/g/yoanm/php-jsonrpc-params-symfony-constraint-doc-sdk.svg?label=Scrutinizer\&logo=scrutinizer)](https://scrutinizer-ci.com/g/yoanm/php-jsonrpc-params-symfony-constraint-doc-sdk/build-status/master)
[![Scrutinizer Code Quality](https://img.shields.io/scrutinizer/g/yoanm/php-jsonrpc-params-symfony-constraint-doc-sdk/master.svg?logo=scrutinizer)](https://scrutinizer-ci.com/g/yoanm/php-jsonrpc-params-symfony-constraint-doc-sdk/?branch=master)
[![Codacy Badge](https://app.codacy.com/project/badge/Grade/8f39424add044b43a70bdb238e2f48db)](https://www.codacy.com/gh/yoanm/php-jsonrpc-params-symfony-constraint-doc-sdk/dashboard?utm_source=github.com\&utm_medium=referral\&utm_content=yoanm/php-jsonrpc-params-symfony-constraint-doc-sdk\&utm_campaign=Badge_Grade)

[![CI](https://github.com/yoanm/php-jsonrpc-params-symfony-constraint-doc-sdk/actions/workflows/CI.yml/badge.svg?branch=master)](https://github.com/yoanm/php-jsonrpc-params-symfony-constraint-doc-sdk/actions/workflows/CI.yml)
[![codecov](https://codecov.io/gh/yoanm/php-jsonrpc-params-symfony-constraint-doc-sdk/branch/master/graph/badge.svg?token=NHdwEBUFK5)](https://codecov.io/gh/yoanm/php-jsonrpc-params-symfony-constraint-doc-sdk)
[![Symfony Versions](https://img.shields.io/badge/Symfony-v4.4%20%2F%20v5.4%2F%20v6.x-8892BF.svg?logo=github)](https://symfony.com/)

[![Latest Stable Version](https://img.shields.io/packagist/v/yoanm/jsonrpc-params-symfony-constraint-doc-sdk.svg)](https://packagist.org/packages/yoanm/jsonrpc-params-symfony-constraint-doc-sdk)
[![Packagist PHP version](https://img.shields.io/packagist/php-v/yoanm/jsonrpc-params-symfony-constraint-doc-sdk.svg)](https://packagist.org/packages/yoanm/jsonrpc-params-symfony-constraint-doc-sdk)

PHP SDK to generate JSON-RPC documentation from symfony constraint

See [yoanm/symfony-jsonrpc-params-sf-constraints-doc](https://github.com/yoanm/symfony-jsonrpc-params-sf-constraints-doc) for automatic dependency injection.

## Versions

*   Symfony v3/4 PHP >=7.1 : `v0.x`
*   Symfony v4/5 PHP >=7.2 : `v1.0`
*   Symfony v4.4/5.4/6.x PHP ^8.0 : `v1.1`

## How to use

Create the transformer :

```php
use Yoanm\JsonRpcParamsSymfonyConstraintDoc\App\Helper\ConstraintPayloadDocHelper;
use Yoanm\JsonRpcParamsSymfonyConstraintDoc\App\Helper\DocTypeHelper;
use Yoanm\JsonRpcParamsSymfonyConstraintDoc\App\Helper\MinMaxHelper;
use Yoanm\JsonRpcParamsSymfonyConstraintDoc\App\Helper\StringDocHelper;
use Yoanm\JsonRpcParamsSymfonyConstraintDoc\App\Helper\TypeGuesser;
use Yoanm\JsonRpcParamsSymfonyConstraintDoc\Infra\Transformer\ConstraintToParamsDocTransformer;

$constraintPayloadDocHelper = new ConstraintPayloadDocHelper();

$transformer = new ConstraintToParamsDocTransformer(
  new DocTypeHelper(
    $constraintPayloadDocHelper,
    new TypeGuesser()
  ),
  new StringDocHelper(),
  new MinMaxHelper(),
  $constraintPayloadDocHelper
);
```

Then use it with single constraint or a list of :

```php
use Symfony\Component\Validator\Constraints as ConstraintNS;
use Yoanm\JsonRpcServerDoc\Domain\Model\Type\ObjectDoc;
use Yoanm\JsonRpcServerDoc\Domain\Model\Type\StringDoc;

$constraint = new ConstraintNS\Collection([
  'a' => new ConstraintNS\Type('string'),
  'b' => new ConstraintNS\Type('integer'),
  'c' => new ConstraintNS\Type('bool')
]);

/** @var ObjectDoc $constraintDoc */
$constraintDoc = $transformer->transform($constraint);

/** @var StringDoc $constraintDoc2 */
$constraintDoc2 = $transformer->transformList([
  new ConstraintNS\Type('string'),
  new ConstraintNS\NotNull()
]);
```

## Contributing

See [contributing note](./CONTRIBUTING.md)
