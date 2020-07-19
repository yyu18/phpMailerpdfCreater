// JavaScript Document

$(document).ready(function() {
    /*$('#submit-button').click(function() {
		checked = $("input[type=checkbox]:checked").length;
  
		if(!checked) {
		  alert("Please Select At Least One Options On Question5.");
		  return false;
		}
  
	  });*/
document.getElementById('main-form').onsubmit = function(e) {
  //your validateChamps stuff goes here
//if(!bResult) e.preventDefault();

var rt=true;
	
	if( $('#specialQuestion14').attr('checked') || $('#specialQuestion4').attr('checked') ){ rt=false; }
	
	if(rt){
		checked = $("input[type=checkbox]:checked").length;
		if(!checked) {
		  alert("Please Select At Least One Options On Question5.");
		  e.preventDefault();
		  $('#main-form').unbind('submit');
		  return false;
		}
	}
	
	if(!rt){
		window.location.href = "https://events.singtao.ca/templates/default/noVoucher.php";
						//window.location.replace("https://events.singtao.ca/templates/default/noVoucher.php");
						flag = 0;
						submit_form();
						  e.preventDefault();
					  $('#main-form').unbind('submit');
		  return false;
	}
	
	submit_form();
	
};




	if(getCookieForFinshedSurvey("finished")==1){
		window.location.href = "https://events.singtao.ca/templates/default/already_finished_form.php";
	} else{
	"use strict";
	
	if ($('input[type=radio]').length){		
		$('input[type=radio]').change(function(){
			var selected_option = $(this).data('append-text');
			if($('input[name="'+selected_option+'"]').length){
				$('input[name="'+selected_option+'"]').show();

		  jQuery('input').each(function(index, element) {		
			if(jQuery(element).parent().parent().parent().css('display')=='none' || jQuery(element).parent().parent().parent().parent().css('display')=='none' ){
				element.removeAttribute("required"); 
				}
		  });
				
				
				
				
				
			} else {
				if ($('.'+$(this).attr('name')+'-append-text').length){
					$('.'+$(this).attr('name')+'-append-text').val('');
					$('.'+$(this).attr('name')+'-append-text').hide();
							
		  jQuery('input').each(function(index, element) {		
			//	var inname=jQuery(element).attr('name');specialQuestion1-append-text specialQuestion-append-text
			if( jQuery(element).parent().parent().parent().css('display')!='none' && jQuery(element).attr('name')!='reasonChoice5' && jQuery(element).attr('name')!='specialQuestion-append-text' && jQuery(element).attr('name')!='specialQuestion1-append-text' ){
			element.setAttribute("required",true); 
			}
				
		  });		
					
				
					
					
				}
				
				
				
				
				
				
				
			}
		});
	}
	
	if ($('input[type=checkbox]').length){	
		$('input[type=checkbox]').click(function(){
			var selected_option=$(this).data('append-text');
			if($('input[name="'+selected_option+'"]').length){
				if ($(this).is(':checked')){
					$('input[name="'+selected_option+'"]').show();
				} else {
					$('input[name="'+selected_option+'"]').val('');
					$('input[name="'+selected_option+'"]').hide();
				}
				
			} 
		});
	}
	
	//assign cookie
	var ait_cookie=getCookie('aituserguid', false);
	if (ait_cookie=='false'){
		ait_cookie={id:'markup'};
		setCookie('aituserguid', ait_cookie);
	} else {
		ait_cookie=$.parseJSON(ait_cookie);
	}
	if ($('input[name=cookie]').length){	
		$('input[name=cookie]').val(ait_cookie.id);
	}
}
});


function hideDiv(a){
	a.parent().parent().next().hide();
}

function showDiv(a){
	a.parent().parent().next().show();
}
function submit_form(){	





	var data={};
	var form_data=$('#main-form').serializeArray();
	
		var rt=0;
	form_data.forEach(data => {

	
			if(data.name=='specialQuestion' || data.name=='specialQuestion1'){
		 if(!data.value.includes("No")){
			//alert("test"); 
		checked = $("input[type=checkbox]:checked").length;
		if(!checked) {
			rt++;
		  //alert("Please Select At Least One Options On Question5.");
		 // return false;
		}
				
				}	
			}
			
			
			});
		//alert(rt); 	
	if(rt>1){
		//alert("Please Select At Least One Options On Question5.");
		return false;
		}		
			

	$.each(form_data,function(idx,val){
		if (val.value.length){
			if (val.name in data){
				data[val.name].push(val.value);
			} else {
				data[val.name]=[];
				data[val.name].push(val.value);
			}			
		}
	});

	$.post( '/api/campaigns/submit_form.php', data, function ( result ) {
		var flag = 1;
		if ( result.message ) {
			alert(result.message);
		} else {	
			form_data.forEach(data => {
				if(data.name=='specialQuestion' || data.name=='specialQuestion1'){
					if(data.value.includes("No")){
						window.location.href = "https://events.singtao.ca/templates/default/noVoucher.php";
						//window.location.replace("https://events.singtao.ca/templates/default/noVoucher.php");
						flag = 0;	
						//event.preventDefault();
						return false;					
					}	
				}
			});
			
			if(flag == 1){
					createCookieExpiredByTime('finished',1,24);	
			}
					return true;
		}
	} ).done( function () {
	
	} ).fail( function ( xhr ) {
		console.log( xhr.responseText || ( xhr.status + ', ' + xhr.statusText ) );
	} );
}

function createCookieExpiredByTime(cookieName, value, hours)
{
    if (hours){
        var date = new Date();
        date.setTime(date.getTime()+(hours*60*60*1000));
        var expires = "; expires="+date.toGMTString();
    }
    else{
        var expires = "";
    }
    document.cookie = cookieName+"="+value+expires+"; path=/";
}

function getCookieForFinshedSurvey(cname) {
	var name = cname + "=";
	var decodedCookie = decodeURIComponent(document.cookie);
	var ca = decodedCookie.split(';');
	for(var i = 0; i <ca.length; i++) {
	  var c = ca[i];
	  while (c.charAt(0) == ' ') {
		c = c.substring(1);
	  }
	  if (c.indexOf(name) == 0) {
		return c.substring(name.length, c.length);
	  }
	}
	return "";
  }