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

require_once('abstract_class.php');
include_once 'commonFuncs.php';

// Define child class Layer. 
class Layer extends Parameter {
// Define the default values of optional parameters in Layer object.    
static $defaults = array (
  "refreshInterval" => 300,
    "refreshDistance" => 100, 
    "fullRefresh" => TRUE,
    "showMessage" => NULL, 
    "biwStyle" => "classic",
    "deletedHotspots" => array(),
    "actions" => array()
  );
}

// Put retrieved layer level parameters into an associative array. 
// 
// Arguments:
//   db ; The database handler. 
//   layerName, string ; The name of the layer in the getPOI request.
// Return: 
//   array ; An associative array which contains parameters defined on layer
//   level.
function getLayerDetails($db, $layerName){
  // Define an empty $layer array.  
  $layer = array();
  // A new table called 'Layer' is created to store general layer level
  // parameters. 
  // 'layer' is the name of this layer. 
  // The SQL statement returns layer which has the same name as the
  // $layerName passed in getPOI request. 
  $sql = $db->prepare( '
            SELECT layer, 
                   refreshInterval, 
                   refreshDistance, 
                   fullRefresh,
                   showMessage, 
                   biwStyle
            FROM Layer
            WHERE layer = :layerName ');
 
  // Binds the named parameter marker ':layerName' to the specified parameter
  // value $layerName                
  $sql->bindParam(':layerName', $layerName, PDO::PARAM_STR);
  // Use PDO::execute() to execute the prepared statement $sql. 
  $sql->execute();
  // Retrieve layer parameters
  $layerValue = $sql->fetch(PDO::FETCH_ASSOC);
  // If $layerName is not found in the database, throw an exception. 
  try{
    if(!$layerValue)
      throw new Exception('layer:' . $layerName . 'is not found in the database.');
    else {
      $layerDetails = new Layer();
      $layerDetails->add('layer' , $layerValue['layer']);
      $layerDetails->add('refreshInterval', changetoInt($layerValue['refreshInterval']));
      $layerDetails->add('refreshDistance', changetoInt($layerValue['refreshDistance']));
      $layerDetails->add('fullRefresh', changetoBool($layerValue['fullRefresh']));
      $layerDetails->add('showMessage', $layerValue['showMessage']);
      $layerDetails->add('biwStyle',$layerValue['biwStyle']);
      // Filter out optional default values
      $layer = $layerDetails-> getFiltered();
    }
  return $layer;
  } 
  catch(Exception $e){
    echo 'Message: ' . $e->getMessage();
  }
}//getlayerDetails

?>
