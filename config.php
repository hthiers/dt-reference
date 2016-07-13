<?php
$config = Config::singleton();

$config->set('physicalPath', 'C:/xampp/htdocs');
$config->set('rootPath', '/som_v2/');
$config->set('controllersFolder', 'controllers/');
$config->set('modelsFolder', 'models/');
$config->set('viewsFolder', 'views/');

$config->set('dbhost', '127.0.0.1');
$config->set('dbname', 'lg_som_v2');
$config->set('dbuser', 'root');
$config->set('dbpass', '12345');

$config->set('timezone', 'America/Santiago');
$config->set('debug', false);
#$config->set('token', '3756a4505914c97f76b8557a688466a4');
?>