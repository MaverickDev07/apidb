<?php

/* PUBLIC */
$app->post('/security/create-token', function() use($app){
  $app->response->headers->set('Content-type', 'application/json');
  $app->response->headers->set('Access-Control-Allow-Origin', '*');
  try {
    $objDatos = json_decode(file_get_contents("php://input"));
    $obj = new SecurityModel();
    $app->response->status(200);
    $app->response->body(json_encode($obj->createToken($objDatos)));
  }catch(PDOException $e) {
    $app->response->status(500);
    $app->response->body(json_encode([
      'result' => [],
      'status' => false,
      'message' => $e->getMessage(),
      'error' => '500',
    ]));
  }
});

/* PUBLIC */
$app->post('/security/signin', function() use($app){
  $app->response->headers->set('Content-type', 'application/json');
  $app->response->headers->set('Access-Control-Allow-Origin', '*');
  try {
    $objDatos = json_decode(file_get_contents("php://input"));
    $obj = new SecurityModel();
    $app->response->status(200);
    $app->response->body(json_encode($obj->verifyUsuario($objDatos)));
  }catch(PDOException $e) {
    $app->response->status(500);
    $app->response->body(json_encode([
      'result' => [],
      'status' => false,
      'message' => $e->getMessage(),
      'error' => '500',
    ]));
  }
});

?>
