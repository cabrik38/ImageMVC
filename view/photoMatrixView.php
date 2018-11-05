<div class="panel panel-default">

	<div class="panel-heading">
		<a class="btn btn-default" href="index.php?controller=photoMatrix&action=prevAction&imgId=<?php echo $data["img"][0]["imgId"]; ?>&nbImg=<?php echo $_GET["nbImg"]; ?>&category=<?php echo urlencode($data["selectedCategory"]); ?>"><i class="fa fa-arrow-left" aria-hidden="true"></i> Prev</a>
		<a class="btn btn-default" href="index.php?controller=photoMatrix&action=nextAction&imgId=<?php echo $data["img"][0]["imgId"]; ?>&nbImg=<?php echo $_GET["nbImg"]; ?>&category=<?php echo urlencode($data["selectedCategory"]); ?>">Next <i class="fa fa-arrow-right" aria-hidden="true"></i></a>
		
		<span id="categories" class="dropdown pull-right">
			<button class="btn btn-default dropdown-toggle" type="button" data-toggle="dropdown">
				Categorie : <span class=""><?php echo $data["selectedCategory"] ?: "Toutes"; ?></span>
				<span class="caret"></span>
			</button>
			<ul class="dropdown-menu">
				<?php if ($data["selectedCategory"] != null){ ?>
					<li><a href="index.php?controller=photoMatrix&action=categoryAction&imgId=<?php echo $data["img"][0]["imgId"]; ?>&nbImg=<?php echo $_GET["nbImg"]; ?>">Toutes</a></li>
				<?php 
				}
				foreach ($data["availableCategories"] as $item){
				?>
					<li><a href="index.php?controller=photoMatrix&action=categoryAction&imgId=<?php echo $data["img"][0]["imgId"]; ?>&nbImg=<?php echo $_GET["nbImg"]; ?>&category=<?= urlencode($item) ?>"><?= $item ?></a></li>
				<?php } ?>
			</ul>
		</span>
	</div>

	<div class="panel-body row">
		<?php foreach ($data["img"] as $img) { ?>
			<div class="col-md-4 heightEqual">
				<a class="thumbnail" href="index.php?controller=photo&action=zoomAction&zoom=1.25&imgId=<?= $img["imgId"] ?>&category=<?= urlencode($data["selectedCategory"]) ?>">
					<img src="<?php echo $img["imgUrl"]; ?>">
				</a>
			</div>
		<?php } ?>
	</div>
	<script type="text/javascript">
	$(function() {
		$('.heightEqual').matchHeight();
	});
	</script>

</div>
