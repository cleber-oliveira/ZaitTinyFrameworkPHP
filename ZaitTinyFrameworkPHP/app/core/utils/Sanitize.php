<?php
/**
 * Sanitize.class.php
 *
 * Classe Sanitize para limpeza de variáveis de entrada
 *
 * @package app\core\utils
 */

namespace app\core\utils;

class Sanitize {
    
    /**
     * @var boolean $cleanCode Habilita ou desabilita a limpeza de injeção de código
     */
    private $cleanCode = false;
    
    /**
     * @var boolean $cleanSql Habilita ou desabilita a limpeza de injeção de SQL
     */
    private $cleanSql = false;
    
    /**
     * @var boolean $cleanRequestVars Habilita ou desabilita a limpeza das variáveis de requisição
     */
    private $cleanRequestVars = false;
    
    /**
     * @var array $input_vars Armazena as variáveis de entrada
     */
    private $input_vars = [];
    
    /**
     * @var array $searchCodeInjection Armazena os padrões de injeção de código a serem procurados
     */
    private $searchCodeInjection = [
        '/<script\b[^>]*>(.*?)<\/script>/is', // injeção de scripts
        '/<[\/\!]*?[^<>]*?>/is', // injeção de tags html
        '/<style\b[^>]*>(.*?)<\/style>/isU', // injeção de style tags
        '/<![\s\S]*?--[ \t\n\r]*>/is'  // injeção de comentários html
    ];
    /**
     * Construtor da classe Sanitize
     *
     * @param boolean $cleanRequestVars Habilita ou desabilita a limpeza das variáveis de requisição
     * @param boolean $cleanCode Habilita ou desabilita a limpeza de injeção de código
     * @param boolean $cleanSql Habilita ou desabilita a limpeza de injeção de SQL
     */
    public function __construct($cleanRequestVars = true, $cleanCode = true, $cleanSql = true) {
        $this->setCleanCode($cleanCode);
        $this->setCleanSql($cleanSql);
        $this->setCleanRequestVars($cleanRequestVars);
    }
    
    /**
     * Limpa todas as variáveis de requisição HTTP (GET, POST, REQUEST e variáveis de entrada)
     *
     * @return void
     */
    public function clearRequestHttp() {
        parse_str(file_get_contents("php://input"), $this->input_vars);
        if ($this->getCleanRequestVars()) {
            $_GET = $this->toClean(filter_input_array(INPUT_GET, FILTER_SANITIZE_STRING));
            $_POST = $this->toClean(filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING));
            $_REQUEST = $this->toClean(filter_input_array(INPUT_REQUEST, FILTER_SANITIZE_STRING));
            $this->input_vars = $this->toClean(filter_var_array($this->input_vars, FILTER_SANITIZE_STRING));
        }
    }
    
    /**
     * Limpa uma variável de entrada. Se a variável for um array, chama-se recursivamente para limpar cada elemento.
     *
     * @param mixed $data O dado a ser limpo
     * @return mixed O dado limpo
     */
    public function toClean($data) {
        if (is_array($data)) {
            $sanitized = [];
            foreach ($data as $key => $val) {
                $sanitized[$key] = $this->toClean($val);
            }
            return $sanitized;
        } elseif (is_numeric($data)) {
            return filter_var($data, FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
        } elseif (strtotime($data) !== false) {
            return date('Y-m-d H:i:s', strtotime($data));
        } elseif (is_string($data)) {
            
            $sanitized = trim($data);
            $sanitized = str_replace("'", "`", $sanitized);
            
            if ($this->getCleanCode()) {
                $sanitized = preg_replace('/<[^>]*>/', '', $sanitized);
                //$sanitized = preg_replace($this->searchCodeInjection, '', $sanitized);
            }
            if ($this->getCleanSql()) {
                $sanitized = addslashes($sanitized);
            }
            
            return $sanitized;
        }
        
        return $data; // Retorno padrão para outros tipos de dados
    }
    
    /**
     * Obtém o valor da propriedade cleanCode
     *
     * @return boolean O valor de cleanCode
     */
    public function getCleanCode() {
        return $this->cleanCode;
    }
    
    /**
     * Define o valor da propriedade cleanCode
     *
     * @param boolean $cleanCode O valor de cleanCode a ser definido
     * @return void
     */
    public function setCleanCode($cleanCode) {
        $this->cleanCode = $cleanCode;
    }
    
    /**
     * Obtém o valor da propriedade cleanSql
     *
     * @return boolean O valor de cleanSql
     */
    public function getCleanSql() {
        return $this->cleanSql;
    }
    
    /**
     * Define o valor da propriedade cleanSql
     *
     * @param boolean $cleanSql O valor de cleanSql a ser definido
     * @return void
     */
    public function setCleanSql($cleanSql) {
        $this->cleanSql = $cleanSql;
    }
    
    /**
     * Obtém o valor da propriedade cleanRequestVars
     *
     * @return boolean O valor de cleanRequestVars
     */
    public function getCleanRequestVars() {
        return $this->cleanRequestVars;
    }
    
    /**
     * Define o valor da propriedade cleanRequestVars
     *
     * @param boolean $cleanRequestVars O valor de cleanRequestVars a ser definido
     * @return void
     */
    public function setCleanRequestVars($cleanRequestVars) {
        $this->cleanRequestVars = $cleanRequestVars;
    }
    
    /**
     * Obtém o valor da propriedade input_vars
     *
     * @return array O valor de input_vars
     */
    public function getInput_vars() {
        return $this->input_vars;
    }
    
    /**
     * Define o valor da propriedade input_vars
     *
     * @param array $input_vars O valor de input_vars a ser definido
     * @return void
     */
    public function setInput_vars($input_vars) {
        $this->input_vars = $input_vars;
    }
    
    /**
     * Obtém o valor da propriedade searchCodeInjection
     *
     * @return array O valor de searchCodeInjection
     */
    public function getSearchCodeInjection() {
        return $this->searchCodeInjection;
    }
    
    /**
     * Define o valor da propriedade searchCodeInjection
     *
     * @param array $searchCodeInjection O valor de searchCodeInjection a ser definido
     * @return void
     */
    public function setSearchCodeInjection($searchCodeInjection) {
        $this->searchCodeInjection = $searchCodeInjection;
    }
}

/**
 * Exemplo de uso da classe Sanitize
 * 
 * Cria uma instância da classe Sanitize
 * $sanitize = new Sanitize();

 * Limpa todas as variáveis de requisição HTTP
 * $sanitize->clearRequestHttp();

 * Exemplo de limpeza de uma variável
 * $data = "<script>alert('Hello World!');</script>";
 * $cleanData = $sanitize->toClean($data);
 * echo $cleanData; // Saída: "alert('Hello World!');"
 */

?>