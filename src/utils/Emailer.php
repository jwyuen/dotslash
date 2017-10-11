<?php

namespace Dotslash\Utils;

use Aws\Ses\SesClient;
use Aws\Credentials\Credentials;

class Emailer {

  private $sesClient;

  public function __construct($config, $sesClient = null) {
    $this->sesClient = $sesClient;

    if (is_null($this->sesClient)) {

      $sesConfig = $config->getEmailSesConfig();

      if (!isset($sesConfig['aws-access-key']) || !isset($sesConfig['aws-secret-key']) ||
        !isset($sesConfig['region'])) {

        throw new \Exception('Unable to initiate Emailer due to missing ' .
          'configuration variables.  Please check your config.');
      }

      $credentials = new Credentials(
        $sesConfig['aws-access-key'], $sesConfig['aws-secret-key']
      );


      $this->sesClient = new SesClient(array(
        'credentials' => $credentials,
        'region' => $sesConfig['region'],
        'version' => '2010-12-01'
      ));
    }
  }

  public function email($subject, $body, $to, $from = null) {
    $args = $this->createSesSendEmailParameterObject(
      $subject, $body, $to, $from
    );

    $this->sesClient->sendEmail($args);
  }

  /*
   * This is a function to simplify the sending of emails using the AWS SDK.
   * For more advanced options, use the AWS SDK library directly.
   * See: http://docs.aws.amazon.com/aws-sdk-php-2/guide/latest/service-ses.html
   */
  private function createSesSendEmailParameterObject($subject, $body, $to, $from = null) {

    if (!is_array($to)) {
      $to = array($to);
    }

    if (!isset($from)) {
      $from = WEBMASTER_EMAIL;
    }

    $args = array(
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

    return $args;
  }

}
