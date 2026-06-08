// JavaScript Document

function goto(page){
	"use strict";
	location.href = '?page='+page;
}

function dialog(title,body,onAcept,onCancel){
	"use strict";
	$('h4#dialog_title').html(title);
	$('div#dialog_body').html(body);
	$('button#dialog_button_cancel').attr('onClick','');
	if (onAcept === null){
		$('button#dialog_button_cancel').html('OK');
		$('button#dialog_button_ok').hide();
		if (onCancel !== null){
			$('button#dialog_button_cancel').attr('onClick',onCancel);
		}
	}else{
		$('button#dialog_button_cancel').html('Cancel');
		$('button#dialog_button_ok').show();
		$('button#dialog_button_ok').attr('onClick',"dialogCallback('"+JSON.stringify(onAcept)+"')");
	}	
	$('div#dialog').modal('show');
}

function dialogCallback(params){
	"use strict";
	if (params === null){
		return;	
	}
	var _data =  JSON.parse( params ) ;
	
	$.ajax({
		type 	 : "POST",
		dataType : "json",
		url 	 : "include/index.php",
		async	 : true,
		data 	 : _data,
		success  : function (json) {
			if (json !== null){
				dialog('Aviso',json.INFO,null,json.dialog);
			}
		},
		error : function (xhr, status) {
			//alert("Error p.27: " + status + " " + JSON.stringify(xhr));
		}
	});
}


function confirmDelete() {
	"use strict";
	var agree=confirm("Deseas borrar este registro?");
	if (agree){
		 return true;
	}else{
		 return false;
	}
}

function changeSelect(element, value){
	"use strict";
	$.ajax({
		type 	 : "POST",
		dataType : "html",
		url 	 : "include/index.php",
		async	 : true,
		data : {
			element : element,
			value	: value
		},
		success : function (data) {		
			$('#'+element).replaceWith(data);//.html(data)
		},
		error : function (xhr, status) {
			alert("Error p.27: " + status + " " + JSON.stringify(xhr));
		}
	});
}

function openClasi(parent, child){
	"use strict";
	$("div#clasi_dropdown").toggle(1000); 	
}

function setClasi(child){
	"use strict";
	$("div#clasi_dropdown").hide(1000); 
	$("div#clasi_index").html($(child).html());	
	$("input#clasi").val($(child).attr("value"));
}