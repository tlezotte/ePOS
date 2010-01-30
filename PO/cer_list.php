<?php
/**
 * Request System
 *
 * cer_list.php displays detailed information on PO.
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
 * PDF Toolkit
 * @link http://www.accesspdf.com/
 */


/**
 * - Config Information
 */
require_once('../include/config.php');
?>


<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>CER List</title>
    <link type="text/css" rel="stylesheet" href="../default_yui.css" />
    <link type="text/css" rel="stylesheet" href="/Common/js/yahoo/fonts/fonts-min.css" />							<!-- Datatable, TabView -->
    <link type="text/css" rel="stylesheet" href="/Common/js/yahoo/assets/skins/sam/datatable.css" />				<!-- Datatable -->
</head>

<body class="yui-skin-sam">
<div id="cerTable"></div>

<script type="text/javascript" src="/Common/js/yahoo/yahoo-dom-event/yahoo-dom-event.js" ></script>		<!-- Menu, TabView, Datatable -->
<script type="text/javascript" src="/Common/js/yahoo/utilities/utilities.js"></script>					<!-- Datatable -->
<script type="text/javascript" src="/Common/js/yahoo/datasource/datasource-beta-min.js"></script>		<!-- Datatable -->
<script type="text/javascript" src="/Common/js/yahoo/datatable/datatable-beta-min.js"></script>			<!-- Datatable -->
<script type="text/javascript" src="/Common/js/yahoo/connection/connection-min.js" ></script>			<!-- Datatable -->

<script type="text/javascript">
YAHOO.util.Event.addListener(window, "load", function() {
	YAHOO.example.XHR_JSON = new function() {
		this.formatCER = function(elCell, oRecord, oColumn, sData) {
			elCell.innerHTML = "<div style='white-space: normal;width:20%'><a href='<?= $default['URL_HOME']; ?>/CER/detail.php?id=" + oRecord.getData("id") + "' title='Click to get a Detailed view' class='dark'><img src='../images/detail.gif' width='18' height='20' border='0' align='absmiddle'> " + oRecord.getData("cer") + "</a></div>";
		};

		this.formatPurpose = function(elCell, oRecord, oColumn, sData) {
			elCell.innerHTML = "<div style='white-space: normal;width:77%'><a href='<?= $default['URL_HOME']; ?>/CER/detail.php?id=" + oRecord.getData("id") + "' title='Click to get a Detailed view' class='dark'>" + oRecord.getData("purpose") + "</a></div>";
		};
				
		var colCER = [
			{key:"cer", label:"CER", sortable:true, formatter:this.formatCER},
			{key:"purpose", label:"Purpose", formatter:this.formatPurpose}
		];
		var cfgCER = {
			initialRequest:"output=json",
			sortedBy:{key:"cer",dir:"desc"}
		};

		this.dsCER = new YAHOO.util.DataSource("../data/cer.php?");
		this.dsCER.responseType = YAHOO.util.DataSource.TYPE_JSON;
		this.dsCER.responseSchema = {
			resultsList: "ResultSet.Result",
			fields: ["id",
					 "cer",
					 "purpose"]
		};

		this.CER = new YAHOO.widget.DataTable("cerTable", colCER, this.dsCER, cfgCER);
	};
});
</script>
</body>
</html>
