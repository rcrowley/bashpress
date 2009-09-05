<?php

loadlib('blog_comment');

$tag = $URL_PARTS[0];
if (file_exists("{$smarty->template_dir}/.tags/$tag.html")) {
	assign('tag');
	display();
}
else { display('404'); }
