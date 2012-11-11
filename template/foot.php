<?php 
require_once('classes/utilities/Server.php');
use \utilities\Server;
?>
</div>
	<footer>
		<div class="shadow"></div>
		<div id="sponsors">
			<div class="sponsor">
				<img src="<?php echo Server::getServerRoot(); ?>img/bcg.jpg" />
			</div>
			<div class="sponsor">
				<img src="<?php echo Server::getServerRoot(); ?>img/accuracy.jpg" />
			</div>
			<div class="sponsor">
				<img src="<?php echo Server::getServerRoot(); ?>img/atkearney.jpg" />
			</div>
			<div class="sponsor">
				<img id="pernodricard" src="<?php echo Server::getServerRoot(); ?>img/pernodricard.jpg" />
			</div>
		</div>
		<div class="right">
			<img id="lapin" onclick="handleRabbitClick" src="<?php echo Server::getServerRoot(); ?>img/lapin.png" />
			<!-- <div id="copyright">Copyright Quentin Fiard 2012</div> -->
		</div>
	</footer>
	<canvas id="canvas"></canvas>
	<audio id="sound" preload="auto">
  		<source src="<?php echo Server::getServerRoot(); ?>sound/blah.wav" type="audio/wav"></source>
	</audio>
</body>
</html>