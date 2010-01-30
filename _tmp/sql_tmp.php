<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
"http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<title>Untitled Document</title>
</head>

<?php
/* Database Settings */ 
$default['odbc_driver'] = "iSeries Access ODBC Driver";
$default['odbc_system'] = "os400";
$default['odbc_library'] = "TIMECUSTOM";
$default['odbc_username'] = "LEZOTTET";
$default['odbc_password'] = "ODBC";

/* DSN Settings */
$dsn = "DRIVER=".$default['odbc_driver'].";SYSTEM=".$default['odbc_system'].";DEFAULTLIBRARIES=".$default['odbc_library'];

/* Connect to Database */
$conn=odbc_connect($dsn, $default['odbc_username'], $default['odbc_password']);
if (!$conn) { exit("Connection Failed: " . $conn); }

//require('../Connections/ODBCos400.php');
//require('../Connections/connDB.php');

//$sql="SELECT * FROM CCF0000 FETCH FIRST 10 ROWS ONLY";
//$sql="SELECT * FROM CCF0000";
$sql="SELECT ccbadlayo, cctext1, ccfstnme, cclstnme FROM CCF0000";
$rs=odbc_exec($conn,$sql);
if (!$rs)
  {exit("Error in SQL");}
   
echo "<table  align=\"center\" border=\"1\" borderColor=\"\" cellpadding=\"0\" cellspacing=\"0\">\n";
echo "<tr> ";
// -- print field name
$colName = odbc_num_fields($rs);
for ($j=1; $j<= $colName; $j++)
{
echo "<th  align=\"left\" bgcolor=\"#CCCCCC\" > <font color=\"#990000\"> ";
echo "(" . $j . ")";
echo odbc_field_name ($rs, $j );
echo "(" . odbc_field_type ($rs, $j) . ")";
echo "(" . odbc_field_len ($rs, $j) . ")";
echo "</font> </th>";
}
$j=$j-1;
$c=0;
// end of field names
while(odbc_fetch_row($rs)) // getting data
{
 $c=$c+1;
 $vendor_data = "";
 if ( $c%2 == 0 )
 echo "<tr bgcolor=\"#d0d0d0\" >\n";
 else
 echo "<tr bgcolor=\"#eeeeee\">\n";
   for($i=1;$i<=odbc_num_fields($rs);$i++) { 
	   $field = odbc_result($rs,$i);  
       echo "<td>";
       echo $field;
       echo "</td>";       
       if ( $i%$j == 0 ) 
           {
           $nrows+=1; // counting no of rows   
         }
	   //$vendor_data .= "'".$field."',";
     }
   echo "</tr>";
   // $vendor_data2 = preg_replace("/\,$/", "", $vendor_data);
   //$vendor_sql = "INSERT INTO Vendor VALUES (" . $vendor_data2 . ")";
	//echo $vendor_sql . "<br><br>";
	//$dbh->query($vendor_sql);
}

   
echo "</td> </tr>\n";
echo "</table >\n";
?>

<body>
</body>
</html>