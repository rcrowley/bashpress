<?php

# Find and load the proper editor
function cli_edit($post) {
	if ($editor = $GLOBALS['EDITOR']) {}
	else if ($editor = getenv('EDITOR')) {}
	else { $editor = "vi"; }
	$command = rtrim(`which $editor`);
	if ($command) { pcntl_exec($command, array($post), $_ENV); }
}
