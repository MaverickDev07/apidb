<?php
require '../vendor/autoload.php';

use \Firebase\JWT\JWT;

$token_payload = [
  'active' => 'habilitado',
];

// This is your client secret
$key = '__test_secret__';

// This is your id token
$jwt = JWT::encode($token_payload, base64_decode(strtr($key, '-_', '+/')), 'HS256');

print "JWT:\n";
print_r($jwt);

$decoded = JWT::decode($jwt, base64_decode(strtr($key, '-_', '+/')), ['HS256']);

print "\n\n";
print "Decoded:\n";
print_r($decoded->active);

?>