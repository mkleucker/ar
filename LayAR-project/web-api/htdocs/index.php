<?php
/**
 * Step 1: Require the Slim Framework
 *
 * If you are not using Composer, you need to require the
 * Slim Framework and register its PSR-0 autoloader.
 *
 * If you are using Composer, you can skip this step.
 */
require 'Slim/Slim.php';

\Slim\Slim::registerAutoloader();

$app = new \Slim\Slim(aray('debug'=>true));

$app->get('/poi', 'getPoiAll');
$app->get('/poi/:id',  'getPoi');

$app->run();

function getPoiAll(){
    $sql = "SELECT * FROM poi ORDER BY id DESC";

    try{
        $db = getConnection();
        $stmt = $db->prepare($sql);
        $stmt->execute();
        $pois = $stmt->fetchAll(PDO::FETCH_OBJ);
        $db = null;
        echo '{"pois": '. json_encode($pois).'}';
    } catch(PDOException $e){
        echo '{"error":{"text":'. $e->getMessage() .'}}';

    }

}

function getConnection() {
    include('config.php');
    $dbh = new PDO("mysql:host=$dbhost;dbname=$dbname", $dbuser, $dbpass);
    $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    return $dbh;
}
