<?php

class Micro_Logger {

  static $log_directory;
  static $log = false;

  function log($message) {
    if (!self::$log) return;

    $file = self::$log_directory.'log.txt';

    file_put_contents($file, $message."\n", FILE_APPEND);
    chmod($file, 0777);

  }
}

?>