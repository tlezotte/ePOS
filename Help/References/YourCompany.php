<?php
/**
 * Request System
 *
 * Company.php generate a PDF references for Company.
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
Summary($dbh, 'Company References', $_SESSION['eid']);

/* --------------------- Generate PDF --------------------------- */
require('fpdf/fpdf.php');


class PDF extends FPDF
{
function Header()
{
	$this->Image('http://www.yourdomain.com/Common/images/Company.jpg',5,5);	//Print Company Logo
	$this->SetFont('Arial','',10);		//Set Font: Type, Size
	$this->SetY(30);
	$this->Cell(00,5,'Your Company',0,1,'C');				//Company Address
	$this->Cell(00,5,'977 East 14 Mile Road',0,1,'C');			//Company Address
	$this->Cell(00,5,'Troy, MI 48083',0,1,'C');					//Company Address
}
}

//Instanciation of inherited class
$pdf=new PDF();


/* Generate Reference page */
$pdf->AddPage();												//Start Page
$pdf->SetFont('Arial','U',12);									//Set Font: Type, Size
$pdf->SetY(70);													//Move below Company Logo
$pdf->Cell(00,5,'BUSINESS REFERENCES',0,1,'C');					//Section Header
$pdf->Ln();
$pdf->SetFont('Arial','',12);									//Set Font: Type, Size
$pdf->Cell(115,5,'A. Schulman');				//Line 1
$pdf->Cell(115,5,'Warren Pipe & Supply',0,1);						//Line 1
$pdf->Cell(115,5,'2100 E. Maple');							//Line 2
$pdf->Cell(115,5,'18660 15 Mile Road',0,1);							//Line 2
$pdf->Cell(115,5,'Birmingham, MI 48008');						//Line 3
$pdf->Cell(115,5,'Fraser, MI 48026',0,1);						//Line 3
$pdf->Cell(115,5,'Phone: 586-643-6100');						//Line 4
$pdf->Cell(115,5,'Phone: 586-294-6810 Brian',0,1);					//Line 4
$pdf->Cell(115,5,'Fax: 586-643-7839');						//Line 5
$pdf->Cell(115,5,'Fax: 586-294-4640',0,1);					//Line 5
$pdf->Ln();
$pdf->Cell(115,5,'Universal Container');								//Line 1
$pdf->Cell(115,5,'Cadillac Electric',0,1);		//Line 1
$pdf->Cell(115,5,'10750 Galaxie');								//Line 2
$pdf->Cell(115,5,'20700 Hubbell',0,1);					//Line 2
$pdf->Cell(115,5,'Ferndale, MI 48220');						//Line 3
$pdf->Cell(115,5,'Detroit, MI 48237',0,1);						//Line 3
$pdf->Cell(115,5,'Phone: 248-543-2788 Laura');						//Line 4
$pdf->Cell(115,5,'Phone: 313-967-1221 MaryLynne',0,1);					//Line 4
$pdf->Cell(115,5,'Fax: 248-543-4952');						//Line 5
$pdf->Cell(115,5,'Fax: 313-967-1281',0,1);					//Line 5
$pdf->Ln();
$pdf->Ln();
$pdf->Ln();
$pdf->SetFont('Arial','U',12);									//Set Font: Type, Size
$pdf->Cell(00,5,'BANK REFERENCES',0,1,'C');						//Section Header
$pdf->Ln();
$pdf->SetFont('Arial','',12);									//Set Font: Type, Size
$pdf->Cell(00,5,'J.P. Morgan Chase & Co.',0,1);								//Line 1
$pdf->Cell(00,5,'PO Box 260180',0,1);									//Line 2
$pdf->Cell(00,5,'Baton Rough, LA 70826-0180',0,1);							//Line 3
$pdf->Cell(00,5,'Account No: 695212043',0,1);					//Line 5
$pdf->Cell(00,5,'225-332-3535 Ledrake',0,1);					//Line 5
$pdf->Cell(00,5,'225-333-7788 Ledrake',0,1);					//Line 5
$pdf->Cell(00,5,'Fax: 225-332-7269',0,1);					//Line 5
$pdf->Ln();
$pdf->Ln();
$pdf->Ln();
$pdf->SetFont('Arial','B',12);									//Set Font: Type, Size
$pdf->Cell(00,5,'TAX EXEMPT - INDUSTRIAL PROCESSING',0,1,'C');	//Tax
$pdf->Cell(00,5,'FEDERAL TAX NUMBERS',0,1,'C');				//Tax
$pdf->Cell(00,5,'20-2695972',0,1,'C');				//Tax
/* Set output preference */
$output = ($_GET['output'] == 'save') ? 'D' : 'I';
$pdf->Output('CompanyReferences.pdf',$output);


/**
 * - Display Debug Information
 */
include_once('debug/footer.php');
/**
 * - Disconnect from database
 */
$dbh->disconnect();
?>