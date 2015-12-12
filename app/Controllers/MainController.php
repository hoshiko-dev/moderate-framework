<?php
$app->get("/", 'checkLogin',function() use ($app) {
    $app->redirect($app->config('path').'/menu',[]);
});

$app->get("/menu", 'checkLogin',function() use ($app) {
    // sample
    $app->render('menu.html.twig',[]);
})->name('menu');
