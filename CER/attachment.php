<?php
/* ---------- START ICON DISPLAY ---------- */
switch ($CER['file_ext']) {
case 'gif':
   $icon = "gif";
   break;
case 'jpg':
case 'jpeg':
   $icon = "jpg";
   break;   
case 'pdf':
   $icon = "pdf";
   break;
case 'doc':
   $icon = "doc";
   break;
case 'xls':
   $icon = "excel";
   break;
case 'htm': 
case 'html':
   $icon = "IE";
   break; 
case 'zip':
case 'tar':
case 'gz':
   $icon = "IE";
   break;        
default:
   $icon = "text";
}
/* ---------- END ICON DISPLAY ---------- */


/* ---------- START CONVERT FILE SIZE ---------- */
if ($CER['file_size'] >= 1048576) {
	$file_size_ind_rnd = round(($CER['file_size']/1024000),3) . " MB";
} elseif ($CER['file_size'] >= 1024) {	
	$file_size_ind_rnd = round(($CER['file_size']/1024),2) . " KB";
} elseif ($CER['file_size'] >= 0) {
	$file_size_ind_rnd = $CER['file_size'] . " bytes";
}
/* ---------- END CONVERT FILE SIZE ---------- */

if (isset($CER['file_name'])) {
	$Attachment = "&nbsp;<img src=\"../images/".$icon.".gif\" width=\"16\" height=\"16\" align=\"absmiddle\">".
				  "&nbsp;<a href=\"files/".$_GET['id'].".".$CER['file_ext']."\" class=\"black\" target=\"attachment\">".$CER['file_name']."</a>&nbsp;&nbsp;(".$file_size_ind_rnd.")";
} else {
	$Attachment = "&nbsp;none";
}
?>