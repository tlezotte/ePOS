<?php
/**
 * License Manager
 *
 * software.php generates graphs for software.
 *
 * @version 1.5
 * @link http://www.yourdomain.com/go/License/
 * @author	Thomas LeZotte (tom@lezotte.net)
 *
 * @filesource
 *
 * ChartDirector
 * @link http://www.advsofteng.com/
 */
 
/**
 * - Database Connection
 */
require_once('../../Connections/connDB.php'); 
/**
 * - Check User Access
 */
require_once('../../security/check_user.php');
/**
 * - Config Information
 */
require_once('../../include/config.php'); 

/* Update Summary */
Summary($dbh, 'Software Licenses', $_SESSION['eid']);

$limit = $_GET['limit'];		//Get software count

$sql = <<< SQL
	SELECT s.name, l.version, sum( l.qty ) AS total
	FROM Strings l, Standards.Software s
	WHERE l.name = s.id
	  AND l.status = '1'
	GROUP BY s.name, l.version
	ORDER BY total DESC
	LIMIT $limit
SQL;

$data_sql = $dbh->prepare($sql);
$data_sth = $dbh->execute($data_sql);

while($data_sth->fetchInto($DATA)) {
  $data[] = $DATA['total'];
  $labels[] = ucwords(strtolower($DATA['name'].' '.$DATA['version']));
} 

require_once("chartdir/phpchartdir.php");

#Create a XYChart object of size 600 x 250 pixels 
$c = new XYChart(650, 450); 

#Add a title to the chart using Arial Bold Italic font 
#$c->addTitle("Software Licenses", "arialbi.ttf", 16, "336699");
#$c->addTitle2(Bottom, "Top $limit Software", "arialbi.ttf", 10, "336699");

#Set the plotarea at (100, 30) and of size 400 x 200 pixels. Set the plotarea 
#border, background and grid lines to Transparent 
$c->setPlotArea(175, 50, 400, 350); 

#Add a bar chart layer using the given data. Use a gradient color for the bars, 
#where the gradient is from dark green (0x008000) to white (0xffffff) 
$layer = $c->addBarLayer($data, 0xff00); 
$layer->set3D(); 

#Swap the axis so that the bars are drawn horizontally 
$c->swapXY(true); 

#Set the bar gap to 10% 
//$layer->setBarGap(0.1); 

#Use the format "US$ xxx millions" as the bar label 
//$layer->setAggregateLabelFormat("\${value|2,.}"); 

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
