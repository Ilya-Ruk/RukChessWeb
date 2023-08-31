<?php

function engine_list($engine_folder, $engine_extension) {
	$handle = opendir($engine_folder);

	if ($handle === false) {
		header($_SERVER['SERVER_PROTOCOL'].' 500 Internal Server Error');

		echo 'Directory open error!' . PHP_EOL;

		exit(1);
	}

	$engine_list = [];

	while (($entry_name = readdir($handle)) !== false) {
		if ($entry_name == '.' || $entry_name == '..') {
			continue;
		}

		if (preg_match("/(.+){$engine_extension}$/", $entry_name, $matches) === 1) {
			$engine_list[] = $matches[1];
		}
	}

	closedir($handle);

	if (count($engine_list) == 0) {
		header($_SERVER['SERVER_PROTOCOL'].' 500 Internal Server Error');

		echo 'Engine(-s) not found!' . PHP_EOL;

		exit(1);
	}

	return $engine_list;
}
