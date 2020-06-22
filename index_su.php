<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="https://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
<title></title>
<meta name="description" content="" />
<meta name="keywords"  content="" />
<link rel="stylesheet" type="text/css" href="https://fonts.googleapis.com/css?family=Lato:300,400,700" />
<link rel="stylesheet" type="text/css" href="./css/main_su.css?var=3" />

<!--[if IE]>
	<script type="text/javascript">
		 var console = { log: function() {} };
	</script>
<![endif]-->

<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.8.3/jquery.min.js"></script>
<script src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.11.1/jquery-ui.min.js"></script>
<script src="./js/jquery.ait.cookie.js"></script>
<script src="./js/main_su.js?var=12345678911111"></script>
</head>

<body>
<?php
	$camp_id=isset($_GET['id'])?intval($_GET['id']):0;
	
	if ($camp_id>0) {
		require_once $_SERVER['DOCUMENT_ROOT'] . '/includes/class-ait-config.php';

		$campaign_service = Campaign_Service::factory();
		$slides=$campaign_service->get_campaign_meta($camp_id, 'json');
		if (!$slides) {
			die('campaign meta of id ' . $camp_id . ' not exists');
		}
	//var_dump($slides);
	//die();
		//var_dump($slides);
		
		if (count($slides) >0){
?>	
<div id="main">
	  <div class="section">
		<div class="header">
			<img src="./imgs/singtao_logo.JPG" alt="Singtao Logo" width="300" height="160">
		</div>
		<div class="content">
			<h4>Business Recovery Advertising Survey</h4>
			<h4>復市廣告宣傳調查</h4>	
			<p>Sing Tao Media Group Canada is conducting a survey about advertising preference of post COVID-19.</p>	
			<p>All responses will be kept confidential and aggregated findings will be used to have a better understanding of the new advertising preference to reach Chinese Canadians, so we can produce some better advertising opportunities for your businesses.</p>
			<p>加拿大星島傳媒集團現正向各商戶進行一項關於營銷策略調查，我們不會向您推銷任何產品，你的意見是絕對保密，這純粹是市場調查，星島希望了解疫情後大家對廣告宣傳習慣的改變，從而制定更迎合市場需求的宣傳套餐，助商戶向華人社區更有效地拓展業務。</p>
			<br>
			<p>By submitting the filled survey (all questions answered), you are entitled to receive one $50 worth of advertising voucher, applicable to any advertising channels among Sing Tao Media Group Canada.</p>
			<p>完成問卷交回後（回答所有問題），可得到$50 廣告代用券乙張，適用於加拿大星島傳媒集團旗下任何廣告投放。</p>
			<br>
			<p>All responses will be kept confidential and aggregated findings will be used for statistical analysis only.</p>
			<p>收集到的資料都會絕對保密，只會作統計分析之用。</p>
			<br>
			<p>The survey is anonymous and will take about 2-3 minutes. </p>
			<p>問卷調查不記名，需時若2-3分鐘。</p>
			<form id="main-form" enctype="multipart/form-data"  accept-charset="UTF-8" action="https://events.singtao.ca/templates/default/result_su.php" method="POST" onsubmit="redirectPageForSubmit()">
				<input type="hidden" name="camp_id" value="<?php echo $camp_id;?>" />
				<input type="hidden" name="cookie" value="213erewr" />
				<input type="hidden" name="agreement" value="false" />
				<?php foreach ($slides as $idx=>$slide){ ?>
						<?php echo generateSlideContent(($idx+1), $slide); ?>
				<?php }//foreach slides ?>

				<p>Thank you very much for your participation.  Advertising voucher will be issued in next page.
非常感謝你的參予，廣告代用券將於下一頁發出。</p>
				<input type="submit" id="submit-button" class="submit-button"  value="SUBMIT 提交" /> 	
			</form>
			
		
		</div>
		<div class="footer">
		</div>
	  </div>
	
</div>
<?php
		}//slides		
	}//camp_id
?>
<?php
function generateRandomString($length) {
	$characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
	$charactersLength = strlen($characters);
	$randomString = '';
	for ($i = 0; $i < $length; $i++) {
		$randomString .= $characters[rand(0, $charactersLength - 1)];
	}
	return $randomString;
}
function generateSlideContent($idx, $slide){
	if (!isset($slide->type) || empty($slide->type))
		return '';
	
	$html='<div class="row">';
	
	if (empty($slide->label))
		$slide->label=$idx.'. '.$slide->ch_desc.' '.$slide->en_desc;
	else 
		$slide->label=$idx.'. '.$slide->label;
	if($slide->key =='confirmation_id'){
		$randomId = generateRandomString(4);
		$html.='<p><input style="display:none;" id = "confirmation_id" type="'.$slide->type.'" class="add-on-textfield" name="'.$slide->key.'" value="'.$randomId.'" /></p>';
	}  elseif($slide->key =='specialQuestion'){
		$html.='<p>'.$slide->label.'</p>';
		foreach ($slide->options as $opt_idx=>$option){				
			
			if ($option->append_text){	
				$html.='<p>
							<input class="form-'.$slide->type.' other-options uncheck" id="'.$slide->key.$opt_idx.'" type="'.$slide->type.'" name="'.$slide->key.'" value="'.$option->value.'" data-append-text="'.$slide->key.'-append-text" onclick="endSurveyForNo()" required/>
							<label for="'.$slide->key.$opt_idx.'">'.$option->value.'</label>
						</p>';	
				$html.='<p><input type="text" style="display:none;" name="'.$slide->key.'-append-text" class="add-on-textfield '.$slide->key.'-append-text" placeholder="Reason 原因:" value=""  /></p>';	
				
			} else {
				$html.='<p>
							<input class="form-'.$slide->type.'" id="'.$slide->key.$opt_idx.'" type="'.$slide->type.'" name="'.$slide->key.'" value="'.$option->value.'" required />
							<label for="'.$slide->key.$opt_idx.'">'.$option->value.'</label>
						</p>';	
			}
		}		
	} else {
	switch($slide->type){
		case 'text':
			$html.='<p><input id = "confirmation_id" type="'.$slide->type.'" class="add-on-textfield" name="'.$slide->key.'" value="" /></p>';
		case 'email':
		case 'number':
		case 'checkbox':
			$html.='<p>'.$slide->label.'</p>';
			foreach ($slide->options as $opt_idx=>$option){				
				
				if ($option->append_text){	
					$html.='<p>
								<input class="form-'.$slide->type.' other-options uncheck" id="'.$slide->key.$opt_idx.'" type="'.$slide->type.'" name="'.$slide->key.'" value="'.$option->value.'" data-append-text="'.$slide->key.'-append-text"  />
								<label for="'.$slide->key.$opt_idx.'">'.$option->value.'</label>
							</p>';	
					$html.='<p><input type="text" style="display:none;" name="'.$slide->key.'-append-text" class="add-on-textfield '.$slide->key.'-append-text" placeholder="" value=""  /></p>';	
					
				} else {
					$html.='<p>
								<input class="form-'.$slide->type.'" id="'.$slide->key.$opt_idx.'" type="'.$slide->type.'" name="'.$slide->key.'" value="'.$option->value.'"  />
								<label for="'.$slide->key.$opt_idx.'">'.$option->value.'</label>
							</p>';	
				}
			}		
			break;
		case 'radio':
			$html.='<p>'.$slide->label.'</p>';
			foreach ($slide->options as $opt_idx=>$option){				
				
				if ($option->append_text){	
					$html.='<p>
								<input class="form-'.$slide->type.' other-options uncheck" id="'.$slide->key.$opt_idx.'" type="'.$slide->type.'" name="'.$slide->key.'" value="'.$option->value.'" data-append-text="'.$slide->key.'-append-text"  required/>
								<label for="'.$slide->key.$opt_idx.'">'.$option->value.'</label>
							</p>';	
					$html.='<p><input type="text" style="display:none;" name="'.$slide->key.'-append-text" class="add-on-textfield '.$slide->key.'-append-text" placeholder="Reason 原因:" value=""  /></p>';	
					
				} else {
					$html.='<p>
								<input class="form-'.$slide->type.'" id="'.$slide->key.$opt_idx.'" type="'.$slide->type.'" name="'.$slide->key.'" value="'.$option->value.'" required />
								<label for="'.$slide->key.$opt_idx.'">'.$option->value.'</label>
							</p>';	
				}
			}		
			break;
		default:
			return '';
	}
}
	$html.='</div>';
	return $html;				
			
}
?>
</body>	
</html>

