<?php
$option = "";

/* ---------- START ICON DISPLAY ---------- */
switch ($PO['file_ext']) {
case 'gif':
case 'GIF':
   $icon = "gif";
   $option = "target=\"attachment\"";
   break;
case 'jpg':
case 'JPG':
case 'jpeg':
   $icon = "jpg";
   $option = "target=\"attachment\"";
   break;   
case 'pdf':
case 'PDF':
   $icon = "pdf";
   $option = "target=\"attachment\"";
   break;
case 'doc':
case 'DOC':
   $icon = "word";
   $option = "target=\"attachment\"";
   break;
case 'xls':
case 'XLS':
   $icon = "excel";
   $option = "target=\"attachment\"";
   break;
case 'htm': 
case 'html':
   $icon = "html";
   $option = "target=\"attachment\"";
   break; 
case 'zip':
case 'tar':
case 'gz':
   $icon = "IE";
   $option = "target=\"attachment\"";
   break;        
default:
   $icon = "text";
   $option = "target=\"attachment\"";
}
/* ---------- END ICON DISPLAY ---------- */


if (!empty($PO['file_name'])) {
	$Attachment = "&nbsp;<img src=\"../../images/".$icon.".gif\" width=\"16\" height=\"16\" align=\"absmiddle\">".
				  "&nbsp;<a href=\"" . $default['url_home'] . "/PO/" . $default['files_store'] . "/" . $_GET['id'] . "." . $PO['file_ext'] . "\"  class=\"black\" ".$option.">".$PO['file_name']."</a>&nbsp;&nbsp;(".getfilesize($PO['file_size']).")";
} else {
	$Attachment = "&nbsp;&nbsp;NONE";
}
?>