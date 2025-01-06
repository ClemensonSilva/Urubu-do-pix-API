<?php
require_once "../vendor/autoload.php";
require_once "/app/routes/routes.php";
/*
var_dump(method_exists("App\Models\UserModel", "getUsers"));
 */

use App\Models\TransactionModel;
use App\Controllers\UserController;
/*

var_dump(TransactionModel::getTransactionInfo(20));
$transaction = new stdClass();
$transaction->user_id = 1;
$transaction->transaction_id = 2;
$transaction->valueToWithdraw = 2;
var_dump(TransactionModel::withdraw($transaction));
*/

try {
    $url = parse_url($_SERVER["REQUEST_URI"])["path"];
    $request = $_SERVER["REQUEST_METHOD"];
    if (!isset($router[$request])) {
        throw new Exception("A routa não existe");
    }
    if (!array_key_exists($url, $router[$request])) {
        throw new Exception("A routa não existe");
    }
    $controller = $router[$request][$url];
    $controller();
} catch (Exception $e) {
    $e->getMessage();
}

?>
