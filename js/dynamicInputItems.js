
function showBlock(type) {
  var num = parseInt(document.forms['Form'].elements[type + '_count'].value) + 1;
  if (type == 'item') {  
	if (num <= 50) {
	  showItems(type + num, num);
    } else {
	   alert('The limit you can enter is 50.');
    }
  } else if (type == 'payment') {  
   if (num <= 6) {
	   showPayment(type + num, num);
   } else {
	   alert('The limit you can enter is 6.');
   }
  } 
}

function showItems(id, num) {
  var text = "<table width=100% border=0 class=BGAccentVeryDarkBorder>\
				  <tr><td colspan=2 class=CalendarHeader>ITEM " + num + "</td></tr>\
				  <tr><td height=5 colspan=2><img src=../images/spacer.gif width=5 height=5></td></tr>\
				  <tr>\
				    <td>Quanty:</td>\
					<td><input name=qty" + num + " type=text id=qty" + num + " size=8 maxlength=8 onBlur=Calculate();></td>\
				  </tr>\
				  <tr>\
					<td>Units:</td>\
					<td><select name=unit" + num + " id=unit" + num + ">\
					  <option value=0>Select One</option>\
					</select></td>\
				  </tr>\
				  <tr>\
					<td>Company Part Number:</td>\
					<td><input name=part" + num + " type=text id=part" + num + " size=25 maxlength=25></td>\
				  </tr>\
				  <tr>\
					<td>Manufactures Part Number:</td>\
					<td><input name=manuf" + num + " type=text id=manuf" + num + " size=25 maxlength=25></td>\
				  </tr>\
				  <tr>\
					<td>Item Description</td>\
					<td><input name=descr" + num + " type=text id=descr" + num + " size=100 maxlength=100></td>\
				  </tr>\
				  <tr>\
					<td>Price:</td>\
					<td><input name=price" + num + " type=text id=price" + num + " size=15 maxlength=15 onBlur=Calculate();></td>\
				  </tr>\
				  <tr>\
					<td>Category:</td>\
					<td><select name=cat" + num + ">\
					  <option value=0>Select One</option>\
					</select></td>\
				  </tr>\
				  <tr>\
					<td>Cadence Tool Number:</td>\
					<td><input name=vt" + num + " type=text id=vt" + num + " size=10 maxlength=10></td>\
				  </tr>\
				  <tr>\
					<td>&nbsp;</td>\
					<td><table border=0 align=right>\
					  <tr>\
						<td width=20><a href=javascript:onClick=showBlock('item');><img src=/Common/images/addition.gif width=16 height=16 border=0></a></td>\
						<td width=20><a href=javascript:onClick=removeBlock('item');><img src=/Common/images/subtraction.gif width=16 height=16 border=0></a></td>\
					  </tr>\
					</table></td>\
				  </tr>\
				</table><br>"; 
  addItem = parseInt(document.forms['Form'].elements['item_count'].value) + 1;
  document.getElementById(id).innerHTML = text;
  document.forms['Form'].elements['item_count'].value = addItem;
  document.getElementById('totalItems').innerHTML = addItem;
}

function showPayment(id, num) {
  var text = "<table border=0 cellspacing=2 cellpadding=0>\
				 <tr>\
					<td width=200>Vendor Payment " + num + ":</td>\
					<td width=125><input name=paymentAmount" + num + " type=text id=paymentAmount" + num + " onBlur=Calculate();></td>\
					<td width=110 align=center nowrap><input name=paymentDate" + num + " type=text id=paymentDate" + num + " size=10 maxlength=10 readonly>\
					  <a href=javascript:show_calendar('Form.paymentDate" + num + "')><img src=../images/calendar.gif width=17 height=18 border=0 align=absmiddle></a></td>\
					<td width=40 class=padding>\
					<a href=javascript:onClick=showBlock('payment');><img src=/Common/images/addition.gif width=16 height=16 border=0></a>\
					<a href=javascript:onClick=removeBlock('payment');><img src=/Common/images/subtraction.gif width=16 height=16 border=0></a></td>\
				  </tr>\
			  </table>"; 
  document.getElementById(id).innerHTML = text;
  document.forms['Form'].elements['payment_count'].value = parseInt(document.forms['Form'].elements['payment_count'].value) + 1;
}


function removeBlock(type) {
  var num = parseInt(document.forms['Form'].elements[type + '_count'].value);
  if (num > 1) { 
	  removeItem = parseInt(document.forms['Form'].elements[type + '_count'].value) - 1;
	  document.getElementById(type + num).innerHTML= "";
	  document.forms['Form'].elements[type + '_count'].value = removeItem;
	  document.getElementById('totalItems').innerHTML = removeItem;
  }
}
