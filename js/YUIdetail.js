<!-- Original:  Cyanide_7 (leo7278@hotmail.com) -->
<!-- Web Site:  http://www7.ewebcity.com/cyanide7 -->

<!-- This script and many more are available free online at -->
<!-- The JavaScript Source!! http://javascript.internet.com -->

<!-- Begin
function formatCurrency(num) {
	num = num.toString().replace(/\$|\,/g,'');
	if(isNaN(num))
	num = "0";
	sign = (num == (num = Math.abs(num)));
	num = Math.floor(num*100+0.50000000001);
	cents = num%100;
	num = Math.floor(num/100).toString();
	if(cents<10)
	cents = "0" + cents;
	for (var i = 0; i < Math.floor((num.length-(1+i))/3); i++)
	num = num.substring(0,num.length-(4*i+3))+','+
	num.substring(num.length-(4*i+3));
	return (((sign)?'':'-') + '$' + num + '.' + cents);
}



YAHOO.util.Event.addListener(window, "load", function() {
//	var successHandler = function(o) {
//		//Here, o contains all of the fields described in the table
//		//above:
//		o.purge(); //removes the script node immediately after executing;
//		console.log(o.data); //the data you passed in your configuration
//						   //object
//	}
//
//	var objTransaction = YAHOO.util.Get.script("http://www.packagemapping.com/track/tracking.php?action=track&tracknum=1Z1812690306250111&rss=1",
//											{ onSuccess: successHandler
//											});

	/* 
	 * ================== List Items ================== 
	 */
	YAHOO.example.XHR_JSON = new function() {
		YAHOO.widget.DataTable.MSG_LOADING="<img src='/Common/images/indicator.gif' align='absmiddle'> Loading data...";
		//YAHOO.widget.DataTable.MSG_EMPTY="No tracking information found.";		
		
		this.formatCurrency = function(elCell, oRecord, oColumn, sData) {
			elCell.innerHTML = "<div style='text-align:right'>" + formatCurrency(oRecord.getData("price")) + "</div>";
		};		
		
		var colItems = [
			{key:"qty", label:"Qty", width:"50px", sortable:true, formatter:"number"},
			{key:"unit", label:"Unit", width:"50px", sortable:true},
			{key:"company", label:"Cadence#", width:"100px", sortable:true, hideable:true},
			{key:"manufacture", label:"Manufacture#", width:"100px", sortable:true, hideable:true},
			{key:"description", label:"Item Description", width:"350px", sortable:true},
			{key:"price", label:"Price", width:"100px", sortable:true, formatter:this.formatCurrency}
		];
		var cfgItems = {
			initialRequest:"?db=items&output=json&id=" + request_id
		};
	
		this.dsItems = new YAHOO.util.DataSource("../data/default.php");
		this.dsItems.responseType = YAHOO.util.DataSource.TYPE_JSON;
		this.dsItems.responseSchema = {
			resultsList: "items.item",
			fields: ["qty",
					 "unit",
					 "company",
					 "manufacture",
					 "description",
					 "price"]
		};
		YAHOO.widget.DataTable.MSG_LOADING="<img src='/Common/images/indicator.gif' align='absmiddle'> Loading items...";
		YAHOO.widget.DataTable.MSG_EMPTY="No items found.";
	
		this.Items = new YAHOO.widget.DataTable("itemsTable", colItems, this.dsItems, cfgItems);
	
		/* 
		 * ================== List Track Shipments ================== 
		 */
		this.formatStatus = function(elCell, oRecord, oColumn, sData) {
			elCell.innerHTML = "<a href='" + oRecord.getData("track_url") + "' title='Get a map view of shipment' class='dark' target='map'><img src='/Common/images/map.gif' border='0' align='absmiddle'> " + oRecord.getData("track_latest") + "</a>";
		};
			
		var colTrackList = [
			{key:"track_number", label:"Tracking Number", width:"175px"},
			{key:"track_date", label:"Status Date", width:"125px"},
			{key:"track_latest", label:"Current Status", width:"450px", formatter:this.formatStatus}
		];
		var cfgTrackList = {
			//initialRequest:"?db=tracking&output=json&id=" + request_id
		};
	
		this.dsTrackList = new YAHOO.util.DataSource(tracking);
		this.dsTrackList.responseType = YAHOO.util.DataSource.TYPE_JSON;
		this.dsTrackList.responseSchema = {
			resultsList: "list",
			fields: ["track_number",
					 "track_date",
					 "track_latest",
					 "track_url"]
		};
	
		this.TrackList = new YAHOO.widget.DataTable("shipmentListTable", colTrackList, this.dsTrackList, cfgTrackList);		
	
		/* 
		 * ================== Attachments ================== 
		 */
		this.formatFilename = function(elCell, oRecord, oColumn, sData) {
			var type_id = oRecord.getData("type_id").toString();
			var folder = "folder/" + type_id.substring(0, 2) + "/" + type_id + "/";
			
			elCell.innerHTML = "<a href='" + folder + oRecord.getData("file_name") + "' title='Download or View " + oRecord.getData("file_name") + "' class='dark'><img src='/Common/images/download.gif' border='0' align='absmiddle'> " + oRecord.getData("file_name") + "</a>";
		};
				
		this.formatSize = function(elCell, oRecord, oColumn, sData) {
			var units = new Array(' B', ' KB', ' MB', ' GB', ' TB');
			var size = oRecord.getData("file_size");
			
			for (var i = 0; size > 1024; i++) { size /= 1024; }
			elCell.innerHTML = size.toFixed(2) + units[i];
		}
		
		var colAttachments = [
			{key:"file_name", label:"Filename", width:"250px", sortable:true, formatter:this.formatFilename},
			{key:"file_type", label:"File Type", width:"250px", sortable:true},
			{key:"file_size", label:"File Size", width:"150px", sortable:true, formatter:this.formatSize},
			{key:"format_file_date", label:"Date", width:"100px", sortable:true, formatter:"date"}
		];
		var cfgAttachments = {
			initialRequest:"?db=files&output=json&id=" + request_id,
			sortedBy:{key:"format_file_date",dir:"asc"}
		};
		
		this.dsAttachments = new YAHOO.util.DataSource("../data/default.php");
		this.dsAttachments.responseType = YAHOO.util.DataSource.TYPE_JSON;
		this.dsAttachments.responseSchema = {
			resultsList: "files.file",
			fields: ["type_id",
					 "file_name",
					 "file_type",
					 "file_size",
					 "format_file_date"]
		};
		
		this.Attachments = new YAHOO.widget.DataTable("attachmentsTable", colAttachments, this.dsAttachments, cfgAttachments);	
	};  // End JSON data
	
	/* 
	 * ================== Detailed Tracking Information ================== 
	 */
	YAHOO.example.XHR_XML = new function() {
		YAHOO.widget.DataTable.MSG_LOADING="<img src='/Common/images/indicator.gif' align='absmiddle'> Loading data...";
		//YAHOO.widget.DataTable.MSG_EMPTY="No tracking information found.";
		
		for (t=0; t < trackingLength; t++) {
			var track_number = tracking.list[t].track_number;
			var track_url = "../proxy/" + track_number + ".xml";
			var track_table = track_number + "Table";
			
			this.formatStatus = function(elCell, oRecord, oColumn, sData) {
				var title = oRecord.getData("title");
				var titleArray = new Array();
				titleArray = title.split(': ');
				
				elCell.innerHTML = titleArray[0];
			};	
			
			this.formatLocation = function(elCell, oRecord, oColumn, sData) {
				var title = oRecord.getData("title");
				var titleArray = new Array();
				titleArray = title.split(': ');
				
				elCell.innerHTML = "<a href='" + oRecord.getData("link") + "' title='Get a map view of shipment' class='dark' target='map'><img src='/Common/images/map.gif' border='0' align='absmiddle'> " + titleArray[1] + "</a>";
			};
			
			var colTrack = [
				{key:"title", label:"Status Date", width:"150px", formatter:this.formatStatus},
				{key:"title", label:"Current Status", width:"600px", formatter:this.formatLocation}
			];
			var cfgTrack = {
				//sortedBy:{key:"pubDate",dir:"asc"}
				//caption:"Tracking information for 1Z1812690306250111"
			};
	
			this.dsTrack = new YAHOO.util.DataSource(track_url);
			this.dsTrack.responseType = YAHOO.util.DataSource.TYPE_XML;
			this.dsTrack.responseSchema = {
				resultNode: "item",
				fields: ["title",
						 "link"]
			};
	
			this.Track = new YAHOO.widget.DataTable(track_table, colTrack, this.dsTrack, cfgTrack);		
		};  // End For loop
	};  // End XML data


	/*
	 *	================= Start Purchasing Department ====================
	 */
	if (request_role == 'purchasing' || request_role == 'executive') {
		YAHOO.example.InlineCellEditing = new function() {
			YAHOO.widget.DataTable.MSG_LOADING="<img src='/Common/images/indicator.gif' align='absmiddle'> Loading data...";
			//YAHOO.widget.DataTable.MSG_EMPTY="No tracking information found.";			
			
			/* ================== Contacts Information ================== */
			this.formatSource = function(elCell, oRecord, oColumn, sData) {
				var image;
				if (oRecord.getData("source") == "cms") { image = "star_f.gif"; } else { image = "star_empty.gif"; }
				
				elCell.innerHTML = "<img src='/Common/images/" + image + "' title='Contact from " + oRecord.getData("source") + "' align='absmiddle' /> " + oRecord.getData("name");
			};			
			
			var myColumnDefs = [
				{key:"name", label:"Name", sortable:true, formatter:this.formatSource, editor:"textarea"},
				{key:"phone", label:"Phone", editor:"textarea"},
				{key:"ext", label:"Ext", editor:"textarea"},
				{key:"fax", label:"Fax", editor:"textarea"},
				{key:"email", label:"Email", sortable:true, editor:"textarea"}
			];
			
			var searchURL = "id=" + request_id;
				  
			var myConfigs = {
				initialRequest:searchURL,
				sortedBy:{key:"name",dir:"desc"},
				rowSingleSelect:false,
				paginated:false,
				paginator:{
					containers:null,
					currentPage:1,
					dropdownOptions:[25,50,100],
					pageLinks:0,
					rowsPerPage:50
				}
			};
			
			this.myDataSource = new YAHOO.util.DataSource("../data/contacts.php?");
			this.myDataSource.connMethodPost = false;
			this.myDataSource.responseType = YAHOO.util.DataSource.TYPE_XML;
			this.myDataSource.responseSchema = {
				resultNode: "contact",
				fields: ["name",
						 "phone",
						 "ext",
						 "fax",
						 "email",
						 "source"]
			};
	
			this.myDataTable = new YAHOO.widget.DataTable("contactsXML", myColumnDefs, this.myDataSource, myConfigs);
			
			// Set up editing flow
			this.highlightEditableCell = function(oArgs) {
				var elCell = oArgs.target;
				if(YAHOO.util.Dom.hasClass(elCell, "yui-dt-editable")) {
					this.highlightCell(elCell);
				}
			};
			this.myDataTable.subscribe("cellMouseoverEvent", this.highlightEditableCell);
			this.myDataTable.subscribe("cellMouseoutEvent", this.myDataTable.onEventUnhighlightCell);
			this.myDataTable.subscribe("cellClickEvent", this.myDataTable.onEventShowCellEditor);
	
			// Hook into custom event to customize save-flow of "radio" editor
			this.myDataTable.subscribe("editorUpdateEvent", function(oArgs) {
				if(oArgs.editor.column.key === "active") {
					this.saveCellEditor();
				}
			});
			this.myDataTable.subscribe("editorBlurEvent", function(oArgs) {
				this.cancelCellEditor();
			});		
		
			/* 
			 * ================== Send Purchase Order ================== 
			 */
			this.formatFax = function(elCell, oRecord, oColumn, sData) {
				elCell.innerHTML = "<a href='detail.php?id=" + oRecord.getData("id") + "' title='Detailed View' class='dark'><img src='/Common/images/detail.gif' align='absmiddle' border='0'></a> " + oRecord.getData("id");
			};
			
			this.formatSource = function(elCell, oRecord, oColumn, sData) {
				var image;
				if (oRecord.getData("source") == "cms") { image = "star_f.gif"; } else { image = "star_empty.gif"; }
				
				elCell.innerHTML = "<img src='/Common/images/" + image + "' title='Contact from " + oRecord.getData("source") + "' align='absmiddle' /> " + oRecord.getData("name");
			};	
	
			this.formatFax = function(elCell, oRecord, oColumn, sData) {
				var disabled = "";
				if (oRecord.getData("fax") == "") { disabled = "disabled"; }
				
				elCell.innerHTML = "<input name='fax[]' type='checkbox' value='" + oRecord.getData("fax") + "' " + disabled + "/> " + oRecord.getData("fax");
			};
			
			this.formatEmail = function(elCell, oRecord, oColumn, sData) {
				var disabled = "";
				if (oRecord.getData("email") == "") { disabled = "disabled"; }
				
				elCell.innerHTML = "<input name='email[]' type='checkbox' value='" + oRecord.getData("email") + "' " + disabled + "/> " + oRecord.getData("email");
			};		
	
			var myColumnDefs = [
				{key:"name", label:"Name", sortable:true, formatter:this.formatSource},
				{key:"phone", label:"Phone"},
				{key:"ext", label:"Ext"},
				{key:"fax", label:"Fax", formatter:this.formatFax},
				{key:"email", label:"Email", sortable:true, formatter:this.formatEmail}
			];
			
			var searchURL = "id=" + request_id;
				  
			var myConfigs = {
				initialRequest:searchURL,
				sortedBy:{key:"name",dir:"desc"},
				rowSingleSelect:false,
				paginated:false,
				paginator:{
					containers:null,
					currentPage:1,
					dropdownOptions:[25,50,100],
					pageLinks:0,
					rowsPerPage:50
				}
			};
			
			this.myDataSource = new YAHOO.util.DataSource("../data/contacts.php?");
			this.myDataSource.connMethodPost = false;
			this.myDataSource.responseType = YAHOO.util.DataSource.TYPE_XML;
			this.myDataSource.responseSchema = {
				resultNode: "contact",
				fields: ["name",
						 "phone",
						 "ext",
						 "fax",
						 "email",
						 "source"]
			};
	
			this.myDataTable = new YAHOO.widget.DataTable("sendPoXML", myColumnDefs, this.myDataSource, myConfigs);			
		};	// End Inline-Editing data
	};  // End Purchasing Department
});	// End Yahoo listener