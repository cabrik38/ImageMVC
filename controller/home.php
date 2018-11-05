<?php

class Home {

	/**
	 * Action par defaut
	 * Afficher la page "home"
	 */
	public function indexAction(){
		$data["view"] = "homeView.php";
		
		$data["menu"]['Home'] = "index.php";
		$data["menu"]['A propos'] = "index.php?controller=home&action=aproposAction";
		$data["menu"]['Voir photos'] = "index.php?controller=photo&action=indexAction";
		$data["menu"]['Add image'] = "index.php?controller=photo&action=editAction";
		
		require_once("view/mainView.php");
	}
	
	/**
	 * Afficher la page "a propos"
	 */
	public function aproposAction(){
		$data["view"] = "aproposView.php";
		
		$data["menu"]['Home'] = "index.php";
		$data["menu"]['A propos'] = "index.php?controller=home&action=aproposAction";
		$data["menu"]['Voir photos'] = "index.php?controller=photo&action=indexAction";
		$data["menu"]['Add image'] = "index.php?controller=photo&action=editAction";
		
		require_once("view/mainView.php");
	}
	
}