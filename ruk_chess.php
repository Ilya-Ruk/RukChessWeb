<?php

//$chess_engine = 'RukChess 3.0 SEF';
//$chess_engine = 'RukChess 3.0 Toga';
//$chess_engine = 'RukChess 3.0 NNUE';
$chess_engine = 'RukChess 3.0 NNUE2';

//$chess_engine = 'demolito_pext';
//$chess_engine = 'Hakkapeliitta 3.0 x64';
//$chess_engine = 'xiphos-0.6-w64-bmi2';

$chess_engine_folder = __DIR__ . '\engines';
$chess_engine_file = $chess_engine_folder . '\\' . $chess_engine . '.exe';

$fen_default = 'rnbqkbnr/pppppppp/8/8/8/8/PPPPPPPP/RNBQKBNR w KQkq - 0 1'; // startpos
$depth_default = 0; // None
$move_time_default = 3000; // 3 sec.

$fen = $_GET['fen'] ?? $fen_default;
$depth = (int)$_GET['depth'] ?? $depth_default;
$move_time = (int)$_GET['movetime'] ?? $move_time_default;

$descriptorspec = [
	0 => ['pipe', 'r'],
	1 => ['pipe', 'w'],
];

$other_options = [
	'bypass_shell' => true,
];

$process = @proc_open($chess_engine_file, $descriptorspec, $pipes, $chess_engine_folder, null, $other_options);

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
