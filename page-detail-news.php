<?php defined('CORE_FOLDER') OR exit('You can not get in here!');
    $social_share=true;
    $hoptions = [
        'page' => "page-detail-news",
    ];
?>
<div id="wrapper">
    <div class="sayfacontent"<?php echo !$sidebar_status ? ' style="width:100%;"' : '';?>>

        <?php if($_theme_name == "Classic"): ?>
            <h4 class="bbbaslik"><strong><?php echo $page["title"]; ?></strong></h4>

            <div class="breadcrumb"> <?php include __DIR__.DS."inc".DS."header-breadcrumb.php"; ?></div>

            <div class="line"></div>
            <div class="clear"></div>
        <?php endif; ?>

        <div>


            <?php echo $page["content"]; ?>

            <div class="clear"></div>
            <?php echo isset($comment_embed_code) ? $comment_embed_code : false; ?>

        </div>
    </div>

    <?php if($sidebar_status): ?>
        <div class="sidebar">
            <h4><?php echo __("website/news/most-read"); ?></h4>
            <div class="sidelinks">
                <?php
                    if($sidebar){
                        foreach($sidebar AS $side){
                            ?><a href="<?php echo $side["route"]; ?>"><span><?php echo $side["title"];?></span></a><?php
                        }
                    }
                ?>
            </div>
        </div>
    <?php endif; ?>
</div>