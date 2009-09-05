<?php

# Strip down an arbitrary string, permalink-style
function url_sanitize($s) {
	return implode('-', array_map('_url_sanitize', explode(" ",
		strtolower($s))));
}
function _url_sanitize($s) {
	preg_match_all('![a-z0-9_-]+!', $s, $match);
	return implode("", $match[0]);
}
