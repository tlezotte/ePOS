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
$p = $_GET['p'];				// Plant id
$d = $_GET['d'];				// Department id
$item = $_GET['i'];				// Item number
$q = $_GET['q'];				// Ajax query


$query="SELECT DISTINCT CONCAT(coa_account,'-',coa_suffix) AS id, CONCAT('(',coa_account,'-',coa_suffix,') ',coa_description) AS name, coa_description
		FROM Standards.COA 
		WHERE coa_plant='" . $p . "'
		  AND coa_department='" . $d . "'
		  AND (CONCAT(coa_account,'-',coa_suffix) REGEXP '" . $q . "' 
		  		OR coa_description REGEXP '" . $q . "')
		ORDER BY coa_description
		LIMIT 10";
$result=mysql_query($query);
$num=mysql_numrows($result);

mysql_close();

$i = 0;
while ($i < $num) {
	/* -- Get Output -- */
	$id = mysql_result($result, $i, "id");
	$name = mysql_result($result, $i, "name");
	$description = mysql_result($result, $i, "coa_description");
	
	/* -- Format Output -- */
	$COAname = caps($name);
	$COAdescription = caps($description);
	$gl = $p . "-" . $d . "-" . $id;
	
	
	/* -- Print Selected Output -- */
	echo "<div onSelect=\"this.txtBox.value = '$COAdescription';
						  $('item" . $item . "_newCOAid').value = '$id';
						  $('item" . $item . "_newGLnumber').innerHTML = '$gl';
						  \"> $COAname </div>";
	$i++;
}

if ($num == 0) {
?>
  <img src="/Common/images/nochange.gif" align="absmiddle"> Not found in the Chart of Accounts. 
<?php
}
?>
