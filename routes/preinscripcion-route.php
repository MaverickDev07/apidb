<?php
/* PUBLIC */
$app->get('/preinscripcion/estudiante/preinscrito/:ci', function ($ci) use ($app) {
  $app->response->headers->set('Content-type', 'application/json');
  $app->response->headers->set('Access-Control-Allow-Origin', '*');
  try {
    $obj = new PreinscripcionModel();
    $app->response->status(200);
    $app->response->body(json_encode($obj->getEstudiantePreInscrito($ci)));
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
$app->get('/preinscripcion/estudiante/:ci/:gestion', function ($ci, $gestion) use ($app) {
  $app->response->headers->set('Content-type', 'application/json');
  $app->response->headers->set('Access-Control-Allow-Origin', '*');
  try {
    $obj = new PreinscripcionModel();
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
$app->post('/preinscripcion/create', function() use($app){
  $app->response->headers->set('Content-type', 'application/json');
  $app->response->headers->set('Access-Control-Allow-Origin', '*');
  try {
    $objDatos = json_decode(file_get_contents("php://input"));
    $obj = new PreinscripcionModel();
    $app->response->status(200);
    $app->response->body(json_encode($obj->create($objDatos)));
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
$app->get('/preinscripcion/:id_pre', function($id_pre) use($app){
  $app->response->headers->set('Content-type','application/pdf');
  $app->response->headers->set('Content-Disposition','attachment;filename="preinscripcion.pdf"');
  $app->response->headers->set('Access-Control-Allow-Origin','*');
  try {
    $obj = new PreinscripcionModel();
    $app->response->status(200);
    $app->response->body($obj->generatePreinscripcion($id_pre));
  }catch(PDOException $e) {
    $app->response->status(500);
    $app->response->body('No se encontro nada');
  }
});

?>