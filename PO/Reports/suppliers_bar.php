<?php
/**
 * - Check User Access
 */
require_once('../../Connections/connDB.php'); 
/**
 * - Common Information
 */
require_once('../../include/config.php'); 

/* Update Summary */
Summary($dbh, 'Vendor Purchases', $_SESSION['eid']);

$start = $_GET['start'];
$startFull = date("M d, Y",strtotime($start));
$end = $_GET['end'];
$endFull = date("M d, Y",strtotime($end));

$sql = <<< SQL
	SELECT s.BTNAME AS name, sum( p.total ) AS total
	FROM PO p, Standards.Vendor s
	WHERE  s.BTVEND = p.sup
	  AND p.reqDate >= '$start'
	  AND p.reqDate <= '$end'
	  AND p.status IN ('A','O','R')
	GROUP BY p.sup
	ORDER BY total DESC
	LIMIT 10
SQL;

$data_sql = $dbh->prepare($sql);
$data_sth = $dbh->execute($data_sql);

while($data_sth->fetchInto($DATA)) {
  $data[] = $DATA['total'];
  $labels[] = ucwords(strtolower($DATA['name']));
} 

require_once("chartdir/phpchartdir.php");

#Create a XYChart object of size 600 x 250 pixels 
$c = new XYChart(500, 350); 

#Add a title to the chart using Arial Bold Italic font 
#$c->addTitle("Supplier Purchases", "arialbi.ttf", 16, "336699");
#$c->addTitle2(Bottom, "Top 10 between\n$startFull and $endFull", "arialbi.ttf", 10, "336699");

#Set the plotarea at (100, 30) and of size 400 x 200 pixels. Set the plotarea 
#border, background and grid lines to Transparent 
$c->setPlotArea(150, 50, 300, 250, 0xF9F8F0, 0xffffff);  

#Add a bar chart layer using the given data. Use a gradient color for the bars, 
#where the gradient is from dark green (0x008000) to white (0xffffff) 
$layer = $c->addBarLayer($data, 0x336699); 
$layer->set3D(); 

#Swap the axis so that the bars are drawn horizontally 
$c->swapXY(true); 

#Set the bar gap to 10% 
//$layer->setBarGap(0.1); 

#Use the format "US$ xxx millions" as the bar label 
$layer->setAggregateLabelFormat("\${value|2,.}"); 

#Set the bar label font to 10 pts Times Bold Italic/dark red (0x663300) 
$layer->setAggregateLabelStyle("arialbi.ttf", 10); 

#Set the labels on the x axis 
$textbox = $c->xAxis->setLabels($labels); 

#Set the x axis label font to 10pt Arial Bold Italic 
//$textbox->setFontStyle("arialbi.ttf"); 
//$textbox->setFontSize(10); 

#Set the x axis to Transparent, with labels in dark red (0x663300) 
//$c->xAxis->setColors(Transparent, 0x663300); 

#Set the y axis and labels to Transparent 
$c->yAxis->setColors(Transparent, Transparent); 

#output the chart 
header("Content-type: image/png"); 
print($c->makeChart2(PNG));
?>
