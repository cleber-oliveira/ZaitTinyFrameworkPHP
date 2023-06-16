<?php

/**
 * index.php
 * @package ZaitTinyFrameworkPHP
 * @author  Msc Cleber Silva de Oliveira, Yossef Zait
 * @version 0.0.0.1
 * @see     https://cleberoliveira.info
 * 
 * Este é o script principal de inicialização para a aplicação.
 * Ele define algumas variáveis úteis, lida com a configuração,
 * realiza o autoload de classes e inicia a roteamento da aplicação.
 *
 * As seguintes operações são realizadas, em ordem:
 *
 * 1. Importa a classe Router do namespace core.
 * 2. Define $rootPath como o diretório atual, que será usado posteriormente para resolver caminhos na aplicação.
 * 3. Define $folderBase como o caminho relativo entre o diretório raiz do servidor e o diretório atual.
 * 4. Inclui o arquivo de configuração principal, o autoloader de classes, e a classe Router.
 * 5. Se a sessão PHP ainda não foi iniciada, inicia uma.
 * 6. Define $routersFile como o caminho para o arquivo json que contém as rotas da aplicação.
 * 7. Cria uma nova instância da classe Router, passando o arquivo de rotas como argumento.
 * 8. Chama o método dispatch() na instância do Router para iniciar a roteamento da aplicação.
 *
 * Requisitos:
 * - PHP 7.4 ou superior e Mysql 8.1 ou superior; 
 * - A extensão json do PHP deve estar habilitada.
 * - O arquivo app/etc/config.php deve existir e ser válido.
 * - O autoloader em autoload.php deve estar funcionando corretamente.
 * - A classe Router deve estar definida em core/Router.class.php e ser carregável pelo autoloader.
 * - O arquivo de rotas em app/etc/routes.json deve existir e ser um JSON válido.
 * 
 * .htaccess: 
 * # Habilita o motor de redirecionamento
 * # Isso ativa o módulo de reescrita de URL do Apache, permitindo que você manipule URLs com o Apache.
 * RewriteEngine On
 * 
 * # Redireciona todas as requisições que não sejam para a pasta public para o index.php
 * # Esta linha configura uma condição para a regra de reescrita seguinte. Ela afirma que se a URI da requisição não começar com "/public", então a regra seguinte deve ser aplicada.
 * RewriteCond %{REQUEST_URI} !^/public
 * 
 * # Estas linhas configuram condições adicionais para a regra de reescrita seguinte. Elas afirmam que se o arquivo ou diretório da requisição não existir, então a regra seguinte deve ser aplicada.
 * RewriteCond %{REQUEST_FILENAME} !-f
 * RewriteCond %{REQUEST_FILENAME} !-d
 * 
 * # Esta é a regra de reescrita que será aplicada se todas as condições acima forem atendidas. Ela redireciona todas as requisições para "index.php".
 * RewriteRule ^(.*)$ index.php [L]
 * 
 * # Ignora as requisições para os arquivos de recursos estáticos (JS, CSS, imagens). Esta linha é uma condição que afirma que se a URI da requisição não terminar com qualquer um dos tipos de arquivo listados (imagens, CSS, JS), a regra de reescrita seguinte será aplicada.
 * RewriteCond %{REQUEST_URI} !\.(jpg|jpeg|png|gif|css|js)$
 * 
 * # Redireciona todas as requisições que sejam para a pasta app ou core para o index.php
 * # Estas linhas configuram condições adicionais para a regra de reescrita seguinte. Elas afirmam que se o arquivo ou diretório da requisição não existir, então a regra seguinte deve ser aplicada.
 * RewriteCond %{REQUEST_FILENAME} !-f
 * RewriteCond %{REQUEST_FILENAME} !-d
 * # Esta é a regra de reescrita que será aplicada se todas as condições acima forem atendidas. Ela redireciona todas as requisições que começam com "app/" ou "core/" para "index.php".
 * RewriteRule ^(app|core)/(.*)$ index.php [L]
 * 
 * # Remove o index.php da URL
 * # Estas linhas configuram condições adicionais para a regra de reescrita seguinte. Elas afirmam que se o arquivo ou diretório da requisição não existir, então a regra seguinte deve ser aplicada.
 * RewriteCond %{REQUEST_FILENAME} !-f
 * RewriteCond %{REQUEST_FILENAME} !-d
 * 
 * # Esta é uma regra de reescrita que redireciona todas as requisições para "/index.php/URI", onde URI é a URI original da requisição. Isso é útil para remover "index.php" do URL.
 * RewriteRule ^(.*)$ /index.php/$1 [L]
 * 
 */
use app\core\utils\Router;

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

$rootPath = __DIR__;

require_once   $rootPath . "/app/etc/config.php";
require_once   $rootPath . "/autoload.php";
$routersFile = $rootPath . '/app/etc/routes.json';

$router = new Router($routersFile);
$router->dispatch();

?>
