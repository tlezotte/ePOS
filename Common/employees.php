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


$q = $_GET['q'];
$v = ($_GET['v'] != 'all') ? "AND e.status='0'" : $blank;
$l = ($_GET['l'] == 'on') ? "INNER JOIN Users u ON u.eid=e.eid" : $blank;
$query="SELECT * 
	  FROM Standards.Employees e
	    $l
	  WHERE e.lst REGEXP '$q'
		$v
	  ORDER BY e.lst LIMIT 10";
$result=mysql_query($query);
$num=mysql_numrows($result);
mysql_close();

$i = 0;
while ($i < $num) {
$lst = mysql_result($result, $i, "lst");
$fst = mysql_result($result, $i, "fst");
$mdl = mysql_result($result, $i, "mdl");
$eid = mysql_result($result, $i, "eid");
$name = ucwords(strtolower($fst." ".$mdl." ".$lst));
echo "<div onSelect=\"this.txtBox.value='$name';$('ajaxEID').value = '$eid'\"> $name </div>";
$i++;
}

if ($num == 0) {
?>
  <img src="/Common/images/nochange.gif" align="absmiddle"> No employees found. 
<?php
  }
?>
