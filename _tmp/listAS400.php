<html>
<body>

<?php
require('../Connections/ODBCos400.php');

//$sql="SELECT * FROM ZZ_TEST.PORQI";
$sql="SELECT * FROM company." . $_GET['t'] . " FETCH FIRST 400 ROWS ONLY";
//$sql="SELECT * FROM ZZ_TEST.PORQD";
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
 if ( $c%2 == 0 )
 echo "<tr bgcolor=\"#d0d0d0\" >\n";
 else
 echo "<tr bgcolor=\"#eeeeee\">\n";
   for($i=1;$i<=odbc_num_fields($rs);$i++)
     {       
       echo "<td>";
       echo odbc_result($rs,$i);
       echo "</td>";       
       if ( $i%$j == 0 ) 
           {
           $nrows+=1; // counting no of rows   
         } 
     }
   echo "</tr>";
}

echo "</td> </tr>\n";
echo "</table >\n";
?>

</body>
</html>