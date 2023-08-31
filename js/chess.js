// https://chessboardjs.com/examples#5000

// NOTE: this example uses the chess.js library:
// https://github.com/jhlywa/chess.js

var board = null
var game = new Chess()

var $color = $('#color')

var $new = $('#new')
var $flip = $('#flip')
var $undo = $('#undo')
var $analyze = $('#analyze')

var $engine = $('#engine')
var $depth = $('#depth')
var $moveTime = $('#moveTime')

var $fen = $('#fen')
var $fenSet = $('#fenSet')

var $status = $('#status')
var $pgn = $('#pgn')
var $info = $('#info')

function updateStatus () {
  var status = ''

  var moveColor = 'White'

  if (game.turn() === 'b') {
    moveColor = 'Black'
  }

  if (game.in_checkmate()) { // checkmate?
    status = 'Game over, ' + moveColor + ' is in checkmate.'
  }
  else if (game.in_draw()) { // draw?
    status = 'Game over, drawn position'
  }
  else { // game still on
    status = moveColor + ' to move'

    if (game.in_check()) { // check?
      status += ', ' + moveColor + ' is in check'
    }
  }

  $fen.val(game.fen())
  $status.html(status)
  $pgn.html(game.pgn())
}

function computerMove (analyze = false) {
  if (game.game_over()) return

  $info.html('the computer thinks ...')

  $.ajax({
    url: 'chess.php',
    cache: false,
    data: {
	  engine: $engine.val(),
      fen: game.fen(),
	  depth: $depth.val(),
      movetime: $moveTime.val()
    },
    dataType: 'text',
    timeout: 60000, // 1 min.
  })
  .done(function (data, textStatus, jqXHR) {
    $info.html(data)

    if (analyze === true) return

    var pos = null
	var bestMove = null

    if ((pos = data.search(/bestmove \(none\)/)) !== -1) {
		return
	}
    else if ((pos = data.search(/bestmove [a-h][0-9][a-h][0-9][nbrq]/)) !== -1) {
		bestMove = data.substr(pos + 9, 5)
	}
    else if (pos = data.search(/bestmove [a-h][0-9][a-h][0-9]/)) {
		bestMove = data.substr(pos + 9, 4)
	}
	else {
		alert('No best move!')

		return
	}

    // see if the move is legal
    var move = game.move(bestMove, { sloppy: true })

    // illegal move
    if (move === null) return 'snapback'

    board.position(game.fen())

    updateStatus()
  })
  .fail(function (jqXHR, textStatus, errorThrown) {
	alert(jqXHR.responseText)
  })
}

function onDragStart (source, piece, position, orientation) {
  // do not pick up pieces if the game is over
  if (game.game_over()) return false

  // only pick up pieces for the side to move
  if ((game.turn() === 'w' && piece.search(/^b/) !== -1) ||
      (game.turn() === 'b' && piece.search(/^w/) !== -1)) {
    return false
  }

  if ($color.val() === 'White') {
    // only pick up pieces for White
    if (piece.search(/^b/) !== -1) return false
  }
  else { // Black
    // only pick up pieces for Black
    if (piece.search(/^w/) !== -1) return false
  }
}

function onDrop (source, target) {
  // see if the move is legal
  var move = game.move({
    from: source,
    to: target,
    promotion: 'q' // NOTE: always promote to a queen for example simplicity
  })

  // illegal move
  if (move === null) return 'snapback'

  updateStatus()

  $info.html('')

  computerMove()
}

// update the board position after the piece snap
// for castling, en passant, pawn promotion
function onSnapEnd () {
  board.position(game.fen())
}

var config = {
  pieceTheme: 'img/chesspieces/wikipedia/{piece}.png',
  draggable: true,
  position: 'start',
  onDragStart: onDragStart,
  onDrop: onDrop,
  onSnapEnd: onSnapEnd
}

board = Chessboard('myBoard', config)

updateStatus()

$color.on('change init', function () {
  if (($color.val() === 'White' && game.turn() === 'b') ||
      ($color.val() === 'Black' && game.turn() === 'w')) {
    computerMove()
  }
}).trigger('init')

$new.on('click', function () {
  game.load('rnbqkbnr/pppppppp/8/8/8/8/PPPPPPPP/RNBQKBNR w KQkq - 0 1') // startpos

  board.position(game.fen())

  updateStatus()

  $info.html('')

  $color.trigger('change')
})

$flip.on('click', function () {
  board.flip()
})

$undo.on('click', function () {
  game.undo()
  game.undo()

  board.position(game.fen())

  updateStatus()

  $info.html('')

  $color.trigger('change')
})

$analyze.on('click', function () {
  $info.html('')

  computerMove(true)
})

$fenSet.on('click', function () {
  game.load($fen.val())

  board.position(game.fen())

  updateStatus()

  $info.html('')

  $color.trigger('change')
})
