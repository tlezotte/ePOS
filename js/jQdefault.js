$(document).ready(function(){
	/* ===== Get Theme ===== */					   
	var cookie = readCookie("style");
	var title = cookie ? cookie : getPreferredStyleSheet();
	setActiveStyleSheet(title);	
	/* ========== Themes ========== */
	$('.style').hover(function() {
	  $(this).addClass('styleSelect');
	}, function() {
	  $(this).removeClass('styleSelect');
	});
	$('.style').select(function() {
	  $(this).addClass('styleSelect');
	});		
	/* ===== Hide Header and Footer ===== */
    var q  = unescape(location.search.substr(1)).split('&');
    for(var i=0; i<q.length; i++){
        var t=q[i].split('=');
        if(t[0].toLowerCase()=='clean' && t[1].toLowerCase()=='true') {
			$('div#hd').hide();
			$('div#mainMenu').hide();
			$('div#ft').hide();				
		};
    }
	/* ===== Display Message Center ===== */   
	if (message.length > 0) {
		$('#messageCenter').slideDown("slow");
	}
	/* ========== Tooltips ========== */
	$('a[@title], span[@title], div[@title], img[@title').cluetip({
	  splitTitle: '|', 
	  arrows: true, 
	  dropShadow: false, 
	  cluetipClass: 'jtip'}
	);	
	$('input[@title]').cluetip({
	  splitTitle: '|', 
	  arrows: true, 
	  dropShadow: false, 
	  cluetipClass: 'itip'}
	);	
});						


$(document).unload( function () {
	/* ===== Set Theme ===== */
	var title = getActiveStyleSheet();
	createCookie("style", title, 365);
});	