<!DOCTYPE html>
<html style="text-align: center;" >
  <head>
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<meta charset="UTF-8">
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		<link rel="stylesheet" type="text/css" href="./css/main.css" />
		<title>NEOSTRATA®新春大礼！</title>
		<meta name="description" content="NEOSTRATA®" />
		<meta name="keywords"  content="NEOSTRATA®, 新春大礼" />
		<style>
		.form-radio:checked::before{
			content: none!important;
		}
		.form-radio {
		-webkit-appearance: checkbox!important;
		top: 5px!important;
        height: 20px!important;
        width: 20px!important;
		}
	
		body{
		/*margin-top: 10px;*/  	
		}
		div{
		/*max-width: 680px;*/
		width: 500px!important;
		}
	@media only screen and  (max-width: 576px){
		/*
		html
		{
		text-align: unset!important;
		}
		*/
		#imgMaz
		{
			bottom: 0px!important;
			margin: 0px!important;
			padding: 0px!important;
		}
		body{
		margin-top: 0px!important;  	
		}
		#main
		{
			display:block!important;
		}
		div
		{
			width:100%!important;
            display: block!important;			
		}
		
		#ch
		{
			line-height: 16px!important;
		}
	}
	</style>
	<!-- Google Tag Manager -->
	<script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
	new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
	j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
	'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
	})(window,document,'script','dataLayer','GTM-W3V9FKF');</script>
	<!-- End Google Tag Manager -->
  </head>
	<body id="main" style="text-align: center; display:inline-flex;">
		  <!-- Google Tag Manager (noscript) -->
          <noscript><iframe src="https://www.googletagmanager.com/ns.html?id=GTM-W3V9FKF"
          height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
          <!-- End Google Tag Manager (noscript) -->
		  <div style="text-align: center; width:50%;  "  >
			  <div style="display:inline-flex; width: 100%;" >
			    <img id="imgMaz" src="neo2020.jpg" style="  text-align: center; "> 
			  </div>
		  </div>	
		  <div style="text-align: center;  background-color:black; color:white; width:50%; "  >
<!--200204		 
		 </br>
		    <p style="text-align: center!important; font-size:26px; padding: 0px; margin:1.5px 0 0 0;	border: 0px;">
		      <strong >NEOSTRATA<sup style="font-size:12px;">&reg;</sup></strong>  
			</p>
			-->
			<div id="userForm" > 	
<?php 
	$camp_id=43; 
	$form_fields=file_get_contents('https://events.singtao.ca/api/campaigns/meta.php?id='.$camp_id); // 190107 full path to excute if file name will get the all text not code excuting 
if (isset($form_fields)){
	if ($form_fields){
	$form_fields=json_decode($form_fields,true);
		}
	}
?>
	<form style="padding:12px;" name = "submitF" id = "main-form" onsubmit="return CsubmitF()" method = "post"
	  enctype="multipart/form-data" accept-charset="utf-8" >
<?php  

   foreach($form_fields as $Eform_fields){// echo $Eform_fields['name'].' :
		
		
		if($Eform_fields['type']=='radio')// 190122 checkbox other input text
				{?>
					<p style="color:white; font-size:18px; width:290px; margin: 5px auto; text-align:center!important;" ><?php echo $Eform_fields['ch_desc'] ?></p>
				<?php 
				$Eform_fields['option_value']=json_decode($Eform_fields['option_value'],true);
				$a=$Eform_fields['option_value']['answers']; //var_dump($Eform_fields['option_value']);
				foreach ($a as $opt_idx=>$value) : ?>
				<!--<p>-->
				<input style="margin-right:0px!important;" class="form-radio" id="<?php echo $Eform_fields['key'].$opt_idx;?>" type="<?php echo $Eform_fields['type'];?>" name="<?php echo $Eform_fields['key'];?>" value="<?php echo  $value['value'];?>" />
				<!--
				<label for="<?php //echo $Eform_fields['ch_desc'].$opt_idx;?>"><?php// echo $value['value'];?>
				</label>
				-->
				<?php echo $value['value'];?>
				
				<?php if (isset($value['append_text'])&& $value['append_text']==true) : ?>
				<p><?php // echo $control['add_on_textfield']; ?><input type="text" class="add-on-textfield" name="<?php echo 'append_text';?>" placeholder="" value="" /></p>
				<?php endif; ?>
				<?php endforeach;
                ?>
				<br /><br />
                <?php				
				continue;
				}// 190122 checkbox other input text
			
			if($Eform_fields['key']=='agreeterm')
		{
			continue;
		}
		if($Eform_fields['key']=='Contact_Number')
		{
			?>
		<input style="width:280px; height:30px;" type="<?php echo $Eform_fields['type'];?>" name="<?php echo $Eform_fields['key'];?>" placeholder="<?php echo str_replace("_"," ",$Eform_fields['key']).' (optional)';?>" >
			<br><br>
			<?php	continue;
		} 	
		?>		
        <input  required="required" style="width:280px; height:30px;" type="<?php echo $Eform_fields['type'];?>" name="<?php echo $Eform_fields['key'];?>" placeholder="<?php echo str_replace("_"," ",$Eform_fields['key']);?>" >
				<br><br>
		<?php
	 //20190122 text type name email 
		if( $Eform_fields['key']=='Name')
			{
		?>
		<input required="required" style="width:280px; height:30px;" type="email" name="email" placeholder="Email" >
		<br><br>
		<?php
			}
	}
		?>


	<input type="hidden" name="cookie" id="st_timestamp" value="new_visitor" />
				<input type="hidden" name="camp_id" value="<?php echo $camp_id;?>"/>


		<p style="  width:310px; color:white; line-height: 20px!important; text-align:left!important; font-size:14px!important;  margin: 5px auto; "  > <input   required id="agreeterm" name="agreeterm" type="checkbox" style="background-color: #f1f1f1; color: #666; top: 10px; height: 20px; width: 20px; border: 0; cursor: pointer;  margin-right: 7px;"  >同意隐私政策和会员服务<a style="color:white;" href="https://www.elitegen.ca/neostrata/Neostrata条款及细则.pdf" target="_blank">	(点击看细则)。</a><br>我同意提供电子邮箱地址给加拿大星岛传媒集团，以作将来联系，包括独家优惠，活动及礼品赠送/体验等。<!--<br>Your personal data will be used to support your experience throughout this website.(Click for the detail!)--></p>
			<br>
			 <input id="btn" required="required" style=" margin:0px auto; display:block; width:290px; height:30px; background-color:#599ea1; color:white; " type="submit" value="提交表单">
		</form>
			</div>
			
			  <div style="display:inline-flex; width: 100%;" >
			    <img id="imgQr" src="neostrata_qr_code.jpg" style="height: 100px; text-align: center;  margin: 2px auto; "> 
			  </div>
			
			
		</div>
	</body>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.8.3/jquery.min.js"></script>
<script src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.11.1/jquery-ui.min.js"></script>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>	
<script type='text/javascript'>
function CsubmitF(){
	document.getElementById('btn').disabled =true; // 191106 prevent the mutiple clicks																				
	const listL = document.getElementsByTagName("input"); // get all p elements
	listL.length; // show number of items	//alert(listL.length);//const i;//list[1].type ="date";//list[1].style.float ="left";//list[1].style.text-align ="left";
	var returnTest=0;
		for (var z = 0; z < listL.length; z++) {  //  xx[i].style.color="red";	//listL[z].style.value =""; //alert(listL[z].style.value);
			if(listL[z].value ==""){ //alert(listL.length);
			   returnTest=returnTest+1;
			   if(listL[z].value =="" && listL[z].name=="Contact_Number"){
				  returnTest=returnTest-1;
				   }
			}
		}
		if(returnTest<1){ //alert(returnTest); // return confirm('是否確定送出表格? / Do you want to submit the form? ');		
		}else{
		alert("請填完表格!(電話可空白) / Please finish the form!(Phone Numebr can be empty.)");
		return false;
		}
var data={};
var form_data=$('#main-form').serializeArray();
$.each(form_data,function(idx,val){
	if (val.value.length){
		if (val.name in data){
			data[val.name].push(val.value);
		}else{
		data[val.name]=[];
		data[val.name].push(val.value);
		}	
	}
});
console.log(data);
$.post( 'https://events.singtao.ca/api/campaigns/submit_form.php', data, function ( result ) {
	if ( result.message ){
		alert(result.message);
		}else{
		window.location.replace('<?php echo "https://".$_SERVER["HTTP_HOST"].dirname($_SERVER["PHP_SELF"])."/";?>neothanksEvent.php?userName='+$('input[name="Name"]').val()+'&userMail='+$('input[name="email"]').val()+'&userPhone='+$('input[name="Contact_Number"]').val()+'&sendR=OK');	
		}
} ).done( function () {} ).fail( function ( xhr ) {
	console.log( xhr.responseText || ( xhr.status + ', ' + xhr.statusText ) );
} );
return false;
}
</script>

<!-- 190121 C counter -->
   
<script src="//api.singtao.ca/counter/AITcounter.php?tag=elitegen&id=st_timestamp"></script> 

</html>

