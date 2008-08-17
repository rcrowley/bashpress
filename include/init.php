<?php

require_once '/usr/share/php/smarty/Smarty.class.php';
$smarty = new Smarty;
$smarty->template_dir = realpath(dirname(__FILE__) . '/../templates');
$smarty->compile_dir = realpath(dirname(__FILE__) . '/../.templates');

function loadlib($name) {
	require_once dirname(__FILE__) . "/lib_$name.php";
}

function assign() {
	$args = func_get_args();
	$key = array_shift($args);
	$ii = sizeof ($args);
	if (1 < $ii) { $value = $args; }
	else if (1 == $ii) { $value = $args[0]; }
	else if (0 == $ii && isset($GLOBALS[$key])) { $value = $GLOBALS[$key]; }
	else { $value = false; }
	$GLOBALS['smarty']->assign($key, $value);
}

function display($name = false) {

	# Send proper Content-Type header
	if ('xml' == $GLOBALS['FORMAT']) {
		header("Content-Type: text/xml\r\n");
	} else if ('rss' == $GLOBALS['FORMAT']) {
		header("Content-Type: application/rss+xml\r\n");
	} else if ('atom' == $GLOBALS['FORMAT']) {
		header("Content-Type: application/atom+xml\r\n");
	} else if ('json' == $GLOBALS['FORMAT']) {
		header("Content-Type: application/json\r\n");
	}

	global $smarty;
	if (false === $name) {
		$smarty->display(reset(explode('.', $GLOBALS['FILE'])) . '.' .
			$GLOBALS['FORMAT']);
	} else {
		if (false === strpos($name, '.')) {
			$smarty->display($name . '.' . $GLOBALS['FORMAT']);
		} else {
			$smarty->display($name);
		}
	}
	exit;
}

function redirect($url) {
	header("Location: $url\r\n");
	echo <<<EOD
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://w3.org/TR/xhtml1/DTD/xhtml1.1.dtd">
<html><head></head><body><p><a href="$url">302, ur leavin&rsquo; here.</a></p></body></html>
EOD;
	exit;
}

require_once dirname(__FILE__) . '/config.php';
