<?php

require 'config.php';
require 'engine_list.php';

$engine_list = engine_list($engine_folder, $engine_extension);

?>
<!doctype html>
<html>
	<head>
		<meta charset="utf-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
		<meta name="viewport" content="width=device-width, initial-scale=1">

		<title>RukChessWeb</title>

		<link rel="stylesheet" href="https://unpkg.com/@chrisoakman/chessboardjs@1.0.0/dist/chessboard-1.0.0.min.css" integrity="sha384-q94+BZtLrkL1/ohfjR8c6L+A6qzNH9R2hBLwyoAfu3i/WCvQjzL2RQJ3uNHDISdU" crossorigin="anonymous">
	</head>

	<body>
		<h1 style="margin: 10px 0">RukChessWeb</h1>

		<div style="margin: 10px 0">
			<label>Play:</label>

			<select id="color">
				<option value="White">White</option>
				<option value="Black">Black</option>
			</select>

			<input id="new" type="button" value="New game">
			<input id="flip" type="button" value="Flips board">
			<input id="undo" type="button" value="Undo move">
			<input id="analyze" type="button" value="Analyze">
		</div>

		<div style="margin: 10px 0">
			<label>Engine:</label>

			<select id="engine">
				<?php foreach ($engine_list as $engine_name): ?>
					<option value="<?= $engine_name ?>"<?= ($engine_name == $engine_default) ? ' selected' : '' ?>><?= $engine_name ?></option>
				<?php endforeach; ?>
			</select>

			<label>Depth limit:</label>

			<select id="depth">
				<option value="0" selected>None</option>
				<option value="1">1</option>
				<option value="2">2</option>
				<option value="3">3</option>
				<option value="4">4</option>
				<option value="5">5</option>
				<option value="6">6</option>
				<option value="7">7</option>
				<option value="8">8</option>
				<option value="9">9</option>
				<option value="10">10</option>
				<option value="11">11</option>
				<option value="12">12</option>
				<option value="13">13</option>
				<option value="14">14</option>
				<option value="15">15</option>
				<option value="20">20</option>
			</select>

			<label>Move time limit:</label>

			<select id="moveTime">
				<option value="1000">1 sec.</option>
				<option value="2000">2 sec.</option>
				<option value="3000" selected>3 sec.</option>
				<option value="5000">5 sec.</option>
				<option value="10000">10 sec.</option>
				<option value="15000">15 sec.</option>
				<option value="20000">20 sec.</option>
				<option value="30000">30 sec.</option>
			</select>
		</div>

		<div style="margin: 10px 0">
			<label>FEN:</label>

			<input id="fen" type="text" style="width: 500px">
			<input id="fenSet" type="button" value="Set">
		</div>

		<div>
			<label>Game status:</label>

			<span id="status"></span>
		</div>

		<div id="myBoard" style="margin: 10px 0; width: 400px"></div>

		<div>
			<label>PGN:</label>

			<span id="pgn"></span>
		</div>

		<div id="info" style="margin-top: 10px"></div>
	</body>

	<script src="https://code.jquery.com/jquery-3.5.1.min.js" integrity="sha384-ZvpUoO/+PpLXR1lu4jmpXWu80pZlYUAfxl5NsBMWOEPSjUn/6Z/hRTt8+pR6L4N2" crossorigin="anonymous"></script>
	<script src="https://unpkg.com/@chrisoakman/chessboardjs@1.0.0/dist/chessboard-1.0.0.min.js" integrity="sha384-8Vi8VHwn3vjQ9eUHUxex3JSN/NFqUg3QbPyX8kWyb93+8AC/pPWTzj+nHtbC5bxD" crossorigin="anonymous"></script>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/chess.js/0.10.2/chess.js" integrity="sha384-s3XgLpvmHyscVpijnseAmye819Ee3yaGa8NxstkJVyA6nuDFjt59u1QvuEl/mecz" crossorigin="anonymous"></script>

	<script src="js/chess.js"></script>
</html>