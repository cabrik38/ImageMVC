<div class="panel panel-default">

	<div class="panel-body">
		<form id="editForm" method="POST" enctype="multipart/form-data">
			<div>
				<label for="name">Nom : </label>
				<input type="text" id="name" name="name" value="<?=  $data["alb"]->getName(); ?>">
                                <?php if(isset($_GET["error"])) { echo "Veuillez entrer un nom";} ?>
			</div>
                        <div>
				<label for="description">Description : </label>
				<input type="text" id="description" name="description" value="<?= $data["alb"]->getDescription(); ?>">
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