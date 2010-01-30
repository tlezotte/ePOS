<?php

/* ------------- START PAGE PROCESSING --------------------- */
if ($_POST['stage'] == "two") {
	
	/* ------------------ START FILE UPLOAD (FIRST HALF) ----------------------- */
	$exp_file = explode(".",$_FILES['file']['name']);
	$file_ext = end($exp_file);
	/* ------------------ END FILE UPLOAD (FIRST HALF) ----------------------- */

	/* Getting Vendor Terms from Standards */	
	$TERMS = $dbh->getRow("SELECT BTTRMC AS id FROM Standards.Vendor WHERE v.BTVEND='" . $_SESSION['supplier'] . "'");	
	
	/* ------------------ START DATABASE CONNECTIONS ----------------------- */
	/* Commiting data into PO database */
	$po_fields = "'".mysql_real_escape_string($_SESSION['eid'])."',
				CURDATE(),			
				'".mysql_real_escape_string($_POST['incareof'])."',
				'".mysql_real_escape_string($_POST['plant'])."',
				'".mysql_real_escape_string($_POST['ship'])."',
				'".mysql_real_escape_string($_SESSION['supplier'])."',
				'".mysql_real_escape_string($TERMS['id'])."',
				'".mysql_real_escape_string($_POST['job'])."',
				'".mysql_real_escape_string($_POST['company'])."',
				'".mysql_real_escape_string($_POST['department'])."',
				'".mysql_real_escape_string($_POST['purpose'])."',
				'".mysql_real_escape_string(htmlentities($_FILES['file']['name'], ENT_QUOTES, 'UTF-8'))."',
				'".mysql_real_escape_string(htmlentities($_FILES['file']['type'], ENT_QUOTES, 'UTF-8'))."',
				'".mysql_real_escape_string($file_ext)."',
				'".mysql_real_escape_string($_FILES['file']['size'])."',				
				'".mysql_real_escape_string($_SESSION['total'])."',
				'".mysql_real_escape_string($_POST['cer'])."',
				'".mysql_real_escape_string($_POST['hot'])."',
				'N',
				'".mysql_real_escape_string($_POST['date1'])."'
				";
	$po_sql = "INSERT into PO (req, reqDate, incareof, plant, ship, sup, terms, job, company, department, purpose, file_name, file_type, file_ext, file_size, total, cer, hot, status, dueDate) VALUES ($po_fields)";
	$dbh->query($po_sql);
	echo $po_sql . "<br><br>";
	
	/* Get PO auto_increment ID */							
	$PO_ID = $dbh->getOne("select max(id) from PO");
	
	/* Commiting data into Authorization database */
	$auth_fields = "NULL,
					'PO',
					'".mysql_real_escape_string($PO_ID)."',
					'".mysql_real_escape_string($_SESSION['app1'])."',
					'".mysql_real_escape_string($_SESSION['app2'])."',
					'".mysql_real_escape_string($_SESSION['app3'])."',
					'".mysql_real_escape_string($_SESSION['app4'])."'
					";													
 	$auth_sql = "INSERT into Authorization (id, type, type_id, app1, app2, app3, app4) VALUES ($auth_fields)";
	$dbh->query($auth_sql);
	echo $auth_sql . "<br><br>";
	
	/* Commiting data into Items database */
 	for ($i = 1; $i <= $_SESSION['total_items']; $i++) {
		$qty = 'qty'.$i;
		$unit = 'unit'.$i;
		$part = 'part'.$i;
		$manuf = 'manuf'.$i;
		$descr = 'descr'.$i;
		$price = 'price'.$i;
		$cat = 'cat'.$i;
		$vt = 'vt'.$i;
		$plant = 'plant'.$i;
		$items_fields = "NULL,
						'".mysql_real_escape_string($PO_ID)."',
						'".mysql_real_escape_string($_SESSION[$qty])."',
						'".mysql_real_escape_string($_SESSION[$descr])."',
						'".mysql_real_escape_string($_SESSION[$price])."',
						'".mysql_real_escape_string($_SESSION[$cat])."',
						'".mysql_real_escape_string($_SESSION[$unit])."',
						'".mysql_real_escape_string($_SESSION[$part])."',
						'".mysql_real_escape_string($_SESSION[$manuf])."',
						'".mysql_real_escape_string($_SESSION[$vt])."',
						'".mysql_real_escape_string($_SESSION[$plant])."',
						'N'
						";
		/* Only recording lines containing information */
		if (!empty($_SESSION[$descr])) {
			$items_sql = "INSERT into Items (id, type_id, qty, descr, price, cat, unit, part, manuf, vt, plant, rec) VALUES ($items_fields)";
			$dbh->query($items_sql);
			echo $items_sql . "<br>";
		}
	}
	/* ------------------ END DATABASE CONNECTIONS ----------------------- */
	
	
	/* ------------------ START FILE UPLOAD (SECOND HALF) ----------------------- */
	$store = $default['files_store'];								//Store uploaded files to this directory
	$dest = $store."/".$PO_ID.".".$file_ext;
	$source = $_FILES['file']['tmp_name'];
	if (file_exists($source)) {
		if (is_writable($default['PO_UPLOAD'])) {
			copy($source, $dest);									//Copy temp upload to $store
		} else {
			$_SESSION['error'] = "Cannot upload file (".$_FILES['file']['name'].")";
			$_SESSION['redirect'] = "http://".$_SERVER['SERVER_NAME']."".$_SERVER['REQUEST_URI'];
			
			header("Location: ../error.php");
		}
	}
	/* ------------------ END FILE UPLOAD (SECOND HALF) ----------------------- */	
	//clearSession();			// Reset Session
	exit();
	/* ----- Forward to router ----- */
	//header("Location: router.php?type_id=".$PO_ID."&approval=app0");
}
/* ------------- END PAGE PROCESSING --------------------- */
?>