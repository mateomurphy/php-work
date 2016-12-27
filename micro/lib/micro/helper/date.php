<?php

class Micro_Helper_Date extends Micro_Helper {

  function selectDay($date, $options = array()) {
    $val = is_object($date) ? $date->day : $date;

    $result = array();

    for($day = 1; $day <= 31; $day ++) {
      if ($day == $val) {
        $result[] = "<option value='$day' selected='selected'>$day</option>";
      } else {
        $result[] = "<option value='$day'>$day</option>";
      }
    }

    return $this->selectTag('day', implode("\n", $result), $options);

  }

}

?>