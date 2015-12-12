<?php

namespace MfTestsResources\Validations;

use MfValidations\Base\MfBaseValidation;

/**
 * Description of TestValidation
 *
 * @author hoshiko
 */
class TestValidation extends MfBaseValidation
{
    protected $requestParams = [
        'data' => [
            'email' => null,
            'password' => null,
            'member_name' => null,
            'user_id' => null,
            'test_number' => null,
            'number_5to10' => null,
            'test_array' => [],
            'test_date' => null,
            'icon_url' => null,
            'is_enabled' => null,
        ],
        'rules' => [
            'email' => 'required|email',
            'password' => 'required|string',
            'member_name' => 'required|string',
            'user_id' => 'integer',
            'test_number' => 'integer|digits:3',
            'number_5to10' => 'digits|digits_between:5,10',
            'test_array' => 'array',
            'test_date' => 'date|date_format:"Y:m:d H:i:s"',
            'icon_url' => 'url',
            'is_enabled' => 'required|accepted|hogehoge',
        ],
        'messages' => [
            'email.required' => ':attributeが入力されていません',
            'email.email' => ':attributeが不正です',
            'paswword' => 'パスワードが不正です',
            'member_name' => '名前が不正です',
            'test_numser' => ':attributeが不正です',
            'test_numser' => ':attributeが設定されていません',
            'number_5to10' => ':attributeの範囲値が不正です',
            'number_5to10' => ':attributeが設定されていません',
            'icon_url' => 'icon_urlが不正です',
        ],
        'attributes' => [
            'email' => 'メールアドレス',
            'test_number' => 'テスト数値',
            'number_5to10' => '5から10の数値',
        ]
    ];
    
    /**
     * コンストラクタ
     * 
     * @param type $app
     */
    public function __construct($app = null)
    {
        parent::__construct($app);
    }
    
    public function call($method) {
        $this->validator->$method('aaa');
    }
}
