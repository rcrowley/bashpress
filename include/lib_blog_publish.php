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
		$smarty->fetch("post.smarty"), LOCK_EX);
}

# Create directories from the publishing directory for this post
function blog_publish_dir($base, $dirs) {
	if (!is_array($dirs)) { return false; }
	if (!is_dir($base)) { mkdir($base, 0755); }
	$path = "";
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

	# Newer/older pointer
	$tree = dir_rscandir("{$smarty->template_dir}/.posts", false);
	$list = dir_flatten($tree);
	$depth = 0;
	$path = "";
	foreach ($parts as $p) {
		++$depth;
		$path .= "/$p";
		$i = array_search($path, $list[$depth]);
		if ($new && $i || !$new && $i < sizeof($list[$depth]) - 1) {
			if ($new) {
				--$i;
				$a = "older";
				$b = "newer";
			}
			else {
				++$i;
				$a = "newer";
				$b = "older";
			}
			file_put_contents("{$smarty->template_dir}/.posts$path/$a",
				$list[$depth][$i]);
			file_put_contents(
				"{$smarty->template_dir}/.posts{$list[$depth][$i]}/$b",
				$path);
		}
	}

	# Current pointer
	file_put_contents("{$smarty->template_dir}/.posts/current",
		"/{$parts[0]}/{$parts[1]}");

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
		"<ul id=\"months\">\n" . implode("", $li) . "</ul>\n", LOCK_EX);
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
		"<ul id=\"tags\">\n" . implode("", $li) . "</ul>\n", LOCK_EX);
}

# Prepend a post to the Atom feed, either bumping an old post off the
# end or replacing one with the same ID
#   In replace_only mode, it will silently do nothing if the ID isn't
#   already in the feed
function blog_publish_feed($path, $file, $timestamp, $title, $body,
	$replace_only = false) {
	global $smarty;
	$permalink = "http://{\$FQDN}$path/$file";
	$date_published = date("c", $timestamp);

	# Get the DOM of the feed
	$dom = new DOMDocument;
	if (file_exists("{$smarty->template_dir}/.feed.atom")) {
		$dom->load("{$smarty->template_dir}/.feed.atom");
	}
	else {
		$dom->loadXML(<<<EOD
<?xml version="1.0" encoding="utf-8"?>
<feed xmlns="http://www.w3.org/2005/Atom">
	<title>{\$TITLE}</title>
	<link href="http://{\$FQDN}/feed" rel="self"/>
	<link href="http://{\$FQDN}/" rel="alternate"/>
	<id>http://{\$FQDN}/feed</id>
	<updated>2008-09-03T23:17:43-07:00</updated>
	<author><name>{\$AUTHOR}</name></author>
</feed>
EOD
		);
	}
	$feed = $dom->documentElement;

	# Find and remove the entry we're displacing or replacing
	#   In replace_only mode this will return if the entry isn't found
	$index = false;
	$entries = $feed->getElementsByTagName('entry');
	$ii = $entries->length;
	for ($i = 0; $i < $ii; ++$i) {
		$entry = $entries->item($i);
		if ($entry->getElementsByTagName('id')->item(0)
			->firstChild->nodeValue == $permalink) {
			$index = $i;
			$feed->removeChild($entry);
			$date_updated = date("c");
			break;
		}
	}
	if (false === $index) {
		if ($replace_only) { return; }
		else {
			$index = 0;
			if (15 == $entries->length) {
				$feed->removeChild($entries->item(14));
			}
			$date_updated = $date_published;
		}
	}

	# Update the feed properties
	$updated = $feed->getElementsByTagName('updated')->item(0);
	$updated->removeChild($updated->firstChild);
	$updated->appendChild($dom->createTextNode($date_updated));

	# Create the new entry
	$entry = $dom->createElement('entry');
	$entry->appendChild($dom->createElement('title',
		htmlspecialchars(input_sanitize_smarty($title))));
	$link = $dom->createElement('link');
	$link->setAttribute('href', $permalink);
	$link->setAttribute('rel', "alternate");
	$entry->appendChild($link);
	$entry->appendChild($dom->createElement('id', $permalink));
	$entry->appendChild($dom->createElement('published', $date_published));
	$entry->appendChild($dom->createElement('updated', $date_updated));
	$author = $dom->createElement('author');
	$author->appendChild($dom->createElement('name', "{\$AUTHOR}"));
	$entry->appendChild($author);
	$content = $dom->createElement('content',
		htmlspecialchars(input_sanitize_smarty($body)));
	$content->setAttribute('type', 'html');
	$entry->appendChild($content);

	# Insert the new entry into the DOM
	if ($entries->length <= $index) { $feed->appendChild($entry); }
	else {
		$feed->insertBefore($entry, $entries->item($index));
	}

	# Serialize and save
	file_put_contents("{$smarty->template_dir}/.feed.atom",
		$dom->saveXML(), LOCK_EX);

}
