<?php
class ProductModel
{
    private $bdd;
    private PDOStatement $addProduct;
    private PDOStatement $delProduct;
    private PDOStatement $getProduct;
    private PDOStatement $getProducts;
    private PDOStatement $editProduct;

   function __construct()
{
    // Connection à la base de donnée
    // Note : Pensez à ajouter le troisième argument : array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION) pour une meilleure gestion des erreurs !
    $this->bdd = new PDO("mysql:host=bdd;dbname=app-database","root","root");
    
    // Création d'une requête préparée qui récupère tout les produits avec LIMIT pour pagination
    $this->getProducts = $this->bdd->prepare("SELECT * FROM `Produit` LIMIT :limit");
    
    // Codez ici :
    // Récupère un seul produit
    $this->getProduct = $this->bdd->prepare("SELECT * FROM `Produit` WHERE id = ?");
    
    // Ajout d'un produit
    $this->addProduct = $this->bdd->prepare("INSERT INTO `Produit` (id, name, price, image) VALUES (?, ?, ?, ?)");
    
    // Suppression d'un produit
    $this->delProduct = $this->bdd->prepare("DELETE FROM `Produit` WHERE id_produit = ?");
    
    // Modification d'un produit
    $this->editProduct = $this->bdd->prepare("UPDATE `Produit` SET id = ?, name = ?, price = ?, image = ? WHERE id_produit = ?");
}


   /**
     * Récupérer tout les produits
     * return array : Renvoi un array de ProductEntity
     * param int $limit : défini le nombre maximum d'Entity renvoyée, par défaut 50.
     * */
    public function getAll(int $limit = 50) : array
    {
        // Définir la valeur de LIMIT, par défault 50
        // LIMIT étant un INT ont n'oublie pas de préciser le type PDO::PARAM_INT.
        $this->getProducts->bindValue("limit",$limit,PDO::PARAM_INT);
        // Executer la requête
        $this->getProducts->execute();
        // Récupérer la réponse 
        $rawProducts = $this->getProducts->fetchAll();
        
        // Formater la réponse dans un tableau de ProductEntity
        $productsEntity = [];
        foreach($rawProducts as $rawProduct){
            $productsEntity[] = new ProductEntity(
                $rawProduct["name"],
                $rawProduct["price"],
                $rawProduct["image"],
                $rawProduct["id"]
            );
        }
        
        // Renvoyer le tableau de ProductEntity
        return $productsEntity;
    }

    /**
     * Recupérer un produit via son id.
     * return Une ProductEntity ou NULL si aucune ne correspond à l'$id
     * param int id : la clé primaire de l'entity demandée.
     * */
    public function get(int $id): ProductEntity | NULL
    {
        $this->getProduct->bindValue("id",$id,PDO::PARAM_INT);
        // Executer la requête
        $this->getProduct->execute([$id]);
        // Récupérer la réponse 
        $rawProduct = $this->getProduct->fetch();
        // Si aucun produit ne correspond à l'id, renvoyer NULL
        if(!$rawProduct){
            return NULL;
        }
        // Formater la réponse dans une ProductEntity
        return new ProductEntity(
            $rawProduct["name"],
            $rawProduct["price"],
            $rawProduct["image"],
            $rawProduct["id"]);
        // SELECT `id`, `name`, `price`, `image` FROM `Produit` WHERE = ?;
       
    }

    /**
     * Ajouter un produit
     * return void : ne renvoi rien
     * param les informations de l'entity
     * */
    public function add(string $name, float $price,string $image) : void
    {   
        $this->addProduct->bindValue("name",$name);
        $this->addProduct->bindValue("price",$price);
        $this->addProduct->bindValue("image",$image);
        // Executer la requête
        $this->addProduct->execute();
        
       // INSERT INTO `Produit`(`name`, `price`, `image`) VALUES '?', '?', '?';
    }

    /**
     * Supprime un produit via son id
     * return void : ne renvoi rien
     * param int $id : la clé primaire de l'entité à supprimer
     * */
    public function del(int $id) : void
    {
        $this->delProduct->bindValue("id",$id,PDO::PARAM_INT);
        // Executer la requête
        $this->delProduct->execute();
       
        // DELETE FROM `Produit` WHERE ?;
    }

    /**
     * Modifier un produit
     * return ProductEntity ou NULL : Le produit modifié après modification ou NULL si l'id n'existe pas.
     * param int $id l'identifiant du produit, ce paramètre ne défini pas la nouvelle valeur de l'id car un id SQL est immuable, mais permet de définir quelle produit modifier.
     * */
    public function edit(int $id,string $name = NULL,
    float $price = NULL, string $image = NULL) : ProductEntity | NULL
    {
          $originalProduct = $this->get($id);
          if(!$originalProduct){
            return NULL;
        }

        if ($name) {
            $this->editProduct->bindValue("name", $name);
        } else {
            $this->editProduct->bindValue("name", $originalProduct->getName());
        }

        if ($price) {
            $this->editProduct->bindValue("price", $price);
        } else {
            $this->editProduct->bindValue("price", $originalProduct->getPrice());
        }
        if ($image) {
            $this->editProduct->bindValue("image", $image);
        } else {
            $this->editProduct->bindValue("image", $originalProduct->getImage());
        }

        $this->editProduct->bindValue("id",$id,PDO::PARAM_INT);

        $this->editProduct->execute();

        // UPDATE `Produit` SET `id`='NULL]',`name`='[?]',`price`='[?]',`image`='[?]' WHERE = ?;
        return $this->get($id);
    }
}

class ProductEntity
{

    private $name;
    private $price;
    private $image;
    private $id;

    //getter
    public function getName(): string
    {
        return $this->name;
    }
    public function getPrice(): float
    {
        return $this->price;
    }
    public function getImage(): string
    {
        return $this->image;
    }
    public function getId(): int
    {
        return $this->id;
    }


    private const NAME_MIN_LENGTH = 3;
    private const PRICE_MIN = 0;
    private const DEFAULT_IMG_URL = "/public/images/default.png";

    //setter
    public function setName(string $name)
    {
        if (strlen($name) < $this::NAME_MIN_LENGTH) {
            throw new Error("Name is too short minimum 
            length is " . $this::NAME_MIN_LENGTH);
        }
        $this->name = $name;
    }
    public function setPrice(float $price)
    {
        if ($price < 0) {
            throw new Error("Price is too short minimum price is " . $this::PRICE_MIN);
        }
        $this->price = $price;
    }
    public function setImage(string $image)
    {
        if (strlen($image) <= 0) {
            $this->image = $this::DEFAULT_IMG_URL;
        }
        $this->image = $image;
    }
    //constructor
    function __construct(string $name, float $price, string $image, int $id = NULL)
    {
        $this->setName($name);
        $this->setPrice($price);
        $this->setImage($image);
        $this->id = $id;
    }
}
