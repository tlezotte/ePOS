<?php
if (array_key_exists('url', $_GET)) {
	$URL = "'" . $_GET['url'] . "'";
}
?>


<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
"http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<title></title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<meta name="cache-control" content="no-cache" />
<meta http-equiv="Expires" content="0" />
  <link type="text/css" href="/Common/newCompany.css" rel="stylesheet" media="screen">
  <link type="text/css" href="../epos.css" charset="UTF-8" rel="stylesheet">
<script type="text/javascript">
	function closeGB() {
	//	window.location.reload();
		parent.parent.GB_hide();
	}
	function reloadGB() {
		parent.parent.GB_hide();
		parent.parent.location.reload();
	}
	function forwardGB(url) {
		parent.parent.location.href = url;
	}		
</script>
</head>

<body <?php if (array_key_exists('gb', $_GET)) { ?>onLoad="setTimeout(<?= $_GET['gb']; ?>GB(<?= $URL; ?>),3000)"<?php } ?>>
<br>
<center><?= $_GET['message']; ?></center>
<br>
</body>
</html>
