<html>
<head>
<script type="text/javascript" src="http://maps.yahooapis.com/v3.5/fl/javascript/apiloader.js?appid=BfIf.RHV34FLsnrBBVvpsYc6ISM7lQAeDH2pSZopGn9RkLgj2ePuwDrVwRYCvIY-"></script>
<style type="text/css">
#mapContainer { 
  height: 600px; 
  width: 600px; 
} 
body {
	margin-left: 0px;
	margin-top: 0px;
	margin-right: 0px;
	margin-bottom: 0px;
}
</style> 
<meta http-equiv="Content-Type" content="text/html; charset=utf-8"></head>
<body>
<div id="mapContainer"></div>

<script type="text/javascript">
var address = "<?= $_GET['address']; ?>";

// Create a Map object. Put your application ID in place of Vendor
var map = new Map("mapContainer", "Vendor", address, 3);
// Make the map draggable 
map.addTool( new PanTool(), true );
// Create a View Widget object 
map.addWidget(new SatelliteControlWidget());
// Add the Navigator Widget to the map and display it 
map.addWidget(new NavigatorWidget("closed")); 

// Create a POI marker object
marker = new CustomPOIMarker( '<?= $_GET['name']; ?>', ' (<?= $_GET['id']; ?>)', address, '0xFF0000', '0xFFFFFF');

// Add the POI marker to the map and display it 
map.addMarkerByAddress( marker, address);
</script> 

</body>
</html>