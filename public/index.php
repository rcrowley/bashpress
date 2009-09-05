<?php

if ($current = @file_get_contents("{$smarty->template_dir}/.posts/current")) {
	header("Location: $current\r\n");
}
else {
	loadlib('blog_comment');
	display();
}
