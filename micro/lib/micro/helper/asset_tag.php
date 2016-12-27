<?php

class Micro_Helper_AssetTag extends Micro_Helper {

  public function imageTag($source, $options = array()) {
    $options['src'] = $source;
    return $this->tag("img", $options);

  }
}

?>