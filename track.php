<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Untitled Document</title>
</head>

<body>
<table border="0" align="center" cellpadding="0" cellspacing="5" id="trackShipments">
  <tr>
    <td valign="top"><table border="0" align="center" cellpadding="0" cellspacing="0">
      <tr>
        <td class="blueNoteAreaBorder"><table border="0">
          <tr class="blueNoteArea">
            <td class="label">Tracking Number</td>
            <td class="label">&nbsp;</td>
          </tr>
          <tr>
            <td class="label"><input name="tracking_number" type="text" id="tracking_number" size="30" maxlength="30" /></td>
            <td class="label"><input name="addTracking" type="image" id="addTracking" src="../images/add.gif" title="Submit Data|Select to submit tracking number" /></td>
          </tr>
        </table></td>
      </tr>
    </table></td>
    <td rowspan="2" align="right" valign="top"><img src="../images/spacer.gif" width="50" height="10" /></td>
    <td align="right" valign="top"><table border="0" align="center" cellpadding="0" cellspacing="0">
      <tr>
        <td class="blueNoteAreaBorder"><?php if ($tracking_count > 0) { ?>
              <table border="0">
                <tr class="blueNoteArea">
                  <td class="label">&nbsp;</td>
                  <td class="label">Tracking Number</td>
                  <td class="label">Delete</td>
                </tr>
                <?php
                                            $count=0;
                                            
                                            while ($tracking->fetchInto($TRACKING)) {
                                                $count++;
                                            ?>
                <tr>
                  <td class="label"><?= $count; ?>
                      <input type="hidden" name="track_id<?= $count; ?>" id="track_id<?= $count; ?>" value="<?= $TRACKING['id']; ?>" /></td>
                  <td class="label"><input name="tracking<?= $count; ?>" type="text" id="tracking<?= $count; ?>" value="<?= number_format($PAYMENTS['pay_amount'],2); ?>" size="30" maxlength="30" /></td>
                  <td align="center" class="label"><input type="checkbox" name="track_remove<?= $count; ?>" id="track_remove<?= $count; ?>" value="yes" /></td>
                </tr>
                <?php } ?>
              </table>
          <input name="payments_count" id="payments_count" type="hidden" value="<?= $payments_count; ?>" /></td>
      </tr>
    </table></td>
  </tr>
  <tr>
    <td>&nbsp;</td>
    <td align="right" height="30"><input name="updateTracking" id="updateTracking" type="image" src="../images/button.php?i=w70.png&amp;l=Update" alt="Update Tracking" border="0" /></td>
  </tr>
</table>
</body>
</html>
