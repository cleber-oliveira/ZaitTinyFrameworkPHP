<?php

/**
 * config.php
 * @package ZaitTinyFrameworkPHP
 * @author  Msc Cleber Silva de Oliveira, Yossef Zait
 * @version 0.0.0.1
 * @see     https://cleberoliveira.info
 * 
 * Este é o arquivo de configuração principal para a aplicação.
 * Ele define uma série de variáveis de configuração que serão usadas em outras partes do aplicativo.
 *
 * As seguintes operações são realizadas, em ordem:
 *
 * 1. Define $rootPath como o diretório pai do diretório atual, que será usado posteriormente para resolver caminhos na aplicação.
 * 2. Define a variável $showErrors que controla se erros de PHP serão exibidos ou não.
 * 3. Se $showErrors for true, ajusta as configurações de exibição de erro do PHP.
 * 4. Define uma array associativa $config com as configurações da aplicação, incluindo:
 *     - Configuração do banco de dados
 *     - Configuração do email
 *     - Caminhos de alias para vários diretórios importantes na aplicação
 * 5. Armazena $config na variável de sessão $_SESSION['config'] para uso em outras partes da aplicação.
 *
 * Requisitos:
 * - PHP 7.4 ou superior.
 * - A extensão json do PHP deve estar habilitada.
 * - A sessão PHP deve estar iniciada antes de utilizar este arquivo.
 */


?>