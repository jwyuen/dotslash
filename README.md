# Configee for PHP! 

## What is it?
A php library that you can add to any project to help you manage
application specific php config files, such as database connection parameters.
For example, if you were creating a PHP framework, you'd want anyone using your
project to be able to define their own config parameters for connecting to their
own database.

## What is the purpose?
The purpose is to have a dead simple interface for setting and getting
configuration parameters by only having to define config files.

## Install
Just add to your composer.json like:
```json
{
  "require": {
    "jwyuen/configee-php": "1.1"
  }
}
``` 

## Usage
Instantiate the main class wherever you need to use your configuration 
parameters, such as a bootstrap file.
```php
$configee = new \Configee\Configee('your/config/path');
$config = $configee->getConfig();
```

## Config Parsing Structure
Configee can handle an n-deep directory structure if you so choose to nest your 
configs like so.  The only requirement is that your config files are php files 
and return an array.

For example, consider the following directory structure:

```
configroot
\- db
 |- mysql.php
 \- AnEmptyFolder
\-general.php
```

Contents of mysql.php:
```php
<?php

return array(
  'host' => 'localhost',
  'user' => 'amysqluser',
  'password' => 'apassword',
);
```

Contents of general.php
```php
<?php

return array(
  'use_cookies' => true,
  'aconfigoption' => 1,
);
```

Given the above, you'll get a config structure like so:
```php
array(
  "db" => array(
    "mysql" => array(
      "host" => "localhost",
      "user" => "amysqluser",
      "password" => "apassword"
    )
  ),
  "general" => array(
    "use_cookies" => true,
    "aconfigoption" => 1
  )
)
```

## Important PSA
If you decide to store sensitive credentials in your config(s), make sure to set
proper permissions on them to make sure not just anyone can look at them!  E.g.
```
chmod 640 <your-config-file>
```
