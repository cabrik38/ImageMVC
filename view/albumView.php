<div class="panel panel-default">

    <div class="panel-heading">

    </div>

    <div class="panel-body row">
        <?php 
        if(isset($data["alb"])) {
            foreach ($data["alb"] as $alb) { ?>
            <div class="col-md-4 heightEqual">
                <a class="thumbnail" href="index.php?controller=albumCtrl&action=viewAlbumAction&albId=<?= $alb->getId(); ?>">
                    <img src="view/images/album.png">
                </a>
                <p><?= $alb->getName(); ?></p>
            </div>
            <?php
            }
        }
        else {
            echo("<p>Aucun album crée</p>");
        } ?>
    </div>
    <script type="text/javascript">
        $(function () {
            $('.heightEqual').matchHeight();
        });
    </script>

</div>
