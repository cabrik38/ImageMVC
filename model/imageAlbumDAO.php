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
    public function albumsfromImage(int $imgId) {
        $s = $this->db->prepare('SELECT albId FROM imagealbum WHERE imgId=:imgId');
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
    public function imagesFromAlbum(int $albId) {
        $s = $this->db->prepare('SELECT imgId FROM imagealbum WHERE albId=:albId');
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

}
