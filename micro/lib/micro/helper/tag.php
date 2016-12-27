<?php

class Micro_Helper_Tag {
  function contentTag($name, $content, $options = null, $escape = true) {
    $options = $this->tagOptions($options, $escape);

    return "<".$name.$options.">".$content."</".$name.">\n";
  }

  function tag($name, $options = null, $open = false, $escape = true) {
    $options = $this->tagOptions($options, $escape);

    return "<".$name.$options.($open ? ">" : " />")."\n";
  }

  function tagOptions($options, $escape = true) {
    if (!is_array($options)) return '';

    $result = array();

    foreach($options as $key => $value) {
      if (is_null($value)) continue;

      if ($escape) $value = htmlentities($value, ENT_QUOTES, 'UTF-8', false);

      $result[] = "$key=\"$value\"";
    }

    if (!count($result)) return '';

    return " ".implode(" ", $result);
  }
}

?>