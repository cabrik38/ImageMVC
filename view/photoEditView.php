<div class="panel panel-default">

	<div class="panel-body">
		<form id="editForm" method="POST" enctype="multipart/form-data">
			<div>
				<label for="category">Category : </label>
				<input type="text" id="category" name="category" value="<?= $data["imgCategory"] ?>">
			</div>
			<?php if ($data["imgUrl"] != ""){ ?>
			<img src="<?= $data["imgUrl"] ?>" width="<?= $data["imgSize"] ?>">
			<?php }else{ ?>
			<input type="file" id="image" name="image">
			<?php } ?>
			<div>
				<label for="comment">Comment : </label>
				<input type="text" id="comment" name="comment" value="<?= $data["imgComment"] ?>">
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