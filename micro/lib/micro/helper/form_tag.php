<?php


class Micro_Helper_FormTag extends Micro_Helper {

  function formTag($url, $options = null) {
    if (is_null($options)) $options = array();

    if (isset($options['multipart']) && $options['multipart']) {
      $options['enctype'] = "multipart/form-data";
      unset($options['multipart']);
    }

    $extra = '';
    if (!isset($options['method'])) {
      $options['method'] = 'post';
    } elseif ($options['method'] == 'get') {
      //
    } else {
      $extra = $this->hiddenFieldTag('_method', $options['method']);
      $options['method'] = 'post';
    }

    $options['action'] = $this->urlFor($url);

    return $this->tag("form", $options, true).$extra;

  }

  function endFormTag() {
    return "</form>";
  }

  function hiddenFieldTag($name, $value= null, $options = null) {
    if (is_null($options)) $options = array();

    $options['type'] = 'hidden';
    $options['class'] = 'hidden_field';

    return $this->textFieldTag($name, $value, $options);

  }

  function fileFieldTag($name, $options = null) {
    if (is_null($options)) $options = array();
    $options['type'] = 'file';

    return $this->textFieldTag($name, null, $options);

  }

  function labelTag($name, $text = null, $options = null) {
    if (is_null($options)) $options = array();
    if (is_null($text)) $text = Micro_Inflector::humanize($name);

    $options = array_merge(array('for' => $name), $options);

    return $this->contentTag("label", $text, $options);
  }

  function passwordFieldTag($name = 'password', $value = null, $options = null) {
    if (is_null($options)) $options = array();

    $options = array_merge(array('type' => 'password'), $options);

    return $this->textFieldTag($name, $value, $options);
  }

  function selectTag($name, $option_tags = null, $options = null) {
    if (is_null($options)) $options = array();

    $id = $name;
    $name = isset($options['multiple']) && $options['multiple'] ? $name.'[]' : $name;

    $options = array_merge(array('name' => $name, 'id' => $id), $options);

    return $this->contentTag("select", $option_tags, $options);

  }

  function submitTag($value = "Save Changes", $options = null) {
    if (is_null($options)) $options = array();

    $options = array_merge(array('type' => 'submit', 'name' => 'commit', 'value' => $value, 'class' => 'submit_tag'), $options);

    return $this->tag('input', $options);
  }

  function textFieldTag($name, $value = null, $options = null) {
    if (is_null($options)) $options = array();

    $options = array_merge(array('type' => 'text', 'name' => $name, 'id' => $name, 'value' => $value, 'class' => 'text_field'), $options);

    return $this->tag('input', $options);
  }

  function textAreaTag($name, $value = null, $options = null) {
    if (is_null($options)) $options = array();

    $options = array_merge(array('name' => $name, 'id' => $name, 'class' => 'text_area'), $options);

    return $this->contentTag('textarea', $value, $options);
  }



}

?>