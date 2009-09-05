<?php

# Routes are a regular-expression-style (without ^ or $ anchors) patterns
# of the URI-portion of a URL and point to a PHP file in public/.  Any
# sub-patterns matched will be made available in $URL_PARTS

$routes = array(

	# Individual posts and secret previews
	'/(\d{4})/(\d{2})/(\d{2})/([^./]+)/([^./]+)' => "post.php",
	'/(\d{4})/(\d{2})/(\d{2})/([^./]+)' => "post.php",

	# Tags
	'/tags/([^.]+)' => "tags.php",

	# Date archives
	'/(\d{4})/(\d{2})/(\d{2})' => "archives.php",
	'/(\d{4})/(\d{2})' => "archives.php",
	'/(\d{4})' => "archives.php",

	# Atom feed
	'/feed' => "feed.php",

	'/' => "index.php",

	'' => "404.php",

);
