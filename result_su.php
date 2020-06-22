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
<title>Thanks For Your Submit</title>
</head>
<body>



<?php 
	if(isset($_POST['confirmation_id'])){
?>
<div style="text-align:center">
<img src="./imgs/singtao_logo.JPG" alt="Singtao Logo" width="300" height="160">
<div id='content' style="text-align:initial;margin:auto;width:600px">
	<p>The survey is submitted successfully, your confirmation number is:
	<strong><?php echo $_POST['confirmation_id']?></strong></p> 
	<p>Please print or save below advertising voucher for future use. </p>
	<p>問卷已成功提交，你的確認碼為:<strong><?php echo $_POST['confirmation_id']?></strong></p>
	<p>請打印或保存下方的廣告代用券，以備將來使用。<input type="button" value="Save&Print" onclick="window.open('https://events.singtao.ca/templates/default/pdfSaver/voucher_<?php echo $_POST['confirmation_id']?>.pdf')" /></p>
	<form action="./send_email.php" method="POST">
		<p><label for="EEmail">If you prefer to have the advertising voucher sent to your email, please enter your email here </label></p>
		<p>如果你希望把廣告代用券發送到你的電子郵箱，請在此處輸入你的電郵地址：</p>
		<input type="text" style="display:none" id="confirmation_id" name="confirmation_id" value="<?php echo $_POST['confirmation_id']?>">
		<p>
			<input type="text" id="CEmail" name="email" required>
			<input type="submit" value="Send">
		</p>
		<br><br>
	</form>

<?php
} else {
	echo '
	<div style="text-align:center">
	<img src="./imgs/singtao_logo.JPG" alt="Singtao Logo" width="300" height="160">
	<h3>You Need To Finish The Survey First!</h3></div>';
}

require('../generatePDF/fpdf.php');

class PDF extends FPDF
{
// Page header
function Header()
{
	// Logo
	//$this->centreImage("https://events.singtao.ca/vouchers/44/A1448547098.png");
	//$this->Cell(0,10,'ADVERTISING VOUCHER',0,1,'C');
	$this->Image("https://events.singtao.ca/templates/default/imgs/singtao_logo.JPG",80,6,50,0,'JPG');
	$this->Ln(25);
	// Arial bold 15
	$this->SetFont('Arial','B',15);
	// Title
	$this->Cell(0,10,'ADVERTISING VOUCHER',0,1,'C');
	// Line break
	$this->Ln(8);
}

// Page footer
function Footer()
{
	// Position at 1.5 cm from bottom
	$this->SetY(-15);
	// Arial italic 8
	$this->SetFont('Arial','I',8);
	// Page number
	$this->Cell(0,10,'Page '.$this->PageNo().'/{nb}',0,0,'C');
}

}

// Instanciation of inherited class
$pdf = new PDF();
$pdf->AliasNbPages();
$pdf->AddPage();
$pdf->SetFont('Times','',12);
if(isset($_POST['confirmation_id'])){
$id = $_POST['confirmation_id'];
$pdf->Multicell(0,5,"Thank you for participating Sing Tao Business Recovery Advertising Survey, your confirmation number is
".$id.". This is your $50 worth of advertising voucher, applicable to any advertising channels among Sing
Tao Media Group Canada.


Terms & Conditions:
-	  Advertising voucher can only be applied to advertising placement, excluding production. 
-	  Advertising booking and placement must be finished by October 31, 2020.  
-	  Advertising voucher is applicable to new advertising orders, but not for settling any previous advertising orders. 
-	  Each Company can only redeem one advertising voucher.

	   
	 
	 
	 
Once again, we thank you for your participation.

Yours Sincerely,
SING TAO MEDIA GROUP CANADA
	",0,1);


$filename="/var/www_events.singtao/html/templates/default/pdfSaver/voucher_$id.pdf";
$pdf->Output('F',$filename);
?>

</div>
<?php
}


?>
</body>
</html>