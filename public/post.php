<?php

# Save the new comment and update the template
#   TODO: Use a Smarty template for this HTML
if ('POST' == $_SERVER['REQUEST_METHOD']) {
	$comment = $URL_PARTS;
	$comment[] = '.' . array_pop($comment) . '.html';
	$comment = implode('/', $comment);

	$text = "\t\t<p>" . implode("</p>\n<p>", preg_split("!\r?\n\r?\n!",
		strip_tags($_POST['text']))) . "</p>\n";

	if (preg_match('!https?://.!', $_POST['website'])) {
		$author = '<a href="' . strip_tags($_POST['website']) . '">' .
			strip_tags($_POST['name']) . '</a>';
	} else {
		$author = strip_tags($_POST['name']);
	}

	$date = date('Y-m-d g:i a');

	file_put_contents("{$smarty->template_dir}/.posts/$comment",
		"\t<li>\n$text\t\t<p>&mdash; $author &mdash; $date</p>\n\t</li>\n\n",
		FILE_APPEND | LOCK_EX);

}

$post = implode('/', $URL_PARTS) . ".$FORMAT";
if (file_exists("{$smarty->template_dir}/.posts/$post")) {
	assign('page', true);
	display(".posts/$post");
} else { display('404'); }
