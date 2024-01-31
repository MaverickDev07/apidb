<?php

/* PRIVATE */
$app->get('/selector/all', function () use ($app) {
  $app->response->headers->set('Content-type', 'application/json');
  $app->response->headers->set('Access-Control-Allow-Origin', '*');
  try {
    $obj = new SelectorModel();
    $app->response->status(200);
    $app->response->body(json_encode($obj->getSelectorAll()));
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

?>
