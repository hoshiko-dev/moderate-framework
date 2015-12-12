<?php
namespace MfTestsResources;

use \ORM as ORM;

class TestManager extends MfBaseManager
{
    // Managerの自動読み出しに使う場合は必ずTrueへ
    const IS_INSTANCE = true;
    // Manager名。$app->baseのフォーマットで使えるようになる
    const MANAGER_NAME = 'test';
    
    protected $databases = [
        'members' => [
            'table_name' => 'members',
            'primary_key' => 'member_id',
            'is_soft_delete' => true, // true 論理削除：false 物理削除
            'remove_flag_name' => 'is_enabled',
            'created_at' => 'created_at',
            'updated_at' => 'updated_at',
        ]
    ];
    
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * ORMで全レコードを取得
     * 
     * @param string $sql
     * @param array $params
     * @return Array
     */
    public function sayHello()
    {
        echo "hello!!!!<br>";
    }

}
