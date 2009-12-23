<?php

loadlib('blog_comment');

if (count($URL_PARTS)) {
	$date = implode('/', $URL_PARTS);
	if (file_exists("{$smarty->template_dir}/.posts/$date.html")) {
		assign('date');
		assign('newer', @file_get_contents(
			"{$smarty->template_dir}/.posts/$date/newer"));
		assign('older', @file_get_contents(
			"{$smarty->template_dir}/.posts/$date/older"));
		display();
	}
	else { display('404'); }
} 
else { display(); }
