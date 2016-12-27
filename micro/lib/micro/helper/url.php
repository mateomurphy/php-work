<?php

class Micro_Helper_Url extends Micro_Mixin {


  public function urlFor($args) {
    if (!is_array($args)) $args = func_get_args();
    return $this->router->call('urlFor', $args);
  }

  public function linkTo($name, $controller) {
    $options = array('href' => $this->urlFor($controller));

    return $this->tag('a', $options, true).$name."</a>";
  }


  public function buttonTo($name, $options = array(), $htmlOptions = array()) {
    $method = isset($htmlOptions['method']) ? $htmlOptions['method'] : 'post';
    unset($htmlOptions['method']);

    if (in_array($method, array('put', 'delete'))) {
      $method_tag = $this->tag('input', array('type'=>'hidden', 'name'=>'_method', 'value'=>$method));
    } else {
      $method_tag = '';
    }

    $method = ($method == 'get') ? 'get' : 'post';
    $action = $this->urlFor($options);
    $htmlOptions = array_merge($htmlOptions, array('type'=>'submit', 'value' => $name));

    return "<form method='$method' action='$action' class='button_to'><div>".$method_tag.$this->tag("input", $htmlOptions)."</div></form>\n";

  }

}


?>