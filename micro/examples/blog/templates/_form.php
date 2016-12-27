<p>You are logged in as <?php print $this->user->username ?> | <a href='<?php print $this->urlFor('LogoutController') ?>'>Logout</a></p>
<form method="post" action="<?php print $action ?>">
<label for="post_title">Title</label><br />
<input name="post_title" type="text" value="<?php print $this->post['title'] ?>"/><br />
<label for="post_body">Body</a><br />
<textarea name="post_body"><?php print $this->post['body']; ?></textarea><br />
<input type="hidden" name = "post_id" value = "<?php print $this->post['id'] ?>" />
<input type="submit" />
</form>