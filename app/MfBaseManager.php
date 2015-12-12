<?php

namespace MfPackage;

use \ORM as ORM;

//
// Memo: Transaction Format 
// 
//// Start a transaction
//ORM::get_db()->beginTransaction();
//
//// Commit a transaction
//ORM::get_db()->commit();
//
//// Roll back a transaction
//ORM::get_db()->rollBack();


class MfBaseManager
{
    // Managerの自動読み出しに使う場合は必ずTrueへ
    const IS_INSTANCE = false;
    // Manager名。$app->baseのフォーマットで使えるようになる
    const MANAGER_NAME = 'base';

    
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
        // PrimaryKeyカラム名をセット(セットしない場合はid固定)
        $tables = [];
        foreach($this->databases as $key => $data) {
            $tables[$data['table_name']] = $data['primary_key'];
        }
        ORM::configure('id_column_overrides', $tables);
        // var_dump(ORM::get_last_query());で実行直後のSQLを吐き出すようになる
        ORM::configure('logging', true);
        
    }

    /**
     * ORMで全レコードを取得
     * 
     * @param string $sql
     * @param array $params
     * @return Array
     */
    public function getAll($sql, $params)
    {
        // TABLE名の指定が必須
        $dummy = reset($this->databases);
        return ORM::for_table($dummy['table_name'])
                        ->raw_query($sql, $params)
                        ->find_many();
    }

    /**
     * ORMで1レコードを取得
     * $paramsが不要な場合は空配列:[]をセット
     * 
     * @param string $sql
     * @param array $params
     * @return array
     */
    public function getRow($sql, $params)
    {
        // TABLE名の指定が必須
        $dummy = reset($this->databases);
        return ORM::for_table($dummy['table_name'])
                        ->raw_query($sql, $params)
                        ->find_one();
    }

    /**
     * ORMで1レコードの1カラムを取得
     * 
     * @param string $sql
     * @param array $params
     * @return object
     */
    public function getOne($sql, $params)
    {
        // TABLE名の指定が必須
        $dummy = reset($this->databases);
        $obj = ORM::for_table($dummy['table_name'])
                        ->raw_query($sql, $params)
                        ->find_array();
        if (!empty($obj) && !empty($obj[0])) {
            return $obj[0];
        }
        return false;
    }

    /**
     * ORMで指定数のレコードを取得
     * ページャー
     * ※テスト未
     * 
     * @param string $sql
     * @param array $params
     * @param integer $offset
     * @param integer $limit
     * @return object
     */
    public function getPagerRows($sql, $params,$offset = 0,$limit = 10)
    {
        // TABLE名の指定が必須
        $dummy = reset($this->databases);
        return  ORM::for_table($dummy['table_name'])
                        ->raw_query($sql, $params)
                        ->offset($offset)->limit($limit)
                        ->find_many();
    }


    /**
     * SQLで、1レコード取得
     * 
     * @param string $table
     * @param integer $id レコードのIDを指定
     * @return object
     */
    public function select($table, $id)
    {
        return ORM::for_table($this->databases[$table]['table_name'])->find_one($id);
    }

    /**
     * SQLで、指定レコードを複数取得
     * $paramsが不要な場合は空配列:[]をセット
     * 
     * @param string $sql
     * @param array $params
     * @return array
     */
    public function selectAll($table)
    {
        return ORM::for_table($this->databases[$table]['table_name'])->find_many();
    }
    /**
     * ORMで1レコードの1カラムを取得
     * 
     * @param string $table
     * @param integer $id レコードのIDを指定
     * @param string $target カラム名を指定
     * @return object
     */
    public function selectOne($table, $id, $target)
    {
        $obj = ORM::for_table($this->databases[$table]['table_name'])
                ->select($target)->find_one($id);
        if (empty($obj)) {
            throw new \Exception('column Not Found id=' . $id . '/target=' . $target);
        }
        return $obj->$target;
    }
    
    /**
     * ORMでINSERT
     * INSERTは極力個別で行うこと
     * 
     * @param string $table
     * @param array $params
     * @return integer
     */
    public function insert($table, $params)
    {
        // TABLE名の指定が必須
        $resource = ORM::for_table($this->databases[$table]['table_name'])->create();
        foreach ($params as $key => $value) {
            $resource->set($key, $value);
        }
        $resource->set_expr($this->databases[$table]['created_at'], 'NOW()');
        $resource->set_expr($this->databases[$table]['updated_at'], 'NOW()');
        $resource->save();
        return $resource->id();
    }

    /**
     * ORMでUPDATE
     * UPDATEは極力個別で行うこと
     * 
     * @param string $table
     * @param integer $id
     * @param array $params
     */
    public function update($table, $id,$params)
    {
        // TABLE名の指定が必須
        $resource = ORM::for_table($this->databases[$table]['table_name'])->find_one($id);
        if (empty($resource)) {
            throw new \Exception('ORM Update Resource Not Found.');
        }
        foreach ($params as $key => $value) {
            $resource->set($key, $value);
        }
        $resource->set_expr($this->databases[$table]['updated_at'], 'NOW()');
        $resource->save();
    }

    /**
     * ORMでDELETE
     * 論理削除をサポート
     * 
     * @param string $table
     * @param integer $isSoft true:論理削除 false:物理削除
     * @param integer $id
     * @param integer $params
     */
    public function remove($table, $isSoft = false, $id = null, $params = null)
    {
        if ($isSoft) {
            if (empty($id)) {
                throw new \Exception('Soft Delete id or params not found');
            }
            $params = [$this->databases[$table]['remove_flag_name'] => 0];
            $this->update($table, $id,$params);
        } else {
            // 物理削除
            if ($id) {
                $resource = ORM::for_table($this->databases[$table]['table_name'])->find_one($id);
                if (empty($resource)) {
                    throw new \Exception('Remove: Resource not found. id=' . $id);
                }
                $resource->delete();
            } else if (!empty($params)) {
                $orm = ORM::for_table($this->databases[$table]['table_name']);
                foreach ($params as $key => $value) {
                    $orm->where($key, $value);
                }

                $resources = $orm->find_many();
                if (empty($resources)) {
                    throw new \Exception('Remove: Resources not found. where=' . var_dump($params));
                }
                foreach($resources as $resource) {
                    $resource->delete();
                }
            } else {
                throw new Exception('Resources Removed must id or params');
            }
        }
    }

}
