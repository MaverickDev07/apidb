<?php

use Dotenv\Dotenv;

class Conexion
{
  private $env;

  public function __construct() {}
  public function getConexion()
  {
    $dotenv = Dotenv::createImmutable(__DIR__.'/..');
    $env = $dotenv->load();
    $conex = null;
    try {
      $usuario = $_ENV['API_DONBOSCO_USER'];
      $pwd = $_ENV['API_DONBOSCO_PASSWORD'];
      $host = $_ENV['API_DONBOSCO_HOST'];
      $db = $_ENV['API_DONBOSCO_DATABASE'];
      $conex = new PDO(
        "mysql:host=" . $host . ";dbname=" . $db . ";charset=utf8",
        $usuario,
        $pwd,
        [PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES 'utf8'"]
      );
      $conex->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    } catch (PDOException $e) {
      echo "Error: " . $e->getMessage();
    }
    return $conex;
  }

  public function getFluent()
  {
    return new FluentPDO($this->getConexion());
  }
}

?>
