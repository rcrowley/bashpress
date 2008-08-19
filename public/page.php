<?php

$page = $URL_PARTS[0];
if (file_exists("{$smarty->template_dir}/$page.html")) {
	display($page);
} else { display('404'); }
