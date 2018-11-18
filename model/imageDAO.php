<?php

require_once("image.php");

class ImageDAO {

    private $db;

    public function __construct() {
        try {
            $this->db = new PDO("mysql:host=" . SERVER_NAME . ";dbname=" . DATABASE_NAME, USERNAME, USER_PASSWORD);
            $this->db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            die("Erreur : " . $e->getMessage());
        }
    }

    /**
     * Retourne le nombre d'images référencées dans le DAO
     *
     * @return int
     */
    public function size(): int {
        return $this->db->query('SELECT count(*) FROM image')->fetch(PDO::FETCH_NUM)[0];
    }

    /**
     * Retourne un objet image correspondant à l'identifiant
     *
     * @param int $imgId
     *
     * @return Image
     */
    public function getImage(int $imgId): Image {
        # Verifie que cet identifiant est correct

        if (!($imgId >= 1 and $imgId <= $this->size())) {
            die("<H1>Erreur dans ImageDAO.getImage: imgId=$imgId incorrect</H1>");
        }

        $s = $this->db->prepare('SELECT * FROM image WHERE id=:id');
        $s->execute(array("id" => $imgId));

        if ($s) {
            $img = $s->fetch();
        } else {
            print "Error in getImage. id=" . $imgId . "<br/>";
            $err = $this->db->errorInfo();
            print $err[2] . "<br/>";
        }

        return new Image(URL_PATH . "/" . $img["path"], $imgId, $img["category"], $img["comment"], $img["notes"]);
    }

    /**
     * Retourne une image au hazard
     *
     * @param string $category La categorie des images à afficher
     *
     * @return Image
     */
    public function getRandomImage(string $category = null): Image {
        if ($category != null) {
            $s = $this->db->prepare('SELECT id FROM image WHERE category = :category');
            $s->execute(array("category" => $category));
            $ids = $s->fetchAll(PDO::FETCH_COLUMN);

            return $this->getImage($ids[random_int(0, count($ids) - 1)]);
        } else {
            return $this->getImage(random_int(1, $this->size()));
        }
    }

    /**
     * Retourne l'objet de la premiere image
     *
     * @param string $category La catégorie de l'image à afficher
     *
     * @return Image
     */
    public function getFirstImage(string $category = null): Image {
      if ($category != null) {
        $s = $this->db->prepare('SELECT id FROM image WHERE category = :category ORDER BY notes DESC, id ASC LIMIT 1');
        $s->execute(array("category" => $category));
        $id = $s->fetch(PDO::FETCH_COLUMN);
        if($id != null){
          return $this->getImage($id);
        } else{
          $s = $this->db->prepare('SELECT id FROM image WHERE category = :category LIMIT 1');
          $s->execute(array("category" => $category));
          return $this->getImage($s->fetch(PDO::FETCH_COLUMN));
        }
      }else {
        $s = $this->db->prepare('SELECT id FROM image ORDER BY notes DESC, id DESC LIMIT 1');
        $s->execute();
        $id = $s->fetch(PDO::FETCH_COLUMN);
        if($id != null){
          return $this->getImage($id);
        } else{
          return $this->getImage(1);
      }
    }
  }

    /**
     * Retourne l'image suivante d'une image
     *
     * @param Image $img
     * @param string $category La categorie des images à afficher
     *
     * @return Image
     */
    public function getNextImage(Image $img, string $category = null): Image {
        $id = $img->getId();
        $notes = $img->getNotes();
        if ($category != null) {
            $s = $this->db->prepare('SELECT id FROM image WHERE category = :category AND ((id > :id  AND notes = :notes) OR notes < :notes) ORDER BY notes DESC, id ASC LIMIT 1');
            $s->execute(array("id" => $id, "category" => $category, "notes" => $notes));
            $resultId = $s->fetch(PDO::FETCH_COLUMN);
            if($resultId == false){
              echo ("Il n'y a plus d'image apres celle la dans cette catégorie");
          }
          else{$id = $resultId;}
        } else {
            $s = $this->db->prepare('SELECT id FROM image WHERE (id > :id AND notes = :notes) OR notes < :notes ORDER BY notes DESC, id ASC LIMIT 1');
            $s->execute(array("id" => $id,"notes" => $notes));
            $resultId = $s->fetch(PDO::FETCH_COLUMN);
            if($resultId == null){
              if ($id < $this->size()) {
                  $id += 1;
              }
            }else{
              $id = $resultId;
            }
        }

        return $this->getImage($id);
    }

    /**
     * Retourne l'image précédente d'une image
     *
     * @param Image $img
     * @param string $category La categorie des images à afficher
     *
     * @return \Image
     */
    public function getPrevImage(Image $img, string $category = null): Image {
        $id = $img->getId();
        $notes = $img->getNotes();
        if ($category != null) {
          $s = $this->db->prepare('SELECT id FROM image WHERE category = :category AND ((id < :id  AND notes = :notes) OR notes > :notes) ORDER BY notes ASC, id DESC LIMIT 1');
          $s->execute(array("id" => $id, "category" => $category, "notes" => $notes));
          $resultId = $s->fetch(PDO::FETCH_COLUMN);
          if($resultId == null){ echo "il n'y a pas d'image avant celle la dans cette catégorie";}
          else{ $id = $resultId; }

        } else {
          $s = $this->db->prepare('SELECT id FROM image WHERE (id < :id AND notes = :notes) OR notes > :notes ORDER BY notes ASC, id DESC LIMIT 1');
          $s->execute(array("id" => $id,"notes" => $notes));
          $resultId = $s->fetch(PDO::FETCH_COLUMN);
          if($resultId == null){
            echo " vous êtes sur la première image";
          }else{
              $id = $resultId;
          }
      }

    return $this->getImage($id);
  }

    /**
     * Saute en avant ou en arrière de $nb images
     * Retourne la nouvelle image
     *
     * @param Image $img
     * @param int $nb
     * @param string $category La categorie des images à afficher
     *
     * @return Image
     */
    public function jumpToImage(Image $img, int $nb, string $category = null): Image {
        $id = $img->getId();
        if ($category != null) {
            $s = $this->db->prepare('SELECT id FROM image WHERE category = :category');
            $s->execute(array("category" => $category));
            $ids = $s->fetchAll(PDO::FETCH_COLUMN);

            $newPos = array_search($id, $ids) + $nb;

            if ($newPos < 0) {
                $newPos = 0;
            } else if (!key_exists($newPos, $ids)) {
                $newPos = array_search($id, $ids);
            }
            $id = $ids[$newPos];
        } else {
            $id += $nb;

            if ($id < 1) {
                $id = 1;
            } else if ($id >= $this->size()) {
                $id = $this->size();
            }
        }

        return $this->getImage($id);
    }

    /**
     * Retourne la liste des images consécutives à partir d'une image
     *
     * @param image $img
     * @param int $nb
     * @param string $category La categorie des images à afficher
     *
     * @return array
     */
    public function getImageList(Image $img, int $nb, string $category = null): array {
        # Verifie que le nombre d'image est non nul
        if (!$nb > 0) {
            debug_print_backtrace();
            trigger_error("Erreur dans ImageDAO.getImageList: nombre d'images nul");
        }

        $id = $img->getId();
        $res = [];

		if ($category != null){
			$s = $this->db->prepare('SELECT * FROM image WHERE category = :category AND id >= :id');
			$s->execute(array("category" => $category, "id" => $id));
			$allImage[] = $s->fetchAll(PDO::FETCH_COLUMN);

			for ($i = 0 ; $i < $nb && $i < count($allImage) ; $i++) {
				$res[] = $allImage[$i];
			}
		}else{
			$max = $id + $nb;
			while ($id < $this->size() && $id < $max) {
				$res[] = $this->getImage($id);
				$id++;
			}
		}

        return $res;
    }

    /**
     * Sauvegarde ou met à jour une image
     *
     * @param Image $img
     */
    public function saveImage(Image $img) {
        $imgId = $img->getId();

        if ($imgId >= 1 and $imgId <= $this->size()) {
            // if image exist : update
            $s = $this->db->prepare('UPDATE image SET path = :url, category = :category, comment = :comment WHERE id = :id');
            $s->execute(array("url" => str_replace(URL_PATH . "/", "", $img->getURL()), "category" => $img->getCategory(), "comment" => $img->getComment(), "id" => $img->getId()));
        } else {
            // else : insert
            $s = $this->db->prepare('INSERT INTO image (id, path, category, comment) VALUES (:id, :url, :category, :comment)');
            $s->execute(array("id" => $img->getId(), "url" => str_replace(URL_PATH . "/", "", $img->getURL()), "category" => $img->getCategory(), "comment" => $img->getCategory()));
        }
    }

    /**
     * Retourne la liste des catégories disponiblent
     *
     * @return array
     */
    public function getCategorieList(): array {
        $s = $this->db->prepare('SELECT DISTINCT category FROM image ORDER BY category');
        $s->execute();

        return $s->fetchAll(PDO::FETCH_COLUMN);
    }

    public function saveLike(Image $img) {
        $imgId = $img->getId();
        $s = $this->db->prepare('UPDATE image SET notes = :notes WHERE id = :id');
        $s->execute(array("notes" => $img->getNotes(), "id" => $imgId));
    }

}

# Test unitaire
# Appeler le code PHP depuis le navigateur avec la variable test
# Exemple : http://localhost/image/model/imageDAO.php?test
if (isset($_GET["test"])) {
    echo "<H1>Test de la classe ImageDAO</H1>";
    $imgDAO = new ImageDAO();
    echo "<p>Creation de l'objet ImageDAO.</p>\n";
    echo "<p>La base contient " . $imgDAO->size() . " images.</p>\n";
    $img = $imgDAO->getFirstImage();
    echo "La premiere image est : " . $img->getURL() . "</p>\n";
    # Affiche l'image
    echo "<img src=\"" . $img->getURL() . "\"/>\n";

    $img = $imgDAO->getNextImage($img);
    echo "La seconde image est : " . $img->getURL() . "</p>\n";
    echo "<img src=\"" . $img->getURL() . "\"/>\n";

    $img = $imgDAO->getPrevImage($img);
    echo "L'image précédante est : " . $img->getURL() . "</p>\n";
    echo "<img src=\"" . $img->getURL() . "\"/>\n";

    $img = $imgDAO->jumpToImage($img, 10);
    echo "L'image 10 plus loin est : " . $img->getURL() . "</p>\n";
    echo "<img src=\"" . $img->getURL() . "\"/>\n";
}
