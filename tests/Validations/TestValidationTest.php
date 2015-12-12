<?php
namespace MfTestsResources\Validations;


/**
 * Description of TestValidationTest
 *
 * @author hoshiko
 */
class TestValidationTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var TestValidation
     */
    protected $object;
    
    protected $app;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        require_once 'vendor/autoload.php';
        include 'config/config.php';
        $config = [];
        $app = \MfBase\MfWrapper::create($config);
        $this->app = $app;
        $this->object = new TestValidation($app);
        
        // カスタムバリデーションルール
        $this->object->setExtendValidation('hogehoge',
            // $attribute:フィールド名:string
            // $value:INパラメータ値
            // $paramters:不明(ルールに渡された引数?)
            function ($attribute,$value=null,$parameters=null) {
                return 1 === $value;
            },':attributeは不正です');
        
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown()
    {
        
    }
    
    public function testValidationTrue()
    {
        $this->object->setValidationData(['email'=>'aaa@aaa.com','password'=>'hogehoge','member_name' => 'tarouyamada',
            'photo_url'=>'http://hogehoge.com','icon_url'=>'http://mogemoge.com','is_enabled' => 1]);
        $result = $this->object->validate();
        $this->assertTrue($result,'Validate is not True');
    }
    
    public function testValidationEmailFail()
    {
        $this->object->setValidationData(['email'=>'aaa','password'=>'hogehoge','member_name' => 'tarouyamada',
            'photo_url'=>'http://hogehoge.com','icon_url'=>'http://mogemoge.com','is_enabled' => 1]);
        $result = $this->object->validate();
        //var_dump($result);
        $this->assertNotTrue($result,'Validate is not True');
        $this->assertArrayHasKey('email', $result->getMessages());
        
        $this->object->setValidationData(['email'=>'','password'=>'hogehoge','member_name' => 'tarouyamada',
            'photo_url'=>'http://hogehoge.com','icon_url'=>'http://mogemoge.com','is_enabled' => 1]);
        $result = $this->object->validate();
        //var_dump($result);
        $this->assertNotTrue($result,'Validate is not True');
        $this->assertArrayHasKey('email', $result->getMessages());
        
        $this->object->setValidationData(['email'=>null,'password'=>'hogehoge','member_name' => 'tarouyamada',
            'photo_url'=>'http://hogehoge.com','icon_url'=>'http://mogemoge.com','is_enabled' => 1]);
        $result = $this->object->validate();
        //var_dump($result);
        $this->assertNotTrue($result,'Validate is not True');
        $this->assertArrayHasKey('email', $result->getMessages());
    }
    
    public function testExtendClosure()
    {
        $key = 'hogehoge';

        $this->object->setValidationData(['email' => 'aaa@bbb.com','password'=>'hogehoge','hogehoge' => '1111',
            'member_name' => 'tarouyamada','photo_url'=>'http://hogehoge.com','icon_url'=>'http://mogemoge.com','is_enabled' => 1]);
        $result = $this->object->validate();
        //var_dump($result,'#############');
        $result = $this->object->call('method__hogehoge');
        //var_dump($result,'#############');
    }
}
