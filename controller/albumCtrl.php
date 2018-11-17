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
            $data["menu"]['Voir photos'] = "index.php?controller=photo&action=indexAction";
            $data["menu"]['Add album'] = "index.php?controller=albumCtrl&action=editAction";
        }
        if(!empty($albums)) {
            foreach($albums as $key => $value) {        
                $data["alb"][$key]= $value;
            }
        }       
        if($album != null) {
            $data["menu"]['Home'] = "index.php";
            $data["menu"]['A propos'] = "index.php?controller=home&action=aproposAction";
            $data["menu"]['Voir photos'] = "index.php?controller=photo&action=indexAction";
            $data["menu"]['Voir albums'] = "index.php?controller=albumCtrl&action=indexAction";
            $data["menu"]['Edit album'] = "index.php?controller=albumCtrl&action=editAction&albId=".$album->getId();
            $data["alb"]= $album;
        }
        
        return $data;     
    }

    public function indexAction() {
        $this->showAlbumsAction();
    }

    public function showAlbumsAction() {
        $albums = $this->albDAO->getAllAlbums();
        $data = $this->getData($albums);
        $data["view"] = "albumView.php";
            
        require_once("view/mainView.php");
    }
    
    public function viewAlbumAction() {
        if (isset($_GET["albId"]) && is_numeric($_GET["albId"])) {
            $albId = $_GET["albId"];
            $data["alb"] = $this->albDAO->getAlbum($albId);
            if($data["alb"] != false) {
                $data = $this->getData(null, $data["alb"]);
                $data["imgs"] = $this->albDAO->getImagesList($data["alb"]);
            }
            $data["view"] = "photoAlbumView.php";
            require_once("view/mainView.php");
        }
    }

    /**
     * Permet d'éditer ou de créer un nouvel album
     */
    public function editAction() {
        if (isset($_GET["albId"]) && is_numeric($_GET["albId"])) {
            $albId = $_GET["albId"];
            $data["alb"] = $this->albDAO->getAlbum($albId);
            if($data["alb"] != false) {
                $data = $this->getData(null, $data["alb"]);
                $data["imgs"] = $this->albDAO->getImagesList($data["alb"]);
            }
            $imageAlbumDAO = new ImageAlbumDAO();
            $data["positions"] = $imageAlbumDAO->getImagesPositions($albId);  
            $data["menu"] = [];
            $data["menu"]['Save'] = "index.php?controller=albumCtrl&action=saveAction&albId=$albId";
            $data["menu"]['Cancel'] = "index.php?controller=albumCtrl&action=cancelAction&albId=$albId";
        } else {
            // Pas d'album, tout a vide (création d'album)
            $data["alb"] = new Album("", "");
            $cancelImgId = "";
            $saveImgId = "";
            if(isset($_GET["imgId"]) && is_numeric($_GET["imgId"])) {
                $imgId = $_GET["imgId"]-1;
                $cancelImgId = "&imgId=" . $imgId;
                $imgId = $_GET["imgId"];
                $saveImgId = "&imgId=" . $imgId;
            }         
            $data["menu"]['Save'] = "index.php?controller=albumCtrl&action=saveAction$saveImgId";
            $data["menu"]['Cancel'] = "index.php?controller=albumCtrl&action=cancelAction$cancelImgId";
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
            if(isset($_GET["albId"]) && is_numeric($_GET["albId"])) {
                return header("Location: index.php?controller=albumCtrl&action=editAction&albId=".$_GET["albId"]."&error=$error");
            }
            else {
                return header("Location: index.php?controller=albumCtrl&action=editAction&error=$error");
            }
        }
        if (isset($_POST["description"])) {
            $album->setDescription($_POST["description"]);
        }
            
        $this->albDAO->saveAlbum($album);
        if(isset($_GET["imgId"]) && is_numeric($_GET["imgId"])) {
            $album = $this->albDAO->getLastAlbum();
            $imageAlbumDAO = new ImageAlbumDAO();
            $imageAlbumDAO->addImageToAlbum($_GET["imgId"], $album->getId());
            $imgId = $_GET["imgId"]-1;
            return header("Location: index.php?controller=photo&action=nextAction&imgId=$imgId");
        }
         if (isset($_GET["albId"]) && is_numeric($_GET["albId"])) {
            return header("Location: index.php?controller=albumCtrl&action=viewAlbumAction&albId=" . $_GET["albId"]);
        }
    }
    
    /**
     * Revenir du mode édit à vue
     */
    public function cancelAction() {
        if (isset($_GET["albId"]) && is_numeric($_GET["albId"])) {
            $this->viewAlbumAction();
        }
        else if(isset($_GET["imgId"]) && is_numeric($_GET["imgId"])) {
            return header("Location: index.php?controller=photo&action=nextAction&imgId=".$_GET["imgId"]);
        }
        else {
            // Pas d'image, se positionne sur la première
            $albums = $this->albDAO->getAllAlbums();
            $data = $this->getData($albums);
            $data["view"] = "albumView.php";
        }

        require_once("view/mainView.php");
    }
    
    /**
     * Permet d'éditer ou de créer un nouvel album
     */
    public function updatePosition() {
        if(isset($_GET["albId"]) && is_numeric($_GET["albId"])) {
            $imageAlbumDAO = new ImageAlbumDAO();
            $positions = $imageAlbumDAO->getImagesPositions($_GET["albId"]);
             if(isset($_GET["imgId"]) && is_numeric($_GET["imgId"])) {
                foreach($positions as $position) {
                    if($position["imgId"] == $_GET["imgId"]) {
                        $positionActuelle = $position["position"];
                    }
                }
             }
             if(isset($_GET["position"]) && is_numeric($_GET["position"])) {
                 if($_GET["position"] > $positionActuelle) {
                    foreach($positions as $position) {
                        if($position["position"] > $positionActuelle && $position["position"] <= $_GET["position"]) {
                            $imageAlbumDAO->updateImagePosition($_GET["albId"], $position["imgId"], $position["position"] -1);
                        }
                    }
                }
                if ($_GET["position"] < $positionActuelle) {
                    foreach($positions as $position) {
                        if($position["position"] < $positionActuelle && $position["position"] >= $_GET["position"]) {
                            $imageAlbumDAO->updateImagePosition($_GET["albId"], $position["imgId"], $position["position"] +1);
                        }
                    }
                }
                $imageAlbumDAO->updateImagePosition($_GET["albId"], $_GET["imgId"], $_GET["position"]);
            }
        return header("Location: index.php?controller=albumCtrl&action=editAction&albId=" . $_GET["albId"]);
        }

    }
 
}
