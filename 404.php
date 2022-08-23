<?php defined('CORE_FOLDER') OR exit('You can not get in here!');
    $hoptions = [
        'page' => "404",
        'aos',
    ];
?>
<style type="text/css">
    .header {
        z-index:99;
    }
</style>
<div class="clear"></div>
<div class="page404">
    <div data-aos="zoom-in-up">
        <img src="<?php echo $tadress;?>/images/404.jpg" width="100"/>
    </div><br/>
    <br><h3 style="color:#e23e3d;"><?php echo __("website/404/text1"); ?></h3><br/>
    <h4><?php echo __("website/404/text2"); ?></h4><br/>
    <a href="<?php echo $home_link; ?>" class="lbtn"><?php echo __("website/404/text3"); ?></a><br/><br/><br/><br/>
    <div class="clear"></div>
</div>
<div class="clear"></div>