<?php


class PHPErrorException extends Exception {
    public function setLine($line) { 
        $this->line = $line;
    }
    
    public function setFile($file) {
        $this->file = $file;
    }
}

class Micro_Error {
  static function handler($code, $string, $file, $line) { 
    $exception = new PHPErrorException($string, $code);
    $exception->setLine($line);
    $exception->setFile($file);
    throw $exception;
  }
} 


?>