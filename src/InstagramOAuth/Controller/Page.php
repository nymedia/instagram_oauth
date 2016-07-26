<?php

namespace InstagramOAuth\Controller;

use Silex\Application;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class Page
 * @package InstagramOAuth\Controller
 */
class Page
{

    public function privacy(Request $request, Application $app)
    {
        return file_get_contents(__DIR__ . '/privacy.html');
    }
}
