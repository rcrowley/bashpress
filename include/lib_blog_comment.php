<?php

loadlib('input');

# Save a submitted comment
#   TODO: Use a Smarty template for this HTML
function blog_comment_save($name, $website, $text, $date = false) {
	$base = _blog_comment_base();

	# Sanitize and HTMLize input
	$name = input_sanitize_smarty(strip_tags(trim($name)));
	$website = input_sanitize_smarty(strip_tags(trim($website)));
	$text = trim($text);
	$mail = strip_tags($text);
	$html = input_htmlize(input_sanitize_smarty($text), "\t\t");

	# If they gave a website, show that with their name
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
		"Name: $name\r\nWebsite: $website\r\n\r\n$mail\r\n",
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
