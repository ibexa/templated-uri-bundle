Hautelook Templated URI Bundle
==============================

Symfony Bundle for the [https://github.com/hautelook/TemplatedUriRouter](https://github.com/hautelook/TemplatedUriRouter)
library. 
`hautelook/TemplatedUriRouter` provides a [RFC-6570](https://tools.ietf.org/html/rfc6570) compatible 
Symfony router and URL Generator.

[![Build Status](https://secure.travis-ci.org/hautelook/TemplatedUriBundle.png?branch=master)](https://travis-ci.org/hautelook/TemplatedUriBundle)
[![SensioLabsInsight](https://insight.sensiolabs.com/projects/cf31d6be-a1b8-41b5-a718-9f35660c321b/mini.png)](https://insight.sensiolabs.com/projects/cf31d6be-a1b8-41b5-a718-9f35660c321b)

## Installation

Assuming you have installed [composer](https://getcomposer.org/), run the following command:

```bash
$ composer require hautelook/templated-uri-bundle
```

Now add the bundle to your Kernel:

```php
<?php
// app/AppKernel.php

public function registerBundles()
{
    $bundles = array(
        // ...
        new Hautelook\TemplatedUriBundle\HautelookTemplatedUriBundle(),
        // ...
    );
}
```

If you are using Symfony Flex, this bundle is added automatically to your `bundles.php` file.

## Usage

The bundle exposes a router service (`hautelook.router.template`) that will generate RFC-6570 compliant URLs.
Here is a sample on how you could use it:

```php
$templateLink = $container->get('hautelook.router.template')->generate('hautelook_demo_route',
    array(
        'page'   => '{page}',
        'sort'   => array('{sort}'),
        'filter' => array('{filter}'),
    )
);
```

This will produce a link similar to:

```
/demo?{&page}{&sort*}{&filter*}
```
