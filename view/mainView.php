<?php
if(isset($_GET['like'])){
	setcookie($data['imgId'], "like", time()+3600*24*365);
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
	<title>Site SIL3</title>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, user-scalable=no">
	<link rel="stylesheet" href="view/lib/bootstrap/css/bootstrap.min.css">
	<link rel="stylesheet" href="view/lib/font-awesome/css/font-awesome.min.css">
	<link rel="stylesheet" type="text/css" href="view/style.css" media="screen" title="Normal" />
	<script src="view/lib/jquery.min.js" charset="utf-8"></script>
	<script src="view/lib/jquery.matchheight.min.js" charset="utf-8"></script>
	<script src="view/lib/jquery.cookie.js" charset="utf-8"></script>
	<script src="view/lib/bootstrap/js/bootstrap.min.js" charset="utf-8"></script>
</head>

<body>
	<header class="container">
		<h1>Site SIL3</h1>
	</header>

	<div class="container">

		<nav class="col-md-3">
			<h3>Menu</h3>
			<div class="list-group">
				<?php
				# Mise en place du menu par un parcours de la table associative
				foreach ($data["menu"] as $item => $act) {
					?>
					<a class="list-group-item" href="<?= $act ?>"><?= $item ?></a>
					<?php
				}
				?>
			</ul>
		</nav>

		<div class="col-md-9" id="corps">
			<?php require_once $data["view"]; ?>
		</div>

	</div>

	<div id="pied_de_page">
	</div>
</body>
</html>
