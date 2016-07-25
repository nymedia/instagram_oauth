<?php

$script_path = dirname(__FILE__) . '/..';

$config = file_get_contents($script_path . '/config.json');
$config = json_decode($config, TRUE);

if (file_exists($script_path . '/whitelist.json')) {
  $whitelist = file_get_contents($script_path . '/whitelist.json');
  $whitelist = json_decode($whitelist, TRUE);
}
else {
  $whitelist = array();
}

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
  if (!empty($whitelist) && !match_that_path($_GET['site'], $whitelist)) {
    header("HTTP/1.0 403 Forbidden");
    echo 'Forbidden';
    exit;
  }


  $base_path = 'https://api.instagram.com/oauth/authorize/';

  $query = http_build_query(array(
    'client_id' => $config['client_id'],
    'response_type' => 'code',
    'redirect_uri' => $config['redirect_url'],
    'state' => $_GET['site']
  ));

  header('Location: ' . sprintf('%s?%s', $base_path, $query));
}




/**
 * Shamelessly stolen from drupal_match_path().
 */
function match_that_path($path, $patterns) {
  $patterns = implode("\n", $patterns);

  // Convert path settings to a regular expression.
  // Therefore replace newlines with a logical or, /* with asterisks and the <front> with the frontpage.
  $to_replace = array(
    '/(\r\n?|\n)/', // newlines
    '/\\\\\*/', // asterisks
    '/(^|\|)\\\\<front\\\\>($|\|)/' // <front>
  );
  $replacements = array(
    '|',
    '.*',
  );
  $patterns_quoted = preg_quote($patterns, '/');
  $regexps = '/^(' . preg_replace($to_replace, $replacements, $patterns_quoted) . ')$/';

  return (bool) preg_match($regexps, $path);
}
