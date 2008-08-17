<?php

$SALT = 'the salt is deadbeef';

# Site parameters
$TITLE = 'Richard Crowley&#8217;s blog';
$FQDN = 'bashpress.rcrowley.org';
$AUTHOR = 'Richard Crowley';

# Expose site parameters to the templates
assign('TITLE');
assign('FQDN');
assign('AUTHOR');
