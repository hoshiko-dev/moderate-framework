<?php
namespace MfMiddleware\Controllers;

use MfMiddleware\MfBaseMiddleware;

/**
 * Controllerの自動読み込み用Middleware
 */
class MfController extends MfBaseMiddleware
{
    
    public function __construct($loader,$targetNameSpace)
    {
        parent::__construct($loader, $targetNameSpace);
    }

    public function call()
    {
        // Controllerの一覧を取得
        $controllers = $this->getTargetFiles('Controller.php');
        
        // require_onceで使うおまじない
        $app = $this->app;
        // Controllerのrequire_onceを実施
        foreach($controllers as $controller) {
            require_once $this->app->getLoadPath($this->targetNameSpace) . '/' . $controller . '.php';
        }
        $this->next->call();
        
        // 後処理部分(このMiddleでは不要)
    }

    protected function getDefaultTargetPath()
    {
        $baseDir = dirname(dirname(dirname(dirname(dirname(__FILE__)))));
        return $baseDir .'/app/Controllers/';
    }
}
