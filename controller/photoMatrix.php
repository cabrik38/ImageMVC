<?php

require_once("model/image.php");
require_once("model/imageDAO.php");

class PhotoMatrix {

	private $imgDAO;

	public function __construct(){
		$this->imgDAO = new ImageDAO();
	}

	private function getData(array $imgs, int $nbImg = null) : array{

		if ($nbImg == null){
			// Si il n'y a pas de taille demandé, on garde la meme taille ou celle par defaut
			if (isset($_GET["nbImg"]) && is_numeric($_GET["nbImg"])) {
				$nbImg = $_GET["nbImg"];
			} else {
				$nbImg = 2;
			}
		}

		foreach ($imgs as $key => $value) {
			$data["img"][$key]["imgUrl"] = $value->getURL();
			$data["img"][$key]["imgId"] = $value->getId();
			$data["img"][$key]["imgComment"] = $value->getComment();
			$data["img"][$key]["imgCategory"] = $value->getCategory();
		}
		
		// catégorie
		$category = $this->getCategoryQuery();
		$data["selectedCategory"] = $category;
		$categories = $this->imgDAO->getCategorieList();
		if ($category != null){
			unset($categories[array_search($category, $categories)]);
		}
		$data["availableCategories"] = $categories;

		// menu
		$imgId = $imgs[array_keys($imgs)[0]]->getId();
		
		$urlCategory = urlencode($category);

		$nbImgBis = $nbImg*2;
		$nbImgTer = $nbImg/2;
		if ($nbImgTer < 1) {
			$nbImgTer = 1;
		}

		$data["menu"]['Home'] = "index.php";
		$data["menu"]['A propos'] = "index.php?controller=home&action=aproposAction";
		$data["menu"]['First'] = "index.php?controller=photoMatrix&action=firstAction&nbImg=$nbImg&category=$urlCategory";
		$data["menu"]['Random'] = "index.php?controller=photoMatrix&action=randomAction&nbImg=$nbImg&category=$urlCategory";
		$data["menu"]['More'] = "index.php?controller=photoMatrix&imgId=$imgId&nbImg=$nbImgBis&category=$urlCategory";
		$data["menu"]['Less'] = "index.php?controller=photoMatrix&imgId=$imgId&nbImg=$nbImgTer&category=$urlCategory";

		return $data;
	}
	
	/**
	 * Récupère la catégorie dans la query string
	 * 
	 * @return string La catégorie ou null
	 */
	private function getCategoryQuery() : string {
		// Récupération des catégories disponibles
		$categories = $this->imgDAO->getCategorieList();
		
		$category = "";
		
		if (isset($_GET["category"]) && in_array($_GET["category"], $categories)) {
			// Si il y a une catégorie et qu'elle est valide
			$category = $_GET["category"];
		}
		
		return $category;
	}

	public function indexAction() {
		$this->showAction();
	}

	public function showAction() {
		if (isset($_GET["imgId"]) && is_numeric($_GET["imgId"])) {
			$imgId = $_GET["imgId"];
			$img = $this->imgDAO->getImage($imgId);
		} else {
			// Pas d'image, se positionne sur la première
			$img = $this->imgDAO->getFirstImage($this->getCategoryQuery());
		}

		if (isset($_GET["nbImg"]) && is_numeric($_GET["nbImg"])) {
			$nbImg = $_GET["nbImg"];
		} else {
			$nbImg = 2;
		}

		$imgs = $this->imgDAO->getImageList($img, $nbImg, $this->getCategoryQuery());


		$data = $this->getData($imgs, $nbImg);
		$data["view"] = "photoMatrixView.php";

		require_once("view/mainView.php");
	}

	public function RandomAction() {
			$img = $this->imgDAO->getRandomImage($this->getCategoryQuery());

		if (isset($_GET["nbImg"]) && is_numeric($_GET["nbImg"])) {
			$nbImg = $_GET["nbImg"];
		} else {
			$nbImg = 2;
		}

		$imgs = $this->imgDAO->getImageList($img, $nbImg, $this->getCategoryQuery());


		$data = $this->getData($imgs, $nbImg);
		$data["view"] = "photoMatrixView.php";

		require_once("view/mainView.php");
	}

	public function firstAction() {
		$img = $this->imgDAO->getFirstImage($this->getCategoryQuery());

		if (isset($_GET["nbImg"]) && is_numeric($_GET["nbImg"])) {
			$nbImg = $_GET["nbImg"];
		} else {
			$nbImg = 2;
		}

		$imgs = $this->imgDAO->getImageList($img, $nbImg, $this->getCategoryQuery());

		$data = $this->getData($imgs, $nbImg);
		$data["view"] = "photoMatrixView.php";

		require_once("view/mainView.php");
	}

	public function nextAction() {
		if (isset($_GET["imgId"]) && is_numeric($_GET["imgId"])) {
			$imgId = $_GET["imgId"];
			$img = $this->imgDAO->getImage($imgId);
		
		} else {
			// Pas d'image, se positionne sur la première
			$img = $this->imgDAO->getFirstImage($this->getCategoryQuery());
		}
		
		if (isset($_GET["nbImg"]) && is_numeric($_GET["nbImg"])) {
			$nbImg = $_GET["nbImg"];
		} else {
			$nbImg = 2;
		}
		
		$img = $this->imgDAO->jumpToImage($img, $nbImg, $this->getCategoryQuery());

		$imgs = $this->imgDAO->getImageList($img, $nbImg, $this->getCategoryQuery());

		$data = $this->getData($imgs, $nbImg);
		$data["view"] = "photoMatrixView.php";

		require_once("view/mainView.php");
	}

	public function prevAction() {
		if (isset($_GET["imgId"]) && is_numeric($_GET["imgId"])) {
			$imgId = $_GET["imgId"];
			$img = $this->imgDAO->getImage($imgId);
		} else {
			// Pas d'image, se positionne sur la première
			$img = $this->imgDAO->getFirstImage($this->getCategoryQuery());
		}

		if (isset($_GET["nbImg"]) && is_numeric($_GET["nbImg"])) {
			$nbImg = $_GET["nbImg"];
		} else {
			$nbImg = 2;
		}
		
		$img = $this->imgDAO->jumpToImage($img, -$nbImg, $this->getCategoryQuery());

		$imgs = $this->imgDAO->getImageList($img, $nbImg, $this->getCategoryQuery());

		$data = $this->getData($imgs, $nbImg);
		$data["view"] = "photoMatrixView.php";

		require_once("view/mainView.php");
	}

	/**
	 * Affiche la première image d'une catégorie
	 */
	public function categoryAction(){
		$this->firstAction();
	}
}
