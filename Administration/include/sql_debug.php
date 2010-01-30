  <?php if ($_SESSION['request_access'] == '3') { ?>
  <div  id="debugPanel" style="display:none">
  <table border="0" align="center" cellpadding="0" cellspacing="0">
	<tr>
	  <td class="BGAccentDarkBorder"><table width="100%" border="0">
		  <tr class="BGAccentDark">
			<td nowrap class="HeaderAccentDark">Date</td>
			<td nowrap class="HeaderAccentDark">EID</td>
			<td width="30" align="center" nowrap class="HeaderAccentDark">Page</td>
			<td width="400" nowrap class="HeaderAccentDark">SQL</td>
		  </tr>
		  <?php
			$debug_query = $dbh->prepare("SELECT * FROM History WHERE type_id='" . $_GET['id'] . "' ORDER BY ts");
			$debug_sth = $dbh->execute($debug_query);
			while($debug_sth->fetchInto($DEBUG)) {
				$count_items++;
				$row_color = ($count_items % 2) ? FFFFFF : DFDFBF;
			?>
		  <tr <?php pointer($row_color); ?>>
			<td bgcolor="<?= $row_color; ?>" valign="top" nowrap><?= $DEBUG['ts']; ?></td>
			<td bgcolor="<?= $row_color; ?>" valign="top" nowrap><?= $DEBUG['eid']; ?></td>
			<td bgcolor="<?= $row_color; ?>" valign="top" nowrap><?= $DEBUG['page']; ?></td>
			<td bgcolor="<?= $row_color; ?>" valign="top"><?= $DEBUG['sql']; ?></td>
		  </tr>
		  <?php } ?>
	  </table>
	  </td>
	</tr>
  </table>
  </div>
  <?php } ?>