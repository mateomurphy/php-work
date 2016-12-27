<?php

if ($this->user) {
	print $this->_form(array('action'=>$this->urlFor('EditController')));
} else {
	print $this->_login();
}

?>