<table cellspacing="0" cellpadding="0" width="200" align="left" summary="" border="0">
    <tr>
      <td valign="top" width="13" background="../../images/asyltlb.gif"><img height="20" alt="" src="../../images/t.gif" width="13" border="0"></td>
      <td valign="top" width="165" bgcolor="#cccc99"><img height="1" alt="" src="../../images/asybase.gif" width="145" border="0"> <br>
          <table width="100%" border="0" cellspacing="0" cellpadding="1" rules="rows">
            <tr>
              <td width="17" height="22" align="center"><a href="javascript:void();" onClick="new Effect.toggle('items','blind')" class="black" <?php help('', 'Show or Hide the Item Information', 'default'); ?>><img src="../../images/text.gif" width="16" height="16" border="0" align="texttop"></a></td>
              <td height="22"><a href="javascript:switchItemsView('tab');" <?php help('', 'Switch Items View', 'default'); ?> class="DarkHeaderSubSub" id="itemsView">Items - Tab View</a></td>
            </tr>
        </table>
	    <div id="phItemsMenu" style="display:<?= $phItemsMenuStatus; ?>">
          <table width="100%" border="0" cellspacing="0" cellpadding="1" rules="rows">
            <tr>
              <td width="17" height="22" align="center"><a href="javascript:switchHistory('off');" class="black" <?php help('', 'Hide Purchase History', 'default'); ?>><img src="/Common/images/history.gif" width="18" height="18" border="0" align="texttop"></a></td>
			  <td height="22"><a href="javascript:switchHistory('on');" <?php help('', 'View Purchase History for Vendor', 'default'); ?> class="DarkHeaderSubSub">Purchase History</a></td>
            </tr>
        </table>
		</div>
	    <table width="100%" border="0" cellspacing="0" cellpadding="1" rules="rows">
          <tr>
            <td width="17" height="22" align="center"><img src="../../images/add.gif" width="14" height="14" border="0" align="absmiddle"></td>
            <td height="22"><a href="../comments.php?request_id=0&action=add" title="Comment" <?php help('', 'Add a Comment', 'default'); ?> class="DarkHeaderSubSub" onClick="return GB_show(this.title, this.href, 350, 685)">Add Comment</a></td>
          </tr>
        </table></td>
      <td valign="top" width="22" background="../../images/asyltrb.gif"><img height="20" alt="" src="../../images/t.gif" width="22" border="0"></td>
    </tr>
    <tr>
      <td valign="top" width="22" colspan="3"><img height="37" alt="" src="../../images/asyltb.gif" width="200" border="0"></td>
    </tr>
</table>
