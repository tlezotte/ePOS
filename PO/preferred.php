<?php
/**
 * Request System
 *
 * suppliers.php list all suppliers.
 *
 * @version 1.5
 * @link http://www.yourdomain.com/go/Request/
 * @author	Thomas LeZotte (tom@lezotte.net)
 *
 * @package Administration
  * @filesource
 *
 * PHP Debug
 * @link http://phpdebug.sourceforge.net/
 */
 

/**
 * - Set debug mode
 */
$debug_page = false;
include_once('debug/header.php');

/**
 * - Database Connection
 */
require_once('../Connections/connDB.php');
/**
 * - Check User Access
 */
require_once('../security/check_user.php');
/**
 * - Config Information
 */
require_once('../include/config.php'); 


$suppliers_query = "SELECT *
					FROM Prefered
					ORDER BY vendor";
$suppliers_sql = $dbh->prepare($suppliers_query);								
/* ------------- END DATABASE CONNECTIONS --------------------- */
?>



<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title><?= $default['title1']; ?></title>
	<link href="http://www.yourdomain.com/Common/newCompany.css" rel="stylesheet" type="text/css" media="screen">
	<link href="../epos.css" type="text/css" charset="UTF-8" rel="stylesheet">
	<script src="http://www.yourdomain.com/Common/js/pointers.js" type="text/javascript"></script>
  <style type="text/css">
<!--
body {
	margin-left: 0px;
	margin-top: 0px;
	margin-right: 0px;
	margin-bottom: 0px;
}
-->
</style>
</head>

<body>
<table width="100%"  border="0" align="center" cellpadding="0" cellspacing="0">
  <tr>
    <td height="25" class="xpHeaderTopActive">&nbsp;Vendor Name</td>
    <td valign="middle" class="xpHeaderTop"><table width="100%"  border="0" cellspacing="0" cellpadding="0">
        <tr>
          <td><strong>&nbsp;Product Description</strong> </td>
          <td width="80" nowrap><!--<a href="javascript:window.close();" class="ErrorNameText">[ close ]</a>--></td>
        </tr>
      </table></td>
  </tr>
  <?php 
			$suppliers_sth = $dbh->execute($suppliers_sql);
			$num_rows = $suppliers_sth->numRows();
			while($suppliers_sth->fetchInto($SUPPLIERS)) {
				/* Line counter for alternating line colors */
				$counter++;
				$row_color = ($counter % 2) ? FFFFFF : DFDFBF;
			?>
  <tr <?php pointer($row_color); ?>>
    <td valign="top" nowrap bgcolor="#<?= $row_color; ?>" class="padding"><?= ucwords(strtolower($SUPPLIERS['vendor'])); ?></td>
    <td valign="top" bgcolor="#<?= $row_color; ?>" class="padding"><?= ucwords(strtolower($SUPPLIERS['description'])); ?></td>
  </tr>
  <?php } ?>
</table>
</body>
</html>


<?php 
/**
 * - Display Debug Information
 */
include_once('debug/footer.php');
/**
 * - Disconnect from database
 */
$dbh->disconnect();
?>