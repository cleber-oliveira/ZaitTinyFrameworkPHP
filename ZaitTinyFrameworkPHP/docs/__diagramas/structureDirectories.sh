#!/bin/bash

	echo " Zait Tiny Framework PHP "
	
	# Criação da estrutura de diretórios
	mkdir -p "../App/core/database1"
	mkdir -p "../App/core/utils"
	mkdir -p "../App/uploads"
	mkdir -p "../public/assets/js"
	mkdir -p "../public/assets/css"
	mkdir -p "../public/assets/images"
	
	# Criação dos arquivos
	touch "../.htaccess"
	touch "../index.php"
	touch "../App/uploads/.htaccess"
	touch "../App/core/database1/DBQuery.class.php"
	touch "../App/core/database1/Where.class.php"
	touch "../App/core/database1/DBConnection.class.php"
	touch "../App/core/database1/DBQueryFromAnnotations.php"
	touch "../App/core/utils/UploadFiles.php"
	touch "../App/core/utils/UploadedCtrl.php"
	touch "../App/core/utils/Base64Files.php"
	touch "../App/core/utils/Mail.php"
	touch "../App/core/utils/Sanitize.class.php"
	touch "../App/core/utils/Router.class.php"
	touch "../public/assets/js/script.js"
	touch "../public/assets/css/style.css"
	touch "../public/assets/images/logo.png"
	
	# Permissões de escrita nos diretórios de uploads
	chmod 755 "../App/uploads"
	chmod 755 "../App/uploads/.htaccess"
	
	echo "Estrutura de diretórios e arquivos criada com sucesso!"
