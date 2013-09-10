function iniMap(elId) {
	var gmLatLng = new google.maps.LatLng(gmap_params.lat, gmap_params.long);
	var gmOptions = {
		zoom: 10,
		center: gmLatLng,
		mapTypeId: google.maps.MapTypeId.HYBRID,
		focus: '0'
	}
	var gmMap = new google.maps.Map(document.getElementById(elId), gmOptions);
	var contentMark = '<div id="gm_mark">' + gmap_params.title + '</div>';
	var contentWindow = new google.maps.InfoWindow({
		content: contentMark
	});
	var gmMarker = new google.maps.Marker({
		position: gmLatLng,
		map: gmMap,
		title: ''
	});
	google.maps.event.addListener(gmMarker, 'click', function() {
		contentWindow.open(gmMap, gmMarker);
	});
}
