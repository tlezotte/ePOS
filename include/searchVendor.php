<table border="0" align="center" cellpadding="0" cellspacing="0">
  <tr>
	<td height="30" class="BGAccentVeryDark"><table width="100%" border="0" cellpadding="0" cellspacing="0">
	  <tr>
		<td width="50%" height="30" class="DarkHeaderSubSub">&nbsp;&nbsp;Search Vendors...</td>
		<td width="50%" valign="middle"><div align="right">&nbsp;&nbsp;</div></td>
	  </tr>
	</table></td>
  </tr>
  <tr>
	<td class="BGAccentVeryDarkBorder"><form name="Form2" method="post" action="<?= $_SERVER['PHP_SELF']; ?>" runat="vdaemon">
		<table width="100%" border="0">
		  <tr>
			<td><vllabel form="Form2" validators="supplier" errclass="valError"></vllabel>
				<input id="vendSearch" name="vendSearch" type="text" size="50" onChange="Effect.SlideDown('vendInformation');" />
				<script type="text/javascript">
						Event.observe(window, "load", function() {
							var aa = new AutoAssist("vendSearch", function() {
								return "../Common/vendor.php?q=" + this.txtBox.value;
							});
						});
					  </script></td>
			<td><input name="imageField" type="image" src="../images/button.php?i=b70.png&l=Next" border="0">
				<input name="supplier" type="hidden" id="supplierNew">
				<input name="stage" type="hidden" id="stage" value="one">
				<vlvalidator name="supplier" type="required" control="supplier"></td>
		  </tr>
		</table>
		<div id="vendInformation" style="display:none">
		<table width="100%" border="0">
		  <tr>
			<td colspan="2" height="35" valign="bottom" class="smallAreaI">Vendor Information</td>
		  </tr>
		  <tr>
			<td width="100"><?= $language['label']['vendID']; ?>:</td>
			<td id="vendID" class="label">&nbsp;</td>
		  </tr>
		  <tr>
			<td><?= $language['label']['vendName']; ?>:</td>
			<td id="vendName" class="label">&nbsp;</td>
		  </tr>
		  <tr>
			<td><?= $language['label']['vendAddress']; ?>:</td>
			<td id="vendAddress1" class="label">&nbsp;</td>
		  </tr>
		  <tr>
			<td>&nbsp;</td>
			<td id="vendAddress2" class="label">&nbsp;</td>
		  </tr>
		  <tr>
			<td><?= $language['label']['vendCity'] . ", " . $language['label']['vendState'] . " " . $language['label']['vendZip']; ?>:</td>
			<td id="vendAddress3" class="label">&nbsp;</td>
		  </tr>
		  <tr>
			<td><?= $language['label']['vendCountry']; ?>:</td>
			<td id="vendCountry" class="label">&nbsp;</td>
		  </tr>
		</table>
		</div>
	</form></td>
  </tr>
</table>