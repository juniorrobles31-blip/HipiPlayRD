<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>Timer</title>
    <script src="js/jquery-2.1.4.min.js"></script>
    <script>
	var GLOBAL = {};
	GLOBAL.RESTANTE = 0;
	
	$(function() {
		"use strict";
		setTimeout(timer,1000);
			
	});
	
	function timer(){
		"use strict";
		if ($("div#timer").length === 0){
			console.log("WARNING 171: No timer defined"); 
			return false;
		}
		GLOBAL.RESTANTE--;
		if (GLOBAL.RESTANTE <= 0){
			GLOBAL.RESTANTE = 60;
			$.ajax({
				type : "POST",
				dataType : "html",
				url : "_.php",
				async: true,
				data : {
					service : "cron.job"
				},
				success : function (json) {
							setTimeout(timer,1000);			
				},
				error : function (xhr, status) {
					$("div#timer").html("ErrorCode 244: " + status + " " + xhr);
				}
			});
		}else{
			setTimeout(timer,1000);
		}
		$("div#timer").html(GLOBAL.RESTANTE);
	}
	</script>
</head>

<body>
    <div id="timer">LOADING</div>
</body>
</html>