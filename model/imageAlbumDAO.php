<?php

class ImageAlbumDAO {

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
     * @return array tableau contenant les id de tous les albums d'une image
     */
    public function getAlbumsfromImage(int $imgId) {
        $s = $this->db->prepare('SELECT albId FROM imagealbum WHERE imgId = :imgId');
        $s->execute(array("imgId" => $imgId));

        if ($s) {
            $albIds = $s->fetchall(PDO::FETCH_COLUMN);
        } else {
            print "Error in albumsfromImage. imgId=" . $imgId . "<br/>";
            $err = $this->db->errorInfo();
            print $err[2] . "<br/>";
        }
        if($albIds != false)
        {
            return $albIds;
        }
        return false;     
    }
    
    /**
     * @return array tableau contenant les id de tous les albums d'une image
     */
    public function getImagesFromAlbum(int $albId) {
        $s = $this->db->prepare('SELECT imgId FROM imagealbum WHERE albId = :albId');
        $s->execute(array("albId" => $albId));

        if ($s) {
            $imgIds = $s->fetchAll(PDO::FETCH_COLUMN);
        } else {
            print "Error in imagesFromAlbum. albId=" . $albId . "<br/>";
            $err = $this->db->errorInfo();
            print $err[2] . "<br/>";
        }
        if($imgIds != false)
        {       
            return $imgIds;
        }
        return false;     
    }
    
    /**
     * Ajoute une image à un album
     *
     * @param Image $img
     *
     */
    public function addImageToAlbum(int $imgId, int $albId) {
        
        $s = $this->db->prepare('SELECT count(id) FROM imagealbum WHERE albId = :albId AND imgId = :imgId');
        $s->execute(array("albId" => $albId, "imgId" => $imgId));

        if ($s) {
            $position = $s->fetch(PDO::FETCH_COLUMN)[0] + 1;
        } else {
            print "Error in addImageToAlbum albId=" . $albId . " et imgId=" . $imgId . "<br/>";
            $err = $this->db->errorInfo();
            print $err[2] . "<br/>";
        }
        
        if ($position != null) {
            $s = $this->db->prepare('INSERT INTO imagealbum (albId, imgId, position) VALUES (:albId, :imgId, :position)');
            $s->execute(array("albId" => $albId,
                "imgId" => $imgId,
                "position" => $position));
        }
    }

    /**
     * Supprime une image d'un album
     *
     * @param Image $img
     * @param Album $album
     *
     */
    public function delImageOfAlbum(Image $img, Album $album) {
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
    public function delAllImagesOfAlbum(Album $album) {
        // if image exist : update
        $s = $this->db->prepare('DELETE FROM imagealbum WHERE albId = :albId');
        $s->execute(array("albId" => $album->getId()));
    }


}
