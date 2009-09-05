<?php

# Recursively walk the directory tree
#   This special rscandir function will return a tree of only directories
#   TODO: Don't count directories that only contain *.preview.html files
function dir_rscandir($dir, $all = true) {
	$ls = array_diff(scandir($dir), array(".", ".."));
	$out = array();
	foreach ($ls as $l) {
		if (is_dir("$dir/$l")) { $out[$l] = dir_rscandir("$dir/$l", $all); }
		else if ($all) { $out[$l] = false; }
	}
	return sizeof($out) ? $out : false;
}

# Flatten the walked directory tree at every depth
function dir_flatten($tree, $path = "", $depth = 1) {
	$list = array();
	foreach ($tree as $dir => $subtree) {
		$list[$depth][] = "$path/$dir";
		if ($subtree) {
			$sublist = dir_flatten($subtree, "$path/$dir", $depth + 1);
			foreach ($sublist as $d => $l) {
				if (!is_array($list[$d])) { $list[$d] = array(); }
				$list[$d] = array_merge($list[$d], $l);
			}
		}
	}
	return $list;
}
