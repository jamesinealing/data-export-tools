<?php

header('Content-Type: text/html; charset=utf-8');
ini_set('max_execution_time', -1); //300 seconds = 5 minutes
error_reporting(E_ALL);
require('./tools/find_dates.php');
require('./tools/geo-lookup.php');

// replace with your own db credentials
$mysqli = new mysqli("host", "user", "password", "db");

// Check connection
if ($mysqli->connect_error) { die("Connection failed: " . $mysqli->connect_error); }
$mysqli->set_charset('utf8');

/* old way of doing it
$query=str_replace(" ", "%20", $_GET['q']);
$facets=$_GET['facets'];
$apicall = "http://data.iwm.org.uk/{API KEY HERE}/select?q=placeString:*&fq=type:object&fq=mediaType%3Aimage&fq=availability%3A%22Share%20and%20Reuse%22&fq=%22".$query."%22%20OR%20".$facets.":%22".$query."%22&rows=5000&wt=json";
*/

$query = (!empty($_GET['q'])) ? '"'.$_GET['q'].'"' : '';
$facets = (!empty($_GET['facets'])) ? str_replace("|", "%20OR%20", $_GET['facets']) : '';
$join = (!empty($query) && !empty($facets)) ? '%20OR%20' : '';
$apicall = "http://data.iwm.org.uk/{API KEY HERE}/select?q=placeString:*&fq=type:object&fq=mediaType%3Aimage&fq=availability%3A%22Share%20and%20Reuse%22&fq=".str_replace(" ","%20",($query.$join.$facets))."&rows=5000&wt=json";
echo "<hr>Api call: ".$apicall."<br>";
$json = file_get_contents($apicall);
$results = json_decode($json, true);
echo '<br>'.$results['response']['numFound'].' results found';

foreach($results['response']['docs'] as $row) {
  $title = mysqli_real_escape_string($mysqli, $row['summaryTitle']);
  $description = isset($row['description']) ? mysqli_real_escape_string($mysqli,implode($row['description'])) : '';
  echo '<hr>ID: '.$row['idIndex'].'. '.$title.'<br>'.$description;
  // check for place
  $placename = end($row['placeString']); // should change this to handle multiple places, but for now just take last one in array as it tends to be most precise
  echo '<br>placeString found: '.$placename;
  $placefound='';
  $lat='';
  $lon='';
  $placename_escaped = mysqli_real_escape_string($mysqli, $placename);
  $sql="SELECT * FROM geo_places WHERE placename = '$placename_escaped'";
  echo '<br>'.$sql;
  if ($placeResults = $mysqli->query($sql)) {
    if($placeResults->num_rows != 0) {
    while ($placeValues = $placeResults->fetch_assoc()) {
      echo '<br>';print_r($placeValues);
      if (!empty($placeValues['lat'])) {
        $lat = $placeValues['lat'];
        $lon = $placeValues['lon'];
        echo '<br>Found existing data for '.$placename;

      } else {
        echo '<br>No placename data was found for '.$placename;
        $geocoded = findGeo($placename);
        if (!empty($geocoded['placefound'])) {
          $lat = $geocoded['lat'];
          $lon = $geocoded['lon'];
        }
      }
    }
  }
 else {
    echo '<br>No placename data - lets geocode '.$placename.' from Google API';
    $geocoded = findGeo($placename);
    print_r($geocoded);
    if (!empty($geocoded[0])) {
      $lat = $geocoded[1];
      $lon = $geocoded[2];
    }
  }
}
  echo '<br>'.$placename." - lat: ".$lat." lon: ".$lon;

// get date info
$dateMade='';
$dateText='';
if (isset($row['dateMade'])) {
  echo '<br>We have a dateMade';
  $dateMade = $row['dateMade'][0];
} else {
  echo '<br>No dateMade, so see if there is one extractable from text';
  $dateFound = find_date($title.' | '.$description);
  unset($dateFound[0]['prefix']);
  print_r($dateFound);
  $dateMade = (!empty($dateFound[0])) ? implode("-",$dateFound[0]) : '';
} // end looping through dates

if (strlen($dateMade)>=8) {
  $date = date_create($dateMade);
  $dateText = date_format($date,"j F Y");
  $dateGranularity=0;
}
else if (strlen($dateMade)>=5) {
  $date = date_create($dateMade.'-01');
  $dateText = date_format($date,"F Y");
  $dateGranularity=4;
}
else if (strlen($dateMade)==4) {
  $date = date_create($dateMade.'-01-01');
  $dateText = 'some time in '.date_format($date,"Y");
  $dateGranularity=6;
}
else {
  $dateText='';
  $dateGranularity='';
  $date='';
}

echo '<br>dateMade: '.$dateText.' ('.$dateMade.' '.$dateGranularity.')';

if ($lat!='' && $dateMade!='') {
// add to db
$map_name=$_GET['map_name'];
$objectId=$row['idIndex'];
$thumb_url='http://media.iwm.org.uk/ciim5/'.$row['midMediaLocation'][0];
$image_url='http://media.iwm.org.uk/ciim5/'.$row['largeMediaLocation'][0];
$mysqlDate = $date->format('Y-m-d');
$sql="INSERT INTO `storymaps`(`map_name`, `objectId`, `title`, `description`, `thumb_url`, `image_url`, `placename`, `dateText`, `date`, `date_granularity`) VALUES ('$map_name','$objectId','$title','$description','$thumb_url','$image_url','$placename_escaped','$dateText','$mysqlDate',$dateGranularity) ON DUPLICATE KEY UPDATE `placename`='$placename_escaped', `date`='$mysqlDate', `date_granularity`=$dateGranularity";
if ($mysqli->query($sql) === TRUE) {
echo "<br>New map point created successfully";
} else {
echo "Error: " . $sql . "<br>" . $mysqli->error;
}
} else {
  echo '</br>Can\'t create new map point as geotags or date are missing';
}
}
$mysqli->close();
?>
