
$ composer install

When using a different name for the Web Application, change its name in:

- app/.htaccess
- conf/*
- add/update entries in httpd.conf

&lt;IfModule alias_module&gt;
    Alias /slim-template /path/to/slim-allinone-template/app
&lt;/IfModule&gt;

NOTE: the Directory should not be in Document Root, because we are trying to protect the App's conf and logs directories 

&lt;Directory "/path/to/slim-allinone-template/app"&gt;
        # Not in Document Root so conf/, logs/ etc... won't be availabe via web server
        Options -Indexes
        AllowOverride All
        Order allow,deny
        Allow from all
&lt;/Directory&gt;


