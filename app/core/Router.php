<?php
// Je vais créer les routes /product/... j'ai donc besoin
// de controleur ProductController
require_once(__DIR__."/../controllers/ProductController.php");
require_once(__DIR__."/../controllers/HomeController.php");
require_once(__DIR__."/../controllers/NotFoundController.php");

class Router{
    public static function getController(string $controllerName){
        switch ($controllerName) {
            // Route : /product
            case 'product':
                return new ProductController();

            // Route : /
            case '':
                return new HomeController();
            
            default:
                // Si aucune route de match
                return new NotFoundController();
        }
    }
}