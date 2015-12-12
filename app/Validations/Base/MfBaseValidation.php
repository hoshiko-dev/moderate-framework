<?php
namespace MfValidations\Base;

use Illuminate\Container\Container;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Translation\FileLoader;
use Illuminate\Translation\Translator;
use Illuminate\Validation\Factory;

/**
 * Description of MfBaseValidation
 *
 * @author Kota
 */
abstract class MfBaseValidation implements MfValidationInterface
{
    /** @var Slimオブジェクト */
    protected $app;
    /** @var validator作成オブジェクト */
    protected $validatorFactory;
    /** @var validator */
    protected $validator;
    
    /** @var Validation対象(name属性値) */
    private  $validationData;
    /** @var Validationルール(Laravel方式) */
    private $validatonRules;
    /** @var Validationエラーメッセージ(日本語可能) */
    private $valiationMessages;
    /** @var Validationエラーメッセージの予約語変換メッセージ(日本語可能) */
    private $validationAttributes;
    
    /** @var HTTP Requestデータ(各Validationクラスで上書きすること) */
    protected $requestParams = [
        'data' => [
            // HTTPリクエストからもらった値をセット
        ],
        'rules' => [
            // illmination/ValidationのValidationルールを記載 
        ],
        'messages' => [
            // エラーメッセージを記載
        ],
        'attributes' => [
            // エラーメッセージ内の予約語を記載
        ]
    ];

    /**
     * コンストラクタ
     * 
     * @param type $app
     */
    public function __construct($app = null)
    {
        $this->app = $app;
        $loader = new FileLoader(new Filesystem, 'lang');
        $translator = new Translator($loader, $this->app->config('lang'));
        $this->validatorFactory = new Factory($translator, new Container);
    }
    
    /**
     * Validationを実施
     * 
     * @return boolean
     * @return \Illuminate\Support\MessageBag
     * @throws \Exception
     */
    public function validate()
    {
        $this->validator = $this->validatorFactory->make($this->validationData, $this->validatonRules,$this->valiationMessages,$this->validationAttributes);
        if (!isset($this->validator)) {
            throw new \Exception('validator not found.');
        }
        if ($this->validator->fails()) {
            return $this->validator->messages();
        }
        return true;
    }
    
    /**
     * エラーメッセージを取得
     * 
     * @return \Illuminate\Support\MessageBag
     */
    public function getMessages() 
    {
        if (!isset($this->validator)) {
            throw new \Exception('validator not found.');
        }
        return $this->validator->messages();
    }

    /**
     * エラーメッセージを取得
     * 
     * @return Array
     */
    public function getMessagesToArray() 
    {
        if (!isset($this->validator)) {
            throw new \Exception('validator not found.');
        }
        return $this->validator->messages()->toArray();
    }
    
    /**
     * エラー対象リソースの配列を取得
     * 
     * @return Array
     */
    public function getFails()
    {
        if (!isset($this->validator)) {
            throw new \Exception('validator not found.');
        }
        return $this->validator->failed();
    }
    
    /**
     * カスタムValidationをクロージャで追加する。
     * 
     * @param string $key
     * @param object $function
     * @param string $message
     * @throws \Exception
     */
    public function setExtendValidation($key,$function,$message =null)
    {
        if (!isset($this->validatorFactory)) {
            throw new \Exception('validation Factory not found.');
        }
        $this->validatorFactory->extend($key,$function,$message);
    }
    
    /**
     * Illuminate/Validationフォーマットバリデーション情報をセットする
     * 
     * @param Array $data
     * @param Array $rules
     * @param Array $messages
     * @param Array $attributes
     */
    public function setUpValidation($data,$rules,$messages=null,$attributes =null)
    {
        $this->validationData = $data;
        $this->validatonRules = $rules;
        $this->valiationMessages = $messages;
        $this->validationAttributes = $attributes;
    }
    
    /**
     * Illuminate/Validationフォーマットの情報を追加
     * 
     * @param Array $data
     * @param Array $rules
     * @param Array $messages
     */
    public function addValidation($data,$rules,$messages=null)
    {
        $this->validationData = array_merge($this->validationData,$data);
        $this->validatonRules = array_merge($this->validatonRules,$rules);
        $this->valiationMessages = array_merge($this->valiationMessages,$messages);
    }
    
    /**
     * ValidationデータにHTTPRequestの情報をセット
     * 
     * @param type $params
     * @throws \Exception
     */
    public function setValidationData($params)
    {
        if (!isset($this->requestParams)) {
            throw new \Exception('Validation Params not found');
        }
        $data = $this->requestParams['data'];
        if (!isset($data)) {
            throw new \Exception('Validation Data not found');
        }
        foreach($params as $key => $param) {
            if (array_key_exists($key,$data)) {
                $this->requestParams['data'][$key] = $param;
            } else {
                $this->requestParams['data'][$key] = $param;
            }
        }
        // TODO: ファイルアップロード対応
        $this->setUpValidation($this->requestParams['data'], $this->requestParams['rules'], $this->requestParams['messages'],$this->requestParams['attributes']);
    }
    
    /**
     * HTTPRequestの情報を上書き
     * ※Modles連携部
     * 
     * @param Array $params
     * @throws \Exception
     */
    public function setRequestParams($params)
    {
        if (!isset($this->requestParams)) {
            throw new \Exception('Validation Params not found');
        }
        $this->requestParams = $params;
    }
}
