<?php

# Create directories from the publishing directory for this post
function blog_dir($base, $dirs) {
	if (!is_array($dirs)) { return false; }
	if (!is_dir($base)) { mkdir($base, 0755); }
	$path = "";
	foreach ($dirs as $d) {
		$path .= "/$d";
		if (!is_dir("$base$path")) { mkdir("$base$path", 0755); }
	}
	return $path;
}

