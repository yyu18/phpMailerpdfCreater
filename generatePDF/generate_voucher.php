<?php
require('fpdf.php');



class PDF extends FPDF
{
// Page header
function Header()
{
	// Logo
	//$this->centreImage("https://events.singtao.ca/vouchers/44/A1448547098.png");
	//$this->Cell(0,10,'ADVERTISING VOUCHER',0,1,'C');
	$this->Image('https://www.singtao.ca/wp-content/themes/singtaoca2019/images/logo.png',80,6,50,0,'PNG');
	$this->Ln(15);
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
echo $_POST['confirmation_id'];
$id = $_POST['confirmation_id'];
$pdf->Multicell(0,5,"Thank you for participating Sing Tao Business Recovery Advertising Survey, your confirmation number is
".$id.". This is your $50 worth of advertising voucher, applicable to any advertising channels among Sing
Tao Media Group Canada.


Terms &amp; Conditions:
-	  Expiry date: September 30, 2020. Order must be placed by the expiry date in order to redeem the
	   advertising voucher. Ad fulfillment must be finished by end of 2020.
-	  Advertising voucher is applicable to any new advertising orders, but not applicable for settling any
	   previous advertising orders.
	   
	 
	 
	 
Once again, we thank you for your participation.

Yours Sincerely,
SING TAO MEDIA GROUP CANADA
	",0,1);
}

$pdf->Output();
?>