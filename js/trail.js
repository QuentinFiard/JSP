	
	/**
	 * With love.
	 * http://hakim.se/experiments/
	 * http://twitter.com/hakimel
	 */
	
	var SCREEN_WIDTH = 900;
	var SCREEN_HEIGHT = 600;
	
	var RADIUS = 110;
	
	var RADIUS_SCALE = 1;
	var RADIUS_SCALE_MIN = 1;
	var RADIUS_SCALE_MAX = 1.5;
	
	// The number of particles that are used to generate the trail
	var QUANTITY = 25;

	var canvas;
	var context;
	var particles;
	
	var mouseX = window.innerWidth;
	var mouseY = window.innerHeight;
	var mouseIsDown = false;

	function trail_init() {

		canvas = document.getElementById('canvas');
		
		if (canvas && canvas.getContext) {
			context = canvas.getContext('2d');
			
			// Register event listeners
			document.addEventListener('mousemove', documentMouseMoveHandler, false);
			document.addEventListener('mousedown', documentMouseDownHandler, false);
			document.addEventListener('mouseup', documentMouseUpHandler, false);
			canvas.addEventListener('touchstart', canvasTouchStartHandler, false);
			canvas.addEventListener('touchmove', canvasTouchMoveHandler, false);
			window.addEventListener('resize', windowResizeHandler, false);
			
			createParticles();
			
			windowResizeHandler();
			
			setInterval( loop, 1000 / 60 );
		}
	}

	function createParticles() {
		particles = [];
		for (var i = 0; i < QUANTITY; i++) {
			var alpha = Math.random();
			var particle = {
				position: { x: mouseX, y: mouseY },
				shift: { x: mouseX, y: mouseY },
				size: 1,
				angle: 0,
				speed: 0.01+Math.random()*0.04,
				targetSize: 1,
				fillColor: '#' + Math.floor(alpha*0x35 + (1-alpha)*0xFF).toString(16) + Math.floor(alpha*0x72 + (1-alpha)*0xFF).toString(16) + Math.floor(alpha*0xaf + (1-alpha)*0xFF).toString(16),
				orbit: RADIUS*.5 + (RADIUS * .5 * Math.random())
			};
			
			particles.push( particle );
		}
	}

	function documentMouseMoveHandler(event) {
		mouseX = event.clientX;
		mouseY = event.clientY;
	}
	
	function documentMouseDownHandler(event) {
		mouseIsDown = true;
	}
	
	function documentMouseUpHandler(event) {
		mouseIsDown = false;
	}

	function canvasTouchStartHandler(event) {
		if(event.touches.length == 1) {
			event.preventDefault();

			mouseX = event.touches[0].pageX - window.innerWidth;
			mouseY = event.touches[0].pageY - window.innerHeight;
		}
	}
	
	function canvasTouchMoveHandler(event) {
		if(event.touches.length == 1) {
			event.preventDefault();

			mouseX = event.touches[0].pageX - window.innerWidth;
			mouseY = event.touches[0].pageY - window.innerHeight;
		}
	}
	
	function windowResizeHandler() {
		//SCREEN_WIDTH = window.innerWidth;
		//SCREEN_HEIGHT = window.innerHeight;
		
		canvas.width = $('body').width();
		canvas.height = $('body').height();
	}

	function loop() {
		
		if( mouseIsDown ) {
			// Scale upward to the max scale
			RADIUS_SCALE += ( RADIUS_SCALE_MAX - RADIUS_SCALE ) * (0.02);
		}
		else {
			// Scale downward to the min scale
			RADIUS_SCALE -= ( RADIUS_SCALE - RADIUS_SCALE_MIN ) * (0.02);
		}
		
		RADIUS_SCALE = Math.min( RADIUS_SCALE, RADIUS_SCALE_MAX );
		
		// Fade out the lines slowly by drawing a rectangle over the entire canvas
		context.fillStyle = 'rgba(0,0,0,0.05)';
   		context.clearRect(0, 0, context.canvas.width, context.canvas.height);
		
		for (i = 0, len = particles.length; i < len; i++) {
			var particle = particles[i];
			
			var lp = { x: particle.position.x, y: particle.position.y };
			
			// Offset the angle to keep the spin going
			particle.angle += particle.speed;
			
			// Follow mouse with some lag
			particle.shift.x += ( mouseX - particle.shift.x) * (particle.speed);
			particle.shift.y += ( mouseY - particle.shift.y) * (particle.speed);
			
			// Apply position
			particle.position.x = particle.shift.x + Math.cos(i + particle.angle) * (particle.orbit*RADIUS_SCALE);
			particle.position.y = particle.shift.y + Math.sin(i + particle.angle) * (particle.orbit*RADIUS_SCALE);
			
			// Limit to screen bounds
			particle.position.x = Math.max( Math.min( particle.position.x, $('body').width() ), 0 );
			particle.position.y = Math.max( Math.min( particle.position.y, $('body').height() ), 0 );
			
			particle.size += ( particle.targetSize - particle.size ) * 0.05;
			
			// If we're at the target size, set a new one. Think of it like a regular day at work.
			if( Math.round( particle.size ) == Math.round( particle.targetSize ) ) {
				particle.targetSize = 1 + Math.random() * 7;
			}
			
			context.beginPath();
			context.fillStyle = particle.fillColor;
			context.strokeStyle = particle.fillColor;
			context.lineWidth = particle.size;
			context.moveTo(lp.x, lp.y);
			context.lineTo(particle.position.x, particle.position.y);
			context.stroke();
			context.arc(particle.position.x, particle.position.y, particle.size/2, 0, Math.PI*2, true);
			context.fill();
		}
	}
	
$(document).ready(function(){
	trail_init();
});