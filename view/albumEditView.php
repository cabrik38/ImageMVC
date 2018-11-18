<?php $errorMessage = "";
if(isset($data["error"]) && $data["error"] == "nameRequired") { 
    $errorMessage = '<div style="margin-top:10px;" class="col-md-12 alert alert-warning">Veuillez entrer un nom</div>';
} 
if(isset($data["error"]) && $data["error"] == "wrongImgId") { 
    $errorMessage = '<div style="margin-top:10px;" class="col-md-12 alert alert-warning">L\'image à ajouter n\'existe pas</div>';
} 
?>    
<div class="panel panel-default">
	<div class="panel-body">
		<form id="editForm" class="col-md-12" method="POST" enctype="multipart/form-data">
			<div class="form-group col-md-6">
				<label for="name">Nom : </label>
				<input type="text" id="name" class="form-control" name="name" value="<?=  $data["alb"]->getName(); ?>">  
                                <?php if(isset($_GET["error"])) { echo $errorMessage; } ?>
			</div>
                        <?php 
                        if(isset($data["imgs"]) && !empty($data["imgs"])) {?>
                            <div class="col-md-12">
                            <?php foreach ($data["imgs"] as $img) { ?>
                            <div class="form-group col-md-4">
                                <a class="thumbnail" href="index.php?controller=photo&action=zoomAction&zoom=1.25&imgId=<?= $img->getId(); ?> ">
                                    <img src="<?php echo $img->getUrl(); ?>">
                                </a>
                                <?php $nbPositions = count($data["positions"]);
                                foreach($data["positions"] as $position) {
                                    if($position["imgId"] == $img->getId()) {
                                        $positionActuelle = $position["position"];
                                    }
                                }
                                ?>
                                <span id="position" class="dropdown pull-left">
                                    <button class="btn btn-default dropdown-toggle" type="button" data-toggle="dropdown">
                                        Position : <span class=""><?= $positionActuelle ?></span>
                                        <span class="caret"></span>
                                    </button>
                                    <ul class="dropdown-menu">
                                        <?php for ($i=1; $i<=$nbPositions; $i++) {
                                                ?>
                                        <li><a href="index.php?controller=albumCtrl&action=updatePosition&albId=<?= $data["alb"]->getId() ?>&imgId=<?= $img->getId() ?>&position=<?= $i ?>"><?= $i ?></a></li>
                                        <?php } ?>
                                    </ul>
                                </span>
                            </div>
                            <?php
                            } ?>
                            </div>
                        <?php }
                        else {
                            if(isset($_GET["albId"])) {
                                echo("<p class=\"col-md-12\">Aucune image dans l'album</p>");
                            }
                        } ?>
                        <div class="form-group col-md-6">
				<label for="description">Description : </label>
				<input type="text" id="description" class="form-control" name="description" value="<?= $data["alb"]->getDescription(); ?>">
			</div>
		</form>
	</div>

</div>

<script type="text/javascript">
	$(function (){
		$("a[href*='saveAction']").click(function (event){
			event.preventDefault();
			
			$("#editForm").attr("action", $(this).attr("href")).submit();
		});
	});
</script>