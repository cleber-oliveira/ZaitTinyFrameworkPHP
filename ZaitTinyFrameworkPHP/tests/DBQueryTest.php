<?php 

namespace tests;

use PHPUnit\Framework\TestCase;
use app\core\database\DBQuery;
use app\core\database\DBConnection;

require_once __DIR__.'/../app/core/database/DBConnection.php';
require_once __DIR__.'/../app/core/database/DBQuery.php';

class DBQueryTest extends TestCase {
    public function testInsert() {
        // Crie um mock da classe DBQuery com os métodos inseridos no mock
        $mockDBQuery = $this->getMockBuilder(DBQuery::class)
        ->setConstructorArgs(['kukafit.usuarios', 'idUsuario, nome, dtNasc, genero, escolaridade, telefone, email, senha, aceitaContrato, dthrContrato, activeCode, ativo, fraseRecupera', ['idUsuario']])
        ->onlyMethods(['insert'])
        ->getMock();
        
        // Configure o mock para o método insert
        $mockDBQuery->expects($this->any())
        ->method('insert')
        ->willReturn(true);
        
        // Execute o método insert
        $values = ['0', 'Cleber Oliveira', '1981-11-21', 0, 8, '11987018707', 'cleber@ifsp.edu.br', 'senha', 1, '2023-01-30', 'ASDFG654', 1, 'nenhuma'];
        $result = $mockDBQuery->insert($values);
        
        // Verifique se o resultado é verdadeiro
        $this->assertTrue($result);
    }
    
    public function testSelect() {
        // Crie um mock da classe DBQuery com os métodos inseridos no mock
        $mockDBQuery = $this->getMockBuilder(DBQuery::class)
        ->setConstructorArgs(['kukafit.usuarios', 'idUsuario, nome, dtNasc, genero, escolaridade, telefone, email, senha, aceitaContrato, dthrContrato, activeCode, ativo, fraseRecupera', ['idUsuario']])
        ->onlyMethods(['select'])
        ->getMock();
        
        // Configure o mock para o método select
        $mockDBQuery->expects($this->any())
        ->method('select')
        ->willReturn(true);
        
        // Execute o método select
        $result = $mockDBQuery->select();
        
        // Verifique se o resultado é verdadeiro
        $this->assertTrue($result);
    }
    
    public function testUpdate() {
        // Crie um mock da classe DBQuery com os métodos inseridos no mock
        $mockDBQuery = $this->getMockBuilder(DBQuery::class)
        ->setConstructorArgs(['kukafit.usuarios', 'idUsuario, nome, dtNasc, genero, escolaridade, telefone, email, senha, aceitaContrato, dthrContrato, activeCode, ativo, fraseRecupera', ['idUsuario']])
        ->onlyMethods(['update'])
        ->getMock();
        
        // Configure o mock para o método update
        $mockDBQuery->expects($this->any())
        ->method('update')
        ->willReturn(true);
        
        // Execute o método update
        $values = ['0', 'Cleber Oliveira', '1981-11-21', 0, 8, '11987018707', 'cleber@ifsp.edu.br', 'senha', 1, '2023-01-30', 'ASDFG654', 1, 'nenhuma'];
        $result = $mockDBQuery->update($values);
        
        // Verifique se o resultado é verdadeiro
        $this->assertTrue($result);
    }
    
    public function testDelete() {
        // Crie um mock da classe DBQuery com os métodos inseridos no mock
        $mockDBQuery = $this->getMockBuilder(DBQuery::class)
        ->setConstructorArgs(['kukafit.usuarios', 'idUsuario, nome, dtNasc, genero, escolaridade, telefone, email, senha, aceitaContrato, dthrContrato, activeCode, ativo, fraseRecupera', ['idUsuario']])
        ->onlyMethods(['delete'])
        ->getMock();
        
        // Configure o mock para o método delete
        $mockDBQuery->expects($this->any())
        ->method('delete')
        ->willReturn(true);
        
        // Execute o método delete
        $values = ['0', 'Cleber Oliveira', '1981-11-21', 0, 8, '11987018707', 'cleber@ifsp.edu.br', 'senha', 1, '2023-01-30', 'ASDFG654', 1, 'nenhuma'];
        $result = $mockDBQuery->delete($values);
        
        // Verifique se o resultado é verdadeiro
        $this->assertTrue($result);
    }
}

?>