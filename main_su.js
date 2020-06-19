// JavaScript Document

$(document).ready(function() {
	
	if(localStorage.getItem("finished_survey")=="1"){
		window.location.replace("https://events.singtao.ca/templates/default/already_finished_form.php");
	} else{
	"use strict";
	
	if ($('#submit-button').length){		
		$('#submit-button').click(function(){
			localStorage.setItem("finished_survey", $('input[name=finished_survey]').val());
			//console.log($('#confirmation_id').val());
			submit_form();
		});
	}
	
	if ($('input[type=radio]').length){		
		$('input[type=radio]').change(function(){
			var selected_option = $(this).data('append-text');
			if($('input[name="'+selected_option+'"]').length){
				$('input[name="'+selected_option+'"]').show();
			} else {
				if ($('.'+$(this).attr('name')+'-append-text').length){
					$('.'+$(this).attr('name')+'-append-text').val('');
					$('.'+$(this).attr('name')+'-append-text').hide();
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

function endSurveyForNo(){
	alert('Thank you for your response.多謝你的回應。');
	window.location.replace("https://events.singtao.ca/templates/default/already_finished_form.php");
} 


function redirectPageForSubmit(){
	localStorage.setItem("finished_survey", 1);
}

function submit_form(){	
	var data={};
	var form_data=$('#main-form').serializeArray();
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

		if ( result.message ) {
			alert(result.message);
		} else {	
			//$.redirect('https://events.singtao.ca/templates/default/result_su.php', {confirmation_id:$('#confirmation_id').val()});
			//$.post('https://events.singtao.ca/templates/default/result_su.php',data_confirmation);
			//window.location.replace('/templates/default/result_su.php?id='+$('input[name="camp_id"]').val());
			return true;
		}
	} ).done( function () {} ).fail( function ( xhr ) {
		console.log( xhr.responseText || ( xhr.status + ', ' + xhr.statusText ) );
	} );
}