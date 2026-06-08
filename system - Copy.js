var GAME_MODE = GAME_MODE || "";

var GLOBAL      = {};
GLOBAL.timeout  = undefined;
GLOBAL.timer    = [];
GLOBAL.winners  = [];
GLOBAL.bid      = undefined;
GLOBAL.RESTANTE = 1;
GLOBAL.NEXT_PLAY= 0;

GLOBAL.countX   = [0,0,0];
GLOBAL.countY   = [0,0,0];
GLOBAL.countZ   = [0,0,0];

GLOBAL.DICE 	= [];
GLOBAL.DICE[1]  = {}; 
GLOBAL.DICE[2]  = {}; 
GLOBAL.DICE[3]  = {};

var SESSION = SESSION || 0;

var $progressBar;
var tab_eula, tab_user, tab_user2, tab_welcome;

var AUDIO = {};
var ZUZUVAMA = ZUZUVAMA || {};

var $LANG = $LANG || undefined; 

function $_GET(index, url) {
	"use strict";
	url = url ? url : window.location.search;
	var re = new RegExp('&' + index + '(?:=([^&]*))?(?=&|$)', 'i');
	return (url = url.replace('?', '&').match(re)) ? (typeof url[1] === 'undefined' ? '' : decodeURIComponent(url[1])) : undefined;
}

$(function() {
	"use strict";
	//document.oncontextmenu = function(){return false;};
	
	$(window).bind('resize', function () { 
		onOrientationChange();
	});
	$(window).bind('resize', function () { 
		onOrientationChange();
	});
	
	$(document).on("pagecreate",function(event){
		//console.log(event);
		//alert("reload");		
		//location.reload(false);
		window.location.assign(event.currentTarget.baseURI);
		onOrientationChange();
	});
	onOrientationChange();
	
	$(document).on('pagehide', function (e) {
		var page = $(e.target);
		if (!$.mobile.page.prototype.options.domCache && (!page.attr('data-dom-cache') || page.attr('data-dom-cache') === "false")) {
			page.remove();
		}
	});
	
	setTimeout(onCreate,100);
	
	var $progressDiv = $("div#progressBar");
	if ($progressDiv.progressStep !== undefined){
		$progressBar = $progressDiv.progressStep({ 
			activeColor: "#54a700",
			//strokeColor: "black",
			fillColor: "white",
			visitedFillColor: "#B0FFB0",
			margin : 8,
			radius : 24,
			//labelOffset: 12,
			"font-size" : 16
		});		
		
		tab_eula = $progressBar.addStep($LANG["start"]);
		tab_eula.onClick = function(){
			//alert(tab_eula.index);
			$('div#eula').show(1000);
			$('div#registro').hide(1000);
			$('div#registro2').hide(1000);
			$('div#welcome').hide(1000);
			return true;
		};
		tab_user = $progressBar.addStep($LANG["user"]);
		tab_user.onClick = function(){
			//alert(tab_user.index + " - " + $progressBar.getCurrentStep());
			if (tab_user.visited){//  tab_user.index < $progressBar.getCurrentStep()){
				$('div#eula').hide(1000);
				$('div#registro').show(1000);
				$('div#registro2').hide(1000);
				$('div#welcome').hide(1000);
				return true;
			}else{
				return false;
			}
		};
		tab_user2 = $progressBar.addStep($LANG["data"]);
		tab_user2.onClick = function(){
			if (tab_user2.visited){//tab_user2.index < $progressBar.getCurrentStep()){
				$('div#eula').hide(1000);
				$('div#registro').hide(1000);
				$('div#registro2').show(1000);
				$('div#welcome').hide(1000);
				return true;
			}else{
				return false;
			}
		};
		tab_welcome = $progressBar.addStep($LANG["finish"]);
		tab_welcome.onClick = function(){
			if (tab_welcome.visited){//tab_user2.index < $progressBar.getCurrentStep()){
				$('div#eula').hide(1000);
				$('div#registro').hide(1000);
				$('div#registro2').hide(1000);
				$('div#welcome').show(1000);
				return true;
			}else{
				return false;
			}
		};
		
		$progressBar.setClickEnabled(true);
		$progressBar.refreshLayout();  
		$progressBar.setCurrentStep(0); 
	}	
	
});

function onCreate(){
	"use strict";
	//alert("pagecreate event fired! GAME_MODE: " + GAME_MODE);
	//getBalance();
	
	switch (GAME_MODE){
	case "horse":
	case "puntazo":
	case "conoce":
		getLastResult();
	break;
	case "roulette":
		AUDIO.roulette = {};
		AUDIO.roulette.end  = new Audio('audio/roulette_end.m4a');
		AUDIO.roulette.roll = new Audio('audio/roulette_roll.m4a');
		AUDIO.roulette.roll.addEventListener('error', function(ev) {
			  console.log(ev);
			  AUDIO.roulette.roll = new Audio('audio/dice_roll.m4a');
			}, false);
		
		//$('img#roulette_base').css('max-height','70%');
		//$('img#roulette_base').css('min-height','70%');
		//$('img#roulette_top').css('max-height','70%');
		//$('img#roulette_top').css('min-height','70%');
	
		resetRoulette();
		roulette_size();
		getLastResult();
		//roulette_roll();
	break;
	case "dice.1":
	case "dice.2":
	case "dice.3":		
		AUDIO.dice = {};
		AUDIO.dice.shake = new Audio('audio/dice_shake.m4a');
		AUDIO.dice.roll  = new Audio('audio/dice_roll.m4a');
		
		$('#cube1').css('webkitTransform', "rotateX(45deg) rotateY(0deg) rotateZ(45deg)" );
		$('#cube1').css('transform'      , "rotateX(45deg) rotateY(0deg) rotateZ(45deg)" );
		$('#cube2').css('webkitTransform', "rotateX(45deg) rotateY(0deg) rotateZ(45deg)" );
		$('#cube2').css('transform'      , "rotateX(45deg) rotateY(0deg) rotateZ(45deg)" );
		$('#cube3').css('webkitTransform', "rotateX(45deg) rotateY(0deg) rotateZ(45deg)" );
		$('#cube3').css('transform'      , "rotateX(45deg) rotateY(0deg) rotateZ(45deg)" );
		
		//setScale("area1",2.0);
		var width;
		var scale;
		if (GAME_MODE === "dice.3"){
			$("div#area1").css('width',"25%");
			$("div#area2").css('width',"25%");
			$("div#area3").css('width',"25%");
			
			width = $("div#area1").width();			
			scale = width/320;
			
			setScale("div#area1", scale);
			setScale("div#area2", scale);
			setScale("div#area3", scale);
			$("div#area1").css('margin-left' ,0);//-width/5			
			$("div#area2").css('margin-left' ,width/5);			
			$("div#area2").css('margin-right',width/5);
			
			$("div#area1").css('margin-top',width/2);
		}else{
			width = dice_size();			
			scale = width/400;
			setScale("div#area1", scale);
			$("div#area1").css('margin-top',width/4);
		}
		
		onChange($("div#checkbox-1a"));
		var radius = 60;
		$('.side').css('border-radius', radius);
		getLastResult();
	break;
	}
	ScaleContentToDevice();
	timer();
}

function getBalance(){
	"use strict";
	if (SESSION === 0){
		return false;
	}
	$.ajax({
		type 	 : "POST",
		dataType : "json",
		url 	 : "system.php",
		async	 : true,
		data : {
			service : "zzvm.balance"
		},
		success : function (json) {		
			if (json.STATUS === "OK") {
				//alert(JSON.stringify(json));
				$('i#balance_refresh').hide(200);
				$('i#balance').show(400);
				
				$('span#balance').html(json.balance);
				$('span#balance2').html(json.balance);
			}else{
				$('span#balance').html(0);
				$('span#balance2').html(0);
				//console.log("ErrorCode 216: "+json.STATUS + " : " + json.INFO);
				//alert("ErrorCode 209: "+json.STATUS + " : " + json.INFO);
			}
		},
		error : function (xhr, status) {
			alert("ErrorCode 221: " + status + " " + JSON.stringify(xhr));
		}
	});
}

function dice_size(){ 
	"use strict";
	// windows size;
	var max_size = 0.80;// 70%
	var width  = $(window).width() * max_size;
	var height = $(window).height()* max_size;
	
	if (width > height){width = height;}else{height = width;}
	return width;
}
//------------------------------------------------
// setFocus
//------------------------------------------------
function setFocus(element, delay){
	"use strict";
	if (delay !== undefined){
		setTimeout('$("'+element+'").focus();', delay);
	}else{
		$(element).focus();
	}
}

function seconds2time(seconds) {
	"use strict";
	if (seconds < 0){
		return "[--:--:--]";
	}
	
	var days    = Math.floor(seconds / 86400);
	var hours   = Math.floor((seconds % 86400) / 3600);
	var minutes = Math.floor(((seconds % 86400) % 3600) / 60);
	var second  = ((seconds % 86400) % 3600) % 60;
	
	var d = (days > 0) ? days+" dias<br>" : "";
	var m = (minutes < 10) ? "0" + minutes : String(minutes);
	var s = (second < 10) ? "0" + second : String(second);
	
	return d + hours + ":" + m + ":" + s;

	// OLD
	/*
	var hours   = Math.floor(seconds / 3600);
	var minutes = Math.floor((seconds - (hours * 3600)) / 60);
	seconds = seconds - (hours * 3600) - (minutes * 60);
	var time    = "";
	
	if (hours !== 0) {
		time = hours + ":";
	}
	if (minutes !== 0 || time !== "") {
		minutes = (minutes < 10 && time !== "") ? "0" + minutes : String(minutes);
		time += minutes + ":";
	}
	if (time === "") {
		time = seconds + "s";
	} else {
		time += (seconds < 10) ? "0" + seconds : String(seconds);
	}
	return time;
	*/
}

function onOrientationChange(){
	"use strict";
    switch(window.orientation) {  
      case -90:
      case 90:
        //alert('landscape');
		//ScaleContentToDevice();
        break; 
      default:
        //alert('portrait');
		//ScaleContentToDevice();
        break; 
    }
	var w = $(".cardView").outerWidth();
	var h = (w/1080)*540;
	$(".cardView").height(h);
  }

window.addEventListener('orientationchange', onOrientationChange);

function ScaleContentToDevice(){
	"use strict";
    scroll(0, 0);
    if ($.mobile){
		var content = $.mobile.getScreenHeight() - $(".ui-header").outerHeight() - $(".ui-footer").outerHeight() - $(".ui-content").outerHeight() + $(".ui-content").height();
	   $(".ui-content").height(content);
	}
}

function setScale(element, scale){	
	"use strict";
	$(element).css('transform',		'scale('+scale+', '+scale+')');
	$(element).css('webkit-transform','scale('+scale+', '+scale+')');
	$(element).css('-webkit-transform','scale('+scale+', '+scale+')');
	$(element).css('-moz-transform','scale('+scale+', '+scale+')');
	$(element).css('-ms-transform',	'scale('+scale+', '+scale+')');
	$(element).css('-o-transform',	'scale('+scale+', '+scale+')');
}

function unCheck(element){
	"use strict";
	//$(element).removeAttr('checked');
	$(element).prop('checked', false);
	$(element).prop('data-cacheval', false);
	
	var clas = $(element).prev().attr('class');
	if (clas){
		clas = clas.replace('ui-checkbox-on','ui-checkbox-off');
		clas = clas.replace('ui-radio-on','ui-radio-off');
		clas = clas.replace('ui-focus','');
		$(element).prev().attr('class', clas);
	}
}

function isPar(input){ 
	"use strict";
	if (input % 2 === 0){ 
		return true; 
	}else{ 
		return false; 
	} 
}

function onChange(event){
	"use strict";
	$("div#contador").html("");
	GLOBAL.DICE[1].play = 0;
	GLOBAL.DICE[2].play = 0;
	GLOBAL.DICE[3].play = 0;
	var i;
	
	// count seleted
	var count 	 = 0;
	for (i=1; i<7; i++){
		if ($("input#checkbox-"+i+"a").is(':checked')){
			count++;
		}	
	}
	// TODO: limit to 3
	if (count < 1){
		switch (GAME_MODE){
		case "roulette":
			if ($("input#color-1").is(':checked') || $("input#color-2").is(':checked') ){
				$("div#contador").html("");
			}else{
				$("div#contador").html($LANG["game.error.color1"]);//?
			}
			//$("div#contador").html($LANG["game.error.color1"]);
		break;
		case "horse":
			if ($("input#horse-1").is(':checked')){
				$("div#contador").html($LANG["game.error.horse1"]);
			}else{
				$("div#contador").html($LANG["game.error.horse3"]);//?
			}
		break;
		case "dice.2":
			$("div#contador").html($LANG["game.error.dice3"]);//seleccion 3 nm_ones
		break;
		default:
			$("div#contador").html($LANG["game.error.dice1"]);
		break;
		}
	}else if (count > 0){
		// Limit
		switch (GAME_MODE){
		case "dice.1":
			if (count > 1){
				$("div#contador").html($LANG["game.error.only1"]);
				$(event).prop('checked', false);
			}
		break;
		case "dice.2":
			if (count > 3){
				$("div#contador").html($LANG["game.error.only3"]);
				$(event).prop('checked', false);
			}
		break;
		case "dice.3":
			if (count > 1){
				$("div#contador").html($LANG["game.error.only1"]);
				$(event).prop('checked', false);
			}
		break;
		case "horse":
			if ($("input#horse-1").is(':checked')){// 1 caballo
				if (count > 1){
					$("div#contador").html($LANG["game.error.only1"]);
					$(event).prop('checked', false);
				}
			}else if ($("input#horse-2").is(':checked')){// 3 caballos
				if (count > 3){
					$("div#contador").html($LANG["game.error.only3"]);
					$(event).prop('checked', false);
				}
			}
		break;
		}
	}
	// X selected
	for (i=1; i<7; i++){
		if ($("input#checkbox-"+i+"a").is(':checked')){
			switch (GAME_MODE){
			case "dice.1":
				if (GLOBAL.DICE[1].play === 0){GLOBAL.DICE[1].play = i; break;}
			break;
			case "dice.2":
			case "horse":
				if (GLOBAL.DICE[1].play === 0){GLOBAL.DICE[1].play = i; continue;}
				if (GLOBAL.DICE[2].play === 0){GLOBAL.DICE[2].play = i; continue;}
				if (GLOBAL.DICE[3].play === 0){GLOBAL.DICE[3].play = i; break;}
			break;
			case "dice.3":
				if (GLOBAL.DICE[1].play === 0){GLOBAL.DICE[1].play = i; break;}
			break;
			}
		}
	}
		
	GLOBAL.bid = $('input[name=radio-choice]:checked').val();
	if (GLOBAL.bid === '0'){
		GLOBAL.bid = $("input#radio-choice-9").val();
		$("input#radio-choice-9").show(500);
	}else{
		$("input#radio-choice-9").hide(500);
	}
	// roulette
	if ($('#color-1').is(':checked')){
		GLOBAL.DICE[1].play = $('#color-1').val();
	}else if ($('#color-2').is(':checked')){
		GLOBAL.DICE[1].play = $('#color-2').val();
	}
	if (GLOBAL.DICE[1].play === 0){
		//$("div#contador").html("Seleccione un color");
	}
	return false;
}

var UPDATE = true;

var tip_conoce = [];

tip_conoce[0] = "Conoce mi país";
tip_conoce[1] = "Click to play";
tip_conoce[2] = "3,000 winners";
tip_conoce[3] = "a week in the caribbean";
tip_conoce[4] = "without combinations";
tip_conoce[5] = "Click to play";
tip_conoce[6] = "a social cause";
tip_conoce[7] = "Click to play";
tip_conoce[8] = "You dont need to assert";
tip_conoce[9] = "Only be near to the winner";

var tip_i = 0;

function timer(){
	"use strict";
	if ($("div#timer").length === 0){
		console.log("WARNING 171: No timer defined"); 
		return false;
	}
	
	switch (GAME_MODE){
	case "puntazo":
	case "conoce":
		if (tip_i > tip_puntazo.length){
			tip_i = 0;
		} 
		$('div#puntazo_do').html(tip_puntazo[Math.floor(tip_i)]);
		$('div#conoce').html(tip_conoce[Math.floor(tip_i)]);
		tip_i += 0.5;
		
	break;
	}
	//$("div#play").html("Esperando Resultados...");
	
	//clearTimeout(GLOBAL.timeout);
	//GLOBAL.timeout = setTimeout(timer, 1000);
	setTimeout(timer, 1000);
	
	if (GLOBAL.RESTANTE > 0){
		GLOBAL.RESTANTE--;
	}
	
	if (GLOBAL.RESTANTE < 10){// retry to connect on error
		//GLOBAL.RESTANTE = 0;
	}
	
	if (GLOBAL.RESTANTE === 0 && UPDATE === true){
		UPDATE = false;
		$("div#timer").html('<span style="font-size:0.6em"> '+$LANG["game.result.play"]+' # ' + GLOBAL.NEXT_PLAY + "</span><br> "+$LANG["connecting"]+"...");
		$.ajax({
			type : "POST",
			//contentType: "application/json",
			//contentType: "application/x-www-form-urlencoded",
			dataType : "json",
			url : "system.php",
			async: true,
			data : {
				service : "game.play",
				game    : GAME_MODE
			},
			success : function (json) {
				UPDATE = true;	
				if (json.STATUS === "OK") {
					$("div#play").html("");
					if (json.roll === true){
						$('i#balance_refresh').show(400);
						$('i#balance').hide(200);
					}
					switch (GAME_MODE){
						case "dice.1":
						case "dice.2":
						case "dice.3":
							if (json.roll === true){
								roll(json.nwon1,1);
								roll(json.nwon2,2);
								roll(json.nwon3,3);
								//$("div#play").html("Esperando Resultados...");
							}else{
							   //$("div#play").html("Esperando Próxima jugada");
							}
							GLOBAL.RESTANTE  = json.dif_time;
							GLOBAL.NEXT_PLAY = json.next_play;
						break;
						case "roulette":
							if (json.roll === true){
								GLOBAL.winners[1] = json.win;
								GLOBAL.place	  = json.place;
								// reset roulette
								resetRoulette();
								roulette_roll();
							}
							GLOBAL.RESTANTE  = json.dif_time;
							GLOBAL.NEXT_PLAY = json.next_play;
							//$("div#play").html("Esperando Resultados...");
						break;
						case "horse":
							if (json.roll === true){
								//console.log(json.nwon1 +"-"+ json.nwon2  +"-"+  json.nwon3  +"-"+  json.nwon4 +"-"+ json.nwon5 +"-"+  json.nwon6);
								onRaceStart(json.nwon1, json.nwon2, json.nwon3, json.nwon4, json.nwon5, json.nwon6);
								//$("div#play").html("Esperando Resultados...");
								GLOBAL.RESTANTE  = json.dif_time;
								GLOBAL.NEXT_PLAY = json.next_play;
							}else{
								GLOBAL.RESTANTE  = json.dif_time;
								GLOBAL.NEXT_PLAY = json.next_play;
								//onRaceStart(1, 2, 3, 4, 5, 6);
							}
						break;
						case "puntazo":
							UPDATE = false;
							GLOBAL.RESTANTE  = json.dif_time;
							GLOBAL.NEXT_PLAY = json.next_play;
							
							$('span#sorteo').html(json.next_play);
							$('span#sorteo_next').html(json.next_play);
							//$('strong#pool').html(json.pool);
							//$('strong#super_pool').html(json.super_pool);
							$('span#sorteo_date').html(json.date_last);
							$("div#timer").html("");
						break;
						case "conoce":
							UPDATE = false;
							GLOBAL.RESTANTE  = 0;
							GLOBAL.NEXT_PLAY = json.next_play;
						break;
						default:
							console.error("ErrorCode 380: undefined GAME_MODE ["+GAME_MODE+"]");
						break;
					}
				} else {
					alert("ErrorCode 393" + json.STATUS + " : " + json.INFO);
				}
				
				if (json.next_play !== undefined){
					$("input#play").val(json.next_play);
				}
			},
			error : function (xhr, status) {
				//alert("ErrorCode 244: " + status + " " + JSON.stringify(xhr));
				GLOBAL.RESTANTE = 3;// reconect
				UPDATE = true;
				$("div#play").html("ErrorCode 244: " + status + " " + JSON.stringify(xhr));
			}
		});
	}else{
		var text;
		if (GLOBAL.RESTANTE === 0){
			text = $LANG["game.result.wait"]+"...";
		}else{		
			text = seconds2time(GLOBAL.RESTANTE);
		}
		switch (GAME_MODE){
		case "puntazo":
		
		break;
		default:
			$("div#timer").html('<span style="font-size:0.6em"> '+$LANG["game.result.play"]+' # ' + GLOBAL.NEXT_PLAY + '</span><br>' + text);
		break;
		}
	}
}

function roll(s, dice){
	"use strict";
	if ($("div#timer").length){
		AUDIO.dice.shake.play();
	}

	GLOBAL.DICE[1].end = false; 
	GLOBAL.DICE[2].end = false; 
	GLOBAL.DICE[3].end = false; 
	
	if (s === undefined){ return false;}
	// reset
	$("div#dice1_"+dice).css("background-color","transparent");
	$("div#dice2_"+dice).css("background-color","transparent");
	$("div#dice3_"+dice).css("background-color","transparent");
	$("div#dice4_"+dice).css("background-color","transparent");
	$("div#dice5_"+dice).css("background-color","transparent");
	$("div#dice6_"+dice).css("background-color","transparent");
	
	$("div#play").html("");
	
	if (GLOBAL.timer[dice] !== undefined){
		clearTimeout(GLOBAL.timer[dice]);
	}
	GLOBAL.DICE[dice].xAngle = 45;
	GLOBAL.DICE[dice].yAngle = 0;
	GLOBAL.DICE[dice].zAngle = 45;
	
	GLOBAL.countX[dice] = 2;
	GLOBAL.countY[dice] = 1;
	GLOBAL.countZ[dice] = 2;

	$('#cube'+dice).css('webkitTransform', "rotateX("+GLOBAL.DICE[dice].xAngle+"deg) rotateY("+GLOBAL.DICE[dice].yAngle+"deg) rotateZ("+GLOBAL.DICE[dice].zAngle+"deg)" );
	$('#cube'+dice).css('transform', "rotateX("+GLOBAL.DICE[dice].xAngle+"deg) rotateY("+GLOBAL.DICE[dice].yAngle+"deg) rotateZ("+GLOBAL.DICE[dice].zAngle+"deg)" );
	
	// Animate
	animate(s, dice);
}

function animate(s, dice){
	"use strict";
	if ($("div#timer").length === 0){console.log("WARNING 105: No timer"); return false;}

	var speed  = 5;
	var valueX = 0;
	var valueY = 0;
	var valueZ = 0;
	
	switch (s){
		case 1: valueX =  45; valueY =   0; valueZ =  45; break;
		case 2: valueX = 135; valueY = 135; valueZ =   0; break;
		case 3: valueX = 135; valueY = 135; valueZ =  90; break;
		case 4: valueX = 135; valueY = 135; valueZ = 180; break;
		case 5: valueX = 135; valueY = 135; valueZ = 270; break;
		case 6: valueX = 225; valueY =   0; valueZ = 225; break;
	}
	
	var stopX = false;
	var stopY = false;
	var stopZ = false;
	
	if (GLOBAL.DICE[dice].xAngle !== valueX){
		GLOBAL.DICE[dice].xAngle += speed;
	}else{ 
		if (GLOBAL.countX[dice] >0){
			GLOBAL.countX[dice]--;
			GLOBAL.DICE[dice].xAngle += speed;
		}else{
			stopX=true;
		}
	}
	
	if (GLOBAL.DICE[dice].yAngle !== valueY){
		GLOBAL.DICE[dice].yAngle += speed;
	}else{
		if (GLOBAL.countY[dice] >0){
			GLOBAL.countY[dice]--;
			GLOBAL.DICE[dice].yAngle += speed;
		}else{
			stopY=true;
		}
	}
	
	if (GLOBAL.DICE[dice].zAngle !== valueZ){
		GLOBAL.DICE[dice].zAngle += speed;
	}else{
		if (GLOBAL.countZ[dice] >0){
			GLOBAL.countZ[dice]--;
			GLOBAL.DICE[dice].zAngle += speed;
		}else{
			stopZ=true;



		}
	}
	
	if (GLOBAL.DICE[dice].xAngle > 360){GLOBAL.DICE[dice].xAngle = GLOBAL.DICE[dice].xAngle-(360+speed);}
	if (GLOBAL.DICE[dice].yAngle > 360){GLOBAL.DICE[dice].yAngle = GLOBAL.DICE[dice].yAngle-(360+speed);}
	if (GLOBAL.DICE[dice].zAngle > 360){GLOBAL.DICE[dice].zAngle = GLOBAL.DICE[dice].zAngle-(360+speed);}
	
	$('#cube'+dice).css('webkitTransform', "rotateX("+GLOBAL.DICE[dice].xAngle+"deg) rotateY("+GLOBAL.DICE[dice].yAngle+"deg) rotateZ("+GLOBAL.DICE[dice].zAngle+"deg)" );
	$('#cube'+dice).css('transform', "rotateX("+GLOBAL.DICE[dice].xAngle+"deg) rotateY("+GLOBAL.DICE[dice].yAngle+"deg) rotateZ("+GLOBAL.DICE[dice].zAngle+"deg)" );

	if (stopX=== false || stopY === false || stopZ === false){
		GLOBAL.timer[dice] = setTimeout("animate("+s+","+dice+");",10);
		AUDIO.dice.shake.play();
	}else{
		//playEnd(s, dice);
		setTimeout("playEnd("+s+","+dice+");",200);
	}
}

function playEnd(w, dice){
	"use strict";
	AUDIO.dice.roll.play();
	
	switch (w){
		case 1: $("div#dice1_"+dice).css("background-color","rgba(255,153,0,0.8)"); break;
		case 2: $("div#dice2_"+dice).css("background-color","rgba(255,153,0,0.8)"); break;
		case 3: $("div#dice3_"+dice).css("background-color","rgba(255,153,0,0.8)"); break;
		case 4: $("div#dice4_"+dice).css("background-color","rgba(255,153,0,0.8)"); break;
		case 5: $("div#dice5_"+dice).css("background-color","rgba(255,153,0,0.8)"); break;
		case 6: $("div#dice6_"+dice).css("background-color","rgba(255,153,0,0.8)"); break;
	}
	GLOBAL.DICE[dice].end = true;
	
	$("div#play").html("");//Gracias por participar, sigue jugando!
	
	getLastResult();
}

function getLastResult(){
	"use strict";
	// clear user data
	$("div#current_bids").html("");
	$.ajax({
		type : "POST",
		dataType : "json",
		url : "system.php",
		async: true,
		data : {
			service : "games.result",
			game    : GAME_MODE,
			id 		: GLOBAL.NEXT_PLAY
		},
		success : function (json) {
			if (json.STATUS === "OK") {
				// current bids
				//-----------------------
				// last results
				//-----------------------
				if (json.results){
					$("div#result").html(formatResult(GAME_MODE,json,0));
				}
				//------------------------
				// my results
				//------------------------
				//$("div#current_bids").html(json.my.results);
				if (json.my.results){
					var data = "";
					var i;
					if (json.game === "puntazo"){
						data = '<table align="center" width="80%" cellpadding="4" cellspacing="0"><tr><th>Sorteo</th><th>Ticket</th><th></tr>';
					}else{
						data = '<table align="center" width="80%" cellpadding="4" cellspacing="0" ><tr ><th>#</th><th>'+$LANG["game.result.amount"]+'</th><th>'+$LANG["game.result.bid"]+'</th></tr>';
					}
					
					for (i = 0; i < json.my.results.length; i++) {
						if (json.game === "puntazo"){
							data += '<tr ><td align="center">'+json.my.results[i].id +'</td><td align="center">'+resultIcon(json.game, json.my.results[i].bid)+'</td></tr>';
						}else if (json.game === "conoce"){
							data += '<tr class="row2"><td align="center">'+resultIcon(json.game, json.my.results[i].bid)+'</td><td align="center" colspan="2">';
						}else{
							data += '<tr class="row2"><td align="center">'+json.my.results[i].id+'</td><td align="center">$'+json.my.results[i].value+'</td><td align="center">'+resultIcon(json.game, json.my.results[i].bid);
							if (json.game === "dice.2" || json.game === "horse"){
								data += " "+resultIcon(json.game, json.my.results[i].bid2);
								data += " "+resultIcon(json.game, json.my.results[i].bid3);
							}
						}
						data += '</td></tr>';
					}
					$("div#current_bids").html(data);
					$("strong#game").html(json.game_name);
				}
				//------------------------				
			} else {
				//console.log(JSON.stringify(json));
				alert("ErrorCode 618: "+json.STATUS + " : " + json.INFO);
			}
		},
		error : function (xhr, status) {
			if (JSON.stringify(xhr).indexOf("Chrome Data Compression") !== -1) {
				alert("ErrorCode 807: This page cannot load via Chrome Data Compression Proxy. Please, desactive it in Configuration > Reduce Data");
			}else{
				alert("ErrorCode 809: " + status + " " + JSON.stringify(xhr));
			}
		}
	});
}

function getCurrentBid(){
	"use strict";
	alert("deprecated");
}

function clearBid(){
	"use strict";
	GLOBAL.DICE[1].play = 0;
	GLOBAL.DICE[2].play = 0;
	GLOBAL.DICE[3].play = 0;
	GLOBAL.bid = undefined;
	//$('div#bid').popup('close');
	unCheck('input#checkbox-1a');
	unCheck('input#checkbox-2a');
	unCheck('input#checkbox-3a');
	unCheck('input#checkbox-4a');
	unCheck('input#checkbox-5a');
	unCheck('input#checkbox-6a');
	unCheck('input#radio-choice-1');
	unCheck('input#radio-choice-2');
	unCheck('input#radio-choice-3');
	unCheck('input#radio-choice-4');
	unCheck('input#radio-choice-5');
	unCheck('input#radio-choice-6');
	unCheck('input#radio-choice-7');
	unCheck('input#radio-choice-8');
	unCheck('input#color-1');	
	unCheck('input#color-2');	
}

function Apostar(){
	"use strict";
	if (GLOBAL.bid === undefined){
		$("div#contador").html($LANG["game.error"]);
		return;
	}

	if (GLOBAL.bid === 0){$("div#contador").html($LANG["game.error.zero"]);return;}

	switch (GAME_MODE){
	case "dice.1":
		if (GLOBAL.DICE[1].play === 0){$("div#contador").html($LANG["game.error.dice1"]);return;}
	break;
	case "dice.2":
		if (GLOBAL.DICE[1].play === 0 || GLOBAL.DICE[2].play === 0 || GLOBAL.DICE[3].play === 0){$("div#contador").html($LANG["game.error.dice3"]);return;}
	break;
	case "dice.3":
		if (GLOBAL.DICE[1].play === 0){$("div#contador").html($LANG["game.error.dice1"]);return;}
	break;
	case "roulette":
		if (GLOBAL.DICE[1].play === 0){$("div#contador").html($LANG["game.error.color1"]);return;}
	break;
	case "horse":
		if ($("input#horse-1").is(':checked')){// 1 caballo
			if (GLOBAL.DICE[1].play === 0){$("div#contador").html($LANG["game.error.horse1"]);return;}
			GLOBAL.DICE[2].play = -1;
			GLOBAL.DICE[3].play = -1;
		}else if ($("input#horse-2").is(':checked')){// 3 caballos
			if (GLOBAL.DICE[1].play === 0 || GLOBAL.DICE[2].play === 0 || GLOBAL.DICE[3].play === 0){$("div#contador").html($LANG["game.error.horse3"]);return;}
		}		
	break;
	}
	$("div#play").html($LANG["connecting"]);
	
	$.ajax({
		type : "POST",
		dataType : "json",
		url : "system.php",
		async: true,
		data : {
			service : "game.bid",
			game    : GAME_MODE,
			amount  : GLOBAL.bid,
			number1 : GLOBAL.DICE[1].play,
			number2 : GLOBAL.DICE[2].play,
			number3 : GLOBAL.DICE[3].play
		},
		success : function (json) {		
			if (json.STATUS === "OK") {
				//$("div#result").html(json.INFO);
				var button = ' <a href="#panel_user" style="display:inline-block;" class="ui-link">Ver apuestas</a>';
				$("div#play").html(json.INFO + button);
				$('span#balance').html(json.balance);
				
				clearBid();
				$('div#bid').popup('close');
				getLastResult();
			} else if (json.STATUS === "WARNING"){
				GLOBAL.RESTANTE = 0;
				UPDATE = true;
			}else{
				//alert("ErrorCode 482: "+json.STATUS + " : " + json.INFO);
				$("div#contador").html("* "+json.INFO);
			}
		},
		error : function (xhr, status) {
			$("div#contador").html("ErrorCode 486: " + status + " " + JSON.stringify(xhr));
		}
	});
}

function resetRoulette(){
	"use strict";
	window.border = 40;
	window.lap_count = 0;
	window.a = 0;
	window.aspeed = 0;
}

function roulette_size(){
	"use strict";
	// windows size;
	var max_size = 0.80;// 70%
	var width  = $(window).width() * max_size;
	var height = $(window).height()* max_size;
	var offset;
	
	if (width > height){width = height;}else{height = width;}

	$('img#roulette_base').css('width',  height);
	$('img#roulette_base').css('height', height);
	$('img#roulette_top').css('width',  height);
	$('img#roulette_top').css('height', height);
	

	if (MOVIL){offset = 0;}else{offset = 140;}
	var x = offset + ($(window).width() - width) / 2 ;
	//$('div#roulette').position().left;
	var y = $('div#roulette').position().top;
	$('img#roulette_top').css('left', x + "px");
	$('img#roulette_top').css('top', y + "px");



	$('img#roulette_base').css('left', x + "px");
	$('img#roulette_base').css('top', y + "px");
	
	var radius = (height*2)/2;
	var ball_size  = height / 25;
	var left   = $('img#roulette_base').position().left ;
	var top    = $('img#roulette_base').position().top  ;
	x = left + ((Math.sin(10) * radius) + radius) - ball_size/2;
	y = top  + ((Math.cos(10) * radius) + radius) - ball_size/2;

	$('img#ball').css('left', x + "px");
	$('img#ball').css('top' , y + "px");
	$('img#ball').attr('width' , ball_size + "px");
	$('img#ball').attr('height', ball_size+ "px");
	
	return height;
}

var lap_count, aspeed, a;

function roulette_roll(){
	"use strict";
	try{
		AUDIO.roulette.roll.play();
	}catch(err){
		//console.log(err.message);	
	}
	var width  = roulette_size();
	var ball_size  = width / 30;// 15
	var border_max = width / 6;// 80
	
	var border = border_max;// remove falling
	
	//console.log(ball_size+" - "+border_max);
	if (border < border_max && lap_count > 0){
		border += 1;
		if (aspeed < 3){
			aspeed += 0.005;
		}
	}
	if (border > border_max){
		border = border_max;
	}

	a += 4-aspeed;
	if ( a > 360*2){
		a = 0;
		lap_count++;
	}
	
	var _r1 = 360*2;
	var _r2 = _r1 / 24;
	var _r3 = a/_r2 + 1;
	if (_r3 > 24.5){
		_r3 -= 24;	
	}
	
	var b = "";
	// reduce aspeed
	if (_r3 > GLOBAL.place-0.30 && lap_count > 0){
		//if (_r3 > GLOBAL.place-0.30  && _r3 < GLOBAL.place && lap_count > 0){
		aspeed = 3.5;
	}
	// stop
	if (_r3 > GLOBAL.place-0.10 && border >= border_max && lap_count > 0){		
		if (isPar(Math.round(GLOBAL.winners[1]))){
			b = $LANG["game.result.winner"]	+ ": "+ $LANG["color.red"];
		}else{
			b = $LANG["game.result.winner"]	+ ": "+ $LANG["color.black"];
		}
		//setTimeout(roulette_roll,2000);
		GLOBAL.winners[2] += 1;
		AUDIO.roulette.roll.pause();
		AUDIO.roulette.end.play();
		
		$("div#play").html(b);
		getLastResult();
	}else{
		setTimeout(roulette_roll,20);
	}

	var angle  = Math.PI * (a / 360);	
	
	var left   = $('img#roulette_base').position().left + border;
	var top    = $('img#roulette_base').position().top  + border;
	var radius = (width-border*2)/2;
	
	$('img#roulette_top').css('transform', "rotate(" + a + "deg)");
	
	var x = left + ((Math.sin(angle) * radius) + radius) - ball_size/2;
	var y = top  + ((Math.cos(angle) * radius) + radius) - ball_size/2;

	$('img#ball').css('left', x + "px");
	$('img#ball').css('top' , y + "px");

	$("div#laps").html("laps: "+lap_count + "<br>a: "+ a + "<br>r3: "+ _r3.toFixed(2) + "<br>b: "+b);	
}

function resultIcon(game_mode, value){
	"use strict";
	
	if (value === undefined){
		return "";//$LANG["coming"];
	}
	if (value === -1){
		return "";
	}
	if (value === ""){
		return "";
	}
	switch (game_mode){
		case "dice.1":
		case "dice.2":
		case "dice.3": 
				return '<img src="images/dice_' + value + '.svg" width="32" style="background:rgba(255,255,255,1)">';
		case "horse": 
				return '<img src="images/caballo.png" width="32" />'+value;
		case "roulette":
			if (value === 1){// negro
				value = '#000';
			}else if (value === 2){// rojo
				value = '#F00';
			}else{
				//value = '#FFF';
				return "";
			}
			return '<div style="border: white;border-style: solid;border-width: 1px;width:32px;height:32px;background-color:' + value + ';display:inline-block;"></div>';
		case "puntazo":
			return '<div class="puntazo_icon">'+value+'</div>';
		case "conoce":
			return '<div class="puntazo_icon_us">'+value+'</div>';
	}
	return "UNDEFINED game_mode";
}

function formatResult(game_mode,json, type){
	"use strict";
	//var row = "row2";
	if (json.STATUS !== "OK"){
		alert("E117: "+json.INFO);
		return;
	}
	
	var data;
	if (type === 0){
		data = $LANG['game.result.header'];
	}else{
		data = $LANG['game.result.header.user'];
	}
	for (var i = 0; i < json.results.length; i++) {
		if (json.results[i].cd_game !== undefined){
			game_mode = json.results[i].cd_game;	
		}
		switch (game_mode){
		case "dice.1":
		case "roulette":
			if (type === 0){// general
				data+= '<tr><td align="center" nowrap="nowrap" data-title="'+$LANG["game.result.play"]+'">' + json.results[i].id + '</td><td align="center" nowrap="nowrap" data-title="'+$LANG["game.result.date"]+'">' + json.results[i].date + '</td><td align="center" data-title="'+$LANG["game.result.winner"]+'">' + resultIcon(game_mode,json.results[i].nwon1)+'</td></tr>';
			}else{// jugador
				data = data + '<tr><td align="center" nowrap="nowrap" data-title="'+$LANG["info"]+'">' + json.results[i].info + '</td><td align="center" nowrap="nowrap" data-title="'+$LANG["game"]+'">' + $LANG["game."+json.results[i].cd_game] + '</td><td align="center" nowrap="nowrap" data-title="'+$LANG["game.result.play"]+'">' + json.results[i].id + '</td><td align="center" data-title="'+$LANG["game.result.date"]+'">' + json.results[i].date + '</td><td align="center" data-title="'+$LANG["game.result.amount"]+'">' + json.results[i].amount + '</td><td align="center" data-title="'+$LANG["game.result.bid"]+'">' + resultIcon(game_mode,json.results[i].nbet1)+'</td><td align="center" data-title="'+$LANG["game.result.winner"]+'">' + resultIcon(game_mode,json.results[i].win.nwon1)+'</td></tr>';
			}
		break;
		case "dice.2":
		case "horse":
			if (type === 0){// general
				data+= '<tr ><td align="center" nowrap="nowrap" data-title="'+$LANG["game.result.play"]+'">' + json.results[i].id + '</td><td align="center" data-title="'+$LANG["game.result.date"]+'">' + json.results[i].date + '</td><td align="center" data-title="'+$LANG["game.result.winner"]+'">' +
				resultIcon(game_mode,json.results[i].nwon1)+" "+
				'</td></tr>';
			}else{
				data = data + '<tr ><td align="center" nowrap="nowrap" data-title="'+$LANG["info"]+'">' + json.results[i].info + '</td><td align="center" nowrap="nowrap" data-title="'+$LANG["game"]+'">' + $LANG["game."+json.results[i].cd_game] + '</td><td align="center" nowrap="nowrap" data-title="'+$LANG["game.result.play"]+'">' + json.results[i].id + '</td><td align="center" data-title="'+$LANG["game.result.date"]+'">' + json.results[i].date + '</td><td align="center" data-title="'+$LANG["game.result.amount"]+'">' + json.results[i].amount + '</td><td align="center" data-title="'+$LANG["game.result.bid"]+'">' + resultIcon(game_mode,json.results[i].nbet1)+" "+ resultIcon(game_mode,json.results[i].nbet2)+" "+ resultIcon(game_mode,json.results[i].nbet3)+" "+'</td><td align="center" data-title="'+$LANG["game.result.winner"]+'">' + resultIcon(game_mode,json.results[i].win.nwon1)+'</td></tr>';
			}
		break;
		case "dice.3":
			if (type === 0){// general
				data += '<tr ><td align="center" nowrap="nowrap" data-title="'+$LANG["game.result.play"]+'">' + json.results[i].id + '</td><td align="center" data-title="'+$LANG["game.result.date"]+'">' + json.results[i].date + '</td><td align="center" data-title="'+$LANG["game.result.winner"]+'">' +
				resultIcon(game_mode,json.results[i].nwon1)+" "+
				resultIcon(game_mode,json.results[i].nwon2)+" "+
				resultIcon(game_mode,json.results[i].nwon3)+
				'</td></tr>';
			}else{
				data += '<tr ><td align="center" nowrap="nowrap" data-title="'+$LANG["info"]+'">' + json.results[i].info + '</td><td align="center" nowrap="nowrap" data-title="'+$LANG["game"]+'">' + $LANG["game."+json.results[i].cd_game] + '</td><td align="center" nowrap="nowrap" data-title="'+$LANG["game.result.play"]+'">' + json.results[i].id + '</td><td align="center" data-title="'+$LANG["game.result.date"]+'">' + json.results[i].date + '</td><td align="center" data-title="'+$LANG["game.result.amount"]+'">' + json.results[i].amount + '</td><td align="center" data-title="'+$LANG["game.result.bid"]+'">' + 
				resultIcon(game_mode,json.results[i].nbet1)+'</td><td align="center" data-title="'+$LANG["game.result.winner"]+'">' + resultIcon(game_mode,json.results[i].win.nwon1)+" ";
				
				if (json.results[i].win.nwon1 > 0){
					data += resultIcon(game_mode,json.results[i].win.nwon2)+" "+resultIcon(game_mode,json.results[i].win.nwon3);				
				}
				data += '</td></tr>';
			}
		break;
		case "puntazo":
			if (type === 0){// general
				data+= '<tr><td align="center" nowrap="nowrap">' + json.results[i].id + '</td><td align="center">' + json.results[i].date + '</td><td align="center">' + resultIcon(game_mode,json.results[i].nwon1)+'</td></tr>';
			}else{// jugador
				data += '<tr><td align="center" nowrap="nowrap" data-title="'+$LANG["info"]+'">' + json.results[i].info + '</td><td align="center" nowrap="nowrap">' + $LANG["game."+json.results[i].cd_game] + '</td><td align="center" nowrap="nowrap">' + json.results[i].id + '</td><td align="center">' + json.results[i].date + '</td><td align="center">' + json.results[i].amount + '</td><td align="center">' + resultIcon(game_mode,json.results[i].nbetp)+'</td><td align="center">';
				if (json.results[i].win.nwon1 == 0){// pendiente
					data += resultIcon(game_mode,undefined);
				}else if (json.results[i].win.nwon1 == 1){// winner
					data += $LANG["game.result.winner"];
				}else if (json.results[i].win.nwon1 == -1){// loser
					data += $LANG["game.result.loser"];
				}
				data += '</td></tr>';
			}
		break;
		default:
			if (type === 0){// general
				
			}else{// jugador
				data = data + '<tr><td align="center" nowrap="nowrap" data-title="'+$LANG["info"]+'">' + json.results[i].info + '</td><td align="center" nowrap="nowrap" data-title="'+$LANG["game"]+'">' + $LANG["game."+json.results[i].cd_game] + '</td><td align="center" nowrap="nowrap" data-title="'+$LANG["game.result.play"]+'">' + json.results[i].id + '</td><td align="center" data-title="'+$LANG["game.result.date"]+'">' + json.results[i].date + '</td><td align="center" data-title="'+$LANG["game.result.amount"]+'">' + json.results[i].amount + '</td><td align="center" data-title="'+$LANG["game.result.bid"]+'"></td><td align="center" data-title="'+$LANG["game.result.winner"]+'"></td></tr>';
			}
			break;
		}
	}
	data+= "</tbody> </table></section>";
	return data;
}

function search_result(game_mode, value, type, pag, element){
	"use strict";
	if (value === ""){ return;}
	if (element === undefined) {element = "search_result";}
	$.ajax({
		type : "POST",
		dataType : "json",
		url : "system.php",
		async: true,
		data : {
			service : "games.search",
			game_mode: game_mode,
			value	: value,
			type	: type,
			pag	    : pag
		},
		success : function (json) {
			//$('#'+element).html(html);
			$("#"+element).html(formatResult(game_mode, json, type));
			$('#'+element).trigger("create");
		},
		error : function (xhr, status) {
			alert("ErrorCode 668: " + status + " " + JSON.stringify(xhr));
		}
	});
}

function raceEnd(){
	"use strict";
	alert("deprecated");
	getLastResult();
}

function buyPuntazo(){
	"use strict";
	$.ajax({
		type 	 : "POST",
		dataType : "json",
		url 	 : "system.php",
		async	 : true,
		data 	 : {
			service   : "game.puntazo.buy",
			game_mode : GAME_MODE,
			dollar	  : DOLLAR,
			lang	  : LANG
		},
		success : function (json) {
			var gm = GAME_MODE;
			if (DOLLAR){
				gm += ".us";
			}
			switch (json.STATUS){
			case "OK":
				$('div#bid').popup('close');
				$('div#pop_message').html(json.INFO + resultIcon(gm, json.point));
				getLastResult();
			break;
			case "WINNER":
				var price1 = "RD$1,000,000";
				var price2 = "una orden de compra por valor de RD$5,000"; 
				if (DOLLAR){
					price1 = "US$1,000,000";
					price2 = "a shopping disconunt of US$5,000";
				}
				$('div#bid').popup('close');
				
				$('div#pop_message').html(
				"!Tu jugada no fue procesada!<br>El ganador del sorteo anterior fue el "+resultIcon(gm,json.winner)+" jugado en fecha <strong>"+json.winners[0].date+"</strong>. Si tienes algun nm_one entre el <strong>["+json.winner+" - "+(json.winner+89)+"]</strong> eres un feliz ganador de <strong>"+price1+"</strong> y si tienes un nm_one entre el <strong>["+(json.winner+90)+" - "+(json.winner+1999)+"]</strong> eres ganador de <strong>"+price2+"</strong>. Espere el próximo sorteo para usted tambien ser un ganador.");
				return true;// dont autoclear
			default:
				//$('div#sphere').html(json.STATUS+ " : "+json.INFO);
				$('div#bid').popup('close');
				$('div#pop_message').html(json.STATUS+ " : "+json.INFO);
			break;
			}
			setTimeout("$('div#pop_message').html('');",8000);
		},
		error : function (xhr, status) {
			alert("ErrorCode 1029: " + status + " " + JSON.stringify(xhr));
		}
	});
}

function buyConoce(){
	"use strict";
	
	var project = $('input[name="project"]:checked').val();

	if (project === undefined){
		alert("Seleccione un proyecto"); 
		return false;
	}
	
	$.ajax({
		type 	 : "POST",
		dataType : "json",
		url 	 : "system.php",
		async	 : true,
		data 	 : {
			service   : "game.conoce.buy",
			game_mode : GAME_MODE,
			project	  : project
		},
		success : function (json) {			
			switch (json.STATUS){
			case "OK":
				$('div#bid').popup('close');
				$('div#pop_message').html("Apuesta realizada<br> el nm_one aleatorio asignado es "+resultIcon(GAME_MODE,json.point));
				//location.reload();
				getLastResult();
			break;
			case "WINNER":
				$('div#bid').popup('close');
				
				$('div#pop_message').html(
				"!Tu jugada no fue procesada!<br>El ganador del sorteo anterior fue el "+resultIcon(GAME_MODE, json.winner)+" jugado en fecha <strong>"+json.winners[0].date+"</strong>. Si tienes algun nm_one entre el <strong>["+json.winner+" - "+(json.winner+2999)+"]</strong> eres un feliz ganador de <strong>uns vacaciones en el caribe</strong>. Espere el próximo sorteo para usted tambien ser un ganador.");
				return true;// dont autoclear
			default:
				//$('div#sphere').html(json.STATUS+ " : "+json.INFO);
				$('div#bid').popup('close');
				$('div#pop_message').html(json.STATUS+ " : "+json.INFO);
			break;
			}
			setTimeout("$('div#pop_message').html('');",8000);
		},
		error : function (xhr, status) {
			alert("ErrorCode 1079: " + status + " " + JSON.stringify(xhr));
		}
	});
}

function getAge(dateString) {
	"use strict";
    var today = new Date();
    var birthDate = new Date(dateString);
    var age = today.getFullYear() - birthDate.getFullYear();
    var m = today.getMonth() - birthDate.getMonth();
    if (m < 0 || (m === 0 && today.getDate() < birthDate.getDate())) {
        age--;
    }
    return age;
}

function SendEmailActivate(email, code){
	"use strict";	
	$.ajax({
		type 	 : "POST",
		dataType : "json",
		url 	 : "system.php",
		async	 : true,
		data 	 : {
			service : "regist.email",
			code 	: code,
			email	: email
		},
		success : function (json) {			
			switch (json.STATUS){
			case "OK":
				$('div#registro2').hide(1000);
				$('div#welcome').show(1000);
				if ($progressBar !== undefined){
					$progressBar.setCurrentStep(3);
				}
			break;
			default:
				alert(json.INFO);
			break;
			}
		},
		error : function (xhr, status) {
			alert("ErrorCode 1271: " + status + " " + JSON.stringify(xhr));
		}
	});
}

function SendEmailForgot(email, code){
	"use strict";	
	$.ajax({
		type 	 : "POST",
		dataType : "json",
		url 	 : "system.php",
		async	 : true,
		data 	 : {
			service : "forgot.email",
			code 	: code,
			email	: email
		},
		success : function (json) {			
			switch (json.STATUS){
			case "OK":
				$('div#registro2').hide(1000);
				$('div#welcome').show(1000);
			break;
			default:
				alert(json.INFO);
			break;
			}
		},
		error : function (xhr, status) {
			alert("ErrorCode 1271: " + status + " " + JSON.stringify(xhr));
		}
	});
}

function regist(){
	"use strict";
	var p1 = $('input#password1').val();
	var p2 = $('input#password2').val();	
	if (p1 !== p2){
		//document.getElementById("password2").setCustomValidity("Las contraseñas no coinciden");
		alert($LANG["password.match.fail"] );

		return false;	
	} 
	//document.getElementById("password2").setCustomValidity("");
	if (getAge($('input#bday').val()) < 18){
		alert($LANG["account.adult.only"]);
		return false;
	}
		
	var country = $('select#cboPais').val();
	var currency= $('select#cboMnda').val();
	var user	= $('input#user_name').val();
	var email	= $('input#email').val();
	var name	= $('input#new_username').val();
	var lastname= $('input#userlastname').val();
	var phone	= $('input#phone').val();
	var pass	= p1;
	var bday    = $('input#bday').val();
	var promo	= $('input#promo').val();
	
	var callback = function (json) {
		switch(json.status){
		case 1:
			//alert(json.msg);
			SendEmailActivate($('input#email').val(), json.cdactivate);
		break;
		case 0:
			alert(json.msg);
		break;
		default:
		// TODO. debug mode, only
			alert(JSON.stringify(json));
		break;
		}
	};
	
	ZUZUVAMA.regist(user, pass, email, name, lastname, phone, bday, promo, country, currency, callback);
	
	return false;
}

function regist_resend(){
	"use strict";
	var callback = function (json) {
		switch(json.status){
		case 1:
			SendEmailActivate($('input#email').val(), json.cdactivate);
		break;
		case 0:


			alert(json.msg);
		break;
		default:
		// TODO. debug mode, only
			alert(JSON.stringify(json));
		break;
		}
	};
	
	ZUZUVAMA.regist_resend($('input#email').val(), callback);
}

function regist_validate(){
	"use strict";
	var callback = function (json) {
		switch(json.status){
		case 1:
			alert(json.msg);
			$("table#code_form").hide();
			$("p#resend_form").hide();
		break;
		case 0:
			alert(json.msg);
		break;
		default:
		// TODO. debug mode, only
			alert(JSON.stringify(json));
		break;
		}
	};
	
	ZUZUVAMA.regist_validate($('input#email').val(),$('input#code').val(), callback);
}

function validateStep(step){
	"use strict";
	switch (step){
	case 0:// EULA
		$('div#eula').hide(1000);
		$('div#registro').show(1000);
		$progressBar.setCurrentStep(1); 	
	break;
	case 1:// Registro.usuario
		var p1 = $('input#password1').val();
		var p2 = $('input#password2').val();	
		if (p1 !== p2){
			//document.getElementById("password2").setCustomValidity("Las contraseñas no coinciden");
			alert($LANG["password.match.fail"]);
			return true;	
		} 
		// Conectarse a ZZVM y verificiar si existe el promotor
		if ($('input#promo').val() !== ""){
			var callback_validar_promo = function (json) {
				switch(json.status){
				case 1:
					$('div#registro').hide(1000);
					$('div#registro2').show(1000);
					$progressBar.setCurrentStep(2);
				break;
				case 0:
					$('input#promo').val("");
					$('input#promo').removeAttr("readonly");
					alert(json.msg);
				break;
				default:
				// TODO. debug mode, only
					alert(JSON.stringify(json));
				break;
				}
			};
			//document.getElementById("password2").setCustomValidity("");
			ZUZUVAMA.validUser($('input#promo').val(), callback_validar_promo);
			
		}else{
			$('div#registro').hide(1000);
			$('div#registro2').show(1000);
			$progressBar.setCurrentStep(2);
		}
		
		return false;
	break;
	case 2:// Registro.datos
		if (getAge($('input#bday').val()) < 18){
			alert($LANG["account.adult.only"]);
			return false;
		}
		regist();
		/*$('div#registro2').hide(1000);
		$('div#welcome').show(1000);
		$progressBar.setCurrentStep(3);*/
	break;
	}
	return false;
}

function changePass(){
	"use strict";
	var callback = function (json) {
		switch(json.status){
		case 1:			alert(json.msg);
			$('#account_main').show(500);
			$('#account_password').hide(500);
		break;
		case 0:
			alert(json.msg);
		break;
		default:
		// TODO. debug mode, only
			alert(JSON.stringify(json));
		break;
		}
	};
	var user = $('input#account_user').val();
	var old  = $('input#account_pass1').val();
	var new1 = $('input#account_pass2').val();
	var new2 = $('input#account_pass3').val();
if (old === "" || new1 === "" || new2 === "" ){
		alert($LANG["game.error"]);
		return false;
	}
	if (new1 !== new2 ){
		alert($LANG["password.match.fail"]);
		return false;
	}
	
	ZUZUVAMA.changePass(user, old, new1, callback);
}

function activeToken(){
	"use strict";
	var callback = function (json) {
		switch(json.status){
		case 1:
			//alert(json.msg);
		break;
		case 0:
			alert(json.msg);
		break;
		default:
		// TODO. debug mode, only
			alert(JSON.stringify(json));
		break;
		}
	};
	var user = $('input#account_user').val();
	var active = $('input#account_token').is(':checked');
	if (active === true){active = 1;}
	ZUZUVAMA.activeToken(user, active, callback);
}

function activeTokenEmail(){
	"use strict";
	var callback = function (json) {
		switch(json.status){

		case 1:
			//alert(json.msg);
		break;
		case 0:
			alert(json.msg);
		break;
		default:
		// TODO. debug mode, only
			alert(JSON.stringify(json));
		break;
		}
	};
	var user = $('input#account_user').val();
	var active = $('input#account_token_email').is(':checked');
	if (active === true){active = 1;}
	ZUZUVAMA.activeTokenEmail(user, active, callback);
}

function getConfig(){
	"use strict";
	var callback = function (json) {
		switch(json.status){
		case 1:
			var t = false;
			var e = false;
			if ( json.token === 1){ t = true; }
			if ( json.send_email === 1){ e = true; }

			$("input#account_token").attr('checked', t);
			$("input#account_token").flipswitch( "refresh" );
			$("input#account_token_email").attr( "checked", e);
			$("input#account_token_email").flipswitch( "refresh" );
		break;
		case 0:
			alert(json.msg);
		break;
		default:
		// TODO. debug mode, only
			//alert(JSON.stringify(json));
		break;
		}
	};
	//var user = $('input#account_user').val();
	ZUZUVAMA.getConfig(SESSION, callback);
}

function retire(type, element){
	"use strict";
	var callback = function (json) {
		switch(json.status){
		case 1:
			$('strong#code').html(json.code);
			$('div#retire_local').hide(500);
			$('div#retire_local_2').show(500);
		break;
		case 0:
			alert(json.msg);
		break;
		default:
		// TODO. debug mode, only
			alert(JSON.stringify(json));
		break;
		}
	};
	var amount = $(element).val() || 0;
	
	//var user = $('input#account_user').val();
	ZUZUVAMA.retire(SESSION, type, amount, callback);

}

function forgotPass(){
	"use strict";
	var callback = function (json) {
		switch(json.status){
		case 1:
			SendEmailForgot($('input#email').val(), json.cdactivate);			
		break;
		case 0:

			alert(json.msg);
		break;
		default:
		// TODO. debug mode, only
			alert(JSON.stringify(json));
		break;
		}
	};
	var email = $('input#email').val() || "";
	if (email === ""){ return "";}
	
	ZUZUVAMA.forgotPass(email, callback);
}

function forgotPass2(){
	"use strict";
	
	$.ajax({
		type 	 : "POST",
		dataType : "json",
		url 	 : "system.php",
		async	 : true,
		data 	 : {
			service   : "forgot.change.pass",
			code 	  : $("input#code").val(),
			password1 : $("input#password1").val(),
			password2 : $("input#password2").val(),
			email	  : $('input#email').val()
		},
		success : function (json) {			
			switch (json.STATUS){
			case "OK":
				$('div#registro2').hide(1000);
				$('div#welcome').show(1000);
			break;
			default:
				alert(json.INFO);
			break;
			}
		},
		error : function (xhr, status) {
			alert("ErrorCode 1591: " + status + " " + JSON.stringify(xhr));
		}
	});
}

function sponsorSave(){
	"use strict";
	var sp = $("input#sponsor").val();
	if (sp === ""){
		sp = 0;
	}
	$.ajax({
		type 	 : "POST",
		dataType : "json",
		url 	 : "system.php",
		async	 : true,
		data 	 : {
			service   : "sponsor.save",
			sponsor	  : sp
		},
		success : function (json) {			
			switch (json.STATUS){
			case "OK":
				location.reload();
			break;
			default:
				alert(json.INFO);
			break;
			}
		},
		error : function (xhr, status) {
			alert("ErrorCode 1591: " + status + " " + JSON.stringify(xhr));
		}
	});
}

function transFilter(){
	"use strict";
	/*var data 	 = {
			service   : "adm.user.trans",
			from	  : $('input#from').val(),
			to	  	  : $('input#to').val(),
			cboGame	  : $('select#cboGame').val(),
			cboType	  : $('select#cboType').val(),
			num_play  : $('input#num_play').val(),
			num_trans : $('input#num_trans').val()
		};*/
	
	$.ajax({
		type 	 : "POST",
		dataType : "html",
		url 	 : "system.php",
		async	 : true,
		data 	 : {
			service   : "adm.user.trans",
			from	  : $('input#from').val(),
			to	  	  : $('input#to').val(),
			cboGame	  : $('select#cboGame').val(),
			cboType	  : $('select#cboType').val(),
			num_play  : $('input#num_play').val(),
			num_trans : $('input#num_trans').val()
		},
		success : function (html) {			
			$('div#user_trans').html(html);
		},
		error : function (xhr, status) {
			alert("ErrorCode 1591: " + status + " " + JSON.stringify(xhr));
		}
	});
}

function recharge(amount){
	"use strict";
	$.ajax({
		type 	 : "POST",
		dataType : "json",
		url 	 : "system.php",
		async	 : true,
		data 	 : {
			service : "user.recharge",
			amount	: amount
		},
		success : function (json) {			
			switch (json.STATUS){
			case "OK":
				location.reload();
			break;
			default:
				alert(json.INFO);
			break;
			}
		},
		error : function (xhr, status) {
			alert("ErrorCode 1591: " + status + " " + JSON.stringify(xhr));
		}
	});
}