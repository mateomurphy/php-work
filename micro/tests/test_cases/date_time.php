<?php

class DateTimeTest extends UnitTestCase {

  var $date;

  function setup() {
    $this->date = new Micro_DateTime('1975-06-30 12:34:56');
  }

  function testMonthNames() {
    $this->assertEqual(Micro_DateTime::monthNames(1), 'January');
  }

  function testToString() {
    $this->assertEqual((string) $this->date, '1975-06-30');
  }

  function testGetters() {
    $this->assertEqual($this->date->year, 1975);
    $this->assertEqual($this->date->month, 6);
    $this->assertEqual($this->date->day, 30);

    $this->assertEqual($this->date->hour, 12);
    $this->assertEqual($this->date->minute, 34);
    $this->assertEqual($this->date->second, 56);
  }

  function testSetters() {
    $this->date->year = 1980;
    $this->assertEqual($this->date->year, 1980);
    $this->date->month = 12;
    $this->assertEqual($this->date->month, 12);
    $this->date->day = 15;
    $this->assertEqual($this->date->day, 15);
    $this->assertEqual((string) $this->date, '1980-12-15');

    $this->date->hour = 11;
    $this->date->minute = 22;
    $this->date->second = 33;

    $this->assertEqual($this->date->format('h:i:s'), '11:22:33');

  }

}

?>