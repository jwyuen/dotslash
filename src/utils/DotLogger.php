<?php

namespace Dotslash\Utils;

use Monolog\Logger;
use Monolog\Handler\RotatingFileHandler;
use Monolog\Formatter\LineFormatter;
use Monolog\Processor\IntrospectionProcessor;

class DotLogger extends \Monolog\Logger {

  private $logPath;

  public function __construct($name, $logDirectory) {
    
    $this->logPath = $logDirectory . '/' . $name;

    parent::__construct($name);
    $this->configureLogger();
  }

  private function configureLogger() {

    $dateFormat = 'Y-m-d H:i:s';
    $logFormat = "%datetime% %level_name% |[%codeInfo%] %message%\n";

    $formatter = new LineFormatter($logFormat, $dateFormat);
    $rotatingLogger = new RotatingFileHandler($this->logPath, 30);
    $rotatingLogger->setFormatter($formatter);
    
    $this->pushHandler($rotatingLogger);
    $this->pushProcessor(new IntrospectionProcessor());
  }

  public function addRecord($level, $message, array $context = array()) {
    return parent::addRecord($level, $message, $context);
  } 


}
