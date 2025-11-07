<?php
    require_once(__DIR__."/../controllers/ProductController.php");
    require_once(__DIR__."/../models/ProductModel.php");
    require_once(__DIR__."/Router.php");


const ROOT_APP_PATH = "first_mvc";

class App{
    public static function start(){
        $uri = str_replace(ROOT_APP_PATH,"",$_SERVER["REQUEST_URI"]);

        $uri_elements = explode("/",$uri);

        $controllerName = isset($uri_elements[1])?$uri_elements[1]:"";
        $methodName = isset($uri_elements[2])?$uri_elements[2]:"";
        $params = array_splice($uri_elements,3);

        // Je récupère le controller
        $controller = Router::getController($controllerName);

        // Appel de la méthode view 
        // La méthode view va executer la méthode en fonction de l'url
        $controller->view($methodName,$params);
    }
}
    

