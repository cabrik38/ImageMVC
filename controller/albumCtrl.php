<?php

require_once("model/album.php");
require_once("model/albumDAO.php");

class AlbumCtrl {

    private $albDAO;

    public function __construct() {
        $this->albDAO = new AlbumDAO();
    }

    private function getData(array $albums = null, Album $album = null) {

        if(!empty($albums) || (empty($albums) && $album == null)) {
            $data["menu"]['Home'] = "index.php";
            $data["menu"]['A propos'] = "index.php?controller=home&action=aproposAction";
            $data["menu"]['Add album'] = "index.php?controller=albumCtrl&action=editAction";
        }
        if(!empty($albums)) {
            foreach($albums as $key => $value) {        
                $data["alb"][$key]= $value;
            }
        }       
        if($album != null) {
            $data["alb"][0] = $album;
        }
        
        return $data;
        // catégorie
        /*
        $category = $this->getCategoryQuery();
        $data["selectedCategory"] = $category;
        $categories = $this->albDAO->getCategorieList();
        if ($category != null) {
            unset($categories[array_search($category, $categories)]);
        }
        $data["availableCategories"] = $categories;

        // menu
        $imgId = $imgs[array_keys($imgs)[0]]->getId();

        $urlCategory = urlencode($category);

        $nbImgBis = $nbImg * 2;
        $nbImgTer = $nbImg / 2;
        if ($nbImgTer < 1) {
            $nbImgTer = 1;
        }
        */
        
    }

    /**
     * Récupère la catégorie dans la query string
     * 
     * @return string La catégorie ou null
     */
    /*
    private function getCategoryQuery(): string {
        // Récupération des catégories disponibles
        $categories = $this->albDAO->getCategorieList();

        $category = "";

        if (isset($_GET["category"]) && in_array($_GET["category"], $categories)) {
            // Si il y a une catégorie et qu'elle est valide
            $category = $_GET["category"];
        }

        return $category;
    }
    */
    public function indexAction() {
        $this->showAlbumsAction();
    }

    public function showAlbumsAction() {
        $albums = $this->albDAO->getAllAlbums();
        $data = $this->getData($albums);
        $data["view"] = "albumView.php";
            
        require_once("view/mainView.php");
    }

    /**
     * Permet d'éditer ou de créer un nouvel album
     */
    public function editAction() {
        if (isset($_GET["albId"]) && is_numeric($_GET["albId"])) {
            $albId = $_GET["albId"];
            $album = $this->albDAO->getAlbum($albId);

            $data = $this->getData($album);

            $data["menu"] = [];
            $data["menu"]['Save'] = "index.php?controller=albumCtrl&action=saveAction&albId=$albId";
            $data["menu"]['Cancel'] = "index.php?controller=albumCtrl&action=cancelAction&albId=$albId";
        } else {
            // Pas d'image, tout a vide (création d'image)
            $data["alb"] = new Album("", "");

            $data["menu"]['Save'] = "index.php?controller=albumCtrl&action=saveAction";
            $data["menu"]['Cancel'] = "index.php?controller=albumCtrl&action=cancelAction";
        }

        $data["view"] = "albumEditView.php";

        require_once("view/mainView.php");
    }
    
    /**
     * Sauvegarder les modifications ou crée une nouvelle image
     */
    public function saveAction() {
        if (isset($_GET["albId"]) && is_numeric($_GET["albId"])) {
            $albId = $_GET["albId"];
            $album = $this->albDAO->getAlbum($albId);
        }
        else {     
            // Pas d'album, en créer un
            $album = new Album("", "");
        }
        if (isset($_POST["name"]) && $_POST["name"] != "") {
            $album->setName($_POST["name"]);
        }
        else {
            $error = "nameRequired";
            return header("Location: index.php?controller=albumCtrl&action=editAction&error=$error");
        }
        if (isset($_POST["description"])) {
            $album->setDescription($_POST["description"]);
        }
            
        $this->albDAO->saveAlbum($album);
        $this->showAlbumsAction();
    }
    
    /**
     * Revenir du mode édit à vue
     */
    public function cancelAction() {
        if (isset($_GET["albId"]) && is_numeric($_GET["albId"])) {
            $albId = $_GET["albId"];
            $album = $this->albDAO->getAlbum($albId);
            $data = $this->getData($album);
            $data["view"] = "photoMatrixView.php";
        }
        else {
            // Pas d'image, se positionne sur la première
            $albums = $this->albDAO->getAllAlbums();
            $data = $this->getData($albums);
            $data["view"] = "albumView.php";
        }

        require_once("view/mainView.php");
    }
    
    /*
    public function RandomAction() {
        $img = $this->albDAO->getRandomImage($this->getCategoryQuery());

        if (isset($_GET["nbImg"]) && is_numeric($_GET["nbImg"])) {
            $nbImg = $_GET["nbImg"];
        } else {
            $nbImg = 2;
        }

        $imgs = $this->albDAO->getImageList($img, $nbImg, $this->getCategoryQuery());


        $data = $this->getData($imgs, $nbImg);
        $data["view"] = "photoMatrixView.php";

        require_once("view/mainView.php");
    }

    public function firstAction() {
        $img = $this->albDAO->getFirstImage($this->getCategoryQuery());

        if (isset($_GET["nbImg"]) && is_numeric($_GET["nbImg"])) {
            $nbImg = $_GET["nbImg"];
        } else {
            $nbImg = 2;
        }

        $imgs = $this->albDAO->getImageList($img, $nbImg, $this->getCategoryQuery());

        $data = $this->getData($imgs, $nbImg);
        $data["view"] = "photoMatrixView.php";

        require_once("view/mainView.php");
    }

    public function nextAction() {
        if (isset($_GET["imgId"]) && is_numeric($_GET["imgId"])) {
            $imgId = $_GET["imgId"];
            $img = $this->albDAO->getImage($imgId);
        } else {
            // Pas d'image, se positionne sur la première
            $img = $this->albDAO->getFirstImage($this->getCategoryQuery());
        }

        if (isset($_GET["nbImg"]) && is_numeric($_GET["nbImg"])) {
            $nbImg = $_GET["nbImg"];
        } else {
            $nbImg = 2;
        }

        $img = $this->albDAO->jumpToImage($img, $nbImg, $this->getCategoryQuery());

        $imgs = $this->albDAO->getImageList($img, $nbImg, $this->getCategoryQuery());

        $data = $this->getData($imgs, $nbImg);
        $data["view"] = "photoMatrixView.php";

        require_once("view/mainView.php");
    }

    public function prevAction() {
        if (isset($_GET["imgId"]) && is_numeric($_GET["imgId"])) {
            $imgId = $_GET["imgId"];
            $img = $this->albDAO->getImage($imgId);
        } else {
            // Pas d'image, se positionne sur la première
            $img = $this->albDAO->getFirstImage($this->getCategoryQuery());
        }

        if (isset($_GET["nbImg"]) && is_numeric($_GET["nbImg"])) {
            $nbImg = $_GET["nbImg"];
        } else {
            $nbImg = 2;
        }

        $img = $this->albDAO->jumpToImage($img, -$nbImg, $this->getCategoryQuery());

        $imgs = $this->albDAO->getImageList($img, $nbImg, $this->getCategoryQuery());

        $data = $this->getData($imgs, $nbImg);
        $data["view"] = "photoMatrixView.php";

        require_once("view/mainView.php");
    }
     
     */


}
