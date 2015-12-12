<?php
namespace MfPackage\Controllers;

$app->get("/info", function() use ($app) {
    $app->log->debug('geho');
    $app->log->info('geho');
    $app->log->warning('geho');
    phpinfo();
})->name("info");


$app->get("/login",function() use ($app) {
    $error = $app->session->get('error');
    // sample
    $app->render('login/login_form.html.twig',['error'=>$error]);
})->name("login");

$app->post("/login/do",'checkCSRF', function() use ($app) {
    // sample code
    $app->session->set('member_id','sample');
    $app->redirect($app->urlFor('menu'));
});

$app->get("/logout", function() use ($app) {
    // セッション破棄
    $app->session->flush();
    $app->redirect($app->urlFor('loginform'));
})->name("logout");