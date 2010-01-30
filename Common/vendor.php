<?php
/**
 * Request System
 *
 * detail.php displays detailed information on PO.
 *
 * @version 1.5
 * @link https://hr.Company.com/go/HCR/
 * @author	Thomas LeZotte (tom@lezotte.net)
 *
 * @package PO
 * @filesource
 */


/**
 * - Database Connection
 */
require_once('../Connections/connDB.php');
/**
 * - Config Information
 */
require_once('../include/config.php'); 
/**
 * - Check User Access
 */
require_once('../security/check_user.php');

/* -- SQL -- */
$q = $_GET['q'];
//$v = ($_GET['v'] != 'all') ? "AND BTSTAT='A'" : $blank;
$query="SELECT * 
	  FROM Standards.Vendor 
	  WHERE (BTNAME REGEXP '$q'
	     OR BTVEND REGEXP '$q')
		AND BTSTAT = 'A'
		AND BTVEND NOT LIKE '%R'
	  ORDER BY BTNAME LIMIT 10";
$result=mysql_query($query);
$num=mysql_numrows($result);

mysql_close();

$i = 0;
while ($i < $num) {
	/* -- Get Output -- */
	$btname = mysql_result($result, $i, "BTNAME");
	$btvend = mysql_result($result, $i, "BTVEND");
	$btadd1 = mysql_result($result, $i, "BTADR1");
	$btadd2 = mysql_result($result, $i, "BTADR2");
	$btadd3 = mysql_result($result, $i, "BTADR3");
	$btpost = mysql_result($result, $i, "BTPOST");
	$btcurr = mysql_result($result, $i, "BTCURR");
	$bttrmc = mysql_result($result, $i, "BTTRMC");
	
	/* -- Format Output -- */
	$vendNameAndID = ucwords(strtolower($btname)) . " (".strtoupper($btvend).")";
	$vendID=strtoupper($btvend);
	$vendName=caps($btname);
	$vendAddress1=caps($btadd1);
	$vendAddress2=caps($btadd2);
	$vendAddress3=caps($btadd3);
	$vendCountry=strtoupper($btcurr);
	
	/* -- Print Selected Output -- */
	echo "<div onSelect=\"this.txtBox.value = '$vendNameAndID';
						  $('supplierNew').value = '$vendID';
						  $('supplierNewTerms').value = '$bttrmc';
						  $('vendID').innerHTML = '$vendID';
						  $('vendName').innerHTML = '$vendName';
						  $('vendAddress1').innerHTML = '$vendAddress1';
						  $('vendAddress2').innerHTML = '$vendAddress2';
						  $('vendAddress3').innerHTML = '$vendAddress3, $btpost';
						  $('vendCountry').innerHTML = '$vendCountry';
						  \"> $vendNameAndID </div>";
	$i++;
}

if ($num == 0) {
?>
  <img src="/Common/images/nochange.gif" align="absmiddle"> No vendor found. 
<?php
}
?>
