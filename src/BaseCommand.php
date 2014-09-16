<?php

namespace Dotslash;

//use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;


/* 
 * Any classes that would normally extend the Symfony Command class should 
 * extend this instead as it abstracts some boilerplate you'd probably want to 
 * use in a Command class such as:
 *
 * - script execution timer
 * - Requiring input validation
 * - unified function for outputting to the logger and objects of the the 
 *   Symfony Output class at the same time
 *
 * You'll also get the following if there's a proper configuration set for them:
 * - logging
 * - email notification on script failure
 */
abstract class BaseCommand extends Command {

  private $logger = null;
  private $emailer = null;
  private $config = null;

  protected $output;
  protected $input;
  protected $dialog;
  protected $timeStart;

  public function __construct($logger = null, $emailer = null, $config = null) {
    parent::__construct();
    
    $this->initializeErrorHandling();
    $this->config = $config;
    
    if ($this->getConfigPath() !== null) {
      $this->config = new Config($configPath);
    }

    // For DI for testing
    if (isset($logger)) {
      $this->logger = $logger;
    } else {
      $this->initializeLogger();
    }
    
    if (isset($emailer)) {
      $this->emailer = $emailer;
    } else {
      $this->initializeEmailer();
    }
  }

  /*
   * This should never be used before runTimeInitialization is called as it 
   * relies on $this->output being set
   */
  protected function writeOutput($message, $useErrorFormat = false) {
    if (isset($this->logger)) {
      $this->logger->info($message);
    }

    if ($useErrorFormat) {
      $this->output->writeln('<error>' . $message . '</error>');
    }
    else {
      $this->output->writeln($message);
    }
  }

  /**
   * This function is used to set certain values that can only be determined at 
   * runtime (when the command get executed rather than when an object
   * of the class gets made).
   */
  private function runTimeInitialization(InputInterface $input, 
    OutputInterface $output) {

    

    $this->input = $input;
    $this->output = $output;
    $this->dialog = $this->getHelperSet()->get('dialog');

    $this->timeStart = microtime(true);
    $this->writeOutput('-------------------------------------------------');
    $this->writeOutput('Beginning execution of ' . get_class($this) . 
      ' script:');
  }
  
  private function initializeErrorHandling() {
    $output = new \Symfony\Component\Console\Output\ConsoleOutput();
    \Dotslash\Utils\ErrorceptionHandler::initializeHandler(
      $this->logger, $output
    );
  }

  private function initializeLogger() {
    if (isset($this->config) && !isset($this->logger)) {
      
      $loggingConfig = $this->config->getLoggingConfig();
      if (isset($loggingConfig)) {

        $this->logger = new \Dotslash\Utils\DotLogger(
          $this->getName(), $loggingConfig['log-directory']
        );
      }
    }
  }

  private function initializeEmailer() {
    if (isset($this->config) && !isset($this->emailer)) {
      $this->emailer = new Emailer($this->config);
    }
  }

  protected function execute(InputInterface $input, OutputInterface $output) {
    try {
      $this->runTimeInitialization($input, $output);
      if ($this->checkValidInput()) {
        $this->executeCommand();
      }

      $timeEnd = microtime(true);
      $time = $timeEnd - $this->timeStart;
      $this->writeOutput('Script execution took ' . $time . ' seconds.');
    } catch (\Exception $e) {
      $this->sendFailureNotificationEmail($e->getTraceAsString());
      throw $e;
    }
  }

  private function sendFailureNotificationEmail($message) {
    if (isset($this->emailer)) {
      $subject = 'Script failure: ' . get_class($this);
      $body = $message;
      $emailConfig = $this->config->getEmailSesConfig();
      $to = $emailConfig['email-recipient'];
      //$to = 'james.yuen@brooklynmuseum.org';
      $this->emailer->email($subject, $body, $to);
    }
  }
  
  /* 
   * Must be overriden.
   *
   * For an example of what this function should contain, see:
   * http://symfony.com/doc/current/components/console/introduction.html
   */
  protected function configure() {
    throw new \Exception('Required function configure() not defined by ' . 
      'child class!');
  }

  /* 
   * Must be overriden.
   *
   * Should define the code the command should execute
   */
  protected function executeCommand() {
    throw new \Exception('Required function executeCommand() not defined by ' . 
      'child class!');
  }

  /* 
   * Must be overriden.
   *
   * Checks the user's input to see if it's valid.  Should return true if the 
   * input is valid, false if the input is invalid
   */
  protected abstract function checkValidInput();

  /*
   * Must be overriden.
   *
   * Should return the path of your config file in string format.  If you don't 
   * have a config file, just return null
   *
   */
  protected abstract function getConfigPath();

}
