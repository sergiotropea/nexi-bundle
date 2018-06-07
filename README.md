Overview
============

Bundle to include Nexi Payment System, you can find service and routes set, ready to use for button - response - abort - post.  
You can override DefaultController to improve and grow up new functionality.

Below eh bundle Structure

- Controller
    - DefaltController.php
- Entity
    - Nexi.php
- Resouces
    - views
        - Default
             - abort.html.twig
             - index.html.twig
             - response.html.twig
        
Installation
============

Step 1: Download the Bundle
---------------------------

Open a command console, enter your project directory and execute the
following command to download the latest stable version of this bundle:

```console
$ composer require sergiotropea/nexi-bundle dev-master
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
    url: <url_base>
    alias: <alias>
    key: <key>
```

Step 4: How to generate Url
-------------------------

Insert into routing file the prefix of any route

```php
// src/AppBundle/Controller/DefaultController.php
// ... 

    $nexiService = $this->get('sergio_tropea_nexi.nexi');

    //Mandatory
    $nexi = new Nexi();
    $nexi->setCodTrans("TEST_".time());
    $nexi->setDivisa("EUR");
    $nexi->setImporto(1200);
    
    //Optional
    

    dump($nexiService->generateUrl($nexi)); 
```    