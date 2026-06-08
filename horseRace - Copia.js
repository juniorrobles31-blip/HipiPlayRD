/// HorseRace
// define variables
var canvas;
var context;
var horse = [];
var SPEED = 0;
var images_loaded = 0;
var init = false;
var fps = 30;
var loading = "loading...";
var horse_index = 0;
var horse_colors = [
	[255,255,255],
	[ 50, 50, 50],
	[150,100, 50],
	[255,255,255],
	[100,100,100],
	[155,100,0],
	[255,255,255]
	]; 
var pant_colors = [
	[255,255,255],
	[10,10,10],
	[255, 255, 255],
	[55,250,55],
	[200,55,55],
	[255,255,255],
	[55,55,255]
	];
var jaket_colors = [
	[255,255,255],
	[250,50,50],
	[0, 0, 200],
	[0,50,0],
	[255,255,100],
	[255,155,0],
	[55,55,55]
];

var SYSTEM = {
	section : 1, 
	SPD : 3,
	race_started : false,
	show_winner  : false,
	winner : 0
};

var imgs  = {
	terrain     : loadImage("images/horseRace/background_terrain.png"),
	forest      : loadImage("images/horseRace/background_forest.png"),
	sky         : loadImage("images/horseRace/background_sky.jpg"),
	valla_front : loadImage("images/horseRace/background_valla_frontal.png"),
	valla     	: loadImage("images/horseRace/background_valla.png"),
	grass       : loadImage("images/horseRace/background_grass.png"),
	//horse 	    : loadImage("images/horseRace/sprite_horse.png"),
	horse  	: [
		loadImage("images/horseRace/sprite_horse_1.png"),
		loadImage("images/horseRace/sprite_horse_2.png"),
		loadImage("images/horseRace/sprite_horse_3.png"),
		loadImage("images/horseRace/sprite_horse_4.png"),
		loadImage("images/horseRace/sprite_horse_5.png"),
		loadImage("images/horseRace/sprite_horse_6.png")
	],
	meta  : loadImage("images/horseRace/sprite_mask.png"),
	audio : loadAudio("audio/galloping.m4a")
};
var totalImages = Object.keys(imgs).length + Object.keys(imgs.horse).length -1; 

function loadImage(path) {
	"use strict";
	//var a = path.split("/");
	//loading = a[Object.keys(a).length-1];
    var image    = new Image();
	image.onload = function() {images_loaded++;};
	image.src = path;
	return image;
}

function loadAudio(path){
	"use strict";
	//var a = path.split("/");
	//loading = a[Object.keys(a).length-1];
    var audio    = new Audio();
	audio.onloadeddata = function() {
		images_loaded++;
		//imgs.audio.play();
		};
	audio.src = path;
	audio.loop = true;
	return audio;
}

// onLoad
$(function() {
	"use strict";
	canvas  = document.getElementById("canvas");
	context = canvas.getContext("2d");
	game_update();
});

function Sprite(id, x, y){
	"use strict";
	var SELF = {
		id		: id,
		width 	: 302, 
		height	: 202, 
		x		: x, 
		y		: y, 
		xstart	: x,
		race	: 0,
		scale	: 1.0,
		section	: 0,// race
		track	: [0,0,0,0,0,0,0,0,0,0],// race
		image	: new Image(),
		image2	: null,
		image3	: null,
		image4	: null,
		_image_loaded 	: false,
		_frames_by_row 	: 0,
		_frames_rows 	: 0,
		_frame_current 	: 0,
		_counter 		: 0,
		animation 		: [],
		anim_speed 		: 1
	};
	
	SELF.set_image = function(image){
		SELF.image 			= image;
		SELF.image_loaded 	= true;
		SELF._frames_by_row	= Math.floor(image.width / SELF.width);
		SELF._frames_rows  	= Math.floor(image.height / SELF.height);
	};
	
	SELF.set_images = function(image2,image3,image4){
		horse_index++;
		if (horse_colors[horse_index][0] === 255 && horse_colors[horse_index][1] === 255 && horse_colors[horse_index][2] === 255){
			SELF.image2 = image2;
		}else{
			SELF.image2 = generateTintImage(image2, generateRGBKs(image2), horse_colors[horse_index][0], horse_colors[horse_index][1], horse_colors[horse_index][2]);
		}
		if (pant_colors[horse_index][0] === 255 && pant_colors[horse_index][1] === 255 && pant_colors[horse_index][2] === 255){
			SELF.image3 = image3;
		}else{
			SELF.image3 = generateTintImage(image3, generateRGBKs(image3), pant_colors[horse_index][0], pant_colors[horse_index][1], pant_colors[horse_index][2]);
		}
		if (jaket_colors[horse_index][0] === 255 && jaket_colors[horse_index][1] === 255 && jaket_colors[horse_index][2] === 255){
			SELF.image4 = image4;
		}else{
			SELF.image4 = generateTintImage(image4, generateRGBKs(image4), jaket_colors[horse_index][0], jaket_colors[horse_index][1], jaket_colors[horse_index][2]);
		}
		SELF.image_loaded 	= true;
		SELF._frames_by_row	= Math.floor(image2.width  / SELF.width);
		SELF._frames_rows  	= Math.floor(image2.height / SELF.height);
	};
	
	SELF.load_image = function(image_file){
		SELF._image_loaded 	= false;
		SELF.image.src 		= image_file;		
		SELF.image.onload 	= function() {	
			SELF.image_loaded 	= true;	
			SELF._frames_by_row	= Math.floor(SELF.image.width / SELF.width);
			SELF._frames_rows  	= Math.floor(SELF.image.height / SELF.height);
		};
	};
	
	SELF.set_anim = function(anim_speed, frame_start, frame_end){
		SELF.anim_speed = anim_speed;
		SELF.animation = [];
		for (var frameNumber = frame_start; frameNumber <= frame_end; frameNumber++){
			SELF.animation.push(frameNumber);
		}	
	};
	
	SELF.draw = function() {
		//console.log("draw horse "+id);
		if (SYSTEM.race_started){
			SELF._counter+= SELF.anim_speed;
			
			if (SYSTEM.section > 0){
			   if (SELF.section < SELF.track[SYSTEM.section]){
				  SELF.section += SYSTEM.SPD;
				  SELF.race    += SYSTEM.SPD;
			   }
			   SELF.x = SELF.xstart + SELF.race;
			}
			
		}else{
			SELF._counter = 0;
		}
		if (SELF._counter >= SELF.animation.length){
		  	SELF._counter = 0;
		}
		SELF._frame_current = Math.floor(SELF._counter);
	  	var row = Math.floor(SELF.animation[SELF._frame_current] / SELF._frames_by_row);
		var col = Math.floor(SELF.animation[SELF._frame_current] % SELF._frames_by_row);
		
		context.drawImage(
				SELF.image,
				col * SELF.width, row * SELF.height,
				SELF.width, SELF.height,
				SELF.x, SELF.y,
				SELF.width*SELF.scale, SELF.height*SELF.scale);
				
		if (SELF.image2 !== null){
			context.drawImage(
					SELF.image2,
					col * SELF.width, row * SELF.height,
					SELF.width, SELF.height,
					SELF.x, SELF.y,
					SELF.width*SELF.scale, SELF.height*SELF.scale);
			context.drawImage(
					SELF.image3,
					col * SELF.width, row * SELF.height,
					SELF.width, SELF.height,
					SELF.x, SELF.y,
					SELF.width*SELF.scale, SELF.height*SELF.scale);
			context.drawImage(
					SELF.image4,
					col * SELF.width, row * SELF.height,
					SELF.width, SELF.height,
					SELF.x, SELF.y,
					SELF.width*SELF.scale, SELF.height*SELF.scale);
		}				
		
		context.font = "18px Arial";
		context.fillStyle = 'white';
		context.fillText(id, SELF.x+130+(id*9), SELF.y+18+(id*3));	
	 };
	
	return SELF;
}

var background = (function() {
	"use strict";
	var sky    = {image: imgs.sky	 , x:0, y:0, speed:0};
	var forest = {image: imgs.forest , x:0, y:256, speed:0};
	var terrain= {image: imgs.terrain, x:0, y:320, speed:0};
	var grass  = {image: imgs.grass  , x:0, y:320};
	var valla  = {image: imgs.valla  , x:0, y:310, speed:0};
	var valla_front = {image: imgs.valla_front, x:0, y:620, speed:0};
	var meta   = {image: imgs.meta, x:-300, y:370, speed:0};
	var dark   = false;

	var draw = function(foreground) {
		if (!foreground){
			draw_tile(sky.image,  sky.x, sky.y);
			draw_tile(forest.image, forest.x, forest.y);
			draw_tile(terrain.image, terrain.x, terrain.y);
			draw_tile(valla.image, valla.x, valla.y);
			context.drawImage(meta.image, meta.x, meta.y);
		}else{
			draw_tile(valla_front.image, valla_front.x, valla_front.y);
		}
		// Pan background
		sky.x 		-= sky.speed;
		forest.x 	-= forest.speed;
		terrain.x 	-= terrain.speed;
		grass.x 	-= grass.speed;
		valla.x 	-= valla.speed;
		valla_front.x -= valla_front.speed;
		
		if (sky.x < -sky.image.width){sky.x=0;}
		if (forest.x < -forest.image.width){forest.x=0;}
		if (terrain.x < -terrain.image.width){terrain.x=0;}
		if (grass.x < -grass.image.width){grass.x=0;}
		if (valla.x < -valla.image.width){valla.x=0;}
		if (valla_front.x < -valla_front.image.width){valla_front.x=0;}
		
		if (dark){
			context.fillRect(0,0,canvas.width,canvas.height);
		}
	};
	/**
	* Reset background to zero
	*/
	var reset = function()  {
		sky.speed 		= SPEED/8;
		forest.speed 	= SPEED/7;
		terrain.speed 	= SPEED/6;
		grass.speed 	= SPEED/6;
		valla.speed 	= SPEED/5;
		valla_front.speed = SPEED;
		SYSTEM.section  = 1;
	};
	
	return {
		draw: draw,
		reset: reset,
		meta : meta,
		dark : dark
	};
})();

function generateRGBKs( img ) {
	"use strict";
	var w = img.width;
	var h = img.height;
	var rgbks = [];

	var canvas = document.createElement("canvas");
	canvas.width = w;
	canvas.height = h;
	
	var ctx = canvas.getContext("2d");
	ctx.drawImage( img, 0, 0 );
	
	var pixels = ctx.getImageData( 0, 0, w, h ).data;

	// 4 is used to ask for 3 images: red, green, blue and
	// black in that order.
	for ( var rgbI = 0; rgbI < 4; rgbI++ ) {
		canvas = document.createElement("canvas");
		canvas.width  = w;
		canvas.height = h;
		
		ctx = canvas.getContext('2d');
		ctx.drawImage( img, 0, 0 );
		var to = ctx.getImageData( 0, 0, w, h );
		var toData = to.data;
		
		for (
				var i = 0, len = pixels.length;
				i < len;
				i += 4
		) {
			toData[i  ] = (rgbI === 0) ? pixels[i  ] : 0;
			toData[i+1] = (rgbI === 1) ? pixels[i+1] : 0;
			toData[i+2] = (rgbI === 2) ? pixels[i+2] : 0;
			toData[i+3] =                pixels[i+3]    ;
		}
		
		ctx.putImageData( to, 0, 0 );
		
		// image is _slightly_ faster then canvas for this, so convert
		var imgComp = new Image();
		imgComp.src = canvas.toDataURL();
		
		rgbks.push( imgComp );
	}

	return rgbks;
}

function generateTintImage( img, rgbks, red, green, blue ) {
	"use strict";
	var buff = document.createElement( "canvas" );
	buff.width  = img.width;
	buff.height = img.height;
	
	var ctx  = buff.getContext("2d");

	ctx.globalAlpha = 1;
	ctx.globalCompositeOperation = 'copy';
	ctx.drawImage( rgbks[3], 0, 0 );

	ctx.globalCompositeOperation = 'lighter';
	if ( red > 0 ) {
		ctx.globalAlpha = red   / 255.0;
		ctx.drawImage( rgbks[0], 0, 0 );
	}
	if ( green > 0 ) {
		ctx.globalAlpha = green / 255.0;
		ctx.drawImage( rgbks[1], 0, 0 );
	}
	if ( blue > 0 ) {
		ctx.globalAlpha = blue  / 255.0;
		ctx.drawImage( rgbks[2], 0, 0 );
	}

	return buff;
}

function onRaceStart(place_1,place_2,place_3,place_4,place_5,place_6){
	"use strict";
	if (!init){
		setTimeout(function(){
			onRaceStart(place_1,place_2,place_3,place_4,place_5,place_6);
		},500);
		return;
	}
	// Order 
    for (var i=1; i<7; i++){
        var value = 500-i*64;
        for (var ii=1; ii<=10; ii++){
            var v = Math.random()*(value/(10-ii));
            if (ii === 10){v = value;}
            value -= v;
			switch (i){
				case 1: horse[place_1].track[ii] = v; break;
				case 2: horse[place_2].track[ii] = v; break;
				case 3: horse[place_3].track[ii] = v; break;
				case 4: horse[place_4].track[ii] = v; break;
				case 5: horse[place_5].track[ii] = v; break;
				case 6: horse[place_6].track[ii] = v; break;
			}
        }        
    }
	SPEED 	= 20;
	onRaceReset();
	SYSTEM.race_started = true;
	SYSTEM.winner	= place_1;
}

function onRaceReset(){
	"use strict";
	for (var i=1; i < 7; i++){
		horse[i].x		= horse[i].xstart;
		horse[i].race	= 0;
		horse[i].section= 1;
	}
	SYSTEM.winner	= null;
	SYSTEM.section 	= 1;
	SYSTEM.show_winner  = false;
	background.reset();
	background.meta.x	 = canvas.width + 250;
	background.dark = false;
}

function onRaceEnd(){
	"use strict";
	SPEED = 0;
	SYSTEM.race_started = false;
	SYSTEM.show_winner  = true;
	background.reset();	
	getLastResult();
	
	setTimeout("background.dark=true",2000);
	setTimeout(onRaceReset,4000);
}

function draw_tile(image,x, y){
	"use strict";
	for (var i=x; i < canvas.width +image.width ; i+=image.width){
		context.drawImage(image, x+i, y);
	}
}
 
/**
 * Game loop
 */
function game_update() {
	"use strict";
	//----------------------------
	// load resources
	//----------------------------
	if (images_loaded < totalImages){
		context.clearRect(0, 0, canvas.width, canvas.height);
		context.font = "30px Arial";
		context.fillText(loading, 10, 50);	
		context.font = "20px Arial";
		context.fillText("images ["+images_loaded+"/"+totalImages+"]", 10, 80);	
		window.setTimeout(game_update, 100);
		return;
	}
	window.setTimeout(game_update, 1000 / fps);
	//----------------------------
	// Start Game
	//----------------------------
	var i;
	if (!init){
		background.reset();
		for (i=1; i < 7; i++){
			var x = 160 - i*16;
			var y = 180 + 50*i;
			horse[i] = Sprite(i, x, y);
			horse[i].set_image(imgs.horse[i-1]);
			//horse[i].set_images(imgs.horse, imgs.horse_base, imgs.horse_pants, imgs.horse_jaket);
			horse[i].set_anim(1, 0, 20);
			horse[i].scale  = 0.7 + i*(0.3/6);
			horse[i].anim_speed  = 0.5+Math.random();
		}
		init = true;
		//setTimeout('horseColor(1)',10);
		//setTimeout('horseColor(2)',20);
		//setTimeout('horseColor(3)',30);
		//setTimeout('horseColor(4)',40);
		//setTimeout('horseColor(5)',50);
		//setTimeout('horseColor(6)',60);
	}
	//----------------------------
	// step
	//----------------------------
	race_step();	
	//----------------------------
	// draw
	//----------------------------
	background.draw(false);
	for (i=1; i < horse.length; i++){
		horse[i].draw();
	}
	background.draw(true);
	
	if (SYSTEM.show_winner){
		context.font	  = "60px Georgia";
		context.fillStyle = 'black';
		context.fillText($LANG["game.result.winner"], canvas.width/2-90,80);
		context.fillText(SYSTEM.winner, canvas.width/2,130);
		//context.fillStyle = 'gray';
		//context.fillText("Ganador", canvas.width/2-90+3,80+3);
		//context.fillText(SYSTEM.winner, canvas.width/2+3,130+3);
	}
}

function horseColor(i){
	"use strict";
	//var i;
    //for (i=1; i < 7; i++){
		horse[i].set_images(imgs.horse_base, imgs.horse_pants, imgs.horse_jaket);
	//}	
}

function race_step(){
	"use strict";
	if (SYSTEM.race_started){
		if (imgs.audio.paused){
			imgs.audio.play();
		}
		var track_end = 0;
		for (var i=1; i<7; i++){
			if (horse[i].section >= horse[i].track[SYSTEM.section]){
			   track_end += 1;
			}
		}
		
		if (track_end === 6){
		   if (SYSTEM.section < 10){
			  for (i=1; i<7; i++){
				  horse[i].section = 0;
			  }
			  SYSTEM.section++;
		   }else{
		   // Race End
			  background.meta.x -= SPEED;
			  if (background.meta.x < 100){
				 onRaceEnd();
			  }
		   }
		}
		return false;
	}
	if (imgs.audio){
		imgs.audio.pause();
	}
}
