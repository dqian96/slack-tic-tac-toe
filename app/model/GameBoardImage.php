<?php 
	# class for generating board images
	class GameBoardImage {
		private $boardResource;
		private $configs;

		public function __construct($boardLength, $moves, $winData, $squareLength) {
			$configs = include('../resources/server-config.php');

			# create a new image resource
			$boardResource = imagecreatetruecolor($boardLength * $squareLength, $boardLength * $squareLength);
			
			# define colors to be used
			$white = imagecolorallocate($boardResource, 255, 255, 255);
			$black = imagecolorallocate($boardResource, 0, 0, 0);
			$green = imagecolorallocate($boardResource, 153, 255, 153);

			# fill the image with white
			imagefill($boardResource, 0, 0, $white);

			# draw the game grid with black
			drawGrid($squareLength, $black);

			# draw the moves, using green for winning moves
			drawMoves($squareLength, $moves, $winData);



		}

		# create an image from the image resources and return the path
		public outputImage() {
			header('Content-Type: image/png');
			$outputPath = $_SERVER['DOCUMENT_ROOT'] + 'public/images/' . configs['boardImageName'];
			imagepng($boardResource, $outputPath);
			return $outputPath;
		}

		private drawGrid($squareLength, $color) {
			# draw the board grid
			$point1 = array(0, 0);
			$point2 = array(0, imagesy($boardResource));
			for ($i = $squareLength; $i < imagesx($boardResource); $i += $squareLength) {
				$point1[0] = $i;
				$point2[0] = $i;
				imageline($boardResource, point1[0], point1[1], point2[0], point2[1], $color);
				imageline($boardResource, point1[1], point1[0], point2[1], point2[0], $color);
			}
		}

		# draw the moves played on the board
		private drawMoves($squareLength, $moves, $winData, $winningColor, $normalColor){
			# Fill x% of the square width and length with an X or O
			# For example, if it is 0.8, then there'll be a 10% margin of all sides
			$percentLengthFill = 0.8;
			$markerLength = $squareLength * $percentLengthFill;

			for ($position = 0; $position < count($moves), $position++) {
			 	if ($moves[position] != 0) {
			 		$center = findSquareCenter($position, $squareLength, $boardLength);
			 		$color = (isWinningMove($position, $boardLength, $winData) ? $winningColor : $normalColor);
			 		if ($moves[i] == -1) {
			 			# draw a circle at coordinate center
			 			imageellipse ($boardResource , $center[0] , $center[1] , $markerLength, $markerLength , $color )
			 		} else {
			 			# draw a cross at coordinate center
			 			drawCross($center, $markerLength, $color);
			 		}
			 	}
			 }
		}

		# find the coordinates in the ceneter of a square (move slot)
		private findSquareCenter($position, $squareLength, $boardLength) {
			# determine row and column of position (index)
			$row = $position / $boardLength;
			$column = $position % $boardLength;

			# determine center of square
			$cx = $squareLength/2 + $row * $squareLength;
			$cy = $squareLength/2 + $column * $squareLength;

			return array($cx, $cy);
		}

		# determine if a move/position is a winning move
		private isWinningMove($position, $boardLength, $winData) {
			$row = $position / $boardLength;
			$column = $position % $boardLength;
			if 
				(
				$row == $winData['row'] or 
				$column == $winData['column'] or
				($winData['diagonalLR'] != -1 and $position % $winData['diagonalLR'] == 0) or 
				($winData['diagonalRL'] != -1 and $position % $winData['diagonalRL'] == 0) 
				) 
			{
				return true;
			}

			return false; 
		}

		# draw an X given a center position
		private drawCross($center, $markerLength, $color) {
			# draw the L-R line
			$point1 = array( $center[0] - $markerLength/2, $center[1] - $markerLength/2);
			$point2 = array( $center[0] + $markerLength/2, $center[1] + $markerLength/2);		
			imageline($boardResource, point1[0], point1[1], point2[0], point2[1], $color);

			# draw the R-L line
			$point1 = array( $center[0] - $markerLength/2, $center[1] + $markerLength/2);
			$point2 = array( $center[0] + $markerLength/2, $center[1] - $markerLength/2);		
			imageline($boardResource, point1[0], point1[1], point2[0], point2[1], $color);
		}



	}




?>