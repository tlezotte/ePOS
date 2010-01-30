YAHOO.util.Event.addListener(window, "load", function() {
	YAHOO.example.XHR_XML = new function() {
		/* 
		 * ----- Get users current Requisitions ----- 
		 */		
		this.formatUrlMyRequests = function(elCell, oRecord, oColumn, sData) {
			var levelEmployee;
			var level = oRecord.getData("level");
			
			switch (level) {
				case 'controller': levelEmployee = oRecord.getData("controller"); break;
				case 'approver1': levelEmployee = oRecord.getData("approver1"); break;
				case 'approver2': levelEmployee = oRecord.getData("approver2"); break;
				case 'approver3': levelEmployee = oRecord.getData("approver3"); break;
				case 'approver4': levelEmployee = oRecord.getData("approver4"); break;
			}
			
			elCell.innerHTML = "<a href='Requests/detail.php?id=" + level + "' title='Waiting for " + levelEmployee + " (" + level + ")' class='dark' target='local'>" + sData + "</a>";
		};

		 this.formatRequestLevel = function(elCell, oRecord, oColumn, sData) {
			var levelEmployee;
			var level = oRecord.getData("level");
			
			switch (level) {
				case 'controller': levelEmployee = oRecord.getData("controller"); break;
				case 'approver1': levelEmployee = oRecord.getData("approver1"); break;
				case 'approver2': levelEmployee = oRecord.getData("approver2"); break;
				case 'approver3': levelEmployee = oRecord.getData("approver3"); break;
				case 'approver4': levelEmployee = oRecord.getData("approver4"); break;
			}
			
			elCell.innerHTML = "<img src='/Common/images/" + level + ".gif' title='Waiting for " + levelEmployee + " (" + level + ")' />";
		};		

		 this.formatPurpose = function(elCell, oRecord, oColumn, sData) {
			elCell.innerHTML = "<div style='white-space: normal;width:160px'>" + oRecord.getData("purpose") + "</div>";
		};	
		
		var colMyRequests = [
			{label:"My Open Requisitions", width:"300px", children:[
				{key:"id", label:"#", width:"30px", sortable:true, formatter:this.formatUrlMyRequests},
				{key:"level", label:"", width:"20px", sortable:true, formatter:this.formatRequestLevel},
				{key:"billtoplant", label:"Plant", width:"90px", sortable:true},
				{key:"purpose", label:"Purpose", sortable:true, formatter:this.formatPurpose}					
			]}
		];
		var cfgMyRequests = {
			initialRequest:"action=my&access=0",
			sortedBy:{key:"id",dir:"desc"}
		};                  
		
		this.dsMyRequests = new YAHOO.util.DataSource("data/requisitions.php?");
		this.dsMyRequests.responseType = YAHOO.util.DataSource.TYPE_XML;
		this.dsMyRequests.responseSchema = {
			resultNode: "requisition",
			fields: [{key:"id", parser:YAHOO.util.DataSource.parseNumber},
					 "billtoplant",
					 "level",						 
					 "purpose",
					 "controller",
					 "approver1",
					 "approver2",
					 "approver3",
					 "approver4"]
		};

		this.MyRequests = new YAHOO.widget.DataTable("myRequestsTable", colMyRequests, this.dsMyRequests, cfgMyRequests);


		/* 
		 * ----- Change Log from Intranet website ----- 
		 */
		this.formatPubDate = function(elCell, oRecord, oColumn, sData) {
			var pubDate = oRecord.getData("pubDate").split(" ");
			elCell.innerHTML = pubDate[2] + " " + pubDate[1] + ", " + pubDate[3];
		};
					
		this.formatUrlIntranet = function(elCell, oRecord, oColumn, sData) {
			elCell.innerHTML = "<a href='" + oRecord.getData("link") + "' title='" + oRecord.getData("pubDate") + "' onClick=\"displayLocalView('on');\" class='dark' target='local'><span style='white-space: normal;width:215px'>" + sData + "</span></a> <a href='" + oRecord.getData("link") + "' title='Open in new window or tab' target='log'><img src='/Common/images/offsite.gif' border='0'></a>";
		};

		var colIntranet = [
			{label:"Changes Log", width:"300px", children:[
				{key:"pubDate", label:"Date", width:"85px", sortable:true, formatter:this.formatPubDate},
				{key:"title", label:"Article", width:"215px", formatter:this.formatUrlIntranet}
			]}
		];
		var cfgIntranet = {
			sortedBy:{key:"pubDate",dir:"asc"}
		};
		
		this.dsIntranet = new YAHOO.util.DataSource("proxy/intranet.xml");
		this.dsIntranet.responseType = YAHOO.util.DataSource.TYPE_XML;
		this.dsIntranet.responseSchema = {
			resultNode: "item",
			fields: ["title",
					 "link",
					 "pubDate"]
		};

		this.Intranet = new YAHOO.widget.DataTable("ChangeLogTable", colIntranet, this.dsIntranet, cfgIntranet);


		/* 
		 * ----- Yahoo Weather ----- 
		 */
		var colWeather = [
			{key:"description", label:"What it's like outside", width:"300px"}
		];
		var cfgWeather = {
			//sortedBy:{key:"pubDate",dir:"asc"}
		};
		
		this.dsWeather = new YAHOO.util.DataSource("proxy/weather.xml");
		this.dsWeather.responseType = YAHOO.util.DataSource.TYPE_XML;
		this.dsWeather.responseSchema = {
			resultNode: "item",
			fields: ["description"]
		};

		this.Weather = new YAHOO.widget.DataTable("WeatherTable", colWeather, this.dsWeather, cfgWeather);


		/* 
		 * ----- Currency Chart ----- 
		 */
		this.formatCountry = function(elCell, oRecord, oColumn, sData) {
			var country;
			
			switch (oRecord.getData("title")) {
				case 'CAD': country = 'Canada'; break;
				case 'EUR': country = 'Euro'; break;
				case 'GBP': country = 'U.K. Pound Sterling'; break;
				case 'JPY': country = 'Japanese Yen'; break;
				case 'CZK': country = 'Czech Koruna'; break;
				case 'CNY': country = 'Chinese Yuan'; break;
			}
			
			elCell.innerHTML = "<a href='http://finance.yahoo.com/q?s=USD" + oRecord.getData("title") + "=X' onClick=\"displayLocalView('on');\" class='dark' target='local'>" + country + "</a> <a href='http://finance.yahoo.com/q?s=USD" + oRecord.getData("title") + "=X' title='Open in new window or tab' target='currency'><img src='/Common/images/offsite.gif' border='0'></a>";
		};

		this.formatCurency = function(elCell, oRecord, oColumn, sData) {
			var country;
			
			switch (oRecord.getData("title")) {
				case 'CAD': country = '$ '; break;
				case 'EUR': country = '&euro; '; break;
				case 'GBP': country = '&#163; '; break;
				case 'JPY': country = '&#165; '; break;
				case 'CZK': country = 'Kc '; break;
				case 'CNY': country = 'Y '; break;
			}
			
			elCell.innerHTML = country + oRecord.getData("description");
		};
		
		var colCurrency = [
			{label:"Currecny Exchange Rates ($1 USD)", width:"300px", children:[
				{key:"title", label:"Country", width:"185px", formatter:this.formatCountry},
				{key:"title", label:"Code", width:"40px"},
				{key:"description", label:"Rate", width:"75px", formatter:this.formatCurency}
			]}
		];
		var cfgCurrency = {
			//sortedBy:{key:"pubDate",dir:"asc"}
		};
		
		this.dsCurrency = new YAHOO.util.DataSource("proxy/currency.xml");
		this.dsCurrency.responseType = YAHOO.util.DataSource.TYPE_XML;
		this.dsCurrency.responseSchema = {
			resultNode: "item",
			fields: ["description",
					 "title"]
		};

		this.Currency = new YAHOO.widget.DataTable("currencyTable", colCurrency, this.dsCurrency, cfgCurrency);
	};



	YAHOO.example.XHR_JSON = new function() {
		/* 
		 * ----- Stock Market ----- 
		 */		
		this.formatMarket = function(elCell, oRecord, oColumn, sData) {
			var market;
			
			switch (oRecord.getData("title")) {
				case 'DJI': market = 'Dow Jones'; break;
				case 'IXIC': market = 'NASDAQ'; break;
				case 'GSPC': market = 'S&P 500'; break;
			}
			
			elCell.innerHTML = "<a href='" + oRecord.getData("link") + "' onClick=\"displayLocalView('on');\" class='dark' target='local'>" + market + "</a> <a href='" + oRecord.getData("link") + "' title='Open in new window or tab' target='currency'><img src='/Common/images/offsite.gif' border='0'></a>";
		};

		this.formatChange = function(elCell, oRecord, oColumn, sData) {
			var change = oRecord.getData("change");
			var negative = change.charAt(0);
			var image = (negative == '-') ? 'down_arrow' : 'up_arrow';
			var numbersOnly = change.substring(1,change.length);
			
			elCell.innerHTML = "<img src='/Common/images/" + image + ".gif' />" + numbersOnly;
		};
		
		var colMarket = [
			{label:"Stock Markets", width:"300px", children:[
				{key:"title", label:"Market", width:"150px", formatter:this.formatMarket},
				{key:"price", label:"Price", width:"75px"},
				{key:"change", label:"Change", width:"75px", formatter:this.formatChange}
			]}
		];
		var cfgMarket = {
			//sortedBy:{key:"pubDate",dir:"asc"}
		};
		
		this.dsMarket = new YAHOO.util.DataSource("proxy/market.json");
		this.dsMarket.responseType = YAHOO.util.DataSource.TYPE_JSON;
		this.dsMarket.responseSchema = {
			resultsList: "value.items",
			fields: ["title",
					 "price",
					 "change",
					 "link"]
		};

		this.Market = new YAHOO.widget.DataTable("marketTable", colMarket, this.dsMarket, cfgMarket);	
		
	};
});