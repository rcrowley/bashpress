<?php

loadlib('blog_comment');

# Save the new comment and update the template
#   TODO: Use a Smarty template for this HTML
if ('POST' == $_SERVER['REQUEST_METHOD']) {
	blog_comment_save($_POST['name'], $_POST['website'], $_POST['text']);
}

$post = implode('/', $URL_PARTS) . ".$FORMAT";
if (file_exists("{$smarty->template_dir}/.posts/$post")) {
	assign('page', true);
	display(".posts/$post");
} else { display('404'); }
