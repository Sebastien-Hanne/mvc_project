<?php
class NotFoundController{
    public function view(string $method,array $params = []){
        try {
            call_user_func([$this,$method],$params);
        } catch (Error $e) {
            // method par default
            call_user_func([$this,"notfound"],$params);
        }
    }

    public function notfound($params = []){
        require_once(__DIR__."/../views/404.php");
    }
}