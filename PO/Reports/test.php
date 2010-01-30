<?php


switch ($_GET['t']) {
	case 'column': $chart_type = 'column'; break;
	case 'bar': $chart_type = 'bar'; break;
	case 'stacked': $chart_type = 'stacked column'; break;
	case 'area': $chart_type = 'area'; break;
	case 'line': $chart_type = 'line'; break;
	case 'polar': $chart_type = 'polar'; break;
	default: $chart_type = 'column'; break;
}


echo "<chart>\n";
echo "  <chart_type>" . $chart_type . "</chart_type>\n";
echo "  <chart_transition type='drop' delay='1' duration='2' order='series' />\n";
echo "</chart>\n";
?>