// ZUZUVAMA.API
var ZUZUVAMA = {
	SERVER 	: "https://www.zuzuvama.com/api/",//"http://localhost/zuzuvama/api/",//
	USER 	: "jdd",
	PASS 	: "4lph4M4x",

   login : function(user, pass, onSuccess, callback) {
		"use strict";
		$.ajax({
			type 		: "POST",
			dataType 	: "json",
			url 		: this.SERVER + "signin.php",
			async		: true,
			crossDomain	: true,
			data 		: {
				apiuser : this.USER,
				apipass : this.PASS,
				metodo  : "login",
				usuario : user,
				clave	: pass,
				callback: onSuccess
			},
			success 	: callback,
			error 		: function (xhr, status) {
				alert("ErrorCode 21: " + status + " " + JSON.stringify(xhr));
			}
		});
    },
	
	regist : function(user, pass, email, name, lastname, phone, bday, promo, country, currency, callback){
		"use strict";
		$.ajax({
			type 		: "POST",
			dataType 	: "json",
			url 		: this.SERVER + "signin.php",
			async		: true,
			crossDomain	: true,
			data 		: {
				apiuser : this.USER,
				apipass : this.PASS,
				method  : "registrar",
				user	: user,
				pass	: pass,
				email	: email,
				name	: name,
				lastname: lastname,
				phone	: phone,
				bday    : bday,
				promo	: promo,
				country : country,
				currency: currency
			},
			success 	: callback,
			error 		: function (xhr, status, ex) {
				alert("ErrorCode 36: " + status + " ("+ex +") " + JSON.stringify(xhr));
			}
		});
	},
	
	regist_resend : function(email, callback){
		"use strict";
		$.ajax({
			type 		: "POST",
			dataType 	: "json",
			url 		: this.SERVER + "signin.php",
			async		: true,			
			crossDomain	: true,
			data 		: {
				apiuser : this.USER,
				apipass : this.PASS,
				method  : "resend_validate",
				email 	: email
			},
			success 	: callback,
			error 		: function (xhr, status, ex) {
				alert("ErrorCode 54: " + status + " ("+ex +") " + JSON.stringify(xhr));
			}
		});
	},
	
	regist_validate : function(email, code, callback){
		"use strict";
		$.ajax({
			type 		: "POST",
			dataType 	: "json",
			url 		: this.SERVER + "signin.php",
			async		: true,			
			crossDomain	: true,
			data 		: {
				apiuser : this.USER,
				apipass : this.PASS,
				method  : "validate",
				email   : email,
				code	: code
			},
			success 	: callback,
			error 		: function (xhr, status, ex) {
				alert("ErrorCode 72: " + status + " ("+ex +") " + JSON.stringify(xhr));
			}
		});
	},
	
	validUser : function(user, callback) {
		"use strict";
		$.ajax({
			type 		: "POST",
			dataType 	: "json",
			url 		: this.SERVER + "common.php",
			async		: true,
			crossDomain	: true,
			data 		: {
				apiuser : this.USER,
				apipass : this.PASS,
				method  : "validarusr",
				iduser  : user
			},
			success 	: callback,
			error 		: function (xhr, status) {
				alert("ErrorCode 90: " + status + " " + JSON.stringify(xhr));
			}
		});
    },
	
	changePass : function(user, old_pass, new_pass, callback) {
		"use strict";
		$.ajax({
			type 		: "POST",
			dataType 	: "json",
			url 		: this.SERVER + "signin.php",
			async		: true,
			crossDomain	: true,
			data 		: {
				apiuser : this.USER,
				apipass : this.PASS,
				method  : "change_pass",
				iduser  : user,
				oldpass	: old_pass,
				newpass	: new_pass
			},
			success 	: callback,
			error 		: function (xhr, status) {
				alert("ErrorCode 108: " + status + " " + JSON.stringify(xhr));
			}
		});
    },
	
	activeToken : function(user, active, callback) {
		"use strict";
		$.ajax({
			type 		: "POST",
			dataType 	: "json",
			url 		: this.SERVER + "signin.php",
			async		: true,
			crossDomain	: true,
			data 		: {
				apiuser : this.USER,
				apipass : this.PASS,
				method  : "active_token",
				iduser  : user,
				active	: active
			},
			success 	: callback,
			error 		: function (xhr, status) {
				alert("ErrorCode 129: " + status + " " + JSON.stringify(xhr));
			}
		});
    },
	
	activeTokenEmail : function(user, active, callback) {
		"use strict";
		$.ajax({
			type 		: "POST",
			dataType 	: "json",
			url 		: this.SERVER + "signin.php",
			async		: true,
			crossDomain	: true,
			data 		: {
				apiuser : this.USER,
				apipass : this.PASS,
				method  : "active_email",
				iduser  : user,
				active	: active
			},
			success 	: callback,
			error 		: function (xhr, status) {
				alert("ErrorCode 147: " + status + " " + JSON.stringify(xhr));
			}
		});
    },
	
	getConfig : function(user, callback) {
		"use strict";
		$.ajax({
			type 		: "POST",
			dataType 	: "json",
			url 		: this.SERVER + "signin.php",
			async		: true,
			crossDomain	: true,
			data 		: {
				apiuser : this.USER,
				apipass : this.PASS,
				method  : "get_config",
				iduser  : user
			},
			success 	: callback,
			error 		: function (xhr, status) {
				alert("ErrorCode 166: " + status + " " + JSON.stringify(xhr));
			}
		});
    },
	
	retire : function(user, type, amount, callback) {
		"use strict";
		if (amount <= 0){ return;}
		$.ajax({
			type 		: "POST",
			dataType 	: "json",
			url 		: this.SERVER + "common.php",
			async		: true,
			crossDomain	: true,
			data 		: {
				apiuser : this.USER,
				apipass : this.PASS,
				method  : "solicitud_retiro",
				iduser  : user,
				type	: type,
				amount	: amount
			},
			success 	: callback,
			error 		: function (xhr, status) {
				alert("ErrorCode 187: " + status + " " + JSON.stringify(xhr));
			}
		});
    },
	
	forgotPass : function(user, callback) {
		"use strict";
		if (user === ""){ return;}
		$.ajax({
			type 		: "POST",
			dataType 	: "json",
			url 		: this.SERVER + "signin.php",
			async		: true,
			crossDomain	: true,
			data 		: {
				apiuser : this.USER,
				apipass : this.PASS,
				method  : "forgot_pass",
				user    : user
			},
			success 	: callback,
			error 		: function (xhr, status) {
				alert("ErrorCode 205: " + status + " " + JSON.stringify(xhr));
			}
		});
    }
	
};
