<?php

$EDITOR = "vi";

# Site parameters
$TITLE = "Richard Crowley&#8217;s blog";
$FQDN = "dev.rcrowley.org";
$AUTHOR = "Richard Crowley";
$MAIL = "r@rcrowley.org";
$COMMENTS = true;#false;

# Expose some site parameters to the templates
assign("TITLE");
assign("FQDN");
assign("AUTHOR");
assign("COMMENTS");

# Date formats
$DATEFORMAT_POST = "Y/m/d";
$DATEFORMAT_COMMENT = "Y/m/d g:i a";
