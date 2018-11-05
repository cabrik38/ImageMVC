<?php
  
class Image {

	private $url = ""; 
	private $id = 0;
	private $category = "";
	private $comment = "";

	/**
	 * @param string $u Url de l'image
	 * @param int $id Id de l'image
	 * @param string $cat Categorie de l'image
	 * @param string $com Commentaire de l'image
	 */
	public function __construct(string $u, int $id, string $cat, string $com) {
		$this->url = $u;
		$this->id = $id;
		$this->category = $cat;
		$this->comment = $com;
	}

	/**
	 * @return string Url de l'image
	 */
	public function getURL() : string {
		return $this->url;
	}

	/**
	 * @return int Id de l'image
	 */
	public function getId() : int {
		return $this->id;
	}

	/**
	 * @return string Categorie de l'image
	 */
	public function getCategory() : string {
		return $this->category;
	}

	/**
	 * @return string Commentaire de l'image
	 */
	public function getComment() : string {
		return $this->comment;
	}
	
	function setCategory($category) {
		$this->category = $category;
	}

	function setComment($comment) {
		$this->comment = $comment;
	}
}