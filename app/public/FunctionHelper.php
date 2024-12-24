<?php 
namespace app\public\FunctionHelper;

class FunctionHelper{

    public static function dd_vars($value){
    echo "<pre>";
    var_dump($value);
    echo "</pre>";
        return 0;
    }
    public static function getUri($type){
        return parse_url($_SERVER['REQUEST_URI'][$type]);
    }
    public static function getMethod(){
        return $_SERVER['REQUEST_METHOD'];
    }
}

/* function urlIs($value){
    return $_SERVER["REQUEST_URI"] === $value;
} */

?>