<?php 

namespace tests;


use PHPUnit\Framework\TestCase;
use app\core\utils\Base64Files;

require_once __DIR__.'/../app/core/utils/Base64Files.php';

class Base64FilesTest extends TestCase{
    
    public $base64 = ""; 
    
    public function testFileToBase64(){   
        $this->base64 = (new Base64Files())->fileToBase64(__DIR__.'/nature.jpg');
        $this->assertIsString($this->base64);
        $this->assertNotEmpty($this->base64);
    }
    
    public function testBase64ToFile(){
        $this->base64 = (new Base64Files())->fileToBase64(__DIR__.'/nature.jpg');
        $fileDestino = __DIR__.'/natureB.jpg';
        (new Base64Files())->base64ToFile($this->base64, $fileDestino);
        $this->assertFileExists($fileDestino);
    }
}


?>