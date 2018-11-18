<?php

require_once 'imageAlbumDAO.php';

class Image {

    private $url = "";
    private $id = 0;
    private $category = "";
    private $comment = "";
    private $notes = 0;
    private $position;
    private $albIds = [];

    /**
     * @param string $u Url de l'image
     * @param int $id Id de l'image
     * @param string $cat Categorie de l'image
     * @param string $com Commentaire de l'image
     * @param int $notes Notes de l'image
     */
    public function __construct(string $u, int $id, string $cat, string $com, int $notes) {
        $this->url = $u;
        $this->id = $id;
        $this->category = $cat;
        $this->comment = $com;
        $this->notes = $notes;
    }

    /**
     * @return string Url de l'image
     */
    public function getURL(): string {
        return $this->url;
    }

    /**
     * @return int Id de l'image
     */
    public function getId(): int {
        return $this->id;
    }

    /**
     * @return string Categorie de l'image
     */
    public function getCategory(): string {
        return $this->category;
    }

    /**
     * @return string Commentaire de l'image
     */
    public function getComment(): string {
        return $this->comment;
    }

    public function getNotes(): int {
        return $this->notes;
    }

    /**
     * @return array|false id des albums de l'image
     */
    public function getAlbIds() {
        $imageAlbumDao = new ImageAlbumDAO();
        $this->albIds = $imageAlbumDao->getAlbumsFromImage($this->id);
        return $this->albIds;
    }
    
    function setCategory($category) {
        $this->category = $category;
    }

    function setComment($comment) {
        $this->comment = $comment;
    }

    function setNotes($notes) {
        $this->notes = $notes;
    }
    
    function setPosition($position) {
        $this->notes = $notes;
    }

}
