# About Slim All In One Template

This is a template for Slim Framework 3.x that I use in my projects. It covers
various Slim extensions that seems to suite my needs, and probably yours: error handling and
logging, debug to a file, custom 404, authentication, authorization, ORM (Doctrine) ...

It uses Twig as template.

It has some core files to code a clean MVC application.

**NOTE: it is functional, but it needs some refactoring, like rename namespaces, clean up code, put some things in proper/better place/design ...**


## Notes About Authentication/Authorization Extension

Added jeremykendall/slim-auth to support Zend Authentication and Acls.

The template is configured for RDBMS authentication and authorization.

See required MySQL instructions above:

```mysql
    CREATE TABLE `users` (
      `id` int(11) NOT NULL AUTO_INCREMENT,
      `username` varchar(50) NOT NULL,
      `role` varchar(50) NOT NULL,
      `password` varchar(255) DEFAULT NULL,
      PRIMARY KEY (`id`)
    ) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=latin1;
```

Add a test username and password:

```mysql    
    INSERT INTO users (username, role, password) VALUES (
       'foobar',
       'admin',
       'teste' -- password is generated from PHP: var_dump( (new PasswordValidator())->rehash('teste') );
    );
    
    mysql> SELECT * FROM users;
    +----+----------+-------+--------------------------------------------------------------+
    | id | username | role  | password                                                     |
    +----+----------+-------+--------------------------------------------------------------+
    |  1 | foobar   | admin | $2y$10$jmYbCe3qtktfBvhpkZyzle0.PPR.RnMOn0UF80E7nRIfbhgrfbtbS |
    +----+----------+-------+--------------------------------------------------------------+
```

## Session

I'm configuring Session with `akrabat/rka-slim-session-middleware`, but I dealing with some problems by now.

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