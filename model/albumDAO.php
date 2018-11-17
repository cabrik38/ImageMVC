<?php

require_once("album.php");
require_once("imageDAO.php");

class AlbumDAO {

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
     * Retourne un objet album correspondant à l'identifiant
     *
     * @param int $albId
     *
     * @return Album
     */
    public function getAlbum(int $albId): Album {
        $s = $this->db->prepare('SELECT * FROM album WHERE id=:id');
        $s->execute(array("id" => $albId));

        if ($s) {
            $alb = $s->fetch();
        } else {
            print "Error in getAlbum. id=" . $albId . "<br/>";
            $err = $this->db->errorInfo();
            print $err[2] . "<br/>";
        }
        if($alb != false)
        {
            return new Album($alb["name"], $alb["description"], $albId);
        }
        return false;
    }
    
    /**
     * Retourne un objet album correspondant à l'identifiant
     *
     * @param int $albId
     *
     * @return Album
     */
    public function getLastAlbum(): Album {
        $s = $this->db->query('SELECT * FROM album ORDER BY id DESC LIMIT 1');

        if ($s) {
            $alb = $s->fetch();
        } else {
            print "Error in getLastAlbum<br/>";
            $err = $this->db->errorInfo();
            print $err[2] . "<br/>";
        }
        if($alb != false)
        {
            return new Album($alb["name"], $alb["description"], $alb["id"]);
        }
        return false;
    }
    
    /**
     * Retourne un tableau conteannt tous les albums
     *
     * @param int $albId
     *
     * @return array
     */
    public function getAllAlbums(): array {
        $s = $this->db->query('SELECT * FROM album');
        $albums = [];
        if ($s) {
            $results = $s->fetchAll();
            foreach($results as $key => $value) {
                $albums[$key] = new Album($value["name"], $value["description"], $value["id"]);
            }
        } else {
            print "Error in getAllAlbum<br/>";
            $err = $this->db->errorInfo();
            print $err[2] . "<br/>";
        }

        return $albums;
    }

    /**
     * Sauvegarde ou met à jour un album
     *
     * @param Album $album
     *
     */
    public function saveAlbum(Album $album, Image $img = null) {
        if ($album->getId() != null) {
            // if image exist : update
            $s = $this->db->prepare('UPDATE album SET name = :name, description = :description WHERE id = :id');
            $s->execute(array("name" => $album->getName(), "description" => $album->getDescription(), "id" => $album->getId()));
        } else {
            // else : insert
            $s = $this->db->prepare('INSERT INTO album (name, description) VALUES (:name, :description)');
            $s->execute(array("name" => $album->getName(), "description" => $album->getDescription()));
        }
        if ($img != null) {
            $this->addImage($img, $album);
        }
    }

    /**
     * Supprime un album
     *
     * @param Album $album
     *
     */
    public function delAlbum(Album $album) {
        $albId = $album->getId();
        if ($albId >= 1 and $albId <= $this->size()) {
            // if image exist : update
            $s = $this->db->prepare('DELETE FROM album WHERE id = :id');
            $s->execute(array("id" => $album->getId()));
        }
    }

    /**
     * Ajoute une image à un album
     *
     * @param Image $img
     *
     */
    public function addImage(Image $img, Album $album) {
        $imgId = $img->getId();
        $imgDAO = new ImageDAO();
        $imageAlbumDAO = new ImageAlbumDAO();
        if ($album->getId() != null) {
            $s = $this->db->prepare('INSERT INTO imagealbum (id, albId, imgId, position) VALUES (:id, :albId, :imgId, :position)');
            $s->execute(array("ig" => $imageAlbumDAO->size(),
                "albId" => $album->getId(),
                "imgId" => $img->getId(),
                "position" => $imageAlbumDAO->size($album->getId()) + 1));
        }
    }

    /**
     * Supprime une image d'un album
     *
     * @param Image $img
     * @param Album $album
     *
     */
    public function delImage(Image $img, Album $album) {
        $imgId = $img->getId();
        $imgDAO = new ImageDAO();
        if ($album->getId() != null) {
            // if image exist : update
            $s = $this->db->prepare('DELETE FROM imagealbum WHERE imgId = :imgId AND albId = :albId');
            $s->execute(array("imgId" => $imgId, "albId" => $album->getId()));
        }
    }

    /**
     * Supprime toute les images d'un album
     *
     * @param Album $album
     *
     */
    public function delAllImages(Album $album) {
        // if image exist : update
        $s = $this->db->prepare('DELETE FROM imagealbum WHERE albId = :albId');
        $s->execute(array("albId" => $album->getId()));
    }

    /**
     * Retourne la liste des images d'un album
     *
     * @param Album $album
     * 
     * @return array
     */
    public function getImagesList(Album $album): array {
        $images = [];
        $imgDAO = new ImageDAO();
        $s = $this->db->prepare('SELECT image.* FROM image INNER JOIN imagealbum on image.id = imgId WHERE albId = :albId ORDER BY imagealbum.position');
        $s->execute(array("albId" => $album->getId()));
        $results = $s->fetchAll();
            foreach($results as $key => $value) {
                $images[$key] = $imgDAO->getImage($value['id']);
            }
        return $images;
    }

    public static function compareAlbums($alb1, $alb2) {
        return $alb1->getId() - $alb2->getId();
    }
}
