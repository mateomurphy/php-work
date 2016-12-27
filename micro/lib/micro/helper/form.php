<?php

class Micro_Helper_Form extends Micro_Helper {

  function valueFor($objectName, $method) {
    return $this->$objectName->$method;
  }

  function nameFor($objectName, $method) {
    return $objectName."[".$method."]";
  }

  function fileField($objectName, $method, $options = array()) {
    $options['id'] = $objectName.'_'.$method;
    return $this->fileFieldTag($this->nameFor($objectName, $method), $options);
  }

  function hiddenField($objectName, $method, $text = null, $options = array()) {
    $options['id'] = $objectName.'_'.$method;
    return $this->hiddenFieldTag($this->nameFor($objectName, $method), $this->valueFor($objectName, $method), $options);
  }

  function label($objectName, $method, $text = null, $options = array()) {
    $options['for'] = $objectName.'_'.$method;
    return $this->labelTag($method, $text, $options);
  }

  function passwordField($objectName, $method, $options = array()) {
    $options['id'] = $objectName.'_'.$method;
    return $this->passwordFieldTag($this->nameFor($objectName, $method), $this->valueFor($objectName, $method), $options);
  }

  function select($objectName, $method, $choices, $options = array(), $htmlOptions = array()) {
    $htmlOptions['id'] = $objectName.'_'.$method;
    $choices = $this->optionsForSelect($choices, $this->valueFor($objectName, $method));
    $choices = $this->addOptions($choices, $options, $this->valueFor($objectName, $method));
    return $this->selectTag($this->nameFor($objectName, $method), $choices, $htmlOptions);
  }

  function textField($objectName, $method, $options = array()) {
    $options['id'] = $objectName.'_'.$method;
    return $this->textFieldTag($this->nameFor($objectName, $method), $this->valueFor($objectName, $method), $options);

  }

  function textArea($objectName, $method, $options = array()) {
    $options['id'] = $objectName.'_'.$method;
    return $this->textAreaTag($this->nameFor($objectName, $method), $this->valueFor($objectName, $method), $options);

  }

  function optionsForSelect($data, $selected = null) {
    if (!is_array($data)) return $data;

    $result = array();

    foreach($data as $key => $value) {
      $selectedAttribute = $this->optionValueSelected($key, $selected) ? ' selected="selected"' : '';

      $result[] = "<option value='$key'$selectedAttribute>$value</option>";
    }

    return implode("\n", $result);
  }

  function optionValueSelected($value, $selected) {
    if (is_array($selected)) return in_array($value, $selected);
    return ($value == $selected);
  }

  function optionsFromCollectionForSelect($collection, $valueMethod, $textMethod, $selected = null) {
    $result = array();
    foreach($collection as $item) {
      $result[$item->{$valueMethod}] = $item->{$textMethod};
    }

    return $this->optionsForSelect($result, $selected);

  }

  function collectionSelect($objectName, $method, $collection, $valueMethod, $textMethod, $options = array(), $htmlOptions = array()) {
    return $this->select($objectName, $method, $this->optionsFromCollectionForSelect($collection, $valueMethod, $textMethod, $this->valueFor($objectName, $method)), $options, $htmlOptions);
  }

  function addOptions($optionTags, $options, $value = null) {

    if (isset($options['include_blank']) && $options['include_blank']) {
      $optionTags = "<option value=''></option>".$optionTags;
    }

    if (isset($value) && isset($options['prompt']) && $options['prompt']) {
      $optionTags = "<option value=''>".(is_string($options['prompt']) ? $options['prompt'] : 'Please Select')."</option>".$optionTags;
    }

    return $optionTags;

  }

}


?>