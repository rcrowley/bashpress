<?php

# Write the post's Smarty template
function blog_publish_smarty($path, $file, $timestamp, $title, $tags, $body) {
	list($title, $tags, $body) = preg_split("!\r?\n\r?\n!",
		file_get_contents($argv[1]), 3);
	$tags = preg_split('!\s+!', $tags);
	assign('path');
	assign('file');
	assign('date', date($GLOBALS['DATEFORMAT_POST'], $timestamp));
	assign('title');
	assign('tags');
	assign('body');
	global $smarty;
	file_put_contents("{$smarty->template_dir}/.posts$path/$file.html",
		$smarty->fetch('post.smarty'), LOCK_EX);
}

# Create directories from the publishing directory for this post
function blog_publish_dir($base, $dirs) {
	global $smarty;
	if (!is_dir($base)) { mkdir($base, 0755); }
	$path = '';
	foreach ($dirs as $d) {
		$path .= "/$d";
		if (!is_dir("$base$path")) { mkdir("$base$path", 0755); }
	}
	return $path;
}
