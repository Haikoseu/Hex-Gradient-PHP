<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Color Gradient Generator</title>
	<link rel="stylesheet" href="hg.css">
    <script>
        function exportToJson() {
			var colors = [];
			document.querySelectorAll('.color-box').forEach(function(box) {
				var colorCode = box.innerText.trim().match(/[0-9a-fA-F]{6}/)[0];
				colors.push(colorCode);
			});
			var dataStr = "data:text/json;charset=utf-8," + encodeURIComponent(JSON.stringify(colors));
			var downloadAnchor = document.createElement('a');
			downloadAnchor.setAttribute("href", dataStr);
			downloadAnchor.setAttribute("download", "hex-gradient.json");
			document.body.appendChild(downloadAnchor);
			downloadAnchor.click();
			downloadAnchor.remove();
		}
		
		function copyToClipboard(text) {
            var textarea = document.createElement("textarea");
            textarea.textContent = text;
            document.body.appendChild(textarea);

            textarea.select();
            try {
                document.execCommand("copy");
            } catch (e) {
                console.warn("Copy to clipboard failed.", e);
            }

            document.body.removeChild(textarea);
        }
    </script>
</head>
<body>
	<div class="settings bloc">
		<h2>Color Gradient Generator</h2>
		<form method="post">
			<div>
				<label for="startingColor">Starting Color:</label>
				<input type="color" id="startingColor" name="startingColor" value="#0080ff">
			</div>

			<div>
				<label for="endingColor">Ending Color:</label>
				<input type="color" id="endingColor" name="endingColor" value="#00ff00">
			</div>

			<div>
				<label for="numColors">Number of Colors:</label>
				<input type="number" id="numColors" name="numColors" value="5" min="3" max="200">
			</div>

			<div class="buttons">
				<input type="submit" name="submit" value="Generate Gradient">
				<button type="button" id="exportJSON" onclick="exportToJson()">Export to JSON</button>
			</div>
		</form>
	</div>
	<div class="hex-gradient bloc">
		<?php
		function gradient($length, $startingColor, $endingColor) {
			$length = $length - 1;
			$result = [];
			$toadd = [];

			$dr = hexdec(substr($startingColor, 0, 2));
			$dg = hexdec(substr($startingColor, 2, 2));
			$db = hexdec(substr($startingColor, 4, 2));

			$ar = hexdec(substr($endingColor, 0, 2));
			$ag = hexdec(substr($endingColor, 2, 2));
			$ab = hexdec(substr($endingColor, 4, 2));

			$increm = [($ar - $dr) / $length, ($ag - $dg) / $length, ($ab - $db) / $length];

			for($i = 0; $i < 3; $i++) {
				$tl = [];
				for($j = 0; $j < $length; $j++) {
					array_push($tl, $j * $increm[$i]);
				}
				array_push($toadd, $tl);
			}

			for($l = 0; $l < $length; $l++) {
				$fr = $dr + round($toadd[0][$l]);
				$fg = $dg + round($toadd[1][$l]);
				$fb = $db + round($toadd[2][$l]);

				$fr = dechex($fr);
				$fg = dechex($fg);
				$fb = dechex($fb);

				$fr = strlen($fr) < 2 ? '0' . $fr : $fr;
				$fg = strlen($fg) < 2 ? '0' . $fg : $fg;
				$fb = strlen($fb) < 2 ? '0' . $fb : $fb;

				$color = $fr . $fg . $fb;
				array_push($result, $color);
			}

			array_push($result, $endingColor);
			return $result;
		}

		if ($_SERVER["REQUEST_METHOD"] == "POST") {
			$startingColor = ltrim($_POST['startingColor'], '#');
			$endingColor = ltrim($_POST['endingColor'], '#');
			$numColors = $_POST['numColors'];

			if (isset($_POST['submit'])) {
				$colors = gradient($numColors, $startingColor, $endingColor);
				echo "<script>console.log('" . json_encode($colors) . "');</script>";

				$counter = 1;
				foreach($colors as $color) {
					echo "<div class='color-box' style='margin: 5px 0;'>
							<span class='counter'>{$counter} - </span>
							<span class='color' style='display: inline-block; width: 20px; height: 20px; background-color: #{$color};'></span>
							<button type='button' onclick='copyToClipboard(\"#{$color}\")'>#{$color}</button>
						  </div>";
					$counter++;
				}
			}
		}
		?>
	</div>
  </body>
</html>
