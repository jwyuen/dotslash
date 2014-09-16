<?php

/* Handler for Errors and Exceptions, hence 'Errorception'
 * Note this only works for errors and exceptions thrown after the 
 * initializeHandler function is called.  For everything before that, you'll 
 * need to rely php's built in logging to be able to appropriately log 
 * exceptions and errors. e.g. set error_log = syslog in php.ini
 */

namespace Dotslash\Utils;

use Symfony\Component\Console\Output\OutputInterface;

class ErrorceptionHandler {

  static $log;
  static $output;
  
  public static function initializeHandler(
    DotLogger $logger = null, 
    OutputInterface $output = null,
    $useStrictErrorHandler = true) {

    self::$log = $logger;
    self::$output = $output;

    error_reporting(E_ALL); 
    ini_set('log_errors','1'); 
    ini_set('display_errors','1'); 

    if ($useStrictErrorHandler) {
      set_error_handler(array(
        '\Dotslash\Utils\ErrorceptionHandler', 
        'exceptionErrorHandler'
      ));
    }

    set_exception_handler(array(
      '\Dotslash\Utils\ErrorceptionHandler', 
      'exceptionHandler'
    ));
  }

  public static function exceptionErrorHandler($code, $error, $file = NULL, 
    $line = NULL) {

    if (!(error_reporting() & $code)) {
      // This error code is not included in error_reporting
      return;
    }
    else {
      throw new \ErrorException($error, $code, 0, $file, $line);
    }
  }

  public static function exceptionHandler($exception) {

    $message = 'EXCEPTION ' . $exception->getMessage() . ' - Line ' . 
      $exception->getLine() . ' - ' . $exception->getTraceAsString();


    if (isset(self::$log)) {
      self::$log->addError($message);
    }
    self::renderException($exception);
    syslog(LOG_ERR, $message);
  }

  private static function renderException($exception) {
    if (self::$output != null) {
      
      self::$output->writeln('<error>[' . get_class($exception) . ']</error>');
      self::$output->writeln('<error>' . $exception->getMessage() . '</error>');
    }
  }
    

}
