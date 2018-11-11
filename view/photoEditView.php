<div class="panel panel-default">

	<div class="panel-body">
		<form id="editForm" method="POST" enctype="multipart/form-data">
			<div class="form-group">
				<label for="category">Category : </label>
				<input type="text" id="category" class="form-control" name="category" value="<?= $data["imgCategory"] ?>">
			</div>
			<?php if ($data["imgUrl"] != ""){ ?>
			<img src="<?= $data["imgUrl"] ?>" width="<?= $data["imgSize"] ?>">
			<?php }else{ ?>
			<input type="file" id="image" name="image">
		<?php } if(isset($_GET["error"])) { echo '<div class="alert alert-warning">'.$_GET['error'].'</div>';} ?>
			<div class="form-group">
				<label for="comment">Comment : </label>
				<input type="text" id="comment" class="form-control" name="comment" value="<?= $data["imgComment"] ?>">
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
