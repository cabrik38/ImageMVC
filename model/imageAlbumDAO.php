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
     * @return int nombre d'image dans un album
     */
    public function size(int $albId): int {
        # Verifie que cet identifiant est correct
        $albDAO = new AlbumDAO();
        if (!($albId >= 1 and $albId <= $albDAO->size())) {
            debug_print_backtrace();
            die("<H1>Erreur dans ImageAlbumDAO.size: albId=$albId incorrect</H1>");
        }

        $s = $this->db->prepare('SELECT count(id) FROM imagealbum WHERE albId=:albId');
        $s->execute(array("albId" => $albId));

        if ($s) {
            return $s;
        } else {
            print "Error in ImageAlbumDAO.size: id=" . $albId . "<br/>";
            $err = $this->db->errorInfo();
            print $err[2] . "<br/>";
        }
    }

}
