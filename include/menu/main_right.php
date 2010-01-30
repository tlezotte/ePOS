<?php
$menu1_label = "Requisition Request";
$menu1_url = $default['url_home'] . "/Request/PO/index.php";
$menu1_css = (preg_match("/PO/", $_SERVER['REQUEST_URI'])) ? current : off;

$menu2_label = "Capital Acquisition";
$menu2_url = $default['url_home'] . "/Request/CER/index.php";
$menu2_css = (preg_match("/CER/", $_SERVER['REQUEST_URI'])) ? current : off;

$menu3_label = "Transfer";
$menu3_url = $default['url_home'] . "/Request/_index.php?action=transfer";
$menu3_css = (preg_match("/transfer/", $_SERVER['REQUEST_URI'])) ? current : off;

$menu4_label = "Conversion";
$menu4_url = $default['url_home'] . "/Request/_index.php?action=conversion";
$menu4_css = (preg_match("/conversion/", $_SERVER['REQUEST_URI'])) ? current : off;

$menu5_label = "Promotion";
$menu5_url = $default['url_home'] . "/Request/_index.php?action=promotion";
$menu5_css = (preg_match("/promotion/", $_SERVER['REQUEST_URI'])) ? current : off;

$menu6_label = "Administration";
$menu6_url = $default['url_home'] . "/Administration/index.php";
$menu6_css = (preg_match("/Administration/", $_SERVER['REQUEST_URI'])) ? current : inactive;

$menu7_label = "My Account";
$menu7_url = $default['url_home'] . "/Administration/index.php";
$menu7_css = (preg_match("/Administration/", $_SERVER['REQUEST_URI'])) ? current : inactive;
?>

<?php if (isset($_COOKIE['request_role'])) { ?>
<script type="text/javascript">var group="<?= $_COOKIE['group']; ?>";</script>
<?php } ?> 
<script type="text/javascript" src="<?= $default['URL_HOME']; ?>/js/rightmenu.js"></script>
<link rel="stylesheet" type="text/css" href="<?= $default['URL_HOME']; ?>/style/dd_tabs.css" />

<table width="100%" border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td><div id="ddcolortabs">
      <ul>
        <li id="<?= $menu1_css; ?>"><a href="<?= $menu1_url; ?>" title="PO" onClick="return clickreturnvalue()" onMouseover="dropdownmenu(this, event, menu1, '150px')" onMouseout="delayhidemenu()"><span><?= $menu1_label; ?></span></a></li>
        <li id="<?= $menu2_css; ?>"><a href="<?= $menu1_ur2; ?>" title="CER" onClick="return clickreturnvalue()" onMouseover="dropdownmenu(this, event, menu2, '150px')" onMouseout="delayhidemenu()"><span><?= $menu2_label; ?></span></a></li>		
		<?php if ($_SESSION['request_access'] >= 1) { ?>
        <li id="<?= $menu6_css; ?>"><a href="<?= $menu6_url; ?>" title="Administration" onClick="return clickreturnvalue()" onMouseover="dropdownmenu(this, event, menu6, '150px')" onMouseout="delayhidemenu()"><span><?= $menu6_label; ?></span></a></li>
		<?php } else { ?>
		<li id="<?= $menu7_css; ?>"><a href="<?= $menu7_url; ?>" title="My Account" onClick="return clickreturnvalue()" onMouseover="dropdownmenu(this, event, menu7, '150px')" onMouseout="delayhidemenu()"><span><?= $menu7_label; ?></span></a></li>
		<?php } ?>
      </ul>
    </div></td>
    <td width="10">&nbsp;</td>
  </tr>
</table>
