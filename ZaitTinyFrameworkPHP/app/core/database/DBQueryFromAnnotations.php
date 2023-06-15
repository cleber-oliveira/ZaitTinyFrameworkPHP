<?php

namespace app\core\database;

use ReflectionClass;
use Exception;
use app\core\utils\Sanitize;

/**
 * Classe DBQueryFromAnnotations
 *
 * Esta classe estende a classe DBQuery e permite a definição das informações da tabela através de anotações.
 *
 * @package app\core\database
 */
class DBQueryFromAnnotations extends DBQuery {
    
    private $schemaName  = "";
    private $tableName  = "";
    private $fieldsName = [];
    private $primaryKeys = [];
    private $uniqueKeys = [];
    private $foreignKeys = [];
    private $notNullFields = [];
    
    // gerar getters and setters
    
    /**
     * Construtor da classe DBQueryFromAnnotations
     *
     * @param object $object O objeto com as anotações
     * @throws Exception Se ocorrer um erro ao analisar as anotações
     */
    public function __construct($object) {
        parent::__construct('', '', '');
        
        $reflector = new ReflectionClass(get_class($object));
        $properties = $reflector->getProperties();
        

        global $matches;
        
        foreach ($properties as $property) {
            $doc = $property->getDocComment();
            $matches = null;
            if (preg_match('/@DBSchema\((.*?)\)/', $doc, $matches)) {
                $this->schemaName = $matches[1][0];
            }
            if (preg_match('/@DBTable\((.*?)\)/', $doc, $matches)) {
                $this->tableName = $matches[1][0];
            }
            
            if (preg_match('/@DBField\((.*?)\)/', $doc, $matches)) {
                $fieldParts = explode(", ", $matches[1]);
                $fieldNameInDB = trim($fieldParts[0], '"');
                $fieldNameInClass = isset($fieldParts[1]) ? trim($fieldParts[1], '"') : $fieldNameInDB;
                
                if ($property->getName() === $fieldNameInClass) {
                    $this->fieldsName[] = $fieldNameInDB;
                } else {
                    throw new Exception("Annotation @DBField does not match property name for " . $property->getName());
                }
                
                if (preg_match('/@DBPrimaryKey/', $doc)) {
                    $this->primaryKeys[] = $fieldNameInDB;
                }
                
                if (preg_match('/@DBNotNull/', $doc)) {
                    $this->notNullFields[] = $fieldNameInDB;
                }
                
                if (preg_match('/@DBUniqueKey/', $doc)) {
                    $this->uniqueKeys[] = $fieldNameInDB;
                }
                
                if (preg_match('/@DBForeignKey\((.*?)\)/', $doc, $matches)) {
                    $foreignKeyParts = explode(", ", $matches[1]);
                    $column = trim($foreignKeyParts[0], '"');
                    $refTable = trim($foreignKeyParts[1], '"');
                    $refColumn = trim($foreignKeyParts[2], '"');
                    $this->foreignKeys[] = [
                        "column" => $column,
                        "refTable" => $refTable,
                        "refColumn" => $refColumn
                    ];
                }
            }
        }
        
        $fieldsNameStr = implode(", ", $this->fieldsName);
        $primaryKeyStr = (count($this->primaryKeys) === 1) ?$this->primaryKeys[0] : $this->primaryKeys;
        
        parent::__construct($this->tableName, $this->fieldsNameStr, $this->primaryKeyStr);
        parent::addForeignKeys($this->foreignKeys);
        parent::addUniqueKeys($this->uniqueKeys);
    }
    
    /**
     * Insere os valores validados na tabela.
     *
     * @param array $values Os valores a serem inseridos
     * @return bool Retorna true se a inserção for bem-sucedida, caso contrário, lança uma exceção
     * @throws // InvalidArgumentException Se o número de valores informados não for equivalente aos campos da tabela
     * @throws Exception Se houver violação de chave única ou estrangeira, ou qualquer outra exceção do PDO
     */
    public function insertValidated($values) {
        $values = (new Sanitize(false, false, true))->toClean($values);
        
        if (count($values) !== count($this->fieldsName)) {
            throw new \InvalidArgumentException("O número de valores informados não é equivalente aos campos da tabela!");
        }
        
        // Verificar campos de chave única
        foreach ($this->uniqueKeys as $fieldName) {
            $uniqueCheckQuery = "SELECT COUNT(*) FROM {$this->tableName} WHERE {$fieldName} = ?";
            $uniqueCheckStatement = $this->conn->prepare($uniqueCheckQuery);
            $uniqueCheckStatement->execute([$values[$fieldName]]);
            $count = $uniqueCheckStatement->fetchColumn();
            if ($count > 0) {
                throw new Exception("O valor '{$values[$fieldName]}' para o campo '{$fieldName}' já existe na tabela!");
            }
        }
        
        // Verificar campos não nulos
        foreach ($this->notNullFields as $fieldName) {
            if (!isset($values[$fieldName]) || $values[$fieldName] === '') {
                throw new Exception("O valor para o campo '{$fieldName}' não pode ser nulo!");
            }
        }
        
        // Verificar campos de chave estrangeira
        /*
        foreach ($this->foreignKeys as $foreignKey) {
            $foreignTable = $foreignKey['refTable'];
            $foreignColumn = $foreignKey['refColumn'];
            $foreignValue = $values[$foreignKey['column']];
            $foreignCheckQuery = "SELECT COUNT(*) FROM {$foreignTable} WHERE {$foreignColumn} = ?";
            $foreignCheckStatement = $this->conn->prepare($foreignCheckQuery);
            $foreignCheckStatement->execute([$foreignValue]);
            $count = $foreignCheckStatement->fetchColumn();
            if ($count === 0) {
                throw new Exception("O valor '{$foreignValue}' para o campo '{$foreignKey['column']}' não existe na tabela de referência '{$foreignTable}'!");
            }
        }
        */
        $sql = "INSERT INTO {$this->tableName} (" . implode(', ', $this->fieldsName) . ")";
        $sql .= " VALUES ('" . implode("', '", $values) . "')";
        
        try {
            return $this->conn->query($sql);
        } catch (\PDOException $error) {
            if ($error->getCode() == 23000) {
                throw new Exception('Violação de chave única ou estrangeira.');
            }
            throw $error;
        }
    }
    

	public function getSchemaName(){
		return $this->schemaName;
	}

	public function setSchemaName($schemaName){
		$this->schemaName = $schemaName;
		return $this;
	}

	public function getTableName(){
		return $this->tableName;
	}

	public function setTableName($tableName){
		$this->tableName = $tableName;
		return $this;
	}

	public function getFieldsName(){
		return $this->fieldsName;
	}

	public function setFieldsName($fieldsName){
		$this->fieldsName = $fieldsName;
		return $this;
	}

	public function getPrimaryKeys(){
		return $this->primaryKeys;
	}

	public function setPrimaryKeys($primaryKeys){
		$this->primaryKeys = $primaryKeys;
		return $this;
	}

	public function getUniqueKeys(){
		return $this->uniqueKeys;
	}

	public function setUniqueKeys($uniqueKeys){
		$this->uniqueKeys = $uniqueKeys;
		return $this;
	}

	public function getForeignKeys(){
		return $this->foreignKeys;
	}

	public function setForeignKeys($foreignKeys){
		$this->foreignKeys = $foreignKeys;
		return $this;
	}

	public function getNotNullFields(){
		return $this->notNullFields;
	}

	public function setNotNullFields($notNullFields){
		$this->notNullFields = $notNullFields;
		return $this;
	}
	
/**
	 * A classe DBQueryFromAnnotations é definida e estende a classe DBQuery.
	 * O construtor da classe DBQueryFromAnnotations recebe um parâmetro $object que representa o objeto com as anotações.
	 *
	 * É criada uma instância da classe ReflectionClass para obter informações sobre a classe do objeto passado como parâmetro.
	 * É inicializada uma variável $properties que armazenará as propriedades da classe.
	 * A chamada ao construtor da classe pai (DBQuery) é feita com os parâmetros vazios, pois eles serão definidos posteriormente.
	 * A variável $matches é declarada globalmente para ser usada posteriormente.
	 * São declaradas variáveis para armazenar o nome da tabela, os nomes dos campos, as chaves primárias, as chaves únicas e as chaves estrangeiras.
	 * Utilizou-se expressão regular para buscar a anotação @DBField no comentário. Se a anotação for encontrada, o código dentro do if é executado.
	 * A anotação @DBField é composta por dois parâmetros separados por vírgula: o nome do campo no banco de dados e o nome do atributo na classe. Esses parâmetros são extraídos e armazenados nas variáveis $fieldNameInDB e $fieldNameInClass, respectivamente.
	 * É feita uma verificação se o nome da propriedade na classe corresponde ao nome especificado na anotação. Se a verificação falhar indica que a anotação não corresponde ao nome da propriedade.
	 * O nome do campo no banco de dados é adicionado ao array $fieldsName.
	 * É feita uma verificação para buscar a anotação @DBPrimaryKey. Se a anotação for encontrada, o nome do campo no banco de dados é adicionado ao array $primaryKeys.
	 * É feita uma verificação para buscar a anotação @DBForeignKey. Se a anotação for encontrada, os parâmetros da anotação são extraídos e armazenados nas variáveis $localField, $referenceTable e $referenceField. Esses valores são adicionados ao array $foreignKeys como um array associativo contendo as informações da chave estrangeira.
	 * É feita uma verificação para buscar a anotação @DBUniqueKey. Se a anotação for encontrada, o nome do campo no banco de dados é adicionado ao array $uniqueKeys.
	 * Após percorrer todas os atributos da classe, os nomes dos campos são transformados em uma string separada por vírgulas e armazenados na variável $fieldsNameStr
	 * A variável $primaryKeyStr é definida com base na quantidade de chaves primárias. Se houver apenas uma chave primária, o valor é o próprio nome do atributo. Caso contrário, o valor é o array de chaves primárias.
	 * O construtor da classe ancestral DBQuery é chamado com as informações coletadas, definindo o nome da tabela, os nomes dos campos e as chaves primárias.
	 * Os métodos addforeignKeys e addUniqueKeys da classe ancestral são chamados para adicionar as chaves estrangeiras e chaves únicas, respectivamente.
	 
	 * Deve-se utilizar as anotações
	 *@DBSchema,
	 *@DBTable,
	 *@DBField,
	 *@DBPrimaryKey,
	 *@DBForeignKey,
	 *@DBNotNull,
	 *@DBUniqueKey.
	 *
	 *Exemplo:
	 
	 ```php
	 
	 /**
	 @DBSchema("kukafit")
	 @DBTable("usuarios")
	 * /
	 class Usuarios {
	    /**
    	 @DBField("idUsuario")
    	 @DBPrimaryKey
    	 @DBNotNull
    	 @DBUniqueKey
    	 //
    	private$idUsuario;
    	
    	/**
    	 @DBField("nome")
    	 @DBNotNull
    	* /
    	private$nome;
    	
    	/**
    	 @DBField("dtNasc")
    	 @DBNotNull
    	* /
    	private$dtNasc;
    	
    	/**
    	 @DBField("cpf")
    	 @DBUniqueKey
    	* /
    	private$cpf;
    	
    	/**
    	 @DBField("escolaridade")
    	 @DBForeignKey("idNivelEscolar", "nivelEscolar", "idNivelEscolar")
    	* /
    	private$escolaridade;
    	
    	/**
    	 @DBField("email")
    	 @DBNotNull
    	 @DBUniqueKey
    	* /
    	private$email;
    	
    	/**
    	 @DBField("senha")
    	 @DBNotNull
    	* /
    	private$senha;
	}
	 ```
*/
}


?>


        