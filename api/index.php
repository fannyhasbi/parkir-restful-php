<?php
require_once "flight/Flight.php";

Flight::route("/", function(){
  echo "Yoyoy";
});

Flight::start();