<div class="panel panel-default">

	<div class="panel-body">
		<form id="editForm" class="col-md-6" method="POST" enctype="multipart/form-data">
			<div class="form-group">
				<label for="name">Nom : </label>
				<input type="text" id="name" class="form-control" name="name" value="<?=  $data["alb"]->getName(); ?>">      
			</div>
                        <?php if(isset($_GET["error"])) { echo '<div class="alert alert-warning">Veuillez entrer un nom</div>';} ?>
                        <div class="form-group">
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