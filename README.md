# Config bundle

This bundle offers some improvements in the configuration management of your Symfony application.

It allows you to write your application configuration to multiple environment-dependant files that will be
loaded automatically when the application starts.

## What benefits does this bundle offer?

- It offers an important DX: you can organize your configuration into several files without having to include them
manually in your main config file.

- It allows you to benefit from Symfony environments in a powerful way, and write different configurations for
each of them easily.
 
- It also provides a way to first determine a default set of services and parameters and then override them depending on
the environment.

- It takes advantage of the Symfony cache system, so it does not affect the loading time of your application.

## Getting started

### Install

You can use [Composer](https://getcomposer.org/) to install this bundle:

    composer require aaronadal/config-bundle

After this, you need to register the bundle in your application kernel:

    // app/AppKernel.php
    
    public function registerBundles()
    {
        return [
            // ...
            new Aaronadal\ConfigBundle\AaronadalConfigBundle(),
            // ...
        ];
    }

### Configure

You can configure two locations (through [glob patterns](http://php.net/manual/en/function.glob.php)) in which the 
bundle will look for the configuration files:

- **Defaults**: determines the path where default configuration files reside.
- **Environment**: determines the path where environment-dependant configuration files reside. As you can see in the
following example, there is an _:env_ placeholder that references the environment at runtime (default: _dev_ or _prod_).

Let's configure it in the config.yml:

    aaronadal_config:
        location:
            defaults:    config/parameters/defaults/*.yml
            environment: config/parameters/:env/*.yml

That's all! Quite simple. Now, all yml files inside the `config/parameters/defaults/` folder will always be loaded
and if the environment is, for example, _dev_, all the yml files inside the `config/parameters/dev/` folder will
override the default values (or will define new ones if not defined).

**NOTE 1:** Due to the way in which parameters are resolved by Symfony, parameters cannot be used in the definition
of the locations. Only the :env placeholder is valid.

**NOTE 2:** The locations may be absolute or relative paths. If they are relative paths, the kernel.root_dir is taken as
the reference path.

### Creating your own environments

Don't you know [how to create new environments in Symfony](http://symfony.com/doc/current/configuration/environments.html)?
