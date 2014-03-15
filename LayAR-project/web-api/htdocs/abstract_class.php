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

// An abstract class which can be inherited by its child class. It is the parent class of Layer, POI and Action 
abstract class Parameter {
  // Defines the default values for optional parameters of the object.
  private $defaults;
  // Define the object itself.
  private $parameters;
  
  // Filter out optional parameters which have default values.  
  private function filter(&$myParameters, $myDefaults) {
    foreach($myDefaults as $key => $value)
      if(array_key_exists($key , $myParameters)){
        if(is_array($value) && !empty($value))
          self::filter($myParameters[$key], $value);
        if($myParameters[$key] == $value || empty($myParameters[$key]) && !is_bool($myParameters[$key]))
          unset($myParameters[$key]);
      }    
  }//filter
  
  // construction function
  function __construct() {
    $childDefaults = get_called_class();
    $this->defaults = $childDefaults::$defaults;
  }//__construct
  
  // Add a parameter to the object.
  function add($name, $value) {
    $this->parameters[$name] = $value;
  }//add
  
  // Get the object 
  function get() {
    return $this->parameters;
  }//get
  
  // Get clean object with non-default optional parameters. 
  function getFiltered() {
    $myParameters = $this->parameters;
    self::filter($myParameters, $this->defaults);
    return $myParameters;
  }//getFiltered

}//Parameter
?>
