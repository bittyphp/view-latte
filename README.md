# Latte View

[![Build Status](https://travis-ci.org/bittyphp/view-latte.svg?branch=master)](https://travis-ci.org/bittyphp/view-latte)
[![Codacy Badge](https://api.codacy.com/project/badge/Coverage/6b7785db455a42f39fde71b2c47ed6bb)](https://www.codacy.com/app/bittyphp/view-latte)
[![PHPStan Enabled](https://img.shields.io/badge/PHPStan-enabled-brightgreen.svg?style=flat)](https://github.com/phpstan/phpstan)
[![Total Downloads](https://poser.pugx.org/bittyphp/view-latte/downloads)](https://packagist.org/packages/bittyphp/view-latte)
[![License](https://poser.pugx.org/bittyphp/view-latte/license)](https://packagist.org/packages/bittyphp/view-latte)

A [Latte](https://latte.nette.org/) template view for Bitty.

## Installation

It's best to install using [Composer](https://getcomposer.org/).

```sh
$ composer require bittyphp/view-latte
```

## Setup

### Basic Usage

```php
<?php

require(dirname(__DIR__).'/vendor/autoload.php');

use Bitty\Application;
use Bitty\View\Latte;

$app = new Application();

$app->getContainer()->set('view', function () {
    return new Latte(dirname(__DIR__).'/templates/', $options);
});

$app->get('/', function () {
    return $this->get('view')->renderResponse('index.latte', ['name' => 'Joe Schmoe']);
});

$app->run();

```

### Options

```php
<?php

use Bitty\View\Latte;

$latte = new Latte(
    dirname(__DIR__).'/templates/',
    [
        // Sets the path to the cache directory.
        // Defaults to system tmp folder.
        'cacheDir' => '/path/to/cache',

        // Whether to auto-refresh templates when the file changes.
        'refresh' => true,

        // Template content type.
        // @see Latte\Engine::CONTENT_* constants.
        'contentType' => 'html',
    ]
);

```

## Adding Filters

One of the great things about Latte is that you can easily extend it to add you own custom functionality. This view would not be complete without allowing access to that ability.

See Latte's [filter docs](https://latte.nette.org/en/filters#toc-usage) for more info.

```php
<?php

use Bitty\View\Latte;

$latte = new Latte(...);

$latte->addFilter('someFilterName', function ($value) {
    return strtolower($value);
});

```

## Advanced

If you need to do any advanced customization, you can access the Latte engine and loader directly at any time.

```php
<?php

use Bitty\View\Latte;
use Latte\Engine;
use Latte\ILoader;

$latte = new Latte(...);

/** @var Engine */
$engine = $latte->getEngine();

/** @var ILoader */
$loader = $latte->getLoader();

```
