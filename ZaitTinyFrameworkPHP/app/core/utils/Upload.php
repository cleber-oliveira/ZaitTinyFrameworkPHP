<?php
namespace app\core\utils;

/**
 * O script para manipulação de upload de arquivo.
 * Converter o arquivo enviado para base64 e salva.
 * Registrar o arquivo no banco de dados.
 */


$uploadPathDir = __DIR__."/../../../"."app/uploads/";
$uploader = new UploadedCtrl();

/**
 * Checa se um arquivo foi enviado.
 * Se sim, ele manipula o upload e o registro do arquivo.
 */
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Checa se arquivo foi enviado
    if (!isset($_FILES['file'])) {
        echo "No file sent";
        exit;
    }

    $file = $_FILES['file'];
    $accessCtrl = $_POST['accessCtrl'] ?? 0; // Default para público se não definido
    $shareMails = $_POST['shareMails'] ?? ""; // Default para nenhum email se não definido

    $tempPath = $file['tmp_name'];
    $fileName = $file['name'];
    $fileExtension = pathinfo($fileName, PATHINFO_EXTENSION);
    $fileBase64Name = "up" . date('YmdHisv') . ".{$fileExtension}.base64";

    /**
     * Instancia a classe Base64Files.
     * Converter o arquivo para base64.
     * Salvar o arquivo base64 no diretório especificado.
     */
    $base64Files = new Base64Files();
    $fileContent = $base64Files->fileToBase64($tempPath);
    $base64Files->base64ToFile($fileContent, $uploadPathDir . $fileBase64Name);

    // Registrar o arquivo
    $owner = $_SESSION["idUsuario"];
    $filePath = $uploadPathDir . $fileBase64Name;
    $uploader->registerUploadedfile($owner, $filePath, $accessCtrl, $shareMails);

    // Retornar o nome do arquivo para o cliente
    echo $fileBase64Name;
}
?>
