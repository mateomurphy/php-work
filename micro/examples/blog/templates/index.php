<?php if (!count($this->posts)) { ?>
<p>No posts found</p>
<?php } else {
	foreach($this->posts as $post) {
		print $this->_post(array('post'=>$post));
	}
} ?>
<p><a href='<?php print $this->urlFor('AddController') ?>'>Add</p>