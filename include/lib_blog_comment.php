<?php

# Save a submitted comment
#   TODO: Use a Smarty template for this HTML
function blog_comment_save($name, $website, $text, $date = false) {
	$base = _blog_comment_base();

	# Sanitize input
	# TODO: Maybe allow some HTML instead of mercilessly stripping tags
	# TODO: Turn links into <a href...
	$name = strip_tags(trim($name));
	$website = strip_tags(trim($website));
	$text = strip_tags(trim($text));

	# HTMLize
	$html = "\t\t<p>" . implode("</p>\n<p>",
		preg_split("!(?:\r?\n){2,}!", $text)) . "</p>\n";
	if (preg_match('!^https?://.!', $website)) {
		$link = "<a href=\"$website\">$name</a>";
	} else { $link = $name; }

	# Take either the passed date or now
	$date = date($GLOBALS['DATEFORMAT_COMMENT'],
		$date ? strtotime($date) : time());

	# Save the comment
	file_put_contents("$base.comments.html",
		"\t<li>\n$html\t\t<p>&mdash; $link &mdash; $date</p>\n\t</li>\n\n",
		FILE_APPEND | LOCK_EX);
	file_put_contents("$base.count",
		'.', FILE_APPEND | LOCK_EX);

	# Email the site owner
	global $FQDN;
	mail($GLOBALS['MAIL'], 'New comment!',
		"Post: http://$FQDN{$GLOBALS['URL']}\r\n\r\n" .
		"Name: $name\r\nWebsite: $website\r\n\r\n$text\r\n",
		"From: Bashpress <bashpress@$FQDN>\r\n");

}

# Get the comment count
$GLOBALS['smarty']->register_function(
	'blog_comment_count', 'blog_comment_count');
function blog_comment_count($params = false, $smarty = false) {
	if (isset($params['tpl'])) {
		$parts = explode('/', $params['tpl']);
		array_unshift($parts, $GLOBALS['smarty']->template_dir);
		$file = array_pop($parts);
		$parts[] = substr($file, 0, strrpos($file, '.')) . '.count';
		return filesize(implode('/', $parts));
	} else { return filesize(_blog_comment_base() . '.count'); }
}

# Get the base of comment file names
function _blog_comment_base() {
	return $GLOBALS['smarty']->template_dir . '/.posts/' .
		implode('/', $GLOBALS['URL_PARTS']);
}
