<!doctype html>
<html>
	<head>
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<meta charset="UTF-8">
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		<link rel="stylesheet" type="text/css" href="./css/main.css" />
		<title>NEOSTRATA®新春大礼！</title>
		<meta name="description" content="NEOSTRATA®" />
		<meta name="keywords"  content="NEOSTRATA®, 新春大礼" />
		<style>
		p{
			text-align: center;	
			margin: 180px 15px;
		}
		</style>
		<script type="text/javascript">
			history.pushState(null, null, location.href);
			window.onpopstate = function () {
			history.go(1);
			};
		</script>
	</head>
	<body>
		<p align="center" >
		<?php
		header("Content-Type:text/html; charset=utf-8");
		if(isset($_GET['sendR']) && !empty($_GET['sendR']))
		{
			if($_GET['sendR']=='m')
			{
				echo"小提示: 每个电邮地址只可以参加一次!";
			?>
            <br><br>			
			<a href="<?php echo "https://".$_SERVER["HTTP_HOST"].dirname($_SERVER["PHP_SELF"])."/";?>" target="_blank">Go to the web page ! </a>
			<?php
			}			
			if($_GET['sendR']=='OK')
			{
				echo"提交成功！请查看您的电子邮箱并核实邮件.";
				$userName=$_GET['userName'];
				$userMail=$_GET['userMail'];
				$userPhone=$_GET['userPhone'];
				$adminMail='';
				$pw='';	
				autoEmail($userName,$userMail,$adminMail,$userPhone,$pw);
			?>
            <br><br>				
			<a href="https://www.neostrata.ca/enlighten/c/28" target="_blank">点击即可浏览 NEOSTRATA®  Enlighten 最新产品 </a>
			<?php
			}
			if($_GET['sendR']=='errorAutoMail')
			{
				echo"謝謝!發生錯誤請聯繫管理員! Singtaoa1events@gmail.com /Thanks however there is error,please contact administrators, Singtaoa1events@gmail.com ! ";
			?>
            <br><br>				
			<a href="<?php echo "https://".$_SERVER["HTTP_HOST"].dirname($_SERVER["PHP_SELF"])."/";?>" target="_blank">Go to the web page ! </a>
			<?php
			}
		}
		else
		{
		die('谢谢！ 发生错误，请联系管理员！ Singtaoa1events@gmail.com /Thanks however there is error,please contact administrators, Singtaoa1events@gmail.com ! !');
		}
		?>
		</p>
	</body>
</html>
<?php
function autoEmail($userName,$userMail,$adminMail,$userPhone,$pw){
			require('./phpmailer/class.phpmailer.php');
			date_default_timezone_set('America/Toronto');
			$mail = new PHPMailer();
			$mail->IsSMTP();
			$mail->SMTPAuth = true; // turn on SMTP authentication
			$mail->Username = "singtaoa1events@gmail.com";
			$mail->Password = "ST12345.";
			$mail->FromName = "Campaign Registration Service";
			$webmaster_email1 = $adminMail; 
			$email1=$adminMail;  // more
			$name1="Campaign Admin";
			$emailx="singtaoa1events@gmail.com";
		    $namex="Staff";
			$email2=$userMail;
			$mail->SMTPDebug = 0; 
			$name2="User";
		$mail->AddAddress($email2,$name2);
					$mail->AddReplyTo("singtaoa1events@gmail.com","staff");
			$mail->WordWrap = 15;
			$mail->IsHTML(true); // send as HTML
				$mail->Subject = 'Get Started in NeoStrata from dushi.singtao.ca (email: '.$userMail.' | subscription date: '.date("Y-m-d H:i:s").')'; 
			$mail->Body = '
<html>
   <head>
        <meta http-equiv="content-type" content="text/html; charset=utf-8" />
       
    </head>
<body> 
  <table cellspacing="0" cellpadding="0" border="0" align="center" style="font-size:13px;font-family:Helvetica,Arial,sans-serif;line-height:1.3em;margin-bottom:0;width:500px;color:#282828;background:#fff">    
	 <tr>
	 	<td>
			<img style="width:180px;" src="https://dushi.singtao.ca/neostrata/logo.png" />
            <br/>
            <br/>
		</td>
	 </tr>
	 
	 <tr>
	 	<td style="font-size:13px;font-family:Helvetica,Arial,sans-serif;line-height:1.3em;vertical-align:top;padding:20px 10px;border-color:#d2d2d2;border-style:solid;border-width:1px;">
		    <p><strong>亲爱的朋友 '.$userName.',</strong></p>
            <p>感谢您参与“NeoStrata新春大礼”抽奖活动！</p>
						
			<p>如果您对活动有任何疑问，请发邮件到 info@ccue.ca 咨询</p>
			
			<p>请确认您的登记信息是否正确：</p>
            <table style="background-color:#eee; width:100%; font-size:13px;font-family:Helvetica,Arial,sans-serif;line-height:1.3em;vertical-align:top;padding:20px 10px;">
				<tr>
				  <td style="width:120px">Contact Person:</td><td>'.$userName.'</td>
				</tr>
				
				<tr>
				  <td style="width:120px">Contact Email:</td><td>'.$userMail.'</td>
				</tr>
				<tr>
				  <td style="width:120px">Phone Number:</td><td>'.$userPhone.'</td>
				</tr>
				<tr>
				  <td style="width:120px">Registration Received:</td><td>'.date("Y-m-d H:i:s").'</td>
				</tr>
			</table>
            <hr/>
            <br/>
            <!--<p><strong>Best Regards,</strong></p>-->
            <p><strong>星岛加拿大都市网</strong></p>
            <p style="font-size:11px;font-family:Helvetica,Arial,sans-serif;line-height:1.3em;color:#808080;margin:0">Web: https://dushi.singtao.ca/toronto/</p>
		</td>
	 </tr>
  </table>
</body>
</html>
';
				if(!$mail->Send()){
					$mailMsg="";
			header('location:https://'.$_SERVER["HTTP_HOST"].dirname($_SERVER["PHP_SELF"]).'/neothanksEvent.php?sendR=errorAutoMail');	
				}// putting the auto email! 20180925
				else
				{
					$AdminEmail = new PHPMailer();
					$AdminEmail->IsSMTP();
					$AdminEmail->SMTPAuth = true; // turn on SMTP authentication
						$webmaster_email1 = $adminMail; 
					$AdminEmail->Username = $adminMail;
					$AdminEmail->Password = $pw;		
					$AdminEmail->Username = "singtaoa1events@gmail.com";
					$AdminEmail->Password = "ST12345.";
					$AdminEmail->FromName = "Campaign Registration Service";
					$AdminEmail->AddAddress('singtaoa1events@gmail.com','ADMIN');
								$AdminEmail->SMTPDebug = 0; 
                    $AdminEmail->WordWrap = 15;
					$AdminEmail->IsHTML(true); // send as HTML
$AdminEmail->Subject = 'New User in NeoStrata from dushi.singtao.ca (email: '.$userMail.' | subscription date: '.date("Y-m-d H:i:s").')'; 
								$AdminEmail->Body = '
<html>
   <head>
        <meta http-equiv="content-type" content="text/html; charset=utf-8" />
       
    </head>
	
	
<body> 
  <table cellspacing="0" cellpadding="0" border="0" align="center" style="font-size:13px;font-family:Helvetica,Arial,sans-serif;line-height:1.3em;margin-bottom:0;width:500px;color:#282828;background:#fff">    
	 <tr>
	 	<td>
			<img style="width:180px;" src="https://dushi.singtao.ca/neostrata/logo.png" />
            <br/>
            <br/>
		</td>
	 </tr>
	 
	 <tr>
	 	<td style="font-size:13px;font-family:Helvetica,Arial,sans-serif;line-height:1.3em;vertical-align:top;padding:20px 10px;border-color:#d2d2d2;border-style:solid;border-width:1px;">
		    <p><strong>Hello 星岛加拿大都市网NeoStrata, New User : '.$userName.',</strong></p>

            <p>感谢您参与“NeoStrata新春大礼”抽奖活动！</p>
						
			<p>如果您对活动有任何疑问，请发邮件到 info@ccue.ca 咨询</p>
			
			<p>请确认您的登记信息是否正确：</p>
            <table style="background-color:#eee; width:100%; font-size:13px;font-family:Helvetica,Arial,sans-serif;line-height:1.3em;vertical-align:top;padding:20px 10px;">
				<tr>
				  <td style="width:120px">Contact Person:</td><td>'.$userName.'</td>
				</tr>
				
				<tr>
				  <td style="width:120px">Contact Email:</td><td>'.$userMail.'</td>
				</tr>
				<tr>
				  <td style="width:120px">Phone Number:</td><td>'.$userPhone.'</td>
				</tr>
				<tr>
				  <td style="width:120px">Registration Received:</td><td>'.date("Y-m-d H:i:s").'</td>
				</tr>
			</table>
            <hr/>
            <br/>
            <!--<p><strong>Best Regards,</strong></p>-->
            <p><strong>星岛加拿大都市网</strong></p>
            <p style="font-size:11px;font-family:Helvetica,Arial,sans-serif;line-height:1.3em;color:#808080;margin:0">Web: https://dushi.singtao.ca/toronto/</p>
		</td>
	 </tr>
  </table>
</body>
</html>
';
                    if($AdminEmail->Send())
					{}
				}// return  $mailMsg;
			}
?>
