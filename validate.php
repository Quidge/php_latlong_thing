<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>lat/long validation</title>
  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
</head>
<body>
  <body class="bg-dark">
  <div class="container bg-dark">
    <div class="row justify-content-center">
      <div class="col-md-6 bg-light my-5">
        <div class="p-5">
          <?php

          // WEDO hendo office lat long coords
          $WEDO_LAT = 35.316770;
          $WEDO_LONG = -82.461131;

          // distance function from https://www.geodatasource.com/developers/php
          function distance($lat1, $lon1, $lat2, $lon2, $unit) {
            if (($lat1 == $lat2) && ($lon1 == $lon2)) {
              return 0;
            }
            else {
              $theta = $lon1 - $lon2;
              $dist = sin(deg2rad($lat1)) * sin(deg2rad($lat2)) +  cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * cos(deg2rad($theta));
              $dist = acos($dist);
              $dist = rad2deg($dist);
              $miles = $dist * 60 * 1.1515;
              $unit = strtoupper($unit);

              if ($unit == "K") {
                return ($miles * 1.609344);
              } else if ($unit == "N") {
                return ($miles * 0.8684);
              } else {
                return $miles;
              }
            }
          }
          // quick miles to feet conversion
          function mi_to_ft($milesUnit) {
            return $milesUnit * 5280;
          }

          // get the input value
          // it turns out that $_POST is a global object available to
          // scripts linked in the <form action="script_name_here.php"></form>
          // tag.
          $latlong_str = $_POST['latlongVal'];
          // split it up with a regex targeting comma characters
          $latlong_arr_raw_strs = preg_split('/,/', $latlong_str);

          // strip out whitespace and cast the values to floats
          foreach($latlong_arr_raw_strs as &$value) {
            $value = preg_replace('/\s+/', '', $value);
            $vaue = (float)$value;
          }
          // php is weird; without unsetting this, $value is still available
          // to the namepsace as the last value in the array
          // get rid of it
          unset($value);
          // rename
          $latlong_floats = $latlong_arr_raw_strs;
          // get rid of the raw strings
          unset($latlong_arr_raw_strs);

          // do math
          $distance_from_office = distance(
            $WEDO_LAT,
            $WEDO_LONG,
            $latlong_floats[0],
            $latlong_floats[1],
            'M'
          );

          // silly stuff
          $dist_str;
          if ($distance_from_office == 0) {
            $dist_str = '0 feet';
          }
          else if ($distance_from_office <= .2) {
            $feet = round(mi_to_ft($distance_from_office));
            $dist_str = sprintf('%d feet', $feet);
          }
          else if (round($distance_from_office) == 1) {
            $dist_str = sprintf('%d mile', round($distance_from_office));
          }
          else {
            $dist_str = sprintf('%d miles', round($distance_from_office));
          }

          // setup the format string
          $format = "<p class='lead font-weight-light'> The coordinates given (<span class='font-weight-bolder'>%f , %f</span>) are <span class='font-weight-bolder'>%s</span> from the WEDO Hendersonville office.</p>";

          // populate and return the format string
          echo sprintf(
            $format,
            $latlong_floats[0],
            $latlong_floats[1],
            $dist_str
          );

          ?>
          <a href="index.html" class="btn btn-primary">Go Back</a>
        </div>
      </div>
    </div>
  </div>
</body>
</body>
</html>