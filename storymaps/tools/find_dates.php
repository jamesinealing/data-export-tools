<?php
/**
 * Adapted from ....
 *
 * Find Date in a String
 *
 * @author   Etienne Tremel
 * @license  http://creativecommons.org/licenses/by/3.0/ CC by 3.0
 * @link     http://www.etiennetremel.net
 * @version  0.2.0
 *
 * @param string  find_date( ' some text 01/01/2012 some text' ) or find_date( ' some text October 5th 86 some text' )
 * @return mixed  false if no date found else array: array( 'day' => 01, 'month' => 01, 'year' => 2012 )
 */
function find_date( $string ) {
  $shortenize = function( $string ) {
    return substr( $string, 0, 3 );
  };

  // Define month name:
  $month_names = array(
    "january",
    "february",
    "march",
    "april",
    "may",
    "june",
    "july",
    "august",
    "september",
    "october",
    "november",
    "december"
  );
  $short_month_names = array_map( $shortenize, $month_names );

  // Define day name
  $day_names = array(
    "monday",
    "tuesday",
    "wednesday",
    "thursday",
    "friday",
    "saturday",
    "sunday"
  );
  $short_day_names = array_map( $shortenize, $day_names );

  // Define ordinal number
  $ordinal_number = ['st', 'nd', 'rd', 'th'];

  $dates = array();
  $day = "";
  $month = "";
  $year = "";

echo '<pre>';
  // Match dates: 01/01/2012 or 30-12-11 or 1 2 1985
  // preg_match_all( '/([0-9]?[0-9])[\.\-\/ ]+([0-1]?[0-9])[\.\-\/ ]+([0-9]{2,4})/', $string, $matches );
  preg_match_all( '/(\w+ )?([0-9]?[0-9])[\.\-\/ ]+([0-1]?[0-9])[\.\-\/ ]+([0-9]{4})/', $string, $matches, PREG_SET_ORDER );
  if ( $matches ) {
    if (!empty($_GET['debug'])) print_r($matches);
    foreach ($matches as $match) {
    $prefix = "";
    $day = "";
    $month = "";
    $year = "";


    if ( $match[0] )
      $matchText = $match[0];
    if ( $match[1] )
        $prefix = $match[1];
    if ( $match[2] )
        $day = $match[2];
    if ( $match[3] )
      $month = $match[3];
    if ( $match[4] )
      $year = $match[4];

      $dates[] = array(
        'prefix'  => $prefix,
        'year'  => $year,
        'month' => $month,
        'day'   => $day
      );
  }
}

  // Match dates: 2012/01/13 or 1985-1-13
    // preg_match_all( '/([0-9]?[0-9])[\.\-\/ ]+([0-1]?[0-9])[\.\-\/ ]+([0-9]{2,4})/', $string, $matches );
    preg_match_all( '/(\w+ )?([0-9]{4})[\.\-\/ ]+([0-1]?[0-9])[\.\-\/ ]+([0-9]?[0-9])/', $string, $matches, PREG_SET_ORDER );
    if ( $matches ) {
      if (!empty($_GET['debug'])) print_r($matches);
      foreach ($matches as $match) {
      $prefix = "";
      $day = "";
      $month = "";
      $year = "";
      if ( $match[0] )
        $matchText = $match[0];
      if ( $match[1] )
        $prefix = $match[1];
      if ( $match[2] )
          $year = $match[2];
      if ( $match[3] )
        $month = $match[3]*1;
      if ( $match[4] )
        $day = $match[4];
        $dates[] = array(
          'prefix' => trim($prefix),
          'year'  => $year,
          'month' => $month,
          'day'   => $day
        );
      }
    }


  // Match dates: Sunday 1st March 2015; Sunday, 1 March 2015; Sun 1 Mar 2015; Sun-1-March-2015
  preg_match_all('/(\w+ )? (?:(?:' . implode( '|', $day_names ) . '|' . implode( '|', $short_day_names ) . ')[ ]+)?([0-9]{1,2})[ ,\-_\/]*(?:' . implode( '|', $ordinal_number ) . ')?[ ,\-_\/]*(' . implode( '|', $month_names ) . '|' . implode( '|', $short_month_names ) . ')[ ,\-_\/]+([0-9]{4})/i', $string, $matches, PREG_SET_ORDER );

  //preg_match_all('/(\w+ )?(?:(?:' . implode( '|', $day_names ) . '|' . implode( '|', $short_day_names ) . ')[ ,\-_\/]*)?([0-9]?[0-9])[ ,\-_\/]*(?:' . implode( '|', $ordinal_number ) . ')?[ ,\-_\/]*(' . implode( '|', $month_names ) . '|' . implode( '|', $short_month_names ) . ')[ ,\-_\/]+([0-9]{2,4})/i', $string, $matches, PREG_SET_ORDER );
  if ( $matches ) {
    //echo 'test';print_r($matches);
    if (!empty($_GET['debug'])) print_r($matches);
    foreach($matches as $match) {
      $prefix = "";
      $day = "";
      $month = "";
      $year = "";
      if ( $match[0] )
        $matchText = $match[0];
      if ( empty( $prefix ) && $match[1] )
        $prefix = $match[1];

      if ( empty( $day ) && $match[2] )
          $day = $match[2];

    if ( empty( $month ) && $match[3] ) {
      $month = array_search( strtolower( $match[3] ),  $short_month_names );

      if ( ! $month )
        $month = array_search( strtolower( $match[3] ),  $month_names );

      $month = $month + 1;
    }

    if ( empty( $year ) && $match[4] )
      $year = $match[4];

      $dates[] = array(
        'prefix' => trim($prefix),
        'year'  => $year,
        'month' => $month,
        'day'   => $day
      );
    }
  }


  // Match dates: March 1st 2015; March 1 2015; March-1st-2015
  preg_match_all('/(\w+ )?(' . implode( '|', $month_names ) . '|' . implode( '|', $short_month_names ) . ')[ ,\-_\/]*([0-9]?[0-9])[ ,\-_\/]*(?:' . implode( '|', $ordinal_number ) . ')?[ ,\-_\/]+([0-9]{4})/i', $string, $matches, PREG_SET_ORDER );
  if ( $matches ) {
    if (!empty($_GET['debug'])) print_r($matches);
    foreach($matches as $match) {
      $match=
      $prefix = "";
      $day = "";
      $month = "";
      $year = "";
      if ( $match[0] )
        $match = $match[0];

    if ( empty( $month ) && $matches[1] ) {
      $month = array_search( strtolower( $matches[1] ),  $short_month_names );

      if ( ! $month )
        $month = array_search( strtolower( $matches[1] ),  $month_names );

      $month = $month + 1;
    }

    if ( empty( $day ) && $matches[2] )
      $day = $matches[2];

    if ( empty( $year ) && $matches[3] )
      $year = $matches[3];
      $dates[] = array(
        'prefix' => trim($prefix),
        'year'  => $year,
        'month' => $month,
        'day'   => $day
      );

  }
}

  return $dates;
}

// test block to run locally based on sample text
/*
//$text = 'Today 1917-03-23. He died Monday 9th January 1917, but the death wasn\'t reported until 24th January 1917. Period Jan 1917 - Mar 1917';
$text = 'THE FIRST BATTLE OF YPRES, OCTOBER-NOVEMBER 1914 The Battle of Gheluvelt 29-31 October 1914. The 2nd Battalion, Scots Guards preparing for a reconnaissance towards Gheluvelt, 20 October 1914.';
$found=find_date($text);
if (!empty($_GET['debug'])) { echo 'Final output:<pre>';print_r($found); }

*/
?>
