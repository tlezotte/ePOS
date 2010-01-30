<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title>Loading, please wait...</title>
	<script type="text/javascript" src="/Common/js/yahoo/yahoo/yahoo.js"></script>
	<script type="text/javascript" src="/Common/js/yahoo/event/event.js" ></script>
	<script type="text/javascript" src="/Common/js/yahoo/dom/dom.js" ></script>
	<script type="text/javascript" src="/Common/js/yahoo/animation/animation.js" ></script>
	<script type="text/javascript" src="/Common/js/yahoo/connection/connection.js" ></script>
	<script type="text/javascript" src="/Common/js/yahoo/container/container.js"></script>
	<link type="text/css" rel="stylesheet" href="/Common/js/yahoo/container/assets/container.css"> 
	
	<script>
		YAHOO.namespace("example.container");

		function init() {
			// Initialize the temporary Panel to display while waiting for external content to load
			YAHOO.example.container.wait = 
					new YAHOO.widget.Panel("wait",  
													{ width:"240px", 
													  fixedcenter:true, 
													  close:false, 
													  draggable:false, 
													  modal:true,
													  visible:false,
													  effect:{effect:YAHOO.widget.ContainerEffect.FADE, duration:0.5} 
													} 
												);

			YAHOO.example.container.wait.setHeader("Loading, please wait...");
			YAHOO.example.container.wait.setBody("<img src=\"http://us.i1.yimg.com/us.yimg.com/i/us/per/gr/gp/rel_interstitial_loading.gif\"/>");
			YAHOO.example.container.wait.render(document.body);

			// Define the callback object for Connection Manager that will set the body of our content area when the content has loaded
	
			var content = document.getElementById("content");

			var callback = {
				success : function(o) {
					content.innerHTML = o.responseText;
					content.style.visibility = "visible";
					YAHOO.example.container.wait.hide();
				},
				failure : function(o) {
					content.innerHTML = o.responseText;
					content.style.visibility = "visible";
					content.innerHTML = "CONNECTION FAILED!";
					YAHOO.example.container.wait.hide();
				}
			}
		
			// Show the Panel
			YAHOO.example.container.wait.show();
			
			// Connect to our data source and load the data
			var conn = YAHOO.util.Connect.asyncRequest("GET", "<?= $_GET['url']; ?>", callback);
		}

		YAHOO.util.Event.addListener(window, "load", init);
	</script>	
</head>

<body>
<div id="content" style="visibility:hidden"></div>
</body>
</html>
