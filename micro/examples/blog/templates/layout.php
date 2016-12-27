<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01//EN">

<html>
<head>
	<title>Microblog</title>
	<link rel="stylesheet" type="text/css" href="<?php print $this->router->baseUrl ?>/styles.css" media="screen" />
</head>

<body>
<h1 class="header"><a href="<?php print $this->urlFor('IndexController'); ?>">blog</a></h1>
<?php print $content ?>
</body>
</html>
