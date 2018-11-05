<?php

require_once("model/image.php");
require_once("model/imageDAO.php");

class Photo {

	private $imgDAO;

	public function __construct(){
		$this->imgDAO = new ImageDAO();
	}

	/**
	* Crée les données pour les vue (menu / information sur l'image)
	*
	* @param Image $img L'image à afficher
	* @param int $imgSize La taille de l'image à afficher
	*
	* @return array
	*/
	private function getData(Image $img, int $imgSize = null) : array{
		if ($imgSize == null){
			// Si il n'y a pas de taille demandé, on garde la meme taille ou celle par defaut
			if (isset($_GET["size"]) && is_numeric($_GET["size"])) {
				$imgSize = $_GET["size"];
			} else {
				$imgSize = DEFAULT_SIZE;
			}
		}
		$data["imgSize"] = $imgSize;

		$data["imgUrl"] = $img->getURL();
		$data["imgId"] = $img->getId();
		$data["imgComment"] = $img->getComment();
		$data["imgCategory"] = $img->getCategory();

		// catégorie
		$category = $this->getCategoryQuery();
		$data["selectedCategory"] = $category;
		$categories = $this->imgDAO->getCategorieList();
		if ($category != null){
			unset($categories[array_search($category, $categories)]);
		}
		$data["availableCategories"] = $categories;

		// menu
		$imgId = $img->getId();

		$urlCategory = urlencode($category);

		$data["menu"]['Home'] = "index.php";
		$data["menu"]['A propos'] = "index.php?controller=home&action=aproposAction";
		$data["menu"]['First'] = "index.php?controller=photo&action=firstAction&size=$imgSize&category=$urlCategory";
		$data["menu"]['Random'] = "index.php?controller=photo&action=randomAction&size=$imgSize&category=$urlCategory";
		$data["menu"]['More'] = "index.php?controller=photoMatrix&imgId=$imgId&nbImg=2&category=$urlCategory";
		$data["menu"]['Zoom +'] = "index.php?controller=photo&action=zoomAction&imgId=$imgId&size=$imgSize&zoom=1.25&category=$urlCategory";
		$data["menu"]['Zoom -'] = "index.php?controller=photo&action=zoomAction&imgId=$imgId&size=$imgSize&zoom=0.75&category=$urlCategory";
		$data["menu"]['Edit'] = "index.php?controller=photo&action=editAction&imgId=$imgId&size=$imgSize&category=$urlCategory";
		$data["menu"]['Add image'] = "index.php?controller=photo&action=editAction";
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

	// -------------------------------------------------------------------------
	// Actions
	// -------------------------------------------------------------------------

	/**
	* Action par defaut
	* Afficher la première image
	*/
	public function indexAction(){
		$this->firstAction();
	}

	/**
	* Afficher la première image
	*/
	public function firstAction(){
		$img = $this->imgDAO->getFirstImage($this->getCategoryQuery());
		$data = $this->getData($img);

		$data["view"] = "photoView.php";

		require_once("view/mainView.php");
	}

	/**
	* Afficher l'image suivante
	*/
	public function nextAction(){
		// Récupération de l'image
		if (isset($_GET["imgId"]) && is_numeric($_GET["imgId"])) {
			$imgId = $_GET["imgId"];
			$img = $this->imgDAO->getNextImage($this->imgDAO->getImage($imgId), $this->getCategoryQuery());
		} else {
			// Pas d'image, se positionne sur la première
			$img = $this->imgDAO->getFirstImage($this->getCategoryQuery());
		}

		$data = $this->getData($img);

		$data["view"] = "photoView.php";

		require_once("view/mainView.php");
	}

	/**
	* Afficher l'image précédente
	*/
	public function prevAction(){
		// Récupération de l'image
		if (isset($_GET["imgId"]) && is_numeric($_GET["imgId"])) {
			$imgId = $_GET["imgId"];
			$img = $this->imgDAO->getPrevImage($this->imgDAO->getImage($imgId), $this->getCategoryQuery());
		} else {
			// Pas d'image, se positionne sur la première
			$img = $this->imgDAO->getFirstImage($this->getCategoryQuery());
		}

		$data = $this->getData($img);

		$data["view"] = "photoView.php";

		require_once("view/mainView.php");
	}

	/**
	* Afficher une image aléatoire
	*/
	public function randomAction(){
		$img = $this->imgDAO->getRandomImage($this->getCategoryQuery());

		$data = $this->getData($img);

		$data["view"] = "photoView.php";

		require_once("view/mainView.php");
	}

	/**
	* Afficher l'image avec un zoom
	*/
	public function zoomAction(){
		// Récupération de l'image
		if (isset($_GET["imgId"]) && is_numeric($_GET["imgId"])) {
			$imgId = $_GET["imgId"];
			$img = $this->imgDAO->getImage($imgId);
		} else {
			// Pas d'image, se positionne sur la première
			$img = $this->imgDAO->getFirstImage($this->getCategoryQuery());
		}

		// Récupération de la taille actuelle
		if (isset($_GET["size"]) && is_numeric($_GET["size"])) {
			$imgSize = $_GET["size"];
		} else {
			$imgSize = DEFAULT_SIZE;
		}

		// Transformation de la taille avec le zoom demandé
		if (isset($_GET["zoom"]) && is_numeric($_GET["zoom"])) {
			$imgSize = round($imgSize * $_GET["zoom"]);
		}

		$data = $this->getData($img, $imgSize);

		$data["view"] = "photoView.php";

		require_once("view/mainView.php");
	}

	/**
	* Affiche la première image d'une catégorie
	*/
	public function categoryAction(){
		$this->firstAction();
	}

	/**
	 * Permet d'éditer la catégorie et le commentaire d'une image
	 * ou de creer une nouvelle image
	 */
	public function editAction(){
		if (isset($_GET["imgId"]) && is_numeric($_GET["imgId"])) {
			$imgId = $_GET["imgId"];
			$img = $this->imgDAO->getImage($imgId);

			$data = $this->getData($img);

			$data["menu"] = [];
			$data["menu"]['Save'] = "index.php?controller=photo&action=saveAction&imgId=$imgId&size=".$data["imgSize"]."&category=".urlencode($data["selectedCategory"]);
			$data["menu"]['Cancel'] = "index.php?controller=photo&action=cancelAction&imgId=$imgId&size=".$data["imgSize"]."&category=".urlencode($data["selectedCategory"]);
		} else {
			// Pas d'image, tout a vide (création d'image)
			$data["imgUrl"] = "";
			$data["imgComment"] = "";
			$data["imgCategory"] = "";

			$data["menu"]['Save'] = "index.php?controller=photo&action=saveAction";
			$data["menu"]['Cancel'] = "index.php?controller=photo&action=cancelAction";
		}

		$data["view"] = "photoEditView.php";

		require_once("view/mainView.php");
	}

	/**
	* Revenir du mode édit à vue
	*/
	public function cancelAction(){
		if (isset($_GET["imgId"]) && is_numeric($_GET["imgId"])) {
			$imgId = $_GET["imgId"];
			$img = $this->imgDAO->getImage($imgId);
		} else {
			// Pas d'image, se positionne sur la première
			$img = $this->imgDAO->getFirstImage($this->getCategoryQuery());
		}

		$data = $this->getData($img);

		$data["view"] = "photoView.php";

		require_once("view/mainView.php");
	}

	/**
	* Sauvegarder les modifications ou crée une nouvelle image
	*/
	public function saveAction(){
		if (isset($_GET["imgId"]) && is_numeric($_GET["imgId"])) {
			$imgId = $_GET["imgId"];
			$img = $this->imgDAO->getImage($imgId);
		} else {
			// Pas d'image, en créer une

			$error = null;
			if (isset($_FILES['image'])){
				// si une image a été uploadé

				if (!$_FILES['image']['error'] > 0){
					// si il n'y a pas eu d'erreur

					$extension_upload = strtolower(substr(strrchr($_FILES['image']['name'], '.'), 1));
					if (in_array($extension_upload, EXTENSIONS)){
						// si le fichier est une image

						$projectRootPath = __DIR__."/../";

						// création du dossier 'upload'
						@mkdir($projectRootPath.URL_PATH."/upload", 0777);

						$imgName = md5(uniqid(rand(), true));
						$imgPath = URL_PATH."/upload/".$imgName.".".$extension_upload;
						$resultat = move_uploaded_file($_FILES['image']['tmp_name'], $projectRootPath.$imgPath);
						if ($resultat){
							// si réussi

							// récupération du nouveau id
							$id = $this->imgDAO->size() + 1;

							$img = new Image($imgPath, $id, "", "");
						}else{
							$error = "La fonction move_uploaded_file a échoué (problème de droit ?)";
						}
					}else{
						$error = "L'éxtension du fichier n'est pas valide.";
					}
				}else{
					$errorName = "";
					switch ($_FILES['image']['error']){
						case UPLOAD_ERR_NO_FILE:
						$errorName = "Fichier manquant.";
						break;
						case UPLOAD_ERR_INI_SIZE:
						$errorName = "Fichier dépassant la taille maximale autorisée par PHP.";
						break;
						case UPLOAD_ERR_FORM_SIZE:
						$errorName = "Fichier dépassant la taille maximale autorisée par le formulaire.";
						break;
						case UPLOAD_ERR_PARTIAL:
						$errorName = "Fichier transféré partiellement.";
						break;
					}

					$error = "Erreur php : ".$errorName;
				}
			}else{
				$error = "Une image doit être séléctionné !";
			}

			if ($error !== null){
				// si erreur
				die("<b>### Erreur : La création de l'image a échoué : </b>$error");
			}
		}

		// Save category
		if (isset($_POST["category"]) && $_POST["category"] !== $img->getCategory()){
			$img->setCategory($_POST["category"]);
		}

		// Save comment
		if (isset($_POST["comment"]) && $_POST["comment"] !== $img->getComment()){
			$img->setComment($_POST["comment"]);
		}

		$this->imgDAO->saveImage($img);

		$data = $this->getData($img);

		$data["view"] = "photoView.php";

		require_once("view/mainView.php");
	}


	/**
	* Permet d'enregistrer un j'aime
	*/
	public function likeAction(){
		if (isset($_GET["imgId"]) && is_numeric($_GET["imgId"])) {
			$img = $this->imgDAO->getImage($_GET["imgId"]);
			$data = $this->getData($img);
			$data["view"] = "photoView.php";

			require_once("view/mainView.php");
		}
	}
}
