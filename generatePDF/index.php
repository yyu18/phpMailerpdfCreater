<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="https://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
<title></title>
<meta name="description" content="" />
<meta name="keywords"  content="" />
<link rel="stylesheet" type="text/css" href="https://fonts.googleapis.com/css?family=Lato:300,400,700" />
<link rel="stylesheet" type="text/css" href="./css/main.css" />

<!--[if IE]>
	<script type="text/javascript">
		 var console = { log: function() {} };
	</script>
<![endif]-->

<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.8.3/jquery.min.js"></script>
<script src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.11.1/jquery-ui.min.js"></script>
<script src="./js/jquery.ait.cookie.js"></script>
<script src="./js/main.js"></script>
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
		
		if (count($slides) >0){
?>	
<div id="main">
	  <div class="section">
		<div class="header"></div>
		<div class="content">		
			<form id="main-form" enctype="multipart/form-data"  accept-charset="UTF-8">
				<input type="hidden" name="camp_id" value="<?php echo $camp_id;?>" />
				<input type="hidden" name="cookie" value="" />
				<input type="hidden" name="agreement" value="false" />
				<?php foreach ($slides as $idx=>$slide){ ?>
						<?php echo generateSlideContent(($idx+1), $slide); ?>
				<?php }//foreach slides ?>
			</form>
			
			<input type="button" id="submit-button" class="submit-button"  value="Subscribe 提交表單" /> 	
		</div>
		<div class="footer"></div>
	  </div>
	
</div>
<?php
		}//slides		
	}//camp_id
?>
<?php
	
function generateSlideContent($idx, $slide){
	if (!isset($slide->type) || empty($slide->type))
		return '';
	
	$html='<div class="row">';
	
	if (empty($slide->label))
		$slide->label=$idx.'. '.$slide->ch_desc.' '.$slide->en_desc;
	else 
		$slide->label=$idx.'. '.$slide->label;
	
	switch($slide->type){
		case 'text':
		case 'email':
		case 'number':
			$html.='<p>'.$slide->label.'</p>'
				   .'<p><input type="'.$slide->type.'" class="add-on-textfield" name="'.$slide->key.'" value="" /></p>';
			break;
		case 'checkbox':
		case 'radio':
			$html.='<p>'.$slide->label.'</p>';
			foreach ($slide->options as $opt_idx=>$option){				
				
				if ($option->append_text){	
					$html.='<p>
								<input class="form-'.$slide->type.' other-options uncheck" id="'.$slide->key.$opt_idx.'" type="'.$slide->type.'" name="'.$slide->key.'" value="'.$option->value.'" data-append-text="'.$slide->key.'-append-text" />
								<label for="'.$slide->key.$opt_idx.'">'.$option->value.'</label>
							</p>';	
					$html.='<p><input type="text" style="display:none;" name="'.$slide->key.'-append-text" class="add-on-textfield '.$slide->key.'-append-text" placeholder="" value="" /></p>';	
					
				} else {
					$html.='<p>
								<input class="form-'.$slide->type.'" id="'.$slide->key.$opt_idx.'" type="'.$slide->type.'" name="'.$slide->key.'" value="'.$option->value.'" />
								<label for="'.$slide->key.$opt_idx.'">'.$option->value.'</label>
							</p>';	
				}
			}		
			break;
		default:
			return '';
	}
	
	$html.='</div>';
	return $html;				
			
}
?>
</body>	
</html>

