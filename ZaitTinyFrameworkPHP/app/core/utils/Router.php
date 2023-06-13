<?php
/**
 * Router.class.php
 * @package ZaitTinyFrameworkPHP
 * @author  Msc Cleber Silva de Oliveira, Yossef Zait
 * @version 0.0.0.1
 * @see     https://cleberoliveira.info
 * 
 * Classe Router
 * 
 * Classe responsável por lidar com o roteamento de requisições HTTP na aplicação.
 * Ela define as rotas com base em um arquivo JSON e direciona a requisição para o arquivo físico correspondente.
 * Além disso, utiliza a classe Sanitize para garantir a segurança das requisições.
 * 
 * O arquivo routes.json utilizado para configurar as rotas deve ser um JSON válido onde:
 * 
 * - As chaves são os nomes das rotas. Exemplos: "home", "about", "contact", "404". 
 *   Quando alguém acessar a URL da sua aplicação seguida por uma dessas rotas, 
 *   a aplicação irá carregar o arquivo correspondente no campo "path".
 * 
 * - Os valores são objetos com dois campos: 
 *   "path": Caminho para o arquivo PHP que deverá ser carregado quando a rota for acessada. 
 *      Por exemplo, se você tem uma rota "home" e o campo "path" for "app/views/home.php", 
 *      quando alguém acessar a URL da sua aplicação seguida por /home, 
 *      a aplicação irá carregar o arquivo app/views/home.php.
 * 
 *   "sanitize": Objeto contendo três campos booleanos ("requestVars", "code", "sql") 
 *      que indicam se variáveis de requisição, códigos e SQL devem ser sanitizados. 
 *      Se algum desses campos estiver setado como true, 
 *      a aplicação irá utilizar a classe Sanitize para limpar as requisições HTTP, o código ou o SQL.
 * 
 * @package core
 */

namespace app\core\utils;

class Router {
    
    /**
     * @var array Array contendo todas as rotas configuradas
     */
    private $routes  = [];
    
    /**
     * Construtor da classe
     *
     * Carrega as rotas a partir de um arquivo JSON.
     *
     * @param string $routersJSonFileName Caminho para o arquivo JSON com as rotas normalmente app/etc/routes.json [
     * {
     *	"home"		: 	{ 
     *		"path" : "app/views/home.php",
     *		"sanitize" : {"requestVars" : true, "code" : true, "sql" : true}
     *	},
     *	"about"		:	{
     *		"path" : "app/views/about.php",
     *		"sanitize" : {"requestVars" : true, "code" : true, "sql" : true}
     *	},
     *	"contact"	:	{
     *		"path" : "app/views/contact.php",
     *		"sanitize" : {"requestVars" : true, "code" : true, "sql" : true}
     *	},
     * 	"404"		:	{
     * 		"path" : "app/views/error404.php",
     * 		"sanitize" : {"requestVars" : true, "code" : true, "sql" : true}
     *  }
     * }
     * 
     * 
     * 
     * 
     */
    public function __construct($routersJSonFileName) {
        global $rootPath;
        $json 	= file_get_contents($routersJSonFileName);
        $routes = json_decode($json, true);
        
        foreach ($routes as $path => $realPath) {
            $this->addRoute($path,  $rootPath."/".$realPath);
        }
    }
    
    /**
     * Adiciona uma rota
     *
     * @param string $requiredPath Caminho requisitado
     * @param string $physicalPath Caminho físico para o arquivo
     */
    public function addRoute($requiredPath, $physicalPath) {
        $this->routes[$requiredPath] = $physicalPath;
    }
    
    /**
     * Executa o roteamento
     *
     * Busca a rota requisitada e direciona para o arquivo correspondente, sanitizando as requisições HTTP
     *
     * @param string $module Módulo a ser acessado (opcional)
     * @param array $params Parâmetros da requisição (opcional)
     */
    public function dispatch($module = null, $params = null) {
        
        global $folderBase;
        
        if ($module == null) {
            if ($folderBase == "") {
                $splitUri = array_filter(explode("/", $_SERVER['REQUEST_URI']));
                $module = reset($splitUri);
                $params = array_slice($splitUri, 1);
                $module = reset($splitUri);
                $params = array_slice($splitUri, 1);
            } else {
                $splitUri = array_filter(explode("/", $_SERVER['REQUEST_URI']));
                $splitUri = array_slice($splitUri, 1);
                $params = array_slice($splitUri, 1);
                $module = reset($splitUri);
                $params = array_slice($splitUri, 1);
            }
        }
        
        $module = strtolower($module);
        
        if (isset($this->routes[$module]) && file_exists($this->routes[$module]['path'])){
            
            $sanitize = new Sanitize(
                $this->routes[$module]['sanitize']['requestVars'],
                $this->routes[$module]['sanitize']['code'],
                $this->routes[$module]['sanitize']['sql']
                );
            $sanitize->clearRequestHttp();
            
            require $this->routes[$module]['path'];
        } else {
            $this->notFound();
        }
    }
    
    /**
     * Método para lidar com rotas não encontradas
     *
     * Redireciona para uma página 404 personalizada quando uma rota não é encontrada.
     */
    public function notFound() {
        if (isset($this->routes['404']) && file_exists($this->routes['404']['path'])) {
            require $this->routes['404']['path'];
        } else {
            header('HTTP/1.0 404 Not Found');
            echo "404 - Page not found.";
        }
    }
}
?>
