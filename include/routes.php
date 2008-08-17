<?php

# Routes are a regular-expression-style (without ^ or $ anchors) patterns
# of the URI-portion of a URL and point to a PHP file in public/.  Any
# sub-patterns matched will be made available in $URL_PARTS

$routes = array(

	# Individual posts
	'/([0-9]{4})/([0-9]{2})/([0-9]{2})/([^.]+)' => 'post.php',

	# Tags
	'/tags/([^.]+)' => 'tags.php',

	# Date archives
	'/([0-9]{4})/([0-9]{2})/([0-9]{2})' => 'archives.php',
	'/([0-9]{4})/([0-9]{2})' => 'archives.php',
	'/([0-9]{4})' => 'archives.php',

	# Atom feed
	'/feed' => 'feed.php',

	'/' => 'index.php',

	'' => '404.php'

);
