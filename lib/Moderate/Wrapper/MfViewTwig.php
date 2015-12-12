<?php
/**
 * Slim - a micro PHP 5 framework
 *
 * @author      Josh Lockhart
 * @link        http://www.slimframework.com
 * @copyright   2011 Josh Lockhart
 *
 * MIT LICENSE
 *
 * Permission is hereby granted, free of charge, to any person obtaining
 * a copy of this software and associated documentation files (the
 * "Software"), to deal in the Software without restriction, including
 * without limitation the rights to use, copy, modify, merge, publish,
 * distribute, sublicense, and/or sell copies of the Software, and to
 * permit persons to whom the Software is furnished to do so, subject to
 * the following conditions:
 *
 * The above copyright notice and this permission notice shall be
 * included in all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND,
 * EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF
 * MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND
 * NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE
 * LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION
 * OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION
 * WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
 */
namespace MfBase;

/*
 * Slim\Extras拡張モジュール
 * メンテナンスされなくなって動かないので。
 * 
 */
class MfViewTwig extends \Slim\View
{
    /**
     * @var string The path to the Twig code directory WITHOUT the trailing slash
     */
    public static $twigDirectory = null;

    /**
     * @var array Paths to directories to attempt to load Twig template from
     */
    public static $twigTemplateDirs = [];

    /**
     * @var array The options for the Twig environment, see
     * http://www.twig-project.org/book/03-Twig-for-Developers
     */
    public static $twigOptions = [];

    /**
     * @var TwigEnvironment The Twig environment for rendering templates.
     */
    private $twigEnvironment = null;

    /**
     * コンストラクタ
     * 
     * @param Array $options
     */
    public function __construct($options) {
        parent::__construct();
        self::$twigOptions = $options;
        
    }
    /**
     * Get a list of template directories
     *
     * Returns an array of templates defined by self::$twigTemplateDirs, falls
     * back to Slim\View's built-in getTemplatesDirectory method.
     *
     * @return array
     **/
    private function getTemplateDirs()
    {
        if (empty(self::$twigTemplateDirs)) {
            return array($this->getTemplatesDirectory());
        }
        return self::$twigTemplateDirs;
    }
    
    /**
     * Slim frameworkのView側から呼び出されるCallback
     * 
     * @param string $templateName
     * @param Array $dummyData
     * @return type
     */
    public function render($templateName,$dummyData = null)
    {

        // Slim/Viewの$dataの作りはおかしい
        // 引数のdataは常に空になる。アプリの引数の実態は常に$this->dataの中
        $data = $this->data->all();
        $data = $this->getMfConfig($data);
        $data = $this->getMfSession($data);
        $twig = $this->getTwigEnv();
        $template = $twig->loadTemplate($templateName);
        return $template->render($data);
    }
    
    /**
     * SlimのConfigファイルをTwig側で読み込み可能にする
     * 
     * @param array $data
     * @return type
     */
    private function getMfConfig(Array $data) 
    {
        if (!empty($this->slimConfig)) {
            // SlimのConfig=AppコンフィグをTwig側で参照可能にする。
            $config['config'] = $this->slimConfig;
            $data = array_merge($data,$config);
        }
        return $data;
    }
    /**
     * illusion/sessionのデータをTwig側で読み込み可能にする
     * 
     * @param array $data
     * @return type
     */
    private function getMfSession(Array $data) 
    {
        if (!empty($this->app->session)) {
            // セッションデータをTwig側で参照可能にする。
            $config['session'] = $this->app->session;
            $data = array_merge($data,$config);
        }
        return $data;
    }
    
    /**
     * Twig初期化
     * 
     * @return type
     */
    public function getTwigEnv()
    {
        if (!$this->twigEnvironment) {
            // Check for Composer Package Autoloader class loading
            if (!class_exists('\Twig_Autoloader')) {
                require_once self::$twigDirectory . '/Autoloader.php';
            }

            \Twig_Autoloader::register();
            $loader = new \Twig_Loader_Filesystem($this->getTemplateDirs());
            $this->twigEnvironment = new \Twig_Environment(
                $loader,
                self::$twigOptions
            );
        }

        return $this->twigEnvironment;
    }
}
