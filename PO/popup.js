// Displaying a catfish popup

var popupzone = 1;
var popupid = 64;
var popupintervaldays = 7;
var popupemail = '';

// "The Catfish is a Plenty Good Enough Fish For Anyone"
// Mark Twain

function createcatfish()
{
	// create catfish here
	catfish = document.createElement('div');
	catfish.id = 'catfish';
	if (catfishpopulate) catfishpopulate(catfish);
	return catfish;
}

function deploycatfish(catfish)
{
	cookiearray = document.cookie.split(/;\s*/);
	var slideme = true;
	
	for (entry in cookiearray)
	{
		if (cookiearray[entry] == 'SPsub=1') return;
		if (cookiearray[entry] == 'SPcatfish=1') return;
		if (cookiearray[entry] == 'SPcatfish=2') slideme = false;
	}
	
	if (!document.defaultView)
	{               // IE position:fixed hacking
		scrollheight = document.body.parentNode.scrollTop;
		var subelements = [];
		for (var i = 0; i < document.body.childNodes.length; i++) {
	 		subelements[i] = document.body.childNodes[i];
		}
	
		var zip = document.createElement('div');    // Create the outer-most div (zip)
		zip.className = 'zip';                      // call it zip
	
		for (var i = 0; i < subelements.length; i++) {
			zip.appendChild(subelements[i]); 
		}
		document.body.appendChild(zip); // add the major div
		catfish.style.position = 'absolute';
		document.body.className = 'zipped';
		document.body.parentNode.className = 'zipped';
		zip.scrollTop = scrollheight; 
		document.body.parentNode.scrollTop = 0;
	}
	catfish.style.marginBottom = '-500px';
	
	document.body.appendChild(catfish);
		
	catfishheight = catfish.offsetHeight ? catfish.offsetHeight : 20;
	catfish.style.marginBottom = (0 - catfishheight) + 'px';
	catfishposition = 0;
	
	if (slideme) catfishtimeout = setTimeout(startcatfish, 1600);
		else finishcatfish();
}

function startcatfish()
// starts the catfish sliding up
{
	catfishtimeout = setInterval(positioncatfish, 25);
}

function positioncatfish()
{
	catfishposition += 10;
	catfish.style.marginBottom = '-' + (((100 - catfishposition) / 100) * catfishheight) + 'px';
	if (catfishposition >= 100)
	{
		clearTimeout(catfishtimeout);
		catfishtimeout = setTimeout(finishcatfish, 1);
	}
}

function finishcatfish()
{
	catfish.style.marginBottom = '0';	
	// jump the bottom of the document to give room for the catfish when scrolled right down
	document.body.parentNode.style.paddingBottom = (catfishheight - getcatfishoverlap()) +'px';
	// set cookie so it won't 'slide' up for the rest of the session
	document.cookie = 'SPcatfish=2; path=/';
	// logging via remote scripting
	logu = document.createElement('img');
	logu.src = '/popup/popuplog.php?zoneid='+popupzone+'&popupid='+popupid;
	
	
	// temporary - set catfish not to appear for another 7 days
	var expire = new Date();
	expire.setTime(expire.getTime() + (604800000)); // 7 days 
	document.cookie = 'SPcatfish=1; expires=' + expire.toGMTString() + '; path=/';
	
}

function destroycatfish()
{
	if (!catfish) return false;
	document.body.removeChild(catfish);
	document.body.parentNode.style.paddingBottom = '0';
	// set cookie so it won't appear for the rest of the day
	//var expire = new Date();
	//expire.setTime(expire.getTime() + (43200000)); // 12 hours 
	//document.cookie = 'SPcatfish=1; expires=' + expire.toGMTString() + '; path=/';
	return false;
}

function destroycatfishnoreturn()
{
	destroycatfish();
	var expire = new Date();
	expire.setTime(expire.getTime() + (86400000 * 365)); 
	document.cookie = 'SPsub=1; expires=' + expire.toGMTString() + '; path=/';
	return false;
}

function catfishlaunchpopup()
{
	pp = window.open(this.href, 'catfishpopup', 'scrollbars,width=560,height=420,resizable');
	return pp ? false : true;
}

popupcookiesenabled = false;
var expire = new Date();
expire.setTime(expire.getTime() + (10000)); 
document.cookie = 'SPtestcookie=1; expires=' + expire.toGMTString() + '; path=/';
cookiearray = document.cookie.split(/;\s*/);
for (entry in cookiearray)
{
	if (cookiearray[entry] == 'SPtestcookie=1') popupcookiesenabled = true;
}
if (popupcookiesenabled)
{
	catfish = createcatfish();
	catfisholdonload = window.onload;
	window.onload = function ()
	{
		deploycatfish(catfish);
		if (catfisholdonload) catfisholdonload();
	} 
}

// Specific to this catfish

function catfishpopulate(catfish)
{
	catfish.style.backgroundImage = 'url(/images/books/freelance1/catfishback.png)';
	
	catfish.style.height = '72px';
	catfish.style.padding = '0';
	
	var btitle = 'The Web Design Business Kit';
	var imgsrc = '/images/books/freelance1/catfish1.png?amx';
	var linkurl = '/popup/popup.php?loadpopid=49&popupid='+popupid+'&zoneid='+popupzone;
	var tagtext = 'Great ideas to increase your freelance revenue!';
	var calltoaction = 'Download the FREE sample chapters!';
	
	// create goaway
	ul = document.createElement('ul');
	l1 = document.createElement('li');
	l2 = document.createElement('li');
	closelink = document.createElement('a');
	closelink.appendChild(document.createTextNode('Close this'));
	closelink.href = '?';
	closelink.onclick = destroycatfish;
	closelink.style.color = '#FFCCAA';
	nomorelink = document.createElement('a');  
	nomorelink.appendChild(document.createTextNode('Don\'t show this again'));
	nomorelink.href = '?';
	nomorelink.onclick = destroycatfishnoreturn;
	nomorelink.style.color = '#FFCCAA';
	l1.appendChild(closelink);
	l2.appendChild(nomorelink);
	ul.appendChild(l1);
	ul.appendChild(l2);
	ul.style.styleFloat = ul.style.cssFloat = 'right';
	ul.style.margin = '31px 16px 0 0';
	ul.style.fontSize = 'x-small';
	ul.style.listStyleType = 'none';
	ul.style.padding = '0';
	catfish.appendChild(ul);
		
	a = document.createElement('a');
	img = document.createElement('img');
	img.src = imgsrc;
	img.alt = btitle;
	a.href = linkurl;
	a.title = calltoaction;
	a.onclick = catfishlaunchpopup;
	a.appendChild(img);
	a.style.styleFloat = a.style.cssFloat = 'left';
	a.style.display = 'block';
	a.style.padding = '27px 10px 0 184px';
	catfish.appendChild(a);
	
	var p = document.createElement('p');
	var a = document.createElement('a');
	a.href = linkurl;
	a.title = btitle;
	a.onclick = catfishlaunchpopup;
	a.appendChild(document.createTextNode(calltoaction));
	a.style.color = '#FFFFFF';
	if (screen.width >= 1024)
	{
		p.appendChild(document.createTextNode(tagtext));
		p.appendChild(document.createElement('br'));
	}
	var img2 = document.createElement('img');
	img2.src = '/images/icons/pdf5.gif';
	img2.alt = '';
	img2.style.verticalAlign = 'middle';
	p.appendChild(img2);
	p.appendChild(document.createTextNode(' '));
	p.appendChild(a);
	p.style.margin = '28px 0 0 30%';
	catfish.appendChild(p);
}

function getcatfishoverlap()
// returns how many pixels this catfish overlaps the content by
{
	return 22;
}