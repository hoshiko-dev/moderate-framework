<?php
namespace MfMiddleware;

use Slim\Middleware;

/**
 *  ModerateFWのMiddleware基底クラス
 */
abstract class MfBaseMiddleware extends Middleware
{
    // composerのloadr
    protected $loader;
    // 
    protected $targetNameSpace;
    
    public function __construct($loader,$targetNameSpace)
    {
        $this->loader = $loader;
        $this->targetNameSpace = $targetNameSpace;
    }
    
    
    protected function getManagerPath()
    {
        if (!empty($this->loader)) {
            $paths = $this->loader->getPrefixesPsr4();
             if (!empty($paths) && !empty($paths[$this->targetNameSpace . '\\'])) {
                 return $paths[$this->targetNameSpace . '\\'][0];
            }
        }
        return null;
    }
    
    /**
     * Controllerファイルの取得
     * 
     * @return Array
     */
    protected function getTargetFiles($filePartsName) 
    {
        $path = $this->getManagerPath();
        if (empty($path)) {
            $path = $this->getDefaultTargetPath();
        }

        $files = scandir($path);
        $files = array_filter($files, function ($file) {
            // 除去
            return !in_array($file, array('.', '..'));
        });
        $targets = [];
        foreach ($files as $file) {
            if (strpos($file, $filePartsName) !== false) {
                $targets[] = rtrim($file,'.php');
            }
        }
        return $targets;
    }
    
    protected function getDefaultTargetPath()
    {
        return null;
    }
}