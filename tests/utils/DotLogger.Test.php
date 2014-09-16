<?php

namespace Dotslash\Utils;

use org\bovigo\vfs\vfsStream;

class DotLogger_Test extends \PHPUnit_Framework_TestCase {
  
  public function test_Logger_Works() {
    // Set up our mock file system
    $mockLogDirectory = array('logs');
    $rootPath = 'root';
    $configureRoot = vfsStream::setup($rootPath);
    $root = vfsStream::create($mockLogDirectory, $configureRoot);
    $logPath = vfsStream::url($rootPath);
    
    // Test our logger works
    $logger = new DotLogger('logger-test', $logPath);
    $this->assertTrue($logger->addInfo('adsf'));
 }

}
