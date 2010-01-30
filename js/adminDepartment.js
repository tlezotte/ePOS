YAHOO.util.Event.addListener(window, "load", function() {
	YAHOO.example.XHR_XML = new function() {
		this.formatName = function(elCell, oRecord, oColumn, sData) {
			elCell.innerHTML = oRecord.getData("fullname") + " (" + oRecord.getData("eid") + ")";
		};
		this.formatQuery = function(elCell, oRecord, oColumn, sData) {
			elCell.innerHTML = "<div style='white-space: normal;width:645px'>" + oRecord.getData("query") + "</div>";
		};
		
		var myColumnDefs = [
			{key:"date", label:"Date", sortable:true},
			{key:"fullname", label:"Name", sortable:true, formatter:this.formatName},
			{key:"page", label:"Page", sortable:true},
			{key:"query", label:"Query", sortable:true, formatter:this.formatQuery}
		];
		
		var searchURL = "id=" + request_id;
			  
		var myConfigs = {
			initialRequest:searchURL,
			sortedBy:{key:"date",dir:"desc"},
			rowSingleSelect:true
		};
		
		this.myDataSource = new YAHOO.util.DataSource("../data/history.php?");
		this.myDataSource.connMethodPost = false;
		this.myDataSource.responseType = YAHOO.util.DataSource.TYPE_XML;
		this.myDataSource.responseSchema = {
			resultNode: "sql",
			fields: ["date",
					 "eid",
					 "fullname",
					 "page",
					 "query"]
		};

		this.myDataTable = new YAHOO.widget.DataTable("historyPanel", myColumnDefs, this.myDataSource, myConfigs);
	};
});