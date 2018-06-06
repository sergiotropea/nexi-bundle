Installation
============

Step 1: Download the Bundle
---------------------------

Open a command console, enter your project directory and execute the
following command to download the latest stable version of this bundle:

```console
$ composer require sergiotropea/nexi-bundle
```

This command requires you to have Composer installed globally, as explained
in the [installation chapter](https://getcomposer.org/doc/00-intro.md)
of the Composer documentation.

Step 2: Enable the Bundle
-------------------------

Then, enable the bundle by adding it to the list of registered bundles
in the `app/AppKernel.php` file of your project:

```php
<?php
// app/AppKernel.php

// ...
class AppKernel extends Kernel
{
    public function registerBundles()
    {
        $bundles = array(
            // ...
            new SergioTropea\NexiBundle\SergioTropeaNexiBundle(),
        );

        // ...
    }

    // ...
}
```

Step 3: Routing
-------------------------

Insert into routing file the prefix of any route

```yml
// app/config/routing.yml

// ...
nexi:
    resource: "@SergioTropeaNexiBundle/Resources/config/routing.yml"
```

Step 4: Config
-------------------------

Insert into routing file the prefix of any route

```yml
// app/config/config.yml

// ...
sergio_tropea_nexi:
    environment: test #test or production
    url: http://sbc2.movingup.it
    alias: ALIAS_WEB_00003045
    key: 07V2S7IZXE2FLXYBRTYLO5K0PSPV9L31
```    