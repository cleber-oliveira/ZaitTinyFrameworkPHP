<?php

namespace tests;

use app\core\utils\Sanitize;
use PHPUnit\Framework\TestCase;

require_once __DIR__.'/../app/core/utils/Sanitize.php';

class SanitizeTest extends TestCase{
    public function testToClean(){
        
        $sanitize = new Sanitize(true, true, true);

        //Teste para limpeza de string com injeção de código HTML
        $input = "<script>alert('Hello World!');</script>";
        $outputExpected = "alert(`Hello World!`);";
        $this->assertEquals( $outputExpected, $sanitize->toClean($input));
                
        // Teste para limpeza de string com caracteres especiais 
        $input = "D'agua";
        $outputExpected = "D`agua";
        $this->assertEquals( $outputExpected, $sanitize->toClean($input));
        
        // Teste para limpeza de string sql injection
        $input = "' OR 1=1; -- ";
        $outputExpected = "` OR 1=1; --";
        $this->assertEquals( $outputExpected, $sanitize->toClean($input));
        
        

    }
}
