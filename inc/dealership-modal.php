<?php defined('CORE_FOLDER') OR exit('You can not get in here!');
    if(isset($acheader_info["dealership"]["status"]) && $acheader_info["dealership"]["status"] == "active"){
        ?>
        <div id="resellerinfo" data-izimodal-title="<?php echo __("website/account/reseller-informations"); ?>" style="display:none;">
            <div class="padding30" style="font-size:14px;">
                <strong><?php echo __("website/account/reseller-text1"); ?></strong>
                <br><br><?php echo __("website/account/reseller-text2"); ?><br><br>

                <?php if($acheader_info["dealership"]["require_min_credit_amount"]>0 || $acheader_info["dealership"]["require_min_discount_amount"]>0 || $acheader_info["dealership"]["only_credit_paid"]): ?>
                    <strong style="color:#4caf50;"><?php echo __("website/account/reseller-text3"); ?></strong><br>
                    <?php if($acheader_info["dealership"]["require_min_credit_amount"]): ?>
                        » <?php echo __("website/account/reseller-condition1",[
                            '{amount}' => '<strong>'.Money::formatter_symbol($acheader_info["dealership"]["require_min_credit_amount"],$acheader_info["dealership"]["require_min_credit_cid"]).'</strong>',
                        ]); ?><br>
                    <?php endif; ?>
                    <?php if($acheader_info["dealership"]["require_min_discount_amount"]): ?>
                        » <?php echo __("website/account/reseller-condition2",[
                            '{amount}' => '<strong>'.Money::formatter_symbol($acheader_info["dealership"]["require_min_discount_amount"],$acheader_info["dealership"]["require_min_discount_cid"]).'</strong>',
                        ]); ?><br>
                    <?php endif; ?>
                    <?php if($acheader_info["dealership"]["only_credit_paid"]): ?>
                        » <?php echo __("website/account/reseller-condition3"); ?><br>
                    <?php endif; ?>
                    <br>
                    <?php echo __("website/account/reseller-text4"); ?><br><br>
                <?php endif; ?>

                <div class="formcon">
                    <div class="yuzde50"><strong><?php echo __("website/account/reseller-text5"); ?></strong></div>
                    <div class="yuzde50"><strong><?php echo __("website/account/reseller-text6"); ?></strong></div>
                </div>

                <?php
                    if($acheader_info["dealership"]["discount_list"]){
                        foreach($acheader_info["dealership"]["discount_list"] AS $row){
                            ?>
                            <div class="formcon">
                                <div class="yuzde50"><?php echo $row["name"]; ?></div>
                                <div class="yuzde50">%<?php echo $row["rate"]; ?></div>
                            </div>
                            <?php
                        }
                    }
                ?>

                <div class="clear"></div>
            </div>
        </div>
        <?php
    }