<?php

# Save a submitted comment
#   TODO: Use a Smarty template for this HTML
function blog_comment_save($name, $website, $text) {
	$base = _blog_comment_base();

	$text = "\t\t<p>" . implode("</p>\n<p>", preg_split("!(?:\r?\n){2,}!",
		strip_tags($text))) . "</p>\n";

	# TODO: Turn links into <a href...

	if (preg_match('!https?://.!', $website)) {
		$author = '<a href="' . strip_tags($website) . '">' .
			strip_tags($name) . '</a>';
	} else { $author = strip_tags($name); }

	$date = date($GLOBALS['DATEFORMAT_COMMENT']);

	file_put_contents("$base.comments.html",
		"\t<li>\n$text\t\t<p>&mdash; $author &mdash; $date</p>\n\t</li>\n\n",
		FILE_APPEND | LOCK_EX);
	file_put_contents("$base.count",
		'.', FILE_APPEND | LOCK_EX);

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
