<?php

namespace app\core\database;

use app\core\utils\Sanitize;

/**
 * Classe Where
 *
 * Essa classe é responsável por gerar cláusulas WHERE em SQL.
 *
 * Exemplo de uso:
 *
 * ```php
 * $where = new Where();
 * $where->addCondition('AND', 'nome', '=', 'João');
 * $where->addCondition('OR', 'idade', '>', 30);
 * $query = $where->build();
 * echo $query; // Produz: WHERE nome = ? AND idade > ?
 * ```
 */
class Where {
    
    private $conditions = array();
    
    public function __construct(){
        
    }
    
    /**
     * Adiciona uma condição à cláusula WHERE.
     *
     * @param string $logicOperator O operador lógico para a condição (AND, OR).
     * @param string $fieldName O nome do campo para a condição.
     * @param string $operator O operador para a condição (por exemplo, =, <, >, LIKE, etc.).
     * @param mixed $value O valor a ser comparado na condição.
     *
     */
    public function addCondition($logicOperator, $fieldName, $operator, $value) {
        $value = (new Sanitize())->toClean($value);
        $this->conditions[] = array(
            "logicOperator" => $logicOperator,
            "fieldName" => $fieldName,
            "operator" => $operator,
            "value" => $value
        );
    }
    
    /**
     * Adiciona uma condição LIKE à cláusula WHERE.
     *
     * @param string $logicOperator O operador lógico para a condição (AND, OR).
     * @param string $fieldName O nome do campo para a condição.
     * @param mixed $value O valor a ser comparado na condição.
     *
     */
    public function addLike($logicOperator, $fieldName, $value) {
        $value = (new Sanitize())->toClean($value);
        $this->conditions[] = array(
            "logicOperator" => $logicOperator,
            "operator" => "LIKE",
            "fieldName" => $fieldName,
            "value" => "%".$value."%"
        );
    }
    
    /**
     * Adiciona uma condição NOT LIKE à cláusula WHERE.
     *
     * @param string $logicOperator O operador lógico para a condição (AND, OR).
     * @param string $fieldName O nome do campo para a condição.
     * @param mixed $value O valor a ser comparado na condição.
     *
     */
    public function addNotLike($logicOperator, $fieldName, $value) {
        $value = (new Sanitize())->toClean($value);
        $this->conditions[] = array(
            "logicOperator" => $logicOperator,
            "operator" => "NOT LIKE",
            "fieldName" => $fieldName,
            "value" => "%".$value."%"
        );
    }
    
    /**
     * Adiciona uma condição IN à cláusula WHERE.
     *
     * @param string $logicOperator O operador lógico para a condição (AND, OR).
     * @param string $fieldName O nome do campo para a condição.
     * @param mixed $values Os valores a serem comparados na condição (deve ser um array).
     *
     */
    public function addIn($logicOperator, $fieldName, $values) {
        $values = (new Sanitize())->toClean($values);
        $this->conditions[] = array(
            "logicOperator" => $logicOperator,
            "operator" => "IN",
            "fieldName" => $fieldName,
            "value" => $values
        );
    }
    
    /**
     * Adiciona uma condição NOT IN à cláusula WHERE.
     *
     * @param string $logicOperator O operador lógico para a condição (AND, OR).
     * @param string $fieldName O nome do campo para a condição.
     * @param mixed $values Os valores a serem comparados na condição (deve ser um array).
     *
     */
    public function addNotIn($logicOperator, $fieldName, $values) {
        $values = (new Sanitize())->toClean($values);
        $this->conditions[] = array(
            "logicOperator" => $logicOperator,
            "operator" => "NOT IN",
            "fieldName" => $fieldName,
            "value" => $values
        );
        return $this;
    }
    
    /**
     * Adiciona uma condição BETWEEN à cláusula WHERE.
     *
     * @param string $logicOperator O operador lógico para a condição (AND, OR).
     * @param string $fieldName O nome do campo para a condição.
     * @param mixed $min O valor mínimo a ser comparado na condição.
     * @param mixed $max O valor máximo a ser comparado na condição.
     *
     */
    public function addBetween($logicOperator, $fieldName, $min, $max) {
        $min = (new Sanitize())->toClean($min);
        $max = (new Sanitize())->toClean($max);
        $this->conditions[] = array(
            "logicOperator" => $logicOperator,
            "operator" => "BETWEEN",
            "fieldName" => $fieldName,
            "value" => array($min, $max)
        );
    }
    
    /**
     * Adiciona uma condição NOT BETWEEN à cláusula WHERE.
     *
     * @param string $logicOperator O operador lógico para a condição (AND, OR).
     * @param string $fieldName O nome do campo para a condição.
     * @param mixed $min O valor mínimo a ser comparado na condição.
     * @param mixed $max O valor máximo a ser comparado na condição.
     */
    public function addNotBetween($logicOperator, $fieldName, $min, $max) {
        $min = (new Sanitize())->toClean($min);
        $max = (new Sanitize())->toClean($max);
        $this->conditions[] = array(
            "logicOperator" => $logicOperator,
            "operator" => "NOT BETWEEN",
            "fieldName" => $fieldName,
            "value" => array($min, $max)
        );
    }
    
    /**
     * Retorna as condições armazenadas.
     *
     * @return array As condições armazenadas.
     */
    public function getConditions() {
        return $this->conditions;
    }
    
    /**
     * Limpa todas as condições armazenadas.
     */
    public function clearConditions() {
        $this->conditions = array();
    }
    
    /**
     * Constrói a cláusula WHERE com base nas condições armazenadas.
     *
     * @return string $whereClause cláusula WHERE construída.
     */
    public function build() {
        $whereClause = "";
        $hasConditions = false;
        
        foreach ($this->conditions as $condition) {
            if (!empty($condition["value"])) {
                if ($hasConditions) {
                    $whereClause .= " " . $condition["logicOperator"] . " ";
                }
                else {
                    $whereClause .= " WHERE ";
                    $hasConditions = true;
                }
                $fieldName = $condition["fieldName"];
                $operator = $condition["operator"];
                $value = $condition["value"];
                
                switch ($operator) {
                    case "IN":
                    case "NOT IN":
                        $value = implode(",", $value);
                        $whereClause .= $fieldName . " " . $operator . " (" . $value . ")";
                        break;
                    case "BETWEEN":
                    case "NOT BETWEEN":
                        $whereClause .= $fieldName . " " . $operator . " " . $value[0] . " AND " . $value[1];
                        break;
                    case "LIKE":
                    case "NOT LIKE":
                        $whereClause .= $fieldName . " " . $operator . " '" . $value . "'";
                        break;
                    default:
                        $whereClause .= $fieldName . " " . $operator . " ?";
                        break;
                }
            }
        }
        return ( $whereClause );
    }
}

/**
  *   Exemplos de uso da classe Where
  *   
  *   Instanciando a classe
  *   $where = new Where();
  *   
  *   addCondition
  *   Exemplo: SELECT  *   FROM tabela WHERE campo = 'valor'
  *   $where->addCondition('AND', 'campo', '=', 'valor');
  *   
  *   addLike
  *   Exemplo: SELECT  *   FROM tabela WHERE campo LIKE '%valor%'
  *   $where->addLike('AND', 'campo', 'valor');
  *   
  *   addNotLike
  *   Exemplo: SELECT  *   FROM tabela WHERE campo NOT LIKE '%valor%'
  *   $where->addNotLike('AND', 'campo', 'valor');
  *   
  *   addIn
  *   Exemplo: SELECT  *   FROM tabela WHERE campo IN ('valor1', 'valor2')
  *   $where->addIn('AND', 'campo', ['valor1', 'valor2']);
  *  
  *   addNotIn
  *   Exemplo: SELECT  *   FROM tabela WHERE campo NOT IN ('valor1', 'valor2')
  *   $where->addNotIn('AND', 'campo', ['valor1', 'valor2']);
  *   
  *   addBetween
  *   Exemplo: SELECT  *   FROM tabela WHERE campo BETWEEN 'valor1' AND 'valor2'
  *   $where->addBetween('AND', 'campo', 'valor1', 'valor2');
  *  
  *   addNotBetween
  *   Exemplo: SELECT  *   FROM tabela WHERE campo NOT BETWEEN 'valor1' AND 'valor2'
  *   $where->addNotBetween('OR', 'campo', 'valor1', 'valor2');
  *   
  *   build
  *   Cria a cláusula WHERE baseada nas condições adicionadas
  *   $whereClause = $where->build();
  *   
  *   getConditions
  *   Retorna as condições adicionadas
  *   $conditions = $where->getConditions();
  *   
  *   clearConditions
  *   Limpa todas as condições adicionadas
  *   $where->clearConditions();
*/

?>
