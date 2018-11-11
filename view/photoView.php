<div class="panel panel-default">

	<div class="panel-heading">
		<a class="btn btn-default" href="index.php?controller=photo&action=prevAction&imgId=<?= $data["imgId"] ?>&size=<?= $data["imgSize"] ?>&category=<?= urlencode($data["selectedCategory"]) ?>"><i class="fa fa-arrow-left" aria-hidden="true"></i> Prev</a>
		<a class="btn btn-default" href="index.php?controller=photo&action=nextAction&imgId=<?= $data["imgId"] ?>&size=<?= $data["imgSize"] ?>&category=<?= urlencode($data["selectedCategory"]) ?>">Next <i class="fa fa-arrow-right" aria-hidden="true"></i></a>
		<?php
			if(isset($_COOKIE[$data["imgId"]]) || isset($_GET["like"])){
				?>
				<i class="fa fa-thumbs-up" aria-hidden="true"></i>
				<i class="fa fa-thumbs-down" aria-hidden="true"></i>
				<?php
			}else{?>
				<a class="btn btn-default center like activation" id="<?php echo $data["imgId"]; ?>"href="index.php?controller=photo&action=likeAction&imgId=<?= $data["imgId"] ?>&size=<?= $data["imgSize"] ?>&category=<?= urlencode($data["selectedCategory"])?>&like=like"><i class="fa fa-thumbs-up" aria-hidden="true"></i></a>
				<a class="btn btn-default center like activation" id="<?php echo $data["imgId"]; ?>" href="index.php?controller=photo&action=likeAction&imgId=<?= $data["imgId"] ?>&size=<?= $data["imgSize"] ?>&category=<?= urlencode($data["selectedCategory"])?>&like=dislike"><i class="fa fa-thumbs-down" aria-hidden="true"></i></a>
				<?php }
			if($data["imgNotes"] > 0){
				$color = "green";
			}else if($data["imgNotes"] < 0){
				$color = "red";
			}
                        else { $color = "black"; } ?>
		<span style="color :<?= $color ?>"> <?php echo $data["imgNotes"]?></span>
		<span id="categories" class="dropdown pull-right">
			<button class="btn btn-default dropdown-toggle" type="button" data-toggle="dropdown">
				Categorie : <span class=""><?= $data["selectedCategory"] ?: "Toutes" ?></span>
				<span class="caret"></span>
			</button>
			<ul class="dropdown-menu">
				<?php if ($data["selectedCategory"] != null){ ?>
					<li><a href="index.php?controller=photo&action=categoryAction&imgId=<?= $data["imgId"] ?>&size=<?= $data["imgSize"] ?>">Toutes</a></li>
					<?php
				}
				foreach ($data["availableCategories"] as $item){
					?>
					<li><a href="index.php?controller=photo&action=categoryAction&imgId=<?= $data["imgId"] ?>&size=<?= $data["imgSize"] ?>&category=<?= urlencode($item) ?>"><?= $item ?></a></li>
				<?php } ?>
			</ul>
		</span>
	</div>

	<div class="panel-body">
            <p>Category : <?= $data["imgCategory"] ?></p>
            <a href="index.php?controller=photo&action=zoomAction&zoom=1.25&imgId=<?= $data["imgId"] ?>&size=<?= $data["imgSize"] ?>&category=<?= urlencode($data["selectedCategory"]) ?>">
                    <img src="<?= $data["imgUrl"] ?>" width="<?= $data["imgSize"] ?>">
            </a>
            <p>Comment : <?= $data["imgComment"] ?></p>
            <?php if(!empty($data["imgAlbs"])) { ?>
                <p>Albums : 
                <?php $lastKey = count($data["imgAlbs"])-1;
                foreach ($data["imgAlbs"] as $key => $alb) { ?>
                    <a href="index.php?controller=albumCtrl&action=viewAlbumAction&albId=<?=$alb->getId();?>"><?=$alb->getName();?></a> 
                <?php if($key != $lastKey) {echo" - ";}               
                } ?> 
                </p>
                <?php } ?>
                <button class="btn btn-default dropdown-toggle" type="button" data-toggle="dropdown">
                    Ajouter à l'album : <span class="">Choisir</span>
                    <span class="caret"></span>
                </button>
                <a href="index.php?controller=albumCtrl&action=editAction&imgId=<?= $data["imgId"] ?>"><button class="btn btn-default" style="margin-left: 50px;" type="button">
                    Créer un album
                    </button></a>
                <ul class="dropdown-menu" style="top:unset; left:unset;">
                    <?php foreach ($data["albumsAvailable"] as $album){
                            ?>
                    <li><a href="index.php?controller=photo&action=addToAlbumAction&albId=<?= $album->getId() ?>&imgId=<?= $data["imgId"] ?>"><?= $album->getName() ?></a></li>
                    <?php } ?>
                </ul>
	</div>
</div>
