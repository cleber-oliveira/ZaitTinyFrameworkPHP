<?php

namespace app\core\utils;

class Base64Files {

    /**
     * Converte um arquivo em base64.
     * @param string $fileOrigem O caminho do arquivo a ser convertido.
     * @return string Retorna o conteúdo do arquivo em formato base64.
     */
    public function fileToBase64($fileOrigem) {
        $data = file_get_contents($fileOrigem);
        return base64_encode($data);
    }

    /**
     * Converte base64 de volta para arquivo.
     * @param string $fileBase64 O conteúdo em base64 a ser convertido para arquivo.
     * @param string $fileDestino O caminho do arquivo a ser criado.
     * @return void
     */
    public function base64ToFile($fileBase64, $fileDestino) {
        $data = base64_decode($fileBase64);
        file_put_contents($fileDestino, $data);
    }

}
?>
