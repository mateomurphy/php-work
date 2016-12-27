<?php

/**
 * Date class, wraps the built in DateTime object and adds additional functionality
 *
 * More information about PHP's built in object here
 *
 * http://laughingmeme.org/2007/02/27/looking-at-php5s-datetime-and-datetimezone/
 */
class Micro_DateTime extends Micro_Object {

  /**
   * Returns the names of the months in english, or the name of the given month
   *
   * @param int $month
   * @return mixed
   */
  static function monthNames($month = null) {
    $months = array(1=>'January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December');

    return isset($month) ? $months[$month] : $months;
  }

  public function __construct($time = null) {
    parent::__construct();
    $this->instanceInclude(date_create($time));
  }

  // Properties

  public function __toString() {
    return $this->format('Y-m-d');
  }

  public function getDay() {
    return $this->format('j');
  }

  public function setDay($day) {
    return $this->setDate($this->getYear(), $this->getMonth(), $day);
  }

  public function getMonth() {
    return $this->format('n');
  }

  public function setMonth($month) {
    return $this->setDate($this->getYear(), $month, $this->getDay());
  }

  public function getYear() {
    return $this->format('Y');
  }

  public function setYear($year) {
    return $this->setDate($year, $this->getMonth(), $this->getDay());
  }

  public function getHour() {
    return $this->format('h');
  }

  public function setHour($hour) {
    return $this->setTime($hour, $this->getMinute(), $this->getSecond());
  }

  public function getMinute() {
    return $this->format('i');
  }

  public function setMinute($minute) {
    return $this->setTime($this->getHour(), $minute, $this->getSecond());
  }

  public function getSecond() {
    return $this->format('s');
  }

  public function setSecond($second) {
    return $this->setTime($this->getHour(), $this->getMinute(), $second);
  }
}


?>