<?php

function checkCaptcha($recaptcha)
{
  /* Recatpcha v3 */
  $recaptcha_url = 'https://www.google.com/recaptcha/api/siteverify';
  $recaptcha_secret = '6LerKdEZAAAAAIvY5JvTqodTe2X6MzcUHyH6UjSj';
  $recaptcha_response = $recaptcha;

  // Make and decode POST request:
  $recaptcha = file_get_contents(
    $recaptcha_url .
      '?secret=' .
      $recaptcha_secret .
      '&response=' .
      $recaptcha_response
  );
  $recaptcha = json_decode($recaptcha);

  // Take action based on the score returned:
  if ($recaptcha->score >= 0.5) {
    // No eres un robot, continuamos con el envío del email
    return true;
  } else {
    // Eres un robot!
    return false;
  }
}
?>
