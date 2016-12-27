<?php print $this->_post(array('post'=>$this->post)) ?>

<p>Comments for this post:</p>
<?php foreach ($this->comments as $comment) { ?>
<h2><?php print $comment->username ?></h2>
<p><?php print $comment->body ?></p>
<?php } ?>

<form method="post" action="<?php print $this->urlFor('CommentController') ?>">
<label for="post_username">Username</label><br />
<input name="post_username" type="text"<br />
<label for="post_comment">Comment</a><br />
<textarea name="post_comment"></textarea><br />
<input type="hidden" name = "post_id" value = "<?php print $this->post->id ?>" />
<input type="submit" />
</form>