<?php

class Api {
  public $koneksi;

  public function __construct(){
    $this->koneksi = mysqli_connect("localhost", "root", "", "parking_system") OR die(mysql_error());
    
    // Mengatasi isu CORS
    header("Access-Control-Allow-Origin: *");
    header("Access-Control-Allow-Headers: x-requested-with, x-requested-by");
    header("Access-Control-Allow-Methods: POST, GET");
    header("Content-Type: application/json");
    // header( "Access-Control-Allow-Credentials: true");
    // header( "Access-Control-Max-Age: 604800");
    // header( "Access-Control-Request-Headers: x-requested-with");
  }

  public function __destruct(){
    mysqli_close($this->koneksi);
  }

  private function response($data = null, $status = 200, $message = "OK"){
    Flight::json([
      "status" => $status,
      "message"=> $message,
      "data"   => $data
    ]);
  }

  private function response400(){
    Flight::json([
      "status" => 400,
      "message"=> "Bad Request",
      "data"   => null
    ]);

    die();
  }

  private function purify($input){
    return mysqli_real_escape_string($this->koneksi, $input);
  }

  public function login(){
    $input = Flight::request()->data;

    if(!(isset($input->username) && isset($input->password) && isset($input->login)))
      $this->response400();

    $input->username = $this->purify($input->username);

    $query = "
      SELECT o.*, p.nama AS nama_parkiran, p.jurusan, p.fakultas FROM officer o
      LEFT JOIN place p
        ON o.id_place = p.id
      WHERE o.username = '$input->username'
    ";

    $check = mysqli_query($this->koneksi, $query);

    if($check->num_rows == 0){
      $this->response(null, 401, "Unauthorized");
      die();
    }
    else {
      $officer = mysqli_fetch_assoc($check);

      if(!password_verify($input->password, $officer['password'])){
        $this->response(null, 401, "Unauthorized");
        die();
      }
      else {
        $this->response([
          "id"       => (int) $officer['id'],
          "nama"     => $officer['nama'],
          "username" => $officer['username'],
          "parkiran" => $officer['nama_parkiran'],
          "jurusan"  => $officer['jurusan'],
          "fakultas" => $officer['fakultas']
        ]);
      }
    }

  }
}
