<?php

namespace Dotslash\Utils;

use \Monolog\Formatter\LineFormatter;

class LineFormatter extends LineFormatter {
  public function format(array $record) {

    $record['codeInfo'] = '';
    if (isset($record['extra']['class']) && isset($record['extra']['function']) 
      && isset($record['extra']['line'])
    ) {
      $record['codeInfo'] = $record['extra']['class'] . '::' . 
        $record['extra']['function'] . ':' . $record['extra']['line'];
    }

    //return parent
    return parent::format($record);
  }
}
