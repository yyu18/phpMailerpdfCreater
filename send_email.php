<!DOCTYPE html>
<html>
<head>
<style>
  body {
	font-family: "Mircosoft JhengHei", "新細明體", "mingliu", Arial, Helvetica, sans-serif;
	color: #231815;
	font-size: 15px;
}
</style>
<title>Send Email</title>
</head>
<body>

<?php
	require('../phpMailer/phpmailer/class.phpmailer.php');

    if(isset($_POST['email']) and isset($_POST['confirmation_id'])){
        $adminMail = '';
        $userMail = $_POST['email'];
        //$userMail = 'hunt.yuyh@gmail.com';
        $confirmation_id = $_POST['confirmation_id'];
        sendEmail($userMail,$adminMail,$confirmation_id);
    } else {
        echo '
        <div style="text-align:center">
        <img src="./imgs/singtao_logo.JPG" alt="Singtao Logo" width="300" height="160">
        <h3>You Need To Finish The Survey First</h3></div>'
        ;}
    function sendEmail($userMail,$adminMail,$id){
        date_default_timezone_set('America/Toronto');
        $mail = new PHPMailer(true);
        $mail->IsSMTP();
        $mail->SMTPAuth = true; // turn on SMTP authentication
        $mail->Username = "singtaoa1events@gmail.com";
        $mail->Password = "ST12345.";
        $mail->FromName = "Campaign Advertising Voucher Service";
        $webmaster_email1 = $adminMail; 
        $email1=$adminMail;  // more
        $name1="Campaign Admin";
        $emailx="singtaoa1events@gmail.com";
        $namex="Staff";
        $email2=$userMail;
        $mail->SMTPDebug = 1; 
        $name2="User";
        $mail->AddAddress($email2,$name2);
        $mail->AddAddress($emailx,'Copy');
        $mail->AddReplyTo("singtaoa1events@gmail.com","staff");
        $mail->WordWrap = 15;
        $mail->IsHTML(true); // send as HTML
        $mail->Subject = "ADVERTISING VOUCHER FOR $id"; 
        $mail->Body = '
                    <html>
                            <head>
                                <meta http-equiv="content-type" content="text/html; charset=utf-8" />
                            </head>
                            <body> 
                                <img src="https://events.singtao.ca/templates/default/imgs/singtao_logo.JPG" alt="Singtao Logo" width="300" height="160">
                                <p>The survey is submitted successfully, your confirmation number is:<strong>'.$id.'</strong></p> 
                                <p>Please print or save the attachment advertising voucher for future use. </p>
                                <p>問卷已成功提交，你的確認碼為:<strong>'.$id.'</strong></p>
                                <p>請打印或保存下方的廣告代用券，以備將來使用。</p>
                            </body>
                    </html>';
        $mail->addAttachment("./pdfSaver/voucher_$id.pdf","voucher_$id.pdf");
        if(!$mail->Send()){
            echo 'send faied';
              echo "Mailer Error: " . $mail->ErrorInfo;
        } else {
            echo '
                <div style="text-align:center">
                <img src="./imgs/singtao_logo.JPG" alt="Singtao Logo" width="300" height="160">
                <h3>Email sent successfully! Please Check it in your mailbox</h3></div>';  
        }
    }
?>

</body>
</html>