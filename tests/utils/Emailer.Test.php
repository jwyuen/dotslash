<?php

namespace Dotslash\Utils;

use Aws\Common\Aws;
use Aws\Ses\SesClient;
use org\bovigo\vfs\vfsStream;

class Emailer_Test extends \PHPUnit_Framework_TestCase {

  public function test_constructorThrowsExceptionOnMissingRegionVariable() {
    $sampleConfig = array(
      'dotslash-config.php' => '<?php return array(
        "ses-email" => array(
          "aws-access-key" => "iamkey",
          "aws-secret-key" => "iamkeysecret"
        ),
        "logging" => array(
          "log-directory" => "/home/user/logs/"
        )
      );'
    );
    
    $configPath = 'root';
    $configRoot = vfsStream::setup($configPath);
    $root = vfsStream::create($sampleConfig, $configRoot);
    $url = vfsStream::url($configPath);
    
    $config = new \Dotslash\Config($url);

    try {
      $emailer = new Emailer($config);
      $this->fail('Expected exception not thrown.');
    } catch (\Exception $e) {}

  }
  
  public function test_constructorThrowsExceptionOnMissingAccessKeyVariable() {
    $sampleConfig = array(
      'dotslash-config.php' => '<?php return array(
        "ses-email" => array(
          "aws-secret-key" => "iamkeysecret",
          "region" => "murica-region-1"
        ),
        "logging" => array(
          "log-directory" => "/home/user/logs/"
        )
      );'
    );
    
    $configPath = 'root';
    $configRoot = vfsStream::setup($configPath);
    $root = vfsStream::create($sampleConfig, $configRoot);
    $url = vfsStream::url($configPath);
    
    $config = new \Dotslash\Config($url);

    try {
      $emailer = new Emailer($config);
      $this->fail('Expected exception not thrown.');
    } catch (\Exception $e) {}

  }
  
  public function test_constructorThrowsExceptionOnMissingSecretKeyVariable() {
    $sampleConfig = array(
      'dotslash-config.php' => '<?php return array(
        "ses-email" => array(
          "aws-access-key" => "iamkey",
          "region" => "murica-region-1"
        ),
        "logging" => array(
          "log-directory" => "/home/user/logs/"
        )
      );'
    );
    
    $configPath = 'root';
    $configRoot = vfsStream::setup($configPath);
    $root = vfsStream::create($sampleConfig, $configRoot);
    $url = vfsStream::url($configPath);
    
    $config = new \Dotslash\Config($url);

    try {
      $emailer = new Emailer($config);
      $this->fail('Expected exception not thrown.');
    } catch (\Exception $e) {}

  }

  public function test_email_calls_SES_sendEmail_WithCorrectParams() {
    
    $sampleConfig = array(
      'dotslash-config.php' => '<?php return array(
        "ses-email" => array(
          "aws-access-key" => "iamkey",
          "aws-secret-key" => "iamkeysecret"
        ),
        "logging" => array(
          "log-directory" => "/home/user/logs/"
        )
      );'
    );
    
    $configPath = 'root';
    $configRoot = vfsStream::setup($configPath);
    $root = vfsStream::create($sampleConfig, $configRoot);
    $url = vfsStream::url($configPath);
    
    $config = new \Dotslash\Config($url);


    $from = 'aguy@guy.com';
    $to = array('mom@mom.com');
    $subject = 'Hi Mom!';
    $body = 'Miss you!';

    $expectedArg = array(
      'Source' => $from,
      'Destination' => array(
        'ToAddresses' => $to
      ),
      'Message' => array(
        'Subject' => array(
          'Data' => $subject
        ),
        'Body' => array(
          'Text' => array(
            'Data' => $body
          )
        )
      )
    );

    $mockSesClient = $this->getMockBuilder('Aws\Ses\SesClient')
      ->setMethods(array('sendEmail'))
      ->disableOriginalConstructor()
      ->getMock();
    $mockSesClient->expects($this->once())
      ->method('sendEmail')
      ->with($expectedArg);

    $emailer = new Emailer($config, $mockSesClient);
    $emailer->email($subject, $body, $to, $from);

  }

}
