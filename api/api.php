<?php

class Api {
  public $koneksi;

  public function __construct(){
    $this->koneksi = mysqli_connect("localhost", "root", "", "parking_system") OR die(mysql_error());
    
    // Mengatasi isu CORS
    header("Access-Control-Allow-Origin: *");
    header("Access-Control-Allow-Headers: x-requested-with, x-requested-by");
    header("Access-Control-Expose-Headers: Access-Control-Allow-Origin");
    header("Access-Control-Allow-Methods: *");
    header("Content-Type: application/json");
    header( "Access-Control-Allow-Credentials: true");
    // header( "Access-Control-Max-Age: 604800");
    header( "Access-Control-Request-Headers: x-requested-with");
  }

  public function __destruct(){
    mysqli_close($this->koneksi);
  }

  private function response($data = null, $status = 200, $message = "OK"){
    Flight::json([
      "status" => (int) $status,
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

  public function realtime(){
    $query = "
      SELECT s.id, s.waktu, v.merk, v.tipe, o.nama FROM scan s
      INNER JOIN vehicle v
        ON s.id_vehicle = v.id
      INNER JOIN owner o
        ON v.id_owner = o.id
    ";
    
    $result = mysqli_query($this->koneksi, $query) or die(mysqli_error($this->koneksi));
    $data = array();

    while($r = mysqli_fetch_assoc($result)){
      $data[] = [
        "id"    => (int) $r['id'],
        "waktu" => $r['waktu'],
        "merk"  => $r['merk'],
        "tipe"  => $r['tipe'],
        "nama"  => $r['nama']
      ];
    }
    
    $this->response($data);
  }

  public function data_parkir(){
    $input = Flight::request()->query;
    if(! isset($input->id_officer))
      $this->response400();

    // check officer data
    $query = "SELECT id FROM officer WHERE id = $input->id_officer";
    $result= mysqli_query($this->koneksi, $query);
    
    if($result->num_rows == 0){
      $this->response(null, 401, "Unauthorized");
      die();
    }
    else {
      // get id_place from officer
      $query = "SELECT id_place FROM officer WHERE id = $input->id_officer";
      $result= mysqli_query($this->koneksi, $query);
      $id_place = mysqli_fetch_assoc($result)['id_place'];

      // get data per parkir
      $query = "
        SELECT
          CONCAT(YEAR(s.waktu), '-', MONTH(s.waktu)) AS waktu,
          COUNT(*) AS jumlah
        FROM scan s
        INNER JOIN officer o
          ON s.id_officer = o.id
        WHERE o.id_place = $id_place
        GROUP BY YEAR(s.waktu), MONTH(s.waktu)
        ORDER BY waktu DESC
      ";

      $result = mysqli_query($this->koneksi, $query);

      $show = array();

      while($r = mysqli_fetch_assoc($result)){
        $show[] = [
          "waktu" => date_definer($r['waktu']),
          "jumlah"=> (int) $r['jumlah']
        ];
      }

      $this->response($show);
    }
  }

  public function scan(){
    $input = Flight::request()->data;

    if(!(isset($input->kode_qr) && isset($input->id_officer)))
      $this->response400();

    $input->kode_qr = $this->purify($input->kode_qr);
    $input->id_officer = (int) $this->purify($input->id_officer);

    // check officer data
    $query = "SELECT id FROM officer WHERE id = $input->id_officer";
    $result= mysqli_query($this->koneksi, $query);

    if($result->num_rows == 0){
      $this->response(null, 403, "Permission denied");
      die();
    }
    else {
      // check qr_code
      $query = "SELECT kode_qr FROM vehicle WHERE kode_qr = '$input->kode_qr'";
      $result= mysqli_query($this->koneksi, $query);

      if($result->num_rows == 0){
        $this->response(null, 404, "QR Code doesn't exists");
        die();
      }
      else {
        $query = "SELECT id FROM vehicle WHERE kode_qr = '$input->kode_qr'";
        $result= mysqli_query($this->koneksi, $query);
        $result= mysqli_fetch_assoc($result);

        $query = "INSERT INTO scan (id_vehicle, id_officer) VALUES (". $result['id'] .", ". $input->id_officer .")";
        mysqli_query($this->koneksi, $query) or $this->response(null, 500, "Internal Server Error");

        $this->response();
      }
    }
  }

}
