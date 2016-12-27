<?php

class StyleController extends Micro_Controller {
	const route = '/styles.css';

	function get() {

		$this->response->headers["Content-Type"] = "text/css; charset=utf-8";

 	  $this->response->body = "
body {
    font-family: Utopia, Georga, serif;
}
h1.header {
    background-color: #fef;
    margin: 0; padding: 10px;
}
div.content {
    padding: 10px;
}
";


	}


}

?>