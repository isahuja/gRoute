<!DOCTYPE html>
<!--[if lt IE 7]>      <html class="no-js lt-ie9 lt-ie8 lt-ie7"> <![endif]-->
<!--[if IE 7]>         <html class="no-js lt-ie9 lt-ie8"> <![endif]-->
<!--[if IE 8]>         <html class="no-js lt-ie9"> <![endif]-->
<!--[if IE 9]>         <html class="ie9"> <![endif]-->
<!--[if gt IE 8]><!--> 
<!--<![endif]-->

<html>
<head>
	<meta charset="utf-8">
	<meta name="msvalidate.01" content="A848D1242C679C3E83AFBF7842016E6C" />
	<meta http-equiv="X-UA-Compatible" content="IE=Edge">
	<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, minimum-scale=1, user-scalable=no" /><meta name="keywords" content="Alternate Route"/>
	<meta http-equiv="content-language" content="en" />

	<script type="text/javascript" src="https://maps.googleapis.com/maps/api/js?key=AIzaSyBrMAYZj4GFedFRQ3AgUymM9yjXmmrk6w0&libraries=places"></script>
	<link rel="stylesheet" href="css/app.css?v=1">
	<script src="js/app.js?v=1"></script>
	<title>Search</title>
</head>
<body>

	<div class="container">
		<div class="centered">
			<form action="">
				<div class="form-group col-xs-5">
					<label for="start_point" class="col-2 col-form-label">ORIGIN</label>
					<div class="col-10">
						<input class="form-control" type="text" value="" id="start_point" >
					</div>
				</div>

				<div class="form-group col-xs-5">
					<label for="end_point" class="col-2 col-form-label">DESTINATION</label>
					<div class="col-10">
						<input class="form-control" type="text" value="" id="end_point" >
					</div>
				</div>

				<div class="form-group col-xs-2">
					<!-- <button id='btt' class="btn btn-primary submitsearch">Submit</button> -->
					<span id="send_data" class="col-2 col-form-label btn button-set">Show Route</span>
					
				</div>

			</form>
		</div>
	</div>

	<center>
		<div id="map-containers">
			<div id="map" k></div>
			<div class="alernate-div">
				<span id="alternate_routes" class="col-2 col-form-label btn button-set">Show Alternate Routes</span>

			</div>
			
			<div id="mapclone1" style=" height:300px"></div>

			<div id="clone-cont-2">	</div>
			<div id="clone-cont-3">	</div>

		</div>
	</center>
	<input type="hidden" id="base_url" value="{{ URL::to('/') }}"></input>
</body>
<script>
	var base_url = $('#base_url').val();
	var autocomplete1 = '';
	var start_lat = '';
	var start_lng = '';

	var autocomplete2 = '';
	var end_lat = '';
	var end_lng = '';

	var directionsDisplay;
	var directionsService = new google.maps.DirectionsService();
	var map;

	function interpret_address(autocomplete1)
	{
		var myAddress = {};
		var city_temp = '';
		var place = autocomplete1.getPlace();
		var component = place.address_components;
		var location = place.geometry.location;
		var address = new Object;

		for (i = component.length - 1; i >= 0 ; i--)
		{
			if(component[i].types[0] == 'country')
			{
				address.country = component[i].short_name;
				address.lat = location.lat();
				address.lng = location.lng();
			}

			if(component[i].types[0] == 'administrative_area_level_1')
			{
				address.state = component[i].long_name;
			}

			if(component[i].types[0] == 'locality')
			{
				address.city = component[i].long_name;
			}

			if(city_temp == '' && component[i].types[0] == 'administrative_area_level_2')
			{
				address.city = component[i].long_name;
			}

			if(component[i].types[0] == 'sublocality_level_1')
			{
				address.area = component[i].long_name;
			}
		}
		return address;
	}

	function initializeStart()
	{
		var input = document.getElementById('start_point');
		var autocomplete = new google.maps.places.Autocomplete(input);

		google.maps.event.addListener(autocomplete, 'place_changed', function()
		{
			autocomplete1 = interpret_address(autocomplete);
			start_lat = autocomplete1.lat;
			start_lng = autocomplete1.lng;

			console.log(start_lat + ' ' + start_lng);
			console.log($('#start_point').val());
		});
	}

	function initializeEnd()
	{
		var input = document.getElementById('end_point');
		var autocomplete = new google.maps.places.Autocomplete(input);

		google.maps.event.addListener(autocomplete, 'place_changed', function()
		{
			autocomplete2 = interpret_address(autocomplete);
			end_lat = autocomplete2.lat;
			end_lng = autocomplete2.lng;

			console.log(end_lat + ' ' + end_lng)
		});
	}

	function myMap()
	{
		directionsDisplay = new google.maps.DirectionsRenderer();
		var chicago = new google.maps.LatLng(53.349805, -6.260310);
		var mapProp = {
						center : chicago,
						zoom : 10,
					};

		var map = new google.maps.Map(document.getElementById("map"),mapProp);
		directionsDisplay.setMap(map);
	}


	google.maps.event.addDomListener(window, 'load', initializeStart);
	google.maps.event.addDomListener(window, 'load', initializeEnd);
	google.maps.event.addDomListener(window, 'load', myMap);

	function calcRoute()
	{
		var request = {
							origin: new google.maps.LatLng(start_lat, start_lng),
							destination: new google.maps.LatLng(end_lat, end_lng),
							travelMode: 'DRIVING'
						};
		directionsService.route(request, function(response, status)
		{
			if (status == 'OK')
			{
				directionsDisplay.setDirections(response);
			}
		});
	}

	$('#send_data').click(function()
	{

		if($('#start_point').val() != "" && $('#end_point').val() != ""){
			calcRoute();
			$('.alernate-div').show();
			$('#mapclone2').remove();
			$('#mapclone3').remove();

			$('#clone-cont-2').html('');
			$('#clone-cont-3').html('');
			
		}
		
	});

	$('#alternate_routes').click(function()
	{
		if($("#mapclone2").length)
			return;

		$.ajax({
			url  : 	base_url + '/get-routes',
			type : 	"get",
			data : 	{
						'start_lat'	: start_lat,
						'start_lng'	: start_lng,
						'end_lat'	: end_lat,
						'end_lng'	: end_lng,
						'start_text'	: $('#start_point').val(),
						'end_text'		: $('#end_point').val()
					},
			success:function(data)
			{
				console.log(data);
				if(data.success == 1)
				{
					
					// first path
					var clone_div = $("#mapclone1");
					var $klon = clone_div.clone().prop('id', 'mapclone2');
					$('#clone-cont-2').append($klon);
					var directionsDisplay11 = new google.maps.DirectionsRenderer();
					var directionsService11 = new google.maps.DirectionsService();

					var chicago = new google.maps.LatLng(41.850033, -87.6500523);
					var mapProp11 = {
									center : chicago,
									zoom : 5,
								};

					var map1 = new google.maps.Map(document.getElementById('mapclone2'),mapProp11);
					directionsDisplay11.setMap(map1);

					var request11 = {
										origin: new google.maps.LatLng(data.data[0].origin_lat.substring(0,7), data.data[0].origin_lng.substring(0,7)),
										destination: new google.maps.LatLng(data.data[0].destination_lat.substring(0,7), data.data[0].destination_lng.substring(0,7)),
										travelMode: 'DRIVING'
									};

					directionsService11.route(request11, function(response11, status11)
					{
						if(status11 == 'OK')
						{
							directionsDisplay11.setDirections(response11);

							$("#clone-cont-2").append("<div class='content-2'><span class='firstel'> " + data.data[0].origin_name + "</span> - <span class='lastel'>" + data.data[0].destination_name +"</span> - <span class='lastel'>" + data.data[0].travel_information + "</span></div>")

						}
						else
						{
							console.log(response11);
						}
					});

					// second path
					var clone_div = $("#mapclone1");
					var $klon = clone_div.clone().prop('id', 'mapclone3');
					$('#clone-cont-3').append($klon);
					var directionsDisplay22 = new google.maps.DirectionsRenderer();
					var directionsService22 = new google.maps.DirectionsService();

					var chicago = new google.maps.LatLng(41.850033, -87.6500523);
					var mapProp22 = {
									center : chicago,
									zoom : 5,
								};

					var map2 = new google.maps.Map(document.getElementById('mapclone3'),mapProp22);
					directionsDisplay22.setMap(map2);

					// $('body').height($(document).height());

					var request22 = {
										origin: new google.maps.LatLng(data.data[1].origin_lat.substring(0,7), data.data[1].origin_lng.substring(0,7)),
										destination: new google.maps.LatLng(data.data[1].destination_lat.substring(0,7), data.data[1].destination_lng.substring(0,7)),
										travelMode: 'DRIVING'
									};

					directionsService22.route(request22, function(response22, status22)
					{
						if(status22 == 'OK')
						{
							directionsDisplay22.setDirections(response22);

							$("#clone-cont-3").append("<div class='content-3'><span class='firstel'>" + data.data[1].origin_name + " </span> - <span class='lastel'> " + data.data[1].destination_name +"</span> - <span class='lastel'>" + data.data[1].travel_information + "</span></div>")
						}
						else
						{
							console.log(response22);
						}
					});
				}
				else
				{
					alert('No Alternate Routes found');
				}
			} 
		});
	});
</script>
</html>