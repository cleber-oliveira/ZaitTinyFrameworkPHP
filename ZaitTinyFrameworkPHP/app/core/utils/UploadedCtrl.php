<?php
namespace app\core\utils;

use app\core\database\DBQuery;
use app\core\database\Where;
use Exception;

class UploadedCtrl {
    private $dbQuery;

    public function __construct() {
        $this->dbQuery = new DBQuery('ctrlFiles', 'owner, filePath, accessCtrl, shareMails', 'filePath');
    }

    /**
     * Registra um novo arquivo.
     * @param string $owner O dono do arquivo.
     * @param string $filePath O caminho do arquivo.
     * @param int $accessCtrl O controle de acesso do arquivo (0 para público, 1 para privado).
     * @param string $shareMails A lista de emails que podem acessar o arquivo.
     * @return string Retorna o nome do arquivo.
     */
    public function registerUploadedfile($owner, $filePath, $accessCtrl, $shareMails) {
        $values = [$owner, $filePath, $accessCtrl, $shareMails];
        $this->dbQuery->insert($values);
        return $filePath;
    }

    /**
     * Adiciona emails à lista de compartilhamento do arquivo.
     * @param string $filePath O caminho do arquivo.
     * @param string $shareMails A lista de emails a serem adicionados.
     * @return void
     */
    public function addShareFile($filePath, $shareMails) {
        $file = $this->dbQuery->selectFiltered(new Where('filePath', '=', $filePath))->fetch();
        $file['shareMails'] .= ",{$shareMails}";
        $this->dbQuery->update($file);
    }

    /**
     * Remove emails da lista de compartilhamento do arquivo.
     * @param string $filePath O caminho do arquivo.
     * @param string $shareMails A lista de emails a serem removidos.
     * @return void
     */
    public function delShareFile($filePath, $shareMails) {
        $file = $this->dbQuery->selectFiltered(new Where('filePath', '=', $filePath))->fetch();
        $file['shareMails'] = str_replace($shareMails, '', $file['shareMails']);
        $this->dbQuery->update($file);
    }

    /**
     * Recupera um arquivo.
     * @param string $filePath O caminho do arquivo.
     * @return string Retorna o conteúdo do arquivo em base64.
     * @throws Exception Se o arquivo não puder ser acessado pelo usuário atual.
     */
    public function getFile($filePath) {
        $file = $this->dbQuery->selectFiltered(new Where('filePath', '=', $filePath))->fetch();
        if ($file['owner'] == $_SESSION['idUsuario'] || in_array($_SESSION['email'], explode(',', $file['shareMails']))) {
            $base64Files = new Base64Files();
            return $base64Files->fileToBase64($filePath);
        } else {
            throw new Exception('Acesso negado ao arquivo.');
        }
    }
}
?>
