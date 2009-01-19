<?php

# Clean up a string for a Smarty template
#   The argument should be a string or an array
function input_sanitize_smarty($dirty) {
	return str_replace(
		array('{', '}', '<<<---THIS-IS-SPECIAL--->>>'),
		array('<<<---THIS-IS-SPECIAL--->>>', '{rdelim}', '{ldelim}'),
		$dirty
	);
}

# HTMLize a given string
#   Turn blocks of text into paragraphs, preserve single-line breaks,
#   convert URLs to links and strip all other tags
#   The URL-to-link code is basically how Wordpress does it
function input_htmlize($text, $begin = '') {

	# Paragraphs and line breaks
	$arr = preg_split("!(?:\r?\n){2,}!", strip_tags(trim($text)));
	foreach ($arr as $i => $text) {
		$arr[$i] = implode("\n$begin<br />", preg_split("!(?:\r?\n)!", $text));
	}
	$out = "$begin<p>" . implode("</p>\n$begin<p>", $arr) . "</p>\n";

	# Turn URLs to links
	$out = preg_replace(
		'!(<a( [^>]+?>|>))<a [^>]+?>([^>]+?)</a></a>!i', '$1$3</a>',
		preg_replace_callback(
		'#(?<=[\s>])(\()?([\w]+?://(?:[\w\\x80-\\xff\#$%&~/\-=?@\[\](+]|[.,;:](?![\s<])|(?(1)\)(?![\s<])|\)))*)#is',
		'_input_htmlize_link', $out));
	return trim($out);

}
function _input_htmlize_link($matches) {
	$url = _input_htmlize_cleanurl($matches[2]);
	if(empty($url)) { return $matches[0]; }
	else { return "{$matches[1]}<a href=\"$url\">$url</a>"; }
}
function _input_htmlize_cleanurl($url) {
	if ('' == $url) { return $url; }
	$url = str_replace(array('%0d', '%0a'), '', str_replace(';//', '://',
		preg_replace("|[^a-z0-9-~+_.?#=!&;,/:%@$*'()x80-xff]|i", '', $url)));
	if (false === strpos($url, ':') && '/' != substr($url, 0, 1)
		&& !preg_match('!^[a-z0-9-]+?\.php!i', $url)) {
		$url = "http://$url";
	}
	return str_replace("'", '&#039;',
		preg_replace('|&([^#])(?![a-z]{2,8};)|', '&#038;$1', $url));
}
