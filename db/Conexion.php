<?php

class Conexion {
    private $password = "r]kxL,!qI6c1";
    private $username = "lpportec_web";
    private $database = "lpportec_db";
    private $hostname = "www.lpporte.com";
    protected $pdo;

    public function __construct() {
        $this->ConnectPDO();
    }

    private function ConnectPDO () {
        try{
            $this->pdo = new PDO("mysql:host={$this->hostname};dbname={$this->database};charset=utf8", $this->username, $this->password);
            $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        }catch(PDOException $err) {
            throw new Exception("Error en la conexion:" . $err->getMessage());
        }
    }

    public function query (String $query, array $params = [] ) {
        try {
            $stmt = $this->pdo->prepare($query);
            return $stmt->execute($params);
        } catch (PDOException $err) {
            throw new Exception($err->getMessage());
        }
    }

    public function fetch (String $query, array $params = []) {
        try {
            $stmt = $this->pdo->prepare($query);
            $stmt -> execute($params);
            $data = $stmt->fetch(PDO::FETCH_ASSOC);
            return $data;
        } catch (PDOException $err) {
            throw new Exception("Error al obtener datos: " . $err->getMessage());
        }
    }

    public function fetchAll (String $query, array $params = []) {
        try {
            $stmt = $this->pdo->prepare($query);
            $stmt -> execute($params);
            $data = $stmt->fetchAll(PDO::FETCH_ASSOC);

            return $data;
            
        } catch (PDOException $err) {
            throw new Exception("Error al obtener datos: " . $err->getMessage());
        }
    }
}