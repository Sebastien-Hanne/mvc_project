<?php
class HomeController{
    public function view(string $method,array $params = []){
        try {
            call_user_func([$this,$method],$params);
        } catch (Error $e) {
            // La route pour ce contrôleur est égale à "/"
            // Donc aucune method ne sera jamais trouvée
            // Donc par défaut on éxecute la methode home
            call_user_func([$this,"home"],$params);
        }
    }
    
    public function home($params = []){
        require_once(__DIR__."/../views/home.php");
    }
}