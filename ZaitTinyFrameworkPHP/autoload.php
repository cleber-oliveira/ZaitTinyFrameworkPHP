<?php

/**
 * autoload.php
 * @package ZaitTinyFrameworkPHP
 * @author  Msc Cleber Silva de Oliveira, Yossef Zait
 * @version 0.0.0.1
 * @see     https://cleberoliveira.info
 * 
 *
 * Implementa um autoloader de classes para a aplicação PHP, em conformidade com a PSR-4.
 * Autoloading é o processo de carregar automaticamente as classes PHP quando são referenciadas pela primeira vez em um script.
 *
 * A função autoload neste arquivo é registrada como a função de autoloading usando spl_autoload_register.
 * Quando uma classe é referenciada e ainda não está definida, a função autoload é invocada pelo PHP.
 *
 * Este autoloader procura por arquivos com as extensões ".class.php" e ".php" em todos os diretórios dentro do diretório atual.
 *
 *
 * Este arquivo implementa um autoloader para a aplicação PHP. Autoloading é o processo de carregamento automático de classes PHP quando elas são referenciadas pela primeira vez em um script. Ele elimina a necessidade de incluir manualmente cada arquivo de classe com uma instrução 'require' ou 'include'.
 *
 * A função autoload dentro deste arquivo é registrada como uma função de autoloading usando spl_autoload_register. Quando uma classe é referenciada e ainda não está definida, a função autoload é invocada pelo PHP. Ela procura a classe em uma lista de diretórios e, se a encontrar, inclui o arquivo da classe automaticamente.
 *
 * Este autoloader está em conformidade com a PSR-4, uma recomendação do PHP Framework Interoperability Group (FIG) para a autoloading de classes. A PSR-4 especifica que as classes devem ser carregadas de forma "automática" de uma forma previsível e fácil de gerenciar.
 *
 * A PSR-4 tem uma grande importância, pois:
 *
 *     Consistência: Ao aderir à PSR-4, você mantém a consistência não apenas em seu código, mas em relação ao código PHP em todo o mundo. Isso facilita para outros desenvolvedores entenderem e trabalharem com o seu código.
 *
 *     Interoperabilidade: As normas PSR permitem que o código de diferentes projetos e autores funcione bem juntos. Isso pode tornar mais fácil a integração de bibliotecas de terceiros e outras dependências.
 *
 *     Eficiência: O autoloading pode tornar o código mais eficiente ao carregar automaticamente os arquivos de classe quando eles são necessários. Isso pode ser mais eficiente do que carregar todos os arquivos de classe no início de cada script.
 *
 * Assim, a utilização da PSR-4 facilita o gerenciamento das classes e arquivos na sua aplicação, tornando o código mais fácil de manter e mais eficiente.
 * 
 */

function autoload($className) { //  new Usuarios 
    
    $extensions = array(".class.php", ".php");
    $directories = glob(__DIR__ . '/*', GLOB_ONLYDIR);
    
    foreach ($extensions as $extension) {
        $filename = str_replace('\\', '/', $className) . $extension;  // Usuarios.class.php, Usuarios.php
        foreach ($directories as $directory) {
            $filePath = $directory . '/' . $filename;
            if (file_exists($filePath)) {
                require_once($filePath);
                return true;
            }
        }
    }
    return false;
}

spl_autoload_register('autoload');
// spl_autoload_register é acionada quando a classe ainda não estar definida no escopo atual.
?>
