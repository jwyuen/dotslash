<?php

namespace Dotslash;

class Config {

  private $config;

  public function __construct($configPath = null) {

    if (!isset($configPath)) {
      throw new \Exception('No configuration file specified.');
    }
    
    $configee = new \Configee\Configee($configPath);
    $config = $configee->getConfig();
    $this->config = $config['dotslash-config'];
  }

  public function getAllConfig() {  
    return $this->config;
  }

  public function getLoggingConfig() {
    return $this->config['logging'];
  }
  
  public function getEmailSesConfig() {
    return $this->config['ses-email'];
  }

}
