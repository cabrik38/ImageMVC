<div class="panel panel-default">

    <div class="panel-heading">

    </div>

    <div class="panel-body row">
        <h3><?= $data["alb"]->getName(); ?></h3>
        <?php 
        if(isset($data["imgs"]) && !empty($data["imgs"])) {
            foreach ($data["imgs"] as $img) { ?>
            <div class="col-md-4 heightEqual">
                    <a class="thumbnail" href="index.php?controller=photo&action=zoomAction&zoom=1.25&imgId=<?= $img->getId(); ?> ">
                            <img src="<?php echo $img->getUrl(); ?>">
                    </a>
            </div>
            <?php
            }
        }
        else {
            echo("<p>Aucune image dans l'album ".$data["alb"]->getName()."</p>");
        } ?>
        <p>Description : <?= $data["alb"]->getDescription(); ?></p>
    </div>
    <script type="text/javascript">
        $(function () {
            $('.heightEqual').matchHeight();
        });
    </script>

</div>
