<?php
namespace MfModels\Base;

/**
 * Description of MfBaseModels
 *
 * @author Kota
 */
abstract class MfBaseModels implements MfBaseMdoleInterface
{
    /** @var Slimオブジェクト */
    protected $app;
    /** @var Validatorオブジェクト */
    protected $validator;
    /** @var Requestパラメータ */
    protected $requestParams;
    
    /**
     * コンストラクタ
     * @param type $app
     */
    protected function __construct($app)
    {
        $this->app = $app;
    }
    
    /**
     * ModelでValidationする場合にセットする
     * 
     * @param \Illuminate\Validation\Validator $validator
     */
    protected function setValidator(\Illuminate\Validation\Validator $validator)
    {
        $this->validator = $validator;
    }
    
    /**
     * Validatorを取得
     * 
     * @return \Illuminate\Validation\Validator
     */
    protected function getValidator()
    {
        return $this->validator;
        
    }
    
    // Validatonは任意
    // どちらかといえばパラメータの管理が大事
    
    /**
     * モデルにリクエストパラメータをセット
     * ※ダミーメソッド。目的合わせて拡張
     * 
     * @param array $params
     */
    protected function setRequestParams(Array $params)
    {
        if (!empty($params)) {
            foreach($params as $key => $value) {
                $this->requestParams[] = [$key => $value];
            }
        }
    }
    
    /**
     * リクエストパラメータを取得
     * @return Array
     */
    protected function getParams()
    {
        return $this->requestParams;
    }
    
    /**
     * モデルのValidationを実施
     * Validation未使用の場合はException
     * 
     * @return Array
     */
    protected function validate()
    {
        if (!isset($this->validator)) {
            throw new Exception('validator not found.');
        }
        if ($this->validator->fails()) {
            return $this->validator->errors();
        }
        return true;
    }
}
