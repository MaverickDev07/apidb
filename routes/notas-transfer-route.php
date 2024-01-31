<?php

/* PUBLIC */
$app->get('/estudiante/:id_est/:gestion', function($id_est, $gestion) use($app){
  $app->response->headers->set('Content-type', 'application/json');
  $app->response->headers->set('Access-Control-Allow-Origin', '*');
  try {
    $obj = new NotasTransferModel();
    $app->response->status(200);
    $app->response->body(json_encode($obj->getEstudiante($id_est, $gestion)));
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
$app->get('/estudiante/buscar/:turno/:curso/:gestion', function($turno, $curso, $gestion) use($app){
  $app->response->headers->set('Content-type', 'application/json');
  $app->response->headers->set('Access-Control-Allow-Origin', '*');
  try {
    $obj = new NotasTransferModel();
    $app->response->status(200);
    $app->response->body(json_encode($obj->buscarEstudiante($turno, $curso, "", $gestion)));
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
$app->get('/estudiante/buscar/:turno/:curso/:nombre/:gestion', function($turno, $curso, $nombre, $gestion) use($app){
  $app->response->headers->set('Content-type', 'application/json');
  $app->response->headers->set('Access-Control-Allow-Origin', '*');
  try {
    $obj = new NotasTransferModel();
    $app->response->status(200);
    $app->response->body(json_encode($obj->buscarEstudiante($turno, $curso, $nombre, $gestion)));
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
$app->get('/estudiante/materias/:id_est/:curso/:turno/:trimestre/:gestion', function($id_est, $curso, $turno, $trimestre, $gestion) use($app){
  $app->response->headers->set('Content-type', 'application/json');
  $app->response->headers->set('Access-Control-Allow-Origin', '*');
  try {
    $obj = new NotasTransferModel();
    $app->response->status(200);
    $app->response->body(json_encode($obj->getEstudianteNotaTrimestre($id_est, $curso, $turno, $trimestre, $gestion)));
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
