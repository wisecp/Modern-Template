<?php defined('CORE_FOLDER') OR exit('You can not get in here!'); if($lang_count>1 || $currencies_count>1): ?>
    <div id="selectLangCurrency">
        <a class="langcurclose" href="javascript:close_modal('selectLangCurrency');void 0;">X</a>
        <div class="padding20">
            <div class="langandcur">

                <?php
                    if($lang_list){
                        ?>
                        <h4><?php echo __("website/index/select-your-language"); ?></h4>
                        <?php

                        $selectedx = false;
                        foreach($lang_list AS $row){
                            $selected   = $row["selected"];
                            if($selected) $selectedx = $row;
                            $link = $row["link"];
                            if(!stristr($link,"?")) $link .= "?chl=true";
                            ?>
                            <a<?php echo $selected ? ' class="activelangcur"' : ' href="'.$link.'"'; ?> rel="nofollow"><img title="<?php echo $row["cname"]." (".$row["name"].")"; ?>" alt="<?php echo $row["cname"]." (".$row["name"].")"; ?>" src="<?php echo $row["flag-img"]; ?>"><?php echo $row["cname"]." (".$row["name"].")"; ?></a>
                            <?php
                        }
                        ?>
                        <div class="clear"></div>
                        <?php
                    }
                ?>

                <?php
                    if($currencies){
                ?>
                <div class="currencyitems">
                    <h4 style="margin-top:25px;"><?php echo __("website/index/select-your-currency"); ?></h4>
                    <?php

                        foreach($currencies AS $row){
                            $selected   = $row["id"] == $selected_currency;
                            $link = $canonical_link;
                            if(stristr($link,"?")){
                                $parse  = explode("?",$link);
                                $link   = $parse[0];
                            }
                            $link .= "?chc=".$row["id"];
                            $symbol_info    = Money::getSymbol($row["id"]);
                            $symbol         = $symbol_info["symbol"];
                            if(!$symbol)
                                $symbol     = $row["code"];
                            ?>
                            <a<?php echo $selected ? ' class="activelangcur"' : ' href="'.$link.'"'; ?> rel="nofollow"><strong><?php echo $symbol; ?></strong> <?php echo $row["name"]; ?></a>
                            <?php
                        }
                    ?>
                </div>
            </div>
            <div class="clear"></div>
            <?php
                }
            ?>
        </div>

        <div class="clear"></div>
    </div>

    </div>
<?php endif; ?>