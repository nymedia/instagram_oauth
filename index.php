<?php

$config = file_get_contents(dirname(__FILE__) . '/config.json');
$config = json_decode($config, TRUE);

if (isset($_GET['code'])) {
  $base_path = 'https://api.instagram.com/oauth/access_token/';

  $code = $_GET['code'];
  $site = isset($_GET['state']) ? $_GET['state'] : FALSE;
  

  $query = http_build_query(array(
    'client_id' => $config['client_id'],
    'client_secret' => $config['client_secret'],
    'grant_type' => 'authorization_code',
    'redirect_uri' => $config['redirect_url'],
    'code' => $code,
  ));

  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $base_path);
  curl_setopt($ch, CURLOPT_POSTFIELDS, $query);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
  $result = curl_exec($ch);
  curl_close($ch);

  $result = json_decode($result, TRUE);

  if (!isset($result['access_token'])) {
    echo 'Something went wrong...';
    echo '<pre>' . print_r($result, TRUE) . '</pre>';
  }
  else {
    header('Location: ' . $site . '?access_token=' . $result['access_token']);
  }
}
elseif (isset($_GET['error'])) {
  echo $_GET['error_description'];
}
else {
  $base_path = 'https://api.instagram.com/oauth/authorize/';

  $query = http_build_query(array(
    'client_id' => $config['client_id'],
    'response_type' => 'code',
    'redirect_uri' => $config['redirect_url'],
    'state' => $_GET['site']
  ));

  $instagram_link = sprintf('%s?%s', $base_path, $query);

  header('Location: ' . $instagram_link);
}
