var language = {
	'de' : {
		"200" : "Gewitter mit leichtem Regen",
		"201" : "Gewitter mit Regen",
		"202" : "Gewitter mit starken Regen",
		"210" : "Leichtes Gewitter",
		"211" : "Gewitter",
		"212" : "Schweres Gewitter",
		"221" : "Raues Gewitter",
		"230" : "Gewitter mit leichtem Nieselregen",
		"231" : "Gewitter mit Nieselregen",
		"232" : "Gewitter mit starkem Nieselregen",

		"300" : "Leichter Niesel",
		"301" : "Niesel",
		"302" : "Starker Niesel",
		"310" : "Leichter Nieselregen",
		"311" : "Nieselregen",
		"312" : "Starker Nieselregen",
		"313" : "Regenschauer und Niesel",
		"314" : "Sarker Regenschauer und Niesel",
		"321" : "Nieselschauer",

		"500" : "Leichter Regen",
		"501" : "Moderater Regen",
		"502" : "Starker regen",
		"503" : "Sehr starker Regen",
		"504" : "Extermer Regen",
		"511" : "Gefrierender Regen",
		"520" : "Leichter Regenschauer",
		"521" : "Regenschauer",
		"522" : "Starker Regenschauer",
		"531" : "Rauer Regenschauer",

		"600" : "Leichter Schneefall",
		"601" : "Schneefall",
		"602" : "Dichter Schneefall",
		"611" : "Graupel",
		"612" : "Schneeregen",
		"615" : "Leichter Regen und Schnee",
		"616" : "Regen und Schnee",
		"620" : "Leichter Schneschauer",
		"621" : "Schneescauer",
		"622" : "Dichte Schneeschauer",

		"701" : "Nebel",
		"711" : "Rauch",
		"721" : "Dunst",
		"731" : "Sand, Staubwirbel",
		"741" : "Nebel",
		"751" : "Sand",
		"761" : "Staub",
		"762" : "Vulkanische Asche",
		"771" : "Böen",
		"781" : "Tornado",

		"800" : "Klarer Himmel",

		"801" : "Kaum Wolken",
		"802" : "Vereinzelte Wolken",
		"803" : "Aufgerissene Bewölkung",
		"804" : "Leichte Bewölkung",

		"900" : "Tornado",
		"901" : "Tropischer Sturm",
		"902" : "Hurrikan",
		"903" : "Kälte",
		"904" : "Hitze",
		"905" : "Windig",
		"906" : "Hagel",

		"951" : "Windstill",
		"952" : "Leichte Brise",
		"953" : "Sanfte Brise",
		"954" : "Mäßige Brise",
		"955" : "Frische Brise",
		"956" : "Kräftige Brise",
		"957" : "Starker Wind, fast Sturm",
		"958" : "Sturm",
		"959" : "Sarker Sturm",
		"960" : "Orkan",
		"961" : "Starker Surm",
		"962" : "Hurrikan",
	},
	'en' : {
		"200" : "thunderstorm with light rain",
		"201" : "thunderstorm with rain",
		"202" : "thunderstorm with heavy rain",
		"210" : "light thunderstorm",
		"211" : "thunderstorm",
		"212" : "heavy thunderstorm",
		"221" : "ragged thunderstorm",
		"230" : "thunderstorm with light drizzle",
		"231" : "thunderstorm with drizzle",
		"232" : "thunderstorm with heavy drizzle",

		"300" : "light intensity drizzle",
		"301" : "drizzle",
		"302" : "heavy intensity drizzle",
		"310" : "light intensity drizzle rain",
		"311" : "drizzle rain",
		"312" : "heavy intensity drizzle rain",
		"313" : "shower rain and drizzle",
		"314" : "heavy shower rain and drizzle",
		"321" : "shower drizzle",

		"500" : "light rain",
		"501" : "moderate rain",
		"502" : "heavy intensity rain",
		"503" : "very heavy rain",
		"504" : "extreme rain",
		"511" : "freezing rain",
		"520" : "light intensity shower rain",
		"521" : "shower rain",
		"522" : "heavy intensity shower rain",
		"531" : "ragged shower rain",

		"600" : "light snow",
		"601" : "snow",
		"602" : "heavy snow",
		"611" : "sleet",
		"612" : "shower sleet",
		"615" : "light rain and snow",
		"616" : "rain and snow",
		"620" : "light shower snow",
		"621" : "shower snow",
		"622" : "heavy shower snow",

		"701" : "mist",
		"711" : "smoke",
		"721" : "haze",
		"731" : "sand, dust whirls",
		"741" : "fog",
		"751" : "sand",
		"761" : "dust",
		"762" : "volcanic ash",
		"771" : "squalls",
		"781" : "tornado",

		"800" : "clear sky",

		"801" : "few clouds",
		"802" : "scattered clouds",
		"803" : "broken clouds",
		"804" : "overcast clouds",

		"900" : "tornado",
		"901" : "tropical storm",
		"902" : "hurricane",
		"903" : "cold",
		"904" : "hot",
		"905" : "windy",
		"906" : "hail",

		"951" : "calm",
		"952" : "light breeze",
		"953" : "gentle breeze",
		"954" : "moderate breeze",
		"955" : "fresh breeze",
		"956" : "strong breeze",
		"957" : "high wind, near gale",
		"958" : "gale",
		"959" : "severe gale",
		"960" : "storm",
		"961" : "violent storm",
		"962" : "hurricane",
	}
}

$(document).ready(function(){
	$(window).load(function(e){
		if($('#tx_jheopenweathermap').length){

			if($('#weatherDataArr').length){

				var weatherDataArr = $('#weatherDataArr').attr('value');

				$.ajax({
					url: '?eID=openweathermap',
					type: 'POST',
					data: 'data=' + weatherDataArr,
					dataType: 'json',
					success: function(result) {
						var error = result.error;
						if(error == false) {
							var html, icons = '';

							$.each( result.weather, function( key, value ) {
								icons += '<i class="owf owf-' + value.id + result.dayOrNight +' icon" title="' + language[result.lang][value.id] + '"></i>';
							});

							html = '' +
								'<p class="jhe-openweathermap">' +
								'   <span>Ihr Wetter in '+ result.city + ': </span>' +
									icons +
								'   <span>Aktuell ' + result.temperature + '&deg;C</span>&nbsp;<span><small>(' + result.temperature_min + '-' + result.temperature_max + '&deg;C)</small></span>' +
								'</p>';

								$('.tx_jheopenweathermap').append(html);
						} else {
							$('#tx_jheopenweathermap').remove();
						}
					},
					error: function(error) {}
				});


			} else {
				$('#tx_jheopenweathermap').remove();
			}



		}
	});
});