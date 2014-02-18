<?php
// Created by Xuan Wang
// Layar Technical Support
// Email: xuan@layar.com
// Website: http://layar.com
// Copyright (c) 2011, Layar B.V.
// All rights reserved.

// Redistribution and use in source and binary forms, with or without
// modification, are permitted provided that the following conditions are met:
//    * Redistributions of source code must retain the above copyright
//      notice, this list of conditions and the following disclaimer.
//    * Redistributions in binary form must reproduce the above copyright
//      notice, this list of conditions and the following disclaimer in the
//      documentation and/or other materials provided with the distribution.
//    * Neither the name of the <organization> nor the
//      names of its contributors may be used to endorse or promote products
//      derived from this software without specific prior written permission.

// THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS"
// AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE
// IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE
// ARE DISCLAIMED. IN NO EVENT SHALL LAYAR B.V BE LIABLE FOR ANY DIRECT,
// INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES
// (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES;
// LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND
// ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT
// (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS
// SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.

// Include the abstract_class.php where the parent class 'Parameter' is defined.
require_once('abstract_class.php');
// Include common functions defined in commonFunc.php
include_once 'commonFuncs.php';

// Define child class POI. 
class POI extends Parameter {
  // Define the default values of optional parameters in POI object. 
  static $defaults = array (
    "imageURL" => NULL, 
    "doNotIndex" => FALSE, 
    "inFocus" => FALSE,
    "showSmallBiw" => TRUE, 
    "showBiwOnClick" => TRUE, 
    "biwStyle" => "classic", 
    "text" => array(
      "description" => NULL, 
      "footnote" => NULL
    ),  
    "anchor" => array("geolocation" => array ("alt" => NULL)),
    "icon" => array("url" => NULL, "type" => 0),
    "object" => array("reducedURL" => NULL),
    "transform" => array(
      "rotate" => array (
        "rel" => FALSE,
        "angle" => 0.0, 
        "axis" => array ("x" => 0.0 , "y" => 0.0, "z" => 1.0)
      ),
      "translate" => array ("x" => 0.0, "y" => 0.0, "z" => 0.0),
      "scale" => 1.0
    ),
    "actions" => array()
  );  
}//POI

// Construct anchor object based on poiType of each POI. 
// Arguments: 
//   rawPoi, array ; An associative array which contains a POI object. 
// 
// Returns:
//  array ; An array which contains anchor dictionary information for a Geo POI. 
function getAnchor($rawPoi) {
  $anchor = array();
  
  $anchor['geolocation']['lat'] = changetoFloat($rawPoi['lat']);
  $anchor['geolocation']['lon'] = changetoFloat($rawPoi['lon']);
  $anchor['geolocation']['alt'] = changetoInt($rawPoi['alt']);

  return $anchor;
}//getAnchor

// Construct text object information
//
// Arguments:
//  rawPoi, array ; An associative array which contains a POI object. 
//
// Returns:
//  array ; An array which contains text dictionary information. 
function getTextDetail($rawPoi) {
  $text = array();
  $text['title'] = $rawPoi['title'];
  $text['description'] = $rawPoi['description'];
  $text['footnote'] =  $rawPoi['footnote'];
  return $text;
}//getTtext  

// Put received POIs into an associative array. The returned values are
// assigned to $reponse['hotspots'].
//
// Arguments:
//   db ; The handler of the database.
//   value , array ; An array which contains all the needed parameters
//   retrieved from GetPOI request. 
//
// Returns:
//   array ; An array of received POIs.
//
function getHotspots( $db, $value ) {
  // Define an empty $hotspots array.
  $hotspots = array();
/* Create the SQL query to retrieve POIs within the 'radius' returned from
   GetPOI request. 
   The first 50 returned POIs are selected.
   The distance is caculated based on the Haversine formula.  Note: this
   way of calculation is not scalable for querying large database.
*/
  
  // Use PDO::prepare() to prepare SQL statement. This statement is used due to
  // security reasons and will help prevent general SQL injection attacks.
  // ':lat1', ':lat2', ':long' and ':radius' are named parameter markers for
  // which real values will be substituted when the statement is executed.
  // $sql is returned as a PDO statement object. 
  $sql = $db->prepare('
    SELECT POI.id,
           imageURL,
           doNotIndex,
           showSmallBiw,
           showBiwOnClick,
           POI.biwStyle,
           title,
           description,
           footnote, 
           lat,
           lon,
           alt,
           (((acos(sin((:lat1 * pi() / 180)) * sin((lat * pi() / 180)) +
                   cos((:lat2 * pi() / 180)) * cos((lat * pi() / 180)) * 
                   cos((:long  - lon) * pi() / 180))
             ) * 180 / pi()
            ) * 60 * 1.1515 * 1.609344 * 1000
           ) as distance
      FROM POI, Layer
     WHERE poiType = "geo" AND 
           POI.layerID = Layer.id AND 
           Layer.layer = :layerName 
    HAVING distance < :radius     
  ORDER BY distance ASC
     LIMIT 0, 50
  ');

  // PDOStatement::bindParam() binds the named parameter markers to the
  // specified parameter values. 
  $sql->bindParam( ':lat1', $value['lat'], PDO::PARAM_STR );
  $sql->bindParam( ':lat2', $value['lat'], PDO::PARAM_STR );
  $sql->bindParam( ':long', $value['lon'], PDO::PARAM_STR );
  $sql->bindParam( ':radius', $value['radius'], PDO::PARAM_INT );
  $sql->bindParam(':layerName', $value['layerName'], PDO::PARAM_STR);
  // Use PDO::execute() to execute the prepared statement $sql. 
  $sql->execute();
  // Iterator for the response array.
  $i = 0; 
  // Use fetchAll to return an array containing all of the remaining rows in
  // the result set.
  // Use PDO::FETCH_ASSOC to fetch $sql query results and return each row as an
  // array indexed by column name.
  $rawPois = $sql->fetchAll(PDO::FETCH_ASSOC);
 
  /* Process the $pois result */
  // if $rawPois array is not empty 
  if ($rawPois) {
    // Put each POI information into $hotspots array.
    foreach ( $rawPois as $rawPoi ) {
      $myPoiParameters = new POI();
      $myPoiParameters->add('id', $rawPoi['id']);
      $myPoiParameters->add('imageURL', $rawPoi['imageURL']);
      $myPoiParameters->add('doNotIndex', changetoBool($rawPoi['doNotIndex']));       
      $myPoiParameters->add('biwStyle' , $rawPoi['biwStyle']);
      $myPoiParameters->add('showSmallBiw', 
        changetoBool($rawPoi['showSmallBiw']));  
      $myPoiParameters->add('showBiwOnClick', 
        changetoBool($rawPoi['showBiwOnClick']));  
      // Get anchor object information
      $myPoiParameters->add('anchor', getAnchor($rawPoi));
      // Get text object information
      $myPoiParameters->add('text', getTextDetail($rawPoi));
       
      // Put the filtered poi parameters into the $hotspots array.
      $hotspots[$i] = $myPoiParameters->getFiltered();
      $i++;
    }//foreach
  }//if
  return $hotspots;
}//getHotspots
?>

