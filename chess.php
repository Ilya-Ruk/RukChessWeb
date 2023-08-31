<?php

require 'config.php';

$engine = $_GET['engine'] ?? $engine_default;
$fen = $_GET['fen'] ?? $fen_default;
$depth = (int)($_GET['depth'] ?? $depth_default);
$move_time = (int)($_GET['movetime'] ?? $move_time_default);

$engine_file = $engine_folder . DIRECTORY_SEPARATOR . $engine . $engine_extension;

$descriptorspec = [
	0 => ['pipe', 'r'],
	1 => ['pipe', 'w'],
];

$other_options = [
	'bypass_shell' => true,
];

$process = @proc_open($engine_file, $descriptorspec, $pipes, $engine_folder, null, $other_options);

if ($process === false) {
	header($_SERVER['SERVER_PROTOCOL'].' 500 Internal Server Error');

	echo 'Process open error!' . PHP_EOL;

	exit(1);
}

fwrite($pipes[0], 'uci' . PHP_EOL);

while (true) {
	$result = fgets($pipes[1]);

	if (strpos($result, 'uciok') === 0) {
		break;
	}
}

fwrite($pipes[0], 'ucinewgame' . PHP_EOL);
fwrite($pipes[0], 'isready' . PHP_EOL);

while (true) {
	$result = fgets($pipes[1]);

	if (strpos($result, 'readyok') === 0) {
		break;
	}
}

fwrite($pipes[0], 'position fen ' . $fen . PHP_EOL);
fwrite($pipes[0], 'go depth ' . $depth . ' movetime ' . $move_time . PHP_EOL);

while (true) {
	$result = fgets($pipes[1]);

	echo nl2br($result);

	if (strpos($result, 'bestmove') === 0) { // bestmove (none) / bestmove e2e4 / bestmove e7e8q
		break;
	}
}

fwrite($pipes[0], 'quit' . PHP_EOL);

fclose($pipes[0]);
fclose($pipes[1]);

proc_close($process);
