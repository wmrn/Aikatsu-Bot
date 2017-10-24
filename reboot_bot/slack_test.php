<?php

function send_to_slack($message) {
  $webhook_url = base64_decode("*******************");
  $options = array(
    'http' => array(
      'method' => 'POST',
      'header' => 'Content-Type: application/json',
      'content' => json_encode($message),
    )
  );
  $response = file_get_contents($webhook_url, false, stream_context_create($options));
  return $response === 'ok';
}

$message = array(
  'username' => 'Bot',
  'text' => 'reboot done!',
);

send_to_slack($message);
?>