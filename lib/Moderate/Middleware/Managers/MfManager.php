<?php
namespace MfMiddleware\Managers;

use MfMiddleware\MfBaseMiddleware;

/**
 *  Managerファイルの自動読み込み
 */
class MfManager extends MfBaseMiddleware
{
    public function __construct($loader,$targetNameSpace)
    {
        parent::__construct($loader, $targetNameSpace);
    }

    public function call()
    {
        // Managerの一覧を取得
        $managers = $this->getTargetFiles('Manager.php');
        $app = $this->app;
        // ManagerのインスタンスSlimのコンテナにDIする。$app->hogehoge->で使えるようになる
        foreach($managers as $manager) {
            $name = $this->targetNameSpace . '\\' . $manager; 
            if ($name::IS_INSTANCE) {
                $this->app->container->singleton($name::MANAGER_NAME, function() use ($app,$name)
                {
                    return new $name($app);
                });
            }
        }
        $this->next->call();
        
        // 後処理部分(このMiddleでは不要)
    }

    
    protected function getDefaultTargetPath()
    {
        $baseDir = dirname(dirname(dirname(dirname(dirname(__FILE__)))));
        return $baseDir .'/app/';
    }
}