<?php
/* ----- Parse variable to generate URL ----- */
list($page, $id, $approval) = split("/", $_GET['q']);

/* ----- Check for correct URL ----- */
if (empty($page) OR empty($id)) {
	$_SESSION['error'] = "The URL is incorrect.";
	$_SESSION['redirect'] = "http://" . $_SERVER['SERVER_NAME'] . $_SERVER['REQUEST_URI'];
	$FORWARD = "error.php";
}

/* ------ Set name of page ----- */
switch ($page) {
	case "1": $final_page="PO/detail.php"; break;
}

/* ----- Check for error before setting final URL ----- */
if (!isset($FORWARD)) {
	if (empty($approval)) {
		$FORWARD = $final_page . "?id=" . urlencode($id);
	} else {
		$FORWARD = $final_page . "?id=" . urlencode($id) . "&approval=" . urlencode($approval);
	}
}
header("location: " . $FORWARD);
exit();
?>
