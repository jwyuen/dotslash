# Dotslash (for PHP) 

## What is it?
A php library that allows you to make command line scripts quickly in PHP with
conveniences that most command line scripts should have such as: 
- logging
- email notifications on failure
- easy way to take in and parse command line options
- execution timer
- nice looking console output

This library is mostly a wrapper over the excellent Symfony Console library, but
with added functionality that doesn't exist in the Console library such as
logging, email notifications, and a execution timer.

## What is the purpose?
The purpose is to simplify creation of command line scripts

## Install
Just add to your composer.json like:
```json
{
  "require": {
    "jwyuen/dotslash": "v0.2"
  }
}
``` 

## Usage
Create a class that extends the \Dotslash\BaseCommand class.  Your class should
extend the following functions:

```php
// See: http://symfony.com/doc/current/components/console/introduction.html
configure()

// Code to execute
executeCommand()

// Any input validation code you want.  Should return boolean
checkValidInput()

// The path to a configuration file if you want to use logging and email
// notifications.  Should return a string if you have a config, or null if you
// don't want to use logging and email functionality
getConfigPath()
```

You'll then need an entry point script from which you'll run your command line
script.  An example is located in sample/console.php

## Config Structure
If you decide you want to enable logging and email functionality, you'll need to
create a config file with the following contents/structure:


Example config.php:
```php
<?php

return array(
  'ses-email' => array(
    'aws-access-key' => '<your_access_key_here>',
    'aws-secret-key' => '<your_secret_key_here>',
    'region' => 'us-east-1',
    'email-recipient' => 'dev@dev.com'
  ),
  'logging' => array(
    'log-directory' => '/home/user/logs/'
  )
);
```

As you can tell from the example config, you'll need an AWS account to send
emails via Simple Email Service.  There may be support for other email options
in the future.

