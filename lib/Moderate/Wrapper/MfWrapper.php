<?php

namespace MfBase;
use MfMiddleware\Sessions\SessionManager;
use MfMiddleware\Sessions\Session;
use MfMiddleware\Managers\MfManager;
use MfMiddleware\Controllers\MfController;

/**
 * Description of MfWrapper
 *
 * @author Kota
 */
class MfWrapper extends \Slim\Slim {
    //put your code here
    
    private $loader;
    private $namespaces =[
        'manager' => 'MfPackage',
        'controller' => 'MfController',
        'middleware' => 'MfMiddleware',
    ];
    
    /**
     * コンストラクタ
     * 
     * @param type $config
     * @param type $loader
     */
    public function __construct($config =null ,$loader = null)
    {
        $this->loader = $loader;
        parent::__construct($config);
    }
    
    /**
     * Slimインスタンスの生成およびModerateFwのセットアップ
     * 
     * @param type $config
     * @param type $loader
     * @return \Base\SlimWrapper
     */
    public static function create($config = null,$loader = null) 
    {
        // Slim frameworkのインスタンスを生成
        $app = new MfWrapper($config,$loader);
        
        // ModerateFwのApp側のファイルのrequire_once(autoload)をセット
        $app->setAutoload();
        
        // Middlewareのセット
        $app->add(new MfManager($app->loader,$app->namespaces['manager']));
        $app->add(new MfController($app->loader,$app->namespaces['controller']));

        // セッション設定
        $app->setSession($config);
        
        // DB設定を実施
        $app->setDatabase();

        return $app;
    }
    
    /**
     * DBの設定を行う
     */
    protected function setDatabase() {
        // Idiorm(ORM)
        \ORM::configure($this->config('db_host'). $this->config('db_name'));
        \ORM::configure('username', $this->config('db_user'));
        \ORM::configure('password', $this->config('db_password'));
        if (!empty($this->config('db_options'))) {
            \ORM::configure('driver_options', $this->config('db_options'));
        }
        \ORM::configure('error_mode', $this->config('db_error_mode'));
        \ORM::configure('logging', true);
    }

    /**
     * セッション機能を設定する
     * 
     * @param Array $config
     */
    protected function setSession(Array $config)
    {
        if (!empty($config['is_session'])) {
            // illusion/session
            $manager = new SessionManager($this);
            $manager->setFilesystem(new \Illuminate\Filesystem\Filesystem());
            $session = new Session($manager);
            $this->add($session);
        }
        // SlimのConfig=AppコンフィグをTwig側で参照可能にする(Twig/Smarty対応)
        $this->view->slimConfig = $config;
        // SessionデータをTwig/Smarty側でも使用可能にする
        $this->view->app = $this;
    }
    
    /**
     * アプリケーションファイルのautoload設定を実施
     */
    protected function setAutoload()
    {
        $namespaces = $this->namespaces;
        // PHP 5.3.0以降なら無名関数を使えます
        spl_autoload_register(function ($class) use($namespaces) {
            $realname = $class;
            $target = '';
            if (strpos($class,$namespaces['manager']) !== false) {
                list(,$realname) = explode('\\',$class);
                $target = $namespaces['manager'];
            } else if (strpos($class,'MfBaseMiddleware') !== false) {
                // 基底クラス
                $realname = 'MfBaseMiddleware';
                $target = $namespaces['middleware'];
            } else {
                return;
            }
            $targetPath = $this->getLoadPath($target);
            if (empty($targetPath)) {
                throw new \Exception('autoload path not found. target='.$target);
            }
            if (file_exists($target . '/' . $realname . '.php')) {
                require_once $target . '/' . $realname . '.php';
            } else {
                throw new \Exception('autolaod: File not found. :' .$target . '/' . $realname . '.php');
            }
        });
    }

    /**
     * composerのPrs4ファイルからアプリの指定パスを取得する
     * 
     * @param string $target
     * @return string
     */
    public function getLoadPath($target)
    {
        if (!empty($this->loader)) {
            $paths = $this->loader->getPrefixesPsr4();
            if (!empty($paths) && !empty($paths[$target . '\\'])) {
                return $paths[$target. '\\'][0];
            }
        }
        return null;
    }
}
