<?php

/**
 * RoutingのMiddlewareを記載する
 * function形式
 */

/**
 * セッション判定
 * 
 * @global type $app
 */
function checkLogin()
{
    global $app;
    
    // sample code キーは書き換えること
    if (!$app->session->get('member_id')) {
        $app->redirect($app->urlFor($app->config('path_login')));
    }
}

/**
 * CSRF
 * 
 * @global type $app
 */
function checkCSRF()
{
    global $app;
    if (!$app->request->isPost()) {
        $app->redirect($app->urlFor($app->config('path_login')));
    }
    $token = $app->request->params('_csrf_token');
    if (empty($token)) {
        $app->redirect($app->urlFor($app->config('path_login')));
    }
    
    if ($token !== $app->session->get('_token')) {
        $app->redirect($app->urlFor($app->config('path_login')));
    }
}
