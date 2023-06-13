<?php

    echo " Zait Tiny Framework PHP ";
    
    // Criação da estrutura de diretórios
    mkdir(__DIR__ ."/../App/core/database1" , 0777, true);
    mkdir(__DIR__ ."/../App/core/utils"     , 0777, true);
    mkdir(__DIR__ ."/../App/uploads"        , 0777, true);
    mkdir(__DIR__ ."/../public/assets/js"   , 0777, true);
    mkdir(__DIR__ ."/../public/assets/css"  , 0777, true);
    mkdir(__DIR__ ."/../public/assets/images", 0777, true);
    
    // Criação dos arquivos
    touch(__DIR__ ."/../.htaccess");
    touch(__DIR__ ."/../index.php");
    touch(__DIR__ ."/../App/uploads/.htaccess");
    touch(__DIR__ ."/../App/core/database1/DBQuery.class.php");
    touch(__DIR__ ."/../App/core/database1/Where.class.php");
    touch(__DIR__ ."/../App/core/database1/DBConnection.class.php");
    touch(__DIR__ ."/../App/core/database1/DBQueryFromAnnotations.php");
    touch(__DIR__ ."/../App/core/utils/UploadFiles.php");
    touch(__DIR__ ."/../App/core/utils/UploadedCtrl.php");
    touch(__DIR__ ."/../App/core/utils/Base64Files.php");
    touch(__DIR__ ."/../App/core/utils/Mail.php");
    touch(__DIR__ ."/../App/core/utils/Sanitize.class.php");
    touch(__DIR__ ."/../App/core/utils/Router.class.php");
    touch(__DIR__ ."/../public/assets/js/script.js");
    touch(__DIR__ ."/../public/assets/css/style.css");
    touch(__DIR__ ."/../public/assets/images/logo.png");
    
    // Permissões de escrita nos diretórios de uploads
    chmod(__DIR__ ."/../App/uploads", 0777);
    chmod(__DIR__ ."/../App/uploads/.htaccess", 0777);
    
    echo "Estrutura de diretórios e arquivos criada com sucesso!";

?>