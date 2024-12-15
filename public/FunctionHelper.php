<?php 
namespace FunctionHelper;
class FunctionHelper{

    function dd_vars($value){
    echo "<pre>";
    var_dump($value);
    echo "</pre>";
        return 0;
    }
}

/* function urlIs($value){
    return $_SERVER["REQUEST_URI"] === $value;
} */

?>