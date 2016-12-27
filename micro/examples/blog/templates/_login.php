<form action="<?php print $this->urlFor('LoginController') ?>" method="post">
<label for="username">Username</label><br />
<input type="text" name="username" /><br />

<label for="password">Password</label><br />
<input type="text" name="password" /><br />

<input type="submit" name="login" value="login" />
</form>