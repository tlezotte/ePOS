<?php
/**
 * Request System
 *
 * track.php track shipments.
 *
 * @version 1.5
 * @link http://www.yourdomain.com/go/Request/
 * @author	Thomas LeZotte (tom@lezotte.net)
 *
 * @package PO
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
require_once('../../Connections/connDB.php');
/**
 * - Config Information
 */
require_once('../../include/config.php'); 
/**
 * - PEAR QuickForm
 */
require_once ('HTML/QuickForm.php');

/* Update Summary */
Summary($dbh, 'Track Shipment - BlackBerry', $_SESSION['eid']);
?>



<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<title><?= $default['title1']; ?></title>
<meta name="author" content="Thomas LeZotte" />
<meta name="copyright" content="2005 Your Company" />
<link href="../handheld.css" rel="stylesheet" type="text/css" media="handheld">
</head>

<body>
<table width="240"  border="0" cellpadding="0" cellspacing="0">
  <tr>
    <td nowrap><div align="center"><a href="../home.php"><img src="/Common/images/Company200.gif" alt="Your Company" name="Company" width="200" height="50" border="0"></a></div></td>
  </tr>
  <tr>
    <td nowrap><div align="center">
      <?= $default['title1']; ?>
    </div></td>
  </tr>
  <tr>
    <td nowrap><div align="center"><strong> Shippment Tracking </strong></div></td>
  </tr>
</table>
<table width="240"  border="0" cellpadding="0" cellspacing="0">
  <tr>
    <td><form action="http://www.fedex.com/Tracking" method="post" name="tracking" id="tracking" target="track" onsubmit="try { var myValidator = validate_tracking; } catch(e) { return true; } return myValidator(this);">
      <div>
        <img src="/Common/images/Company_Bullet.gif" width="11" height="15">
        <input name="language" type="hidden" value="english" />
        <input name="cntry_code" type="hidden" value="us" />
        <input name="ascend_header" type="hidden" value="1" />
        <input name="clienttype" type="hidden" value="pluginff" />
        <input name="template_type" type="hidden" value="plugin" />
        <input name="tracknumbers" type="text" size="7" />
        <input name="fedex" type="submit" value="FedEx" class="button">
      </div>
    </form></td>
  </tr>
  <tr>
    <td><form action="http://wwwapps.ups.com/WebTracking/processInputRequest" method="get" name="trkinput" id="trkinput" target="track" onsubmit="try { var myValidator = validate_trkinput; } catch(e) { return true; } return myValidator(this);">
      <div>
        <img src="/Common/images/Company_Bullet.gif" width="11" height="15">
        <input name="AgreeToTermsAndConditions" type="hidden" value="yes" />
        <input name="HTMLVersion" type="hidden" value="5.0" />
        <input name="loc" type="hidden" value="en_US" />
        <input name="Requester" type="hidden" value="UPSHome" />
        <input name="tracknum" type="text" size="7" />
        <input name="ups" type="submit" value="UPS" class="button">
      </div>
    </form></td>
  </tr>
  <tr>
    <td><form action="http://trkcnfrm1.smi.usps.com/netdata-cgi/db2www/cbd_243.d2w/output" method="post" name="getTrackNum" id="getTrackNum" target="track" onsubmit="try { var myValidator = validate_getTrackNum; } catch(e) { return true; } return myValidator(this);">
      <div>
        <img src="/Common/images/Company_Bullet.gif" width="11" height="15">
        <input name="CAMEFROM" type="hidden" value="OK" />
        <input name="strOrigTrackNum" type="text" size="7" />
        <input name="usps" type="submit" value="USPS" class="button">
      </div>
    </form></td>
  </tr>
  <tr>
    <td><form action="http://track.dhl-usa.com/TrackByNbr.asp" method="post" name="frmTrackByNbr" id="frmTrackByNbr" target="track" onsubmit="try { var myValidator = validate_frmTrackByNbr; } catch(e) { return true; } return myValidator(this);">
      <div>
        <img src="/Common/images/Company_Bullet.gif" width="11" height="15">
        <input name="txtTrackNbrs" type="text" size="7" />
        <input name="dhl" type="submit" value="DHL" class="button">
      </div>
    </form></td>
  </tr>
</table>
</body>
</html>


<?php
/**
 * - Display Debug Information
 */
include_once('debug/footer.php');
?>