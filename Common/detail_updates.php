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
echo "hi";

  if ($_GET['a'] == 'po') {
	  $query="UPDATE PO SET po='" . $_POST['poNumber'] . "' WHERE id='" . $_GET['id'] . "'"; 
	  echo $query;
  }
  
  $result=mysql_query($query);
  $num=mysql_numrows($result);
  mysql_close();
  
  echo $('poNumber').innerHTML = ($num > 0) ? $_POST['poNumber'] : error;
?>
