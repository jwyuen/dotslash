<?php

namespace Dotslash;

use org\bovigo\vfs\vfsStream;

class Config_Test extends \PHPUnit_Framework_TestCase {
  
  public function test_getAllConfig_ReturnsCorrectConfig() {

    $sampleConfigDirectory = 
      include(DOTSLASH_FIXTURES_PATH . 'exampleConfigDirectory.php');

    $expectedConfig = array(
      "ses-email" => array(
        "aws-access-key" => "iamkey",
        "aws-secret-key" => "iamkeysecret",
        "region" => "murica-region-1",
        "email-recipient" => "dev@dev.com"
      ),
      "logging" => array(
        "log-directory" => "/home/user/logs/"
      )
    );


    $configPath = 'root';
    $configRoot = vfsStream::setup($configPath);
    $root = vfsStream::create($sampleConfigDirectory, $configRoot);
    $url = vfsStream::url($configPath);
    
    $config = new Config($url);
    $actualConfig = $config->getAllConfig();
    $this->assertEquals($expectedConfig, $actualConfig);
  }

  public function test_getLoggingConfig_ReturnsCorrectConfig() {

    $sampleConfigDirectory = 
      include(DOTSLASH_FIXTURES_PATH . 'exampleConfigDirectory.php');

    $expectedConfig = array(
      'log-directory' => '/home/user/logs/'
    );


    $configPath = 'root';
    $configRoot = vfsStream::setup($configPath);
    $root = vfsStream::create($sampleConfigDirectory, $configRoot);
    $url = vfsStream::url($configPath);
    
    $config = new Config($url);
    $actualConfig = $config->getLoggingConfig();
    $this->assertEquals($expectedConfig, $actualConfig);
  }

  public function test_getEmailSesConfig_ReturnsCorrectConfig() {

    $sampleConfigDirectory = 
      include(DOTSLASH_FIXTURES_PATH . 'exampleConfigDirectory.php');

    $expectedConfig = array(
      'aws-access-key' => 'iamkey',
      'aws-secret-key' => 'iamkeysecret',
      'region' => 'murica-region-1',
      'email-recipient' => 'dev@dev.com'
    );


    $configPath = 'root';
    $configRoot = vfsStream::setup($configPath);
    $root = vfsStream::create($sampleConfigDirectory, $configRoot);
    $url = vfsStream::url($configPath);
    
    $config = new Config($url);
    $actualConfig = $config->getEmailSesConfig();
    $this->assertEquals($expectedConfig, $actualConfig);
  } 
}
