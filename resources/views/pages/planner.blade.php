  @extends('master')

  @section('title')
  Planner
  @stop

  @section('content')
  <script type="text/javascript" src="https://maps.googleapis.com/maps/api/js?key=AIzaSyC4bbyifwfej8H4k5dCeTIV_tyFMfK8H4c&sensor=false"></script>

  <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js"></script>

<!-- Style to put some height on the map -->
<style type="text/css">
    #map-canvas { height: 500px };
</style>


<br>
<br>
<input id="locationText" type="text" />
<button id="addNewLocation" onclick="addNewLocation();">Add</button>
<button onclick="init();"type="button" name="button">goo</button>

<div id="locationList">
</div>

<script type="text/javascript">
// Declare location array
var locations = [];

// Button bindings
var locationButton = document.getElementById("addNewLocation");
locationButton.onclick = addNewLocation;

// Button functions
function addNewLocation() {
  var location = document.getElementById("locationText").value;
  locations.push(location);
  document.getElementById("locationText").value = "";
}

// Create Json Object function
function makeJsonObject() {
  var json = {
    location : locations
}
  return json;
}

// Write location to Screen function
function writeLocation () {
}


// Initialise some variables
var directionsService = new google.maps.DirectionsService();
var num, map, data;
var requestArray = [], renderArray = [];

// A JSON Array containing some people/routes and the destinations/stops
var jsonArray = makeJsonObject();

// 16 Standard Colours for navigation polylines
// var colourArray = ['navy', 'grey', 'fuchsia', 'black', 'white', 'lime', 'maroon', 'purple', 'aqua', 'red', 'green', 'silver', 'olive', 'blue', 'yellow', 'teal'];

// Let's make an array of requests which will become individual polylines on the map.
function generateRequests(){

    requestArray = [];

    for (var route in jsonArray){
        // This now deals with one of the people / routes

        // Somewhere to store the wayoints
        var waypts = [];

        // 'start' and 'finish' will be the routes origin and destination
        var start, finish

        // lastpoint is used to ensure that duplicate waypoints are stripped
        var lastpoint

        data = jsonArray[route]

        limit = data.length
        for (var waypoint = 0; waypoint < limit; waypoint++) {
            if (data[waypoint] === lastpoint){
                // Duplicate of of the last waypoint - don't bother
                continue;
            }

            // Prepare the lastpoint for the next loop
            lastpoint = data[waypoint]

            // Add this to waypoint to the array for making the request
            waypts.push({
                location: data[waypoint],
                stopover: true
            });
        }

        // Grab the first waypoint for the 'start' location
        start = (waypts.shift()).location;
        // Grab the last waypoint for use as a 'finish' location
        finish = waypts.pop();
        if(finish === undefined){
            // Unless there was no finish location for some reason?
            finish = start;
        } else {
            finish = finish.location;
        }

        // Let's create the Google Maps request object
        var request = {
            origin: start,
            destination: finish,
            waypoints: waypts,
            travelMode: google.maps.TravelMode.DRIVING
        };

        // and save it in our requestArray
        requestArray.push({"route": route, "request": request});
    }

    processRequests();
}

function processRequests(){

    // Counter to track request submission and process one at a time;
    var i = 0;

    // Used to submit the request 'i'
    function submitRequest(){
        directionsService.route(requestArray[i].request, directionResults);
    }

    // Used as callback for the above request for current 'i'
    function directionResults(result, status) {
        if (status == google.maps.DirectionsStatus.OK) {

            // Create a unique DirectionsRenderer 'i'
            renderArray[i] = new google.maps.DirectionsRenderer();
            renderArray[i].setMap(map);

            // // Some unique options from the colorArray so we can see the routes
            // renderArray[i].setOptions({
            //     preserveViewport: true,
            //     suppressInfoWindows: true,
            //     polylineOptions: {
            //         strokeWeight: 4,
            //         strokeOpacity: 0.8,
            //         strokeColor: colourArray[i]
            //     },
            //     markerOptions:{
            //         icon:{
            //             path: google.maps.SymbolPath.BACKWARD_CLOSED_ARROW,
            //             scale: 3,
            //             strokeColor: colourArray[i]
            //         }
            //     }
            // });

            // Use this new renderer with the result
            renderArray[i].setDirections(result);
            // and start the next request
            nextRequest();
        }

    }

    function nextRequest(){
        // Increase the counter
        i++;
        // Make sure we are still waiting for a request
        if(i >= requestArray.length){
            // No more to do
            return;
        }
        // Submit another request
        submitRequest();
    }

    // This request is just to kick start the whole process
    submitRequest();
}

// Called Onload
function init() {

    // Some basic map setup (from the API docs)
    var mapOptions = {
        center: new google.maps.LatLng(50.677965, -3.768841),
        zoom: 8,
        mapTypeControl: false,
        streetViewControl: false,
        mapTypeId: google.maps.MapTypeId.ROADMAP
    };

    map = new google.maps.Map(document.getElementById('map-canvas'), mapOptions);

    // Start the request making
    generateRequests()
}

    // Get the ball rolling and trigger our init() on 'load'
    // google.maps.event.addDomListener(window, 'load', init);
</script>

<!-- Somewhere in the DOM for the map to be rendered -->
<div id="map-canvas"></div>

@stop
