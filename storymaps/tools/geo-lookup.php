<?php
header('Content-Type: text/html; charset=utf-8');
ini_set('max_execution_time', -1); // let it run and run until done!
error_reporting(E_ALL);


function replaceAccents($str) {
  $search = explode(",","ç,æ,œ,á,é,í,ó,ú,à,è,ì,ò,ù,ä,ë,ï,ö,ü,ÿ,â,ê,î,ô,û,å,ø,Ø,Å,Á,À,Â,Ä,È,É,Ê,Ë,Í,Î,Ï,Ì,Ò,Ó,Ô,Ö,Ú,Ù,Û,Ü,Ÿ,Ç,Æ,Œ");
  $replace = explode(",","c,ae,oe,a,e,i,o,u,a,e,i,o,u,a,e,i,o,u,y,a,e,i,o,u,a,o,O,A,A,A,A,A,E,E,E,E,I,I,I,I,O,O,O,O,U,U,U,U,Y,C,AE,OE");
  return str_replace($search, $replace, $str);
}

function replaceAbbreviations($str) {
  // it seems you get better results if you remove abbreviations
  // list of county abbreviations taken in part from https://familysearch.org/wiki/en/UK_County_Abbreviations
  $abbreviations = array("Co.", "Rd.", "St.,", "Dr.", "Ct.", "Sq.", "Lancs", "Berks", "Herts", "Hunts.", "Staffs.", "Yorks.", "Glos.", "Hants.", "Notts.", "Worcs.", "Wilts.", "Warks.", "Staffs.", "Salop.", "Northants", "Middx", "Cambs.", "Mon.");
  $expanded = array("County", "Road", "Street,", "Drive", "Court","Square", "Lancashire", "Berkshire", "Hertfordshire", "Huntingdonshire", "Staffordshire", "Yorkshire", "Gloucestershire", "Hampshire", "Nottinghamshire", "Worcestershire", "Wiltshire", "Warwickshire", "Staffordshire", "Shropshire", "Northamptonshire", "Middlesex", "Cambridgeshire", "Monmouthshire");
  return str_replace($abbreviations, $expanded, $str);
}

function findGeo ($placename) {
      $address = replaceAccents(replaceAbbreviations($placename)); // cleaning up placename gives better geocoding hit rate
      // now we call the Google geocoding API
      $googleKey=' {Insert Key Here} ';  // get a key from https://developers.google.com/maps/documentation/geocoding/get-api-key#key
      $url = "https://maps.google.com/maps/api/geocode/json?address=".urlencode($address)."&region=gb&key=".$googleKey;
      echo '<blockquote>'.$placename.'<br><a href="'.$url.'" target="_blank">API call</a><br>';
      $ch = curl_init();
      curl_setopt($ch, CURLOPT_URL, $url);
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
      curl_setopt($ch, CURLOPT_PROXYPORT, 3128);
      curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
      curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
      $response = curl_exec($ch);
      curl_close($ch);
      $response_a = json_decode($response);
      // if there's a response, extract data
      if (isset($response_a->results[0])) {
        echo "place found: ";
        echo $placefound = $response_a->results[0]->formatted_address;
        echo "<br />lat: ";
        echo $lat = $response_a->results[0]->geometry->location->lat;
        echo "<br />lon: ";
        echo $lon = $response_a->results[0]->geometry->location->lng;
        echo "<br />";
        echo "<a href='http://maps.google.com/?q=".$lat.",".$lon."' target='_blank'>LatLon success!</a><br>";
        // format placecefound by Google to store in DB
      } else {
        // if Google doesn't return any results
        echo "no place found<br>";
        $placefound = '';
        $lat = 'null';
        $lon = 'null';
      }
      echo '</blockquote>';
      return array($placefound,$lat,$lon);
}

?>
