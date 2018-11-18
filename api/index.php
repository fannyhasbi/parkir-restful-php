<?php
require_once __DIR__."/flight/Flight.php";
require_once __DIR__."/api.php";

$api = new Api();

Flight::route("/", function(){
  echo "Ini adalah API sebagai endpoint.";
});

Flight::route("GET /login", [$api, 'login']);


// 404 redirected to index
Flight::route("GET *", function(){
  Flight::redirect('/');
});

Flight::start();