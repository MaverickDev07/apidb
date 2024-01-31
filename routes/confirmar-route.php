<?php

/* PUBLIC */
$app->get('/confirmar/estudiante/:ci/:gestion', function ($ci, $gestion) use ($app) {
  $app->response->headers->set('Content-type', 'application/json');
  $app->response->headers->set('Access-Control-Allow-Origin', '*');
  try {
    $obj = new ConfirmarModel();
    $app->response->status(200);
    $app->response->body(json_encode($obj->getEstudiante($ci, $gestion)));
  } catch (PDOException $e) {
    $app->response->status(500);
    $app->response->body(
      json_encode([
        'result' => [],
        'status' => false,
        'message' => $e->getMessage(),
        'error' => '500',
      ])
    );
  }
});
/* PUBLIC */
$app->post('/confirmar/create/:gestion', function($gestion) use($app){
  $app->response->headers->set('Content-type', 'application/json');
  $app->response->headers->set('Access-Control-Allow-Origin', '*');
  try {
    $objDatos = json_decode(file_get_contents("php://input"));
    $obj = new ConfirmarModel();
    $app->response->status(200);
    $app->response->body(json_encode($obj->create($objDatos, $gestion)));
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