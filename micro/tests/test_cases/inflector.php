<?php

class InflectorTest extends UnitTestCase {

  function testCamelize() {
    $this->assertEqual(Micro_Inflector::camelize('lower_case_and_underscored_word'), 'LowerCaseAndUnderscoredWord');
    $this->assertEqual(Micro_Inflector::camelize('lower_case_and_underscored_word', false), 'lowerCaseAndUnderscoredWord');

    $this->assertEqual(Micro_Inflector::camelize('lower_case/underscored/word'), 'LowerCase_Underscored_Word');
    $this->assertEqual(Micro_Inflector::camelize('lower_case/underscored/word', false), 'lowerCase_Underscored_Word');
  }

  function testUnderscore() {
    $this->assertEqual(Micro_Inflector::underscore('LowerCaseAndUnderscoredWord'), 'lower_case_and_underscored_word');
    $this->assertEqual(Micro_Inflector::underscore('LowerCase_Underscored_Word'), 'lower_case/underscored/word');
  }

}

?>