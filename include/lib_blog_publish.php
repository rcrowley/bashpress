<?php

loadlib('dir');
loadlib('input');

# Write the post's Smarty template
#   The "special" string is safe because you should be escaping your
#   HTML anyway
function blog_publish_smarty($path, $file, $timestamp, $title, $tags,
	$body, $comments) {
	assign('path');
	assign('file');
	assign('date', date($GLOBALS['DATEFORMAT_POST'], $timestamp));
	assign('title', input_sanitize_smarty($title));
	assign('tags', input_sanitize_smarty($tags));
	assign('body', input_sanitize_smarty($body));
	assign('comments', $comments);
	global $smarty;
	file_put_contents("{$smarty->template_dir}/.posts$path/$file.html",
		$smarty->fetch('post.smarty'), LOCK_EX);
}

# Create directories from the publishing directory for this post
function blog_publish_dir($base, $dirs) {
	if (!is_array($dirs)) { return false; }
	if (!is_dir($base)) { mkdir($base, 0755); }
	$path = '';
	foreach ($dirs as $d) {
		$path .= "/$d";
		if (!is_dir("$base$path")) { mkdir("$base$path", 0755); }
	}
	return $path;
}

# Create newer/older pointers in the given direction from this post
#   Only special import functions working from newest to oldest will
#   need to set new == false
function blog_publish_pointers($parts, $new = true) {
	if (!is_array($parts)) { return; }
	global $smarty;
	$tree = dir_rscandir("{$smarty->template_dir}/.posts", false);
	$list = dir_flatten($tree);
	$depth = 0;
	$path = '';
	foreach ($parts as $p) {
		++$depth;
		$path .= "/$p";
		$i = array_search($path, $list[$depth]);
		if ($new && $i || !$new && $i < sizeof($list[$depth]) - 1) {
			if ($new) {
				--$i;
				$a = 'older';
				$b = 'newer';
			} else {
				++$i;
				$a = 'newer';
				$b = 'older';
			}
			file_put_contents("{$smarty->template_dir}/.posts$path/$a",
				$list[$depth][$i]);
			file_put_contents(
				"{$smarty->template_dir}/.posts{$list[$depth][$i]}/$b",
				$path);
		}
	}
}

# Update the month listing
#   This assumes the directories for this post have already been created
function blog_publish_months() {
	$tree = dir_rscandir("{$GLOBALS['smarty']->template_dir}/.posts", false);
	$list = dir_flatten($tree);
	$li = array();
	foreach ($list[2] as $m) {
		$li[] = "\t<li><a href=\"$m\">" . substr($m, 1) . "</a></li>\n";
	}
	file_put_contents("{$GLOBALS['smarty']->template_dir}/.months.html",
		"<ul id=\"months\">\n" . implode('', $li) . "</ul>\n", LOCK_EX);
}	

# Update the tag listing
#   Pass the tags for this post to this function
function blog_publish_tags($tags) {
	if (!is_array($tags)) { return; }
	global $smarty;
	if (file_exists("{$smarty->template_dir}/.tags.html")) {
		$xml = simplexml_load_file("{$smarty->template_dir}/.tags.html");
		foreach ($xml->li as $li) { $tags[] = (string)$li->a; }
	}
	$tags = array_unique($tags);
	sort($tags);
	$li = array();
	foreach ($tags as $t) {
		$li[] = "\t<li><a href=\"/tags/$t\">$t</a></li>\n";
	}
	file_put_contents("{$smarty->template_dir}/.tags.html",
		"<ul id=\"tags\">\n" . implode('', $li) . "</ul>\n", LOCK_EX);
}
