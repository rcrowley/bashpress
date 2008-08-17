<?php

require_once dirname(__FILE__) . '/../include/init.php';

# Find the proper route to use
require_once dirname(__FILE__) . '/../include/routes.php';
if ('' == $_GET['__url__']) { $_GET['__url__'] = '/'; }
foreach ($routes as $pattern => $FILE) {
	if (preg_match(
		"!^$pattern(?:/|\.(html|xml|json|rss|rdf|atom|php))?(?:\?.*)?$!",
		$_GET['__url__'], $URL_PARTS)) {
		$URL = array_shift($URL_PARTS);
		if (false === strpos($URL, '.')) { $FORMAT = 'html'; }
		else { $FORMAT = array_pop($URL_PARTS); }
		break;
	}
}
if (!isset($URL)) {
	$FILE = '404.php';
	$URL = $_GET['__url__'];
	$URL_PARTS = array();
	$FORMAT = 'html';
}
unset($routes);
unset($pattern);
unset($_GET['__url__']);

assign('FILE', $FILE);
assign('URL', $URL);
assign('URL_PARTS', $URL_PARTS);
assign('FORMAT', $FORMAT);

# Dispatch
#   Beyond the superglobals, these are passed to the called file:
#     FILE: the name of the file included to handle the request
#     URL: the complete request URL, minus the query string
#     URL_PARTS: array of matched sub-patterns in the URL
#     FORMAT: one (html|xml|json|rss|rdf|php) for determining output format
require_once $FILE;
display();
