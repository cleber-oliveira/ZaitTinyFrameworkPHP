<?php

namespace tests;

use PHPUnit\Framework\TestCase;
use app\core\utils\UploadedCtrl;
use Exception;
use ReflectionClass;

require_once __DIR__.'/../app/core/utils/UploadedCtrl.php';
require_once __DIR__.'/../app/core/utils/Upload.php';
require_once __DIR__.'/../app/core/utils/Base64Files.php';

require_once __DIR__.'/../app/core/database/DBQuery.php';
require_once __DIR__.'/../app/core/database/DBConnection.php';

class UploadedCtrlTest extends TestCase{
    
    public function testRegisterUploadedfile(){
        
        $dbQueryMock = $this->createMock(\app\core\database\DBQuery::class);
        $dbQueryMock->expects($this->once())
        ->method('insert')
        ->with(['741258', __DIR__.'/nature.jpg', 1, ""]);
        
        $uploadedCtrl = new UploadedCtrl();
        $this->setProperty($uploadedCtrl, 'dbQuery', $dbQueryMock);
        
        $owner = '741258';
        $filePath = __DIR__.'/nature.jpg';
        $accessCtrl = 1;
        $shareMails = 'email1@example.com,email2@example.com';
        
        $result = $uploadedCtrl->registerUploadedfile($owner, $filePath, $accessCtrl, $shareMails);
        
        $this->assertEquals($filePath, $result);
    }
    
    public function testAddShareFile(){
        
        $file = [
            'filePath' => __DIR__.'/nature.jpg',
            'shareMails' => 'cleber@ifsp.edu.br, cleber.gulhos@gmail.com',
        ];
        
        $dbQueryMock = $this->createMock(\app\core\database\DBQuery::class);
        $dbQueryMock->expects($this->once())
        ->method('selectFiltered')
        ->willReturnSelf();
        $dbQueryMock->expects($this->once())
        ->method('fetch')
        ->willReturn($file);
        $dbQueryMock->expects($this->once())
        ->method('update')
        ->with($file);
        
        $uploadedCtrl = new UploadedCtrl();
        $this->setProperty($uploadedCtrl, 'dbQuery', $dbQueryMock);
        
        $filePath = __DIR__.'/nature.jpg';
        $shareMails = 'cleber@ifsp.edu.br, cleber.gulhos@gmail.com';
        
        $uploadedCtrl->addShareFile($filePath, $shareMails);
    }
    
    public function testDelShareFile(){
        
        $file = [
            'filePath' => __DIR__.'/nature.jpg',
            'shareMails' => 'cleber@ifsp.edu.br, cleber.gulhos@gmail.com',
        ];
        
        $dbQueryMock = $this->createMock(\app\core\database\DBQuery::class);
        
        $dbQueryMock->expects($this->once())
        ->method('selectFiltered')
        ->willReturnSelf();
        
        $dbQueryMock->expects($this->once())
        ->method('fetch')
        ->willReturn($file);
        
        $dbQueryMock->expects($this->once())
        ->method('update')
        ->with($file);
        
        $uploadedCtrl = new UploadedCtrl();
        $this->setProperty($uploadedCtrl, 'dbQuery', $dbQueryMock);

        $filePath = __DIR__.'/nature.jpg';
        $shareMails = 'cleber@ifsp.edu.br';
        
        $uploadedCtrl->delShareFile($filePath, $shareMails);
    }
    
    public function testGetFile(){

        $file = [
            'filePath' => __DIR__.'/nature.jpg',
            'owner' => '741258',
            'shareMails' => 'cleber@ifsp.edu.br, cleber.gulhos@gmail.com',
        ];
        
        $dbQueryMock = $this->createMock(\app\core\database\DBQuery::class);
        $dbQueryMock->expects($this->once())
        ->method('selectFiltered')
        ->willReturnSelf();
        
        $dbQueryMock->expects($this->once())
        ->method('fetch')
        ->willReturn($file);
        
        $base64FilesMock = $this->createMock(\app\core\utils\Base64Files::class);
        $base64FilesMock->expects($this->once())
        ->method('fileToBase64')
        ->with($file['filePath'])
        ->willReturn('base64Content');
        
        $uploadedCtrl = new UploadedCtrl();
        $this->setProperty($uploadedCtrl, 'dbQuery', $dbQueryMock);
        
        $this->setProperty($uploadedCtrl, 'base64Files', $base64FilesMock);
        
        $filePath = __DIR__.'/nature.jpg';
        
        $result = $uploadedCtrl->getFile($filePath);
        
        $this->assertEquals('base64Content', $result);
    }
    
    public function testGetFileThrowsException(){
        $file = [
            'filePath' => __DIR__.'/nature.jpg',
            'owner' => '741258',
            'shareMails' => 'cleber@ifsp.edu.br, cleber.gulhos@gmail.com',
        ];
        
        $dbQueryMock = $this->createMock(\app\core\database\DBQuery::class);
        
        $dbQueryMock->expects($this->once())
        ->method('selectFiltered')
        ->willReturnSelf();
        
        $dbQueryMock->expects($this->once())
        ->method('fetch')
        ->willReturn($file);
        
        $uploadedCtrl = new UploadedCtrl();
        $this->setProperty($uploadedCtrl, 'dbQuery', $dbQueryMock);
        
        $filePath = __DIR__.'/nature.jpg';
        
        $this->expectException(Exception::class);
        
        $uploadedCtrl->getFile($filePath);
    }
    
    private function setProperty($object, $propertyName, $value){
        $reflection = new ReflectionClass($object);
        $property = $reflection->getProperty($propertyName);
        $property->setAccessible(true);
        $property->setValue($object, $value);
    }
}


?>