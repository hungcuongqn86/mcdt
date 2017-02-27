function DoLink(p_order, sLink){
	clearTimeout(timer);
	objMenu = document.getElementsByName("td_menu");
	v_length = objMenu.length;	
	refesh();
	for( i = 0; i < v_length; i++){
		if(p_order == i ){
			objMenu[p_order].className =  "swap_bottom_right";
		}else{
			objMenu[i].className =  "swap_bottom_right";
		}
	}
	document.getElementById('noi_dung_tthc').src = sLink;
	window.top.location="http://dhtn.lethuy.gov.vn/ecs/mhcu/index.xml#top";
	location.href=document.referrer+'#top'
	HideLoad1();
}
function writeTime(s)
{
	var mydate;
	if (s)
		mydate = new Date(s);
	else
		mydate = new Date();
	var year = mydate.getYear()
	if (year < 1000)
		year += 1900
	var month = mydate.getMonth() + 1
	if (month < 10)
		month = "0" + month
	var day = mydate.getDate()
	if (day < 10)
		day = "0" + day
	var dayw = mydate.getDay()
	
	var hrNow = mydate.getHours()
	
	if (hrNow == 0) {
		hour = 12;
		ap = " am";
	} else if(hrNow <= 11) {
		ap = " am";
		hour = hrNow;
	} else if(hrNow == 12) {
		ap = " pm";
		hour = 12;
	} else if (hrNow >= 13) {
		hour = (hrNow - 12);
		ap = " pm";
	}
	var minute=mydate.getMinutes()
	if (minute < 10)
		minute = "0" + minute
	var dayarray=new Array("Ch&#7911; Nh&#7853;t","Th&#7913; Hai","Th&#7913; Ba","Th&#7913; T&#432;","Th&#7913; N&#259;m","Th&#7913; S&#225;u","Th&#7913; B&#7843;y")
	document.write(dayarray[dayw]+", ng&#224;y "+day+"-"+month+"-"+year)
}
function show_row(p_row_id){
	objShowHide = document.getElementsByName(p_row_id);
	var v_length = objShowHide.length;	
	if (v_length){
		for (var i=0;i<v_length;i++){
			objShowHide[i].style.display = "block";
		}
	}else{
		eval("document.getElementById('" + p_row_id + "').style.display = '" + "block'");
	}
}
function hide_row(p_row_id){
	objShowHide = document.getElementsByName(p_row_id);
	var v_length = objShowHide.length;		
	if (v_length){
		for (var i=0;i<v_length;i++){
			objShowHide[i].style.display = "none";
		}
	}else{
		eval("document.getElementById('" + p_row_id + "').style.display = '" + "none'");
	}
}
function ShowHide(pName){
	clearTimeout(timer);	
	HideLoad2();
	timer = setTimeout ( "HideLoad()",90000);	
	if( pName == "PrentTaichinhID" ){	
		show_row('ChildTaichinhID');		
		hide_row('ChildXaydungID');
		hide_row('ChildTainguyenID');
		hide_row('ChildTuphapID');
		hide_row('ChildChinhsachID');		
	}else if( pName == "PrentXaydungID"){
		hide_row('ChildTaichinhID');		
		show_row('ChildXaydungID');
		hide_row('ChildTainguyenID');
		hide_row('ChildTuphapID');
		hide_row('ChildChinhsachID');
	}else if( pName == "PrentTainguyenID"){
		hide_row('ChildTaichinhID');		
		hide_row('ChildXaydungID');
		show_row('ChildTainguyenID');
		hide_row('ChildTuphapID');
		hide_row('ChildChinhsachID');
	}else if( pName == "PrentTuphapID"){
		hide_row('ChildTaichinhID');		
		hide_row('ChildXaydungID');
		hide_row('ChildTainguyenID');
		show_row('ChildTuphapID');
		hide_row('ChildChinhsachID');
	}else if( pName == "PrentChinhsachID"){
		hide_row('ChildTaichinhID');		
		hide_row('ChildXaydungID');
		hide_row('ChildTainguyenID');
		hide_row('ChildTuphapID');
		show_row('ChildChinhsachID');
	}else{
		hide_row('ChildTaichinhID');		
		hide_row('ChildXaydungID');
		hide_row('ChildTainguyenID');
		hide_row('ChildTuphapID');
		hide_row('ChildChinhsachID');
	}
	$('td').removeClass('current');
	pName = '#' + pName;
	$(pName).removeClass('current');
	$(pName).addClass('current');
	
}