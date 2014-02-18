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

/*** Include some external files ***/

// Include database credentials. Please customize these fields with your own
// database configuration.  
require_once('config.inc.php');
// Include POI.php
include 'POI.php'; 
// Include Layer.php
include 'Layer.php';

/*** Main entry point ***/

/* Put parameters from GetPOI request into an associative array named $value */
// Put needed parameter names from GetPOI request in an array called $keys. 
$keys = array('layerName', 'lat', 'lon', 'radius');
// Initialize an empty associative array.
$requestParams = array(); 
// Call funtion getRequestParams()  
$requestParams = getRequestParams($keys);

/* Connect to MySQL server. We use PDO which is a PHP extension to formalise database connection.
   For more information regarding PDO, please see http://php.net/manual/en/book.pdo.php. 
 */  
// Connect to predefined MySQl database.  
$db = connectDb(); 
  
/* Construct the response into an associative array.*/
  
// Create an empty array named response.
$response = array();
  
// Assign cooresponding values to mandatory JSON response keys.
$response = getLayerDetails($db, $requestParams['layerName']);
  
// Use Gethotspots() function to retrieve POIs with in the search range.  
$response['hotspots'] = getHotspots($db, $requestParams);

// if there is no POI found, return a custom error message.
if (!$response['hotspots'] ) {
  $response['errorCode'] = 20;
   $response['errorString'] = 'No POI found. Please adjust the range.';
}//if
else {
  $response['errorCode'] = 0;
  $response['errorString'] = 'ok';
}//else
   
  /* All data is in $response, print it into JSON format.*/
  
  // Put the JSON representation of $response into $jsonresponse.
  $jsonresponse = json_encode($response);
  
  // Declare the correct content type in HTTP response header.
  header('Content-type: application/json; charset=utf-8');
  
  // Print out Json response.
  echo $jsonresponse;

?>
