<?php

loadlib('blog_comment');

# Save the new comment and update the template
if ('POST' == $_SERVER['REQUEST_METHOD'] && 'qwerty' == $_POST['test']) {
	blog_comment_save($_POST['name'], $_POST['website'], $_POST['text']);
	unset($_POST['test']);
	unset($_POST['name']);
	unset($_POST['website']);
	unset($_POST['text']);
}

# Check the hash if this is supposed to be a post preview
if (4 != sizeof($URL_PARTS)) {
	$hash = array_pop($URL_PARTS);
	$post = implode('/', $URL_PARTS);
	if ($hash != sha1_file(dirname(__FILE__) . "/../posts/$post")) {
		display('404');
	}
	$post .= ".preview.$FORMAT";
	assign('preview', true);
}

# Display the post
else { $post = implode('/', $URL_PARTS) . ".$FORMAT"; }
if (file_exists("{$smarty->template_dir}/.posts/$post")) {
	assign('page', true);
	display(".posts/$post");
}
else { display('404'); }
