<?php

# Site parameters
$TITLE = 'Richard Crowley&#8217;s blog';
$FQDN = 'bashpress.rcrowley.org';
$AUTHOR = 'Richard Crowley';
$MAIL = 'r@rcrowley.org';

# Expose some site parameters to the templates
assign('TITLE');
assign('FQDN');
assign('AUTHOR');

# Date formats
$DATEFORMAT_POST = 'Y/m/d';
$DATEFORMAT_COMMENT = 'Y/m/d g:i a';
