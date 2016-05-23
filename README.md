# About Slim All In One Template

This is a template for Slim Framework 3.x that I use in my projects. It covers
various Slim extensions that seems to suite my needs, and probably yours: error handling and
logging, debug to a file, custom 404, authentication, authorization, ORM (Doctrine) ...

It uses Twig as template.

It has some core files to code a clean MVC application.

**NOTE: it is functional, but it needs some refactoring, like namespaces.**


## Notes About Authentication/Authorization Extension

Added jeremykendall/slim-auth to support Zend Authentication and Acls. 

Commit 6c7cb6c85002a87e31f0a3c46152896a76901f6e is a working (hardcoded) example/reference for Slim 3.

See required MySQL instructions in the same git log revision.


# Installing

Download to some/directory/slim-allinone-template

Install the extensions:

$ composer install

When using a different name for the Web Application, change its name in:

- app/.htaccess
- conf/*
- add/update entries in httpd.conf

## Apache 2.2 httpd.conf:

```html
<IfModule alias_module>
    Alias /slim-template /path/to/slim-allinone-template/app
</IfModule>
```


**NOTE: the Directory should not be in Document Root, because we are trying to protect the App's conf and logs directories** 

```html
<Directory "/path/to/slim-allinone-template/app">
        # Not in Document Root so conf/, logs/ etc... won't be availabe via web server
        Options -Indexes
        AllowOverride All
        Order allow,deny
        Allow from all
</Directory>
```

# MVC

- app/index.php is the Front Controller, but require_once some routes that are responsible to delegate the request to a Controller:
    - routes.php routes for the App
    - routes-cli.php routes for a CLI
    - routes-middleware.php a separeted file for Slim Middlewares
    - routes-api.php routes for a API (like REST/oAuth) 

- app/ctrl Controller(s) that dispatch the request to some Command
- app/cmd Classes extending app/cmd/AbstractCommand.php process the requests
- app/app some Slim utilities, like static calls to Slim functions and more
- app/dao DAO and Entities for the Doctrine ORM