<?php

if (isset($this->user)) {
	print $this->_form(array('action'=>$this->urlFor('AddController')));
} else {
	print $this->_login();
}

?>