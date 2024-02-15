var meteoRequest = new XMLHttpRequest();
var longitude = 5.4510;
var latitude = 43.5156;
var url = "https://api.openweathermap.org/data/2.5/weather?lat=" + latitude + "&lon=" + longitude + "&lang=fr&APPID=ae546c64c1c36e47123b3d512efa723e";

function refreshWeather() {
    meteoRequest.open('GET', url, true);
    meteoRequest.setRequestHeader('Accept', 'application/json');
    meteoRequest.send();
}

meteoRequest.onload = function () {
    var json = JSON.parse(this.responseText);


    var temp = Math.round(getTemp(json));
    var vent = getWind(json).toFixed(0);
    var humidity = json.main.humidity;
    var sunsetTime = new Date(json.sys.sunset * 1000).toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });

    // Mise à jour de l'affichage
    document.getElementById('temperature').textContent = temp + "°C";
    document.getElementById('wind').textContent = vent + " KM/H - " + humidity + "% humidité";
    document.getElementById('sunsetTime').textContent = "Le soleil se couche à " + sunsetTime;
};

function getTemp(json) {
    return kelvinToC(json.main.temp);
}

function getWind(json) {
    return msToKmh(json.wind.speed);
}


function kelvinToC(kelvin) {
    return kelvin - 273.15;
}


function msToKmh(speed) {
    return speed * 3.6;
}

refreshWeather();
