YAHOO.namespace("example.container");

function init() {
	
	// Define various event handlers for Dialog
	var handleYes = function() {
		document.Form.submit();
		this.hide();
	};
	var handleNo = function() {
		this.hide();
	};

	// Instantiate the Dialog
	YAHOO.example.container.simpledialog1 = new YAHOO.widget.SimpleDialog("simpledialog1", 
																			 { width: "325px",
																			   fixedcenter: true,
																			   visible: false,
																			   draggable: false,
																			   modal: true,
																			   close: true,
																			   text: "Do you want to change, this may change the Financial Controller?",
																			   icon: YAHOO.widget.SimpleDialog.ICON_WARN,
																			   constraintoviewport: true,
																			   buttons: [ { text:"Yes", handler:handleYes },
																						  { text:"No",  handler:handleNo, isDefault:true } ]
																			 } );
	YAHOO.example.container.simpledialog1.setHeader("Are you sure?");
	
	// Render the Dialog
	YAHOO.example.container.simpledialog1.render(document.body);

	YAHOO.util.Event.addListener("plant", "change", YAHOO.example.container.simpledialog1.show, YAHOO.example.container.simpledialog1, true);
	/* ----- Only display department change dialog for HQ(9) ----- */
	//YAHOO.util.Event.addListener("department", "change", YAHOO.example.container.simpledialog1.show, YAHOO.example.container.simpledialog1, true);
}

YAHOO.util.Event.addListener(window, "load", init);