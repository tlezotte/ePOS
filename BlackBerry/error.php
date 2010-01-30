<?php 
/**
 * Request System
 *
 * home.php is the default page after a seccessful login.
 *
 * @version 1.5
 * @link http://www.yourdomain.com/go/Request/
 * @author	Thomas LeZotte (tom@lezotte.net)
 *
 * @filesource
 *
 * PHP Debug
 * @link http://phpdebug.sourceforge.net/
 * Pear HTML_QuickForm
 * @link http://pear.php.net/package/HTML_QuickForm
 */


/**
 * - Config Information
 */
require_once('../include/config.php');
?>


<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<title><?= $default['title1']; ?></title>
<meta name="author" content="Thomas LeZotte" />
<meta name="copyright" content="2005 Your Company" />
<link href="handheld.css" rel="stylesheet" type="text/css" media="handheld">
</head>

<body>
<table width="240"  border="0" cellpadding="0" cellspacing="0">
  <tr>
    <td align="center"><div align="center"><a href="home.php"><img src="/Common/images/Company200.gif" alt="Your Company" name="Company" width="200" height="50" border="0"></a></div></td>
  </tr>
  <tr>
    <td align="center"><?= $default['title1']; ?></td>
  </tr>
  <tr>
    <td height="25" align="center"><strong>Error Report</strong></td>
  </tr>
  <tr>
    <td height="25" align="center">&nbsp;</td>
  </tr>
  <tr>
    <td class="ErrorNameText"><div align="center">
      <?= $_SESSION['error']; ?>
    </div></td>
  </tr>
  <tr>
    <td height="25">&nbsp;</td>
  </tr>
  <tr>
    <td><div align="center"><a href="javascript:history.go(-1)"><input name="back" type="button" value="Back" class="button"></a></div></td>
  </tr>
</table>
</body>
</html>
