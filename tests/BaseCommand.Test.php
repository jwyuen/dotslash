<?php

namespace Dotslash;

use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;

/* To properly test the BaseCommand class, we need to create test classes that 
 * extend it to check for certain behavior.
 */

class TestBaseCommand extends BaseCommand {
  protected function checkValidInput() {}
  protected function getConfigPath() {}
}

class TestBaseCommandUndefinedConfigure extends TestBaseCommand {
  static $commandName = 'testCommand1';
  protected function executeCommand() {}
}


class TestBaseCommandUndefinedExecuteCommand extends TestBaseCommand {
  static $commandName = 'testCommand2';
  protected function configure() {}
}

class TestGoodBaseCommand extends TestBaseCommand {
  static $commandName = 'testCommand3';
  protected function configure() {
    $this
      ->setName(self::$commandName)
      ->setDescription('Test');
  }

  protected function checkValidInput() {
    return true;
  }

  protected function executeCommand() {}
}

class TestSendFailureNotificationEmailCommand extends TestGoodBaseCommand {

  protected function executeCommand() {
    throw new \Exception('Testing!');
  }

}


class BaseCommandTest extends \PHPUnit_Framework_TestCase {

  public function testCommandUndefinedConfigure_ShouldThrowException() {
    
    $application = new Application();
    try {
      $application->add(new TestBaseCommandUndefinedConfigure());
      $this->fail('An expected exception was not thrown!');
    }
    catch (\Exception $e) {}
  }

  public function testCommandUndefinedExecuteCommand_ShouldThrowException() {
    
    $application = new Application();
    
    try {
      
      $application->add(new TestBaseCommandUndefinedExecuteCommand());
      $this->fail('An expected exception was not thrown!');
    }
    catch (\Exception $e) {}
  }

  public function testGoodCommand_ShouldNotThrowException() {
    
    $application = new Application();
    $application->add(new TestGoodBaseCommand());
    $command = $application->find(TestGoodBaseCommand::$commandName);
    $commandTester = new CommandTester($command);

    try {
      $commandTester->execute(
        array('command' => $command->getName())
      );
    }
    catch (Exception $e) {
      $this->fail('An unexpected exception was thrown!');
    }
  }
  
  public function testGoodCommand_ShouldHaveCorrectOutput() {
    
    $application = new Application();
    $application->add(new TestGoodBaseCommand());
    $command = $application->find(TestGoodBaseCommand::$commandName);
    $commandTester = new CommandTester($command);
    $commandTester->execute(
      array('command' => $command->getName())
    );
    
    $className = get_class(new TestGoodBaseCommand());
    $expectedOutputLine1 = '-------------------------------------------------';
    $expectedOutputLine2 = 'Beginning execution of ' . $className . ' script:';
    $expectedOutputLine3 = 'Script execution took';
    $this->assertContains($expectedOutputLine1, $commandTester->getDisplay());
    $this->assertContains($expectedOutputLine2, $commandTester->getDisplay());
    $this->assertContains($expectedOutputLine3, $commandTester->getDisplay());
  }

  public function test_command_sendsFailureNotificationEmail_OnException() {
    $emailRecipient = 'devo@devo.com';

    // Stub config
    $stubConfig = $this->getMockBuilder('Dotslash\Config') 
      ->disableOriginalConstructor()
      ->getMock();

    $stubConfig->expects($this->any())
      ->method('getEmailSesConfig')
      ->will($this->returnValue(array('email-recipient' => $emailRecipient)));

    // Mock emailer
    $mockEmailer = $this->getMockBuilder('Dotslash\Utils\Emailer')
      ->setMethods(array('email'))
      ->disableOriginalConstructor()
      ->getMock();


    $mockEmailer->expects($this->once())
      ->method('email')
      ->with(
        $this->anything(), 
        $this->anything(), 
        $this->equalTo($emailRecipient)
      );
    
    $application = new Application();
    $application->add(new TestSendFailureNotificationEmailCommand(
      null, $mockEmailer, $stubConfig
    ));
    $command = $application->find(
      TestSendFailureNotificationEmailCommand::$commandName
    );

    $commandTester = new CommandTester($command);
    try {
      $commandTester->execute(
        array('command' => $command->getName())
      );
      $this->fail('Exception supposed to be thrown but none thrown!');
    } catch (\Exception $e) {

    }
  }



}
