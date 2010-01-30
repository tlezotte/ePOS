$(document).ready(function(){
	/* ===== Panel Tabs ===== */
	$("#track_tabs > ul").tabs();
	$("#purchasing_tabs > ul").tabs();
	/* ========== Control Tabs ========== */
	$('#purchasing_tabs ul').tabs({ disabled: [4] });
	$('#purchasing_tabs ul').tabs(1);
	/* ===== Panel Toggle ===== */		
	$('#information_title').slidePanel({status:'open'});
	$('#itemInformation_title').slidePanel({status:'open'});
	$('#track_title').slidePanel({status:'open'});
	$('#attachments_title').slidePanel({status:'open'});
	$('#comments_title').slidePanel({status:'open'});
	$('#financeDepartment_title').slidePanel({status:'open'});
	$('#approvals_title').slidePanel({status:'open'});
	$('#purchasingDepartment_title').slidePanel({status:'open'});
	/* ===== Strip Item Information ===== */
	$('#itemTable tr:odd').addClass('odd');
	$('#itemTable tr:even').addClass('even');
	/* ===== Comment Area Toggle ===== */
	$('#commentsCounter').click(function() {
		$(this).slideToggle("slow");
		$('#commentsArea').slideToggle("slow");
	});
	$('#commentsArea').click(function() {
		$(this).slideToggle("slow");
		$('#commentsCounter').slideToggle("slow");
	});
	/* ===== Upload Attachments Area ===== */
	$('#uploadToggle').click(function() {
		$('#uploadFile').slideToggle("slow");
	});
	/* ===== Tracking Area ===== */
	$('#addTrackingToggle').click(function() {
		$('#addTracking').slideToggle("slow");
	});		
	/* ===== Scroll to Approvals ===== */
	$('#requestStatus, .appJump').click(function() {
		$.scrollTo( $('#approvals_panel'), {speed:2500} );
	});	
	$('#backToTop').click(function() {
		$.scrollTo( $('#CompanyLogo'), {speed:2500} );
	});	
	/* ==== Scroll to Comments ==== */
	$('.checkComments').click(function() {
		$.scrollTo( $('#comments_panel'), {speed:1000} );
		$('#commentsCounter').slideToggle("slow");
		$('#commentsArea').slideToggle("slow");
	});
	/* ===== Administration Panel Toggle ===== */
	$('#adminPanelMenu').click(function() {
		$('#adminPanel').slideToggle("slow");
	});		
	/* ===== History Panel Toggle ===== */
	$('#historyPanelMenu').click(function() {
		$('#historyPanel').slideToggle("slow");
	});	
	/* ========== Approvals ========== */
	if (level==approval) {
		var row = '#'.concat(approval).concat('Status');
		$(row).addClass('highlight');	
	}
	if (status=='X') {
		$(canceled).addClass('canceledHighlight');	
	}
	/* ========== Requisition Status ========== */
	switch (status) {
		case 'N':
			$('#requestStatus').addClass('newStatus');
		break;		
		case 'A':
			$('#requestStatus').addClass('approvedStatus');
		break;			
		case 'O':
			$('#requestStatus').addClass('kickoffStatus');
		break;
		case 'X':
		case 'C':
			$('#requestStatus').addClass('canceledStatus');
		break;		
	}
	/* ===== jQuery UI Calendar ===== */
	$.datepicker.setDefaults({showOn: 'both', buttonImageOnly: true, dateFormat: 'YMD-', buttonImage: '/Common/images/calendar.gif'});
	$('input.popupcalendar').datepicker();
	/* ===== Firebug Log ===== */
/*	console.log("Approval: ", approval);
	console.log("Level: ", level);
	console.log("Row: ", row);
	console.log("Status: ", status);
	console.log("Canceled: ", canceled);*/
});						