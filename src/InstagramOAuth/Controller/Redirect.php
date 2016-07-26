<?php

namespace InstagramOAuth\Controller;

use Silex\Application;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use League\OAuth2\Client\Provider\Instagram;

/**
 * Class Redirect
 * @package InstagramOAuth\Controller
 */
class Redirect
{

  /**
   * Authorization controller.
   *
   * @param \Symfony\Component\HttpFoundation\Request $request
   * @param \Silex\Application $app
   * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
   */
    public function auth(Request $request, Application $app)
    {

        $provider = new Instagram([
        'clientId'          => $app['settings']['client_id'],
        'clientSecret'      => $app['settings']['client_secret'],
        'redirectUri'       => $app['settings']['redirect_uri'],
        ]);
        $code = $request->get('code');
        $redirect = $request->get('state') ?: $request->get('site');

        // Check against whitelist.
        if (!empty($app['settings']['whitelist']) && !$this->matchPath($redirect, $app['settings']['whitelist'])) {
            $app->abort(Response::HTTP_FORBIDDEN, 'Forbidden');
        }

        if (!$code) {
            // If we don't have an authorization code then get one
            $authUrl = $provider->getAuthorizationUrl([
            'state' => $redirect,
            ]);
            return $app->redirect($authUrl);
        } elseif ($code && $redirect) {
            // If we have code - get access token.
            $token = $provider->getAccessToken('authorization_code', [
            'code' => $code
            ]);
            return $app->redirect($redirect . '?access_token=' . $token->getToken());
        }
        return Response('Unknown operation', Response::HTTP_INTERNAL_SERVER_ERROR);
    }

  /**
   * Whitelist check.
   *
   * Shamelessly stolen from drupal_match_path().
   */
    protected function matchPath($path, $patterns)
    {
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
}
