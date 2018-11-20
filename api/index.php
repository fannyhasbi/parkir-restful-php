<?php
require_once __DIR__."/flight/Flight.php";
require_once __DIR__."/api.php";

$api = new Api();

Flight::route("/", function(){
  echo "Ini adalah API sebagai endpoint.";
});

Flight::route("POST /login", [$api, 'login']);

Flight::route("GET /data-parkir", [$api, 'data_parkir']);

// 404 redirected to index
Flight::route("GET|POST *", function(){
  Flight::json([
    "status"  => 404,
    "message" => "Not Found",
    "data" => null
  ]);
});

Flight::start();