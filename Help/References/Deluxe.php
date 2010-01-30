<?php
/**
 * Request System
 *
 * Deluxe.php generates a PDF references for Deluxe.
 *
 * @version 1.5
 * @link http://www.yourdomain.com/go/Request/
 * @author	Thomas LeZotte (tom@lezotte.net)
 *
  * @filesource
 *
 * PHP Debug
 * @link http://phpdebug.sourceforge.net/
 */
 
/**
 * - Start Page Loading Timer
 */
include_once('../../include/Timer.php');
$starttime = StartLoadTimer();
/**
 * - Set debug mode
 */
$debug_page = false;
include_once('debug/header.php');

/**
 * - Database Connection
 */
require_once('../../Connections/connDB.php'); 
/**
 * - Config Information
 */
require_once('../../include/config.php'); 

/* Update Summary */
Summary($dbh, 'Deluxe References', $_SESSION['eid']);


/* --------------------- Generate PDF --------------------------- */
require('fpdf/fpdf.php');


class PDF extends FPDF
{
function Header()
{
	$this->Image('http://www.yourdomain.com/Common/images/Deluxe.jpg',5,5);	//Print Deluxe Logo
	$this->SetFont('Arial','',10);		//Set Font: Type, Size
	$this->SetY(20);
	$this->Cell(0,5,'P.O. Box 9002',0,1,'C');				//Deluxe Address
	$this->Cell(0,5,'Fraser, MI 48026-9002',0,1,'C');		//Deluxe Address
	$this->Cell(0,5,'Phone: 586-276-1867',0,1,'C');			//Deluxe Address
	$this->Cell(0,5,'Fax: 586-276-1851',0,1,'C');			//Deluxe Address
	$this->Cell(0,5,'Contact: Laura Smith',0,1,'C');		//Deluxe Address	
}
}

//Instanciation of inherited class
$pdf=new PDF();


/* Generate Reference page */
$pdf->AddPage();												//Start Page
$pdf->SetFont('Arial','U',12);									//Set Font: Type, Size
$pdf->SetY(60);													//Move below Company Logo
$pdf->Cell(00,5,'BUSINESS REFERENCES',0,1,'C');					//Section Header
$pdf->Ln();
$pdf->SetFont('Arial','',12);									//Set Font: Type, Size
$pdf->Cell(115,5,'Complete Surface Technologies');				//Line 1
$pdf->Cell(115,5,'Imperial Cutting',0,1);						//Line 1
$pdf->Cell(115,5,'21338 Carlo Drive');							//Line 2
$pdf->Cell(115,5,'27300 Gloede',0,1);							//Line 2
$pdf->Cell(115,5,'Clinton Twp., MI 48038');						//Line 3
$pdf->Cell(115,5,'Warren, MI 48093',0,1);						//Line 3
$pdf->Cell(115,5,'Phone: 586-493-5800');						//Line 4
$pdf->Cell(115,5,'Phone: 586-772-2710',0,1);					//Line 4
$pdf->Cell(115,5,'Attn: Lou');									//Line 5
$pdf->Cell(115,5,'Attn: Larry or Debbie',0,1);					//Line 5
$pdf->Ln();
$pdf->Cell(115,5,'Crown Boring');								//Line 1
$pdf->Cell(115,5,'Industrial Service & Supply Inc.',0,1);		//Line 1
$pdf->Cell(115,5,'15985 Sturgeon');								//Line 2
$pdf->Cell(115,5,'27610 Collee Park Dr.',0,1);					//Line 2
$pdf->Cell(115,5,'Roseville, MI 48066');						//Line 3
$pdf->Cell(115,5,'Warren, MI 48088',0,1);						//Line 3
$pdf->Cell(115,5,'Phone: 586-773-4900');						//Line 4
$pdf->Cell(115,5,'Phone: 586-771-4720',0,1);					//Line 4
$pdf->Cell(115,5,'Attn: Steve Roncelli');						//Line 5
$pdf->Cell(115,5,'Attn: Michelle or Mike',0,1);					//Line 5
$pdf->Ln();
$pdf->Cell(115,5,'GT Gundrilling');								//Line 1
$pdf->Cell(115,5,'US Boring',0,1);								//Line 1
$pdf->Cell(115,5,'13313 West Star Dr.');						//Line 2
$pdf->Cell(115,5,'24895 Mound Rd.',0,1);						//Line 2
$pdf->Cell(115,5,'Shelby Twp, MI 48315');						//Line 3
$pdf->Cell(115,5,'Warren, MI 48091',0,1);						//Line 3
$pdf->Cell(115,5,'Phone: 586-778-1340');						//Line 4
$pdf->Cell(115,5,'Phone: 586-756-7511',0,1);					//Line 4
$pdf->Cell(115,5,'Attn: Gail',0,1);								//Line 5
$pdf->Ln();
$pdf->Ln();
$pdf->Ln();
$pdf->SetFont('Arial','U',12);									//Set Font: Type, Size
$pdf->Cell(00,5,'BANK REFERENCES',0,1,'C');						//Section Header
$pdf->Ln();
$pdf->SetFont('Arial','',12);									//Set Font: Type, Size
$pdf->Cell(115,5,'Bank One, N.A.');								//Line 1
$pdf->Cell(115,5,'Richard Babcock',0,1);						//Line 1
$pdf->Cell(115,5,'Group A');									//Line 2
$pdf->Cell(115,5,'Loan Officer',0,1);							//Line 2
$pdf->Cell(115,5,'611 Woodward Ave.');							//Line 3
$pdf->Cell(115,5,'Phone: 312-732-2002',0,1);					//Line 3
$pdf->Cell(115,5,'Detroit, MI 48226',0,1);						//Line 4
$pdf->Cell(115,5,'Account No: 5807974',0,1);					//Line 5
$pdf->Ln();
$pdf->Ln();
$pdf->Ln();
$pdf->SetFont('Arial','B',12);									//Set Font: Type, Size
$pdf->Cell(00,5,'PURCHASE ORDERS REQUIRED',0,1,'C');			//Tax 
$pdf->Cell(00,5,'TAX EXEMPT - INDUSTRIAL PROCESSING',0,1,'C');	//Tax
$pdf->Cell(00,5,'FEDERAL TAX# 38-2901017',0,1,'C');				//Tax
/* Set output preference */
$output = ($_GET['output'] == 'save') ? 'D' : 'I';
$pdf->Output('DeluxeReferences.pdf',$output);


/**
 * - Display Debug Information
 */
include_once('debug/footer.php');
/**
 * - Disconnect from database
 */
$dbh->disconnect();
?>