<?php
if(file_exists(__DIR__ . "/../config/config_dev.php")){include __DIR__ . '/../config/config_dev.php';}else{$configDev=[];}

$config = [
    // Twig等で使いたいurl
    'url' => 'http://hogehoge',
    // PATH設定がある場合
    'path' => '/',

    // 言語設定
    'lang' => 'ja',
    
    // DB設定(Idiorm)
    'db_host' => 'mysql:host=localhost;',
    'db_name' => 'dbname=XXXX',
    'db_user' => '',
    'db_password' => '',
    'db_options' => '',
    'db_error_mode' => 'PDO::ERRMODE_WARNING',
    'db_option' => null,    // Array

    'templates.path' => __DIR__ . '/../templates',
    
    // NOTE: Twig/Smartyを切り替える場合はコメントアウトで対応
    // Twig設定
    'view' => new MfBase\MfViewTwig(
                [
                    'cache' => __DIR__ . '/../tmp/views/',
                    'debug' => true,
                ]),
//    // Smarty設定
//    'view' => new MfBase\MfViewSmarty([
//                'smartyDirectory' => __DIR__ . '/../vendor/smarty/smarty/libs',
//                'smartyCompileDirectory' => __DIR__ . '/../tmp/smarty/templates_c/',
//                'smartyCacheDirectory' => __DIR__ . '/../tmp/smarty/cache/',
//                'smartyTemplatesDirectory' => __DIR__ . '/../templates',
//            ]),
    // SlimのDebugモード
    'debug'              => false,
    // Slimのログ設定
    'log.writer' =>  new \Slim\Extras\Log\DateTimeFileWriter(
        [
        'path' => __DIR__ . '/../logs/',
        'name_format' => 'Ymd',
        'message_format' => '[%date%] %label%: %message%'
        ]
    ),
    'log.level'          => \Slim\Log::WARN,
    'log.enabled'        => false,
    
    // セッション設定
    'is_session' => true,
    'sessions.driver' => 'file', // or database
    'sessions.files' => __DIR__ . '/../tmp/sessions', // require mkdir
    'sessions.lifetime' => 60,  // minute
    'sessions.expire_on_close' => false, // trueにするとブラウザを閉じた場合にセッションクローズ

    // Cookie設定
    'cookies.encrypt'    => true,    //cookie
    'cookies.lifetime' => 3600,
    'cookies.path' => '/',
    'cookies.domain' => null,
    'cookies.secure' => true,
    
    // PATHの制御(環境に合わせて書き換え必須)
    'path_login' => 'login',
    'path_mainmenu' => 'menu',
];

// config_dev.phpがあればそっちが優先
$config = array_merge($config,$configDev);
