<h1><?php print $post->title ?></h1>
<p><?php print $post->body ?></p>
<p><a href='<?php print $this->urlFor('EditController', $post->id) ?>'>Edit</a> | <a href='<?php print $this->urlFor('ViewController', $post->id) ?>'>View</a></p>