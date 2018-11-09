<?php

class Album {

    private $id;
    private $name;
    private $description;
    private $imgIds = [];

    /**
     * @param int $id Id de l'album
     * @param string $name Nom de l'album
     * @param string $desc Description de l'album
     */
    public function __construct(string $name, string $desc, int $id = null) {
        $this->id = $id;
        $this->name = $name;
        $this->description = $desc;
    }

    /**
     * @return int Id de l'album
     */
    public function getId() {
        return $this->id;
    }

    /**
     * @return string Nom de l'album
     */
    public function getName(): string {
        return $this->name;
    }

    /**
     * @return string Description de l'album
     */
    public function getDescription(): string {
        return $this->description;
    }
    
    /**
     * @return array id des images de l'album
     */
    public function getImgIds(): string {
        $imageAlbumDao = new ImageAlbumDAO();
        $this->imgIds = $imageAlbumDao->imagesFromAlbum($this->id);
        return $this->imgIds;
    }
    
    function setId($id) {
        $this->id = $id;
    }

    function setName($name) {
        $this->name = $name;
    }

    function setDescription($description) {
        $this->description = $description;
    }

}
