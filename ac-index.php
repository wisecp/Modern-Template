<?php  
    defined('CORE_FOLDER') OR exit('You can not get in here!');
    $wide_content = true;
?>
<div class="wclientblockscon">
    <div id="clientAreaIndex-wrapper">

        <?php
            $menu_links = [
                'buy-domain'  => Controllers::$init->CRLink("domain"),
                'buy-hosting' => Controllers::$init->CRLink("products",["hosting"]),
                'buy-server'  => Controllers::$init->CRLink("products",["server"]),
                'buy-software' => Controllers::$init->CRLink("softwares"),
                'buy-sms'      => Controllers::$init->CRLink("products",["sms"]),
                'buy-sms-intl' => Controllers::$init->CRLink("international-sms"),
            ];

            if(Config::get("options/pg-activation/domain")){
                ?>
                <div class="product-group-item">
                    <a href="<?php echo $menu_links["buy-domain"]; ?>">
                    <span><i class="fa fa-globe" aria-hidden="true"></i> <?php echo __("website/account/text17"); ?></span>
                    <p class="product-group-item-desc">
                        <?php echo __("website/domain/header-description"); ?>
                    </p>
                    </a>
                </div>
                <?php
            }

            if(Config::get("options/pg-activation/hosting")){
                ?>
               <div class="product-group-item">
                    <a href="<?php echo $menu_links["buy-hosting"]; ?>">
                        <span><i class="fa fa-cloud" aria-hidden="true"></i> <?php echo __("website/account/text18"); ?></span>
                        <p class="product-group-item-desc">
                        <?php echo ___("constants/category-hosting/sub_title"); ?>
                        </p>
                    </a>
                </div>
                <?php
            }

            if(Config::get("options/pg-activation/server")){
                ?>
               <div class="product-group-item">
                    <a href="<?php echo $menu_links["buy-server"]; ?>">
                        <span><i class="fa fa-server" aria-hidden="true"></i> <?php echo __("website/account/text19"); ?></span>
                        <p class="product-group-item-desc">
                        <?php echo ___("constants/category-server/sub_title"); ?>
                        </p>
                    </a>
                </div>
                <?php
            }

            if(Config::get("options/pg-activation/software")){
                ?>
                <div class="product-group-item">
                    <a href="<?php echo $menu_links["buy-software"]; ?>">
                        <span><i class="fa fa-code" aria-hidden="true"></i> <?php echo __("website/account/text20"); ?></span>
                        <p class="product-group-item-desc">
                        <?php echo ___("constants/category-software/sub_title"); ?>
                        </p>
                    </a>
                </div>
                <?php
            }

            if(Config::get("options/pg-activation/international-sms")){
                ?>
                <div class="product-group-item">
                    <a href="<?php echo $menu_links["buy-sms-intl"]; ?>">
                        <span><i class="fa fa-envelope" aria-hidden="true"></i> <?php echo __("website/account_sms/introduction-header-title"); ?></span>
                        <p class="product-group-item-desc">
                        <?php echo __("website/account_sms/introduction-header-description"); ?>
                        </p>
                    </a>
                </div>
                <?php
            }

            if(Config::get("options/pg-activation/sms") && ___("package/code") == "tr"){
                ?>
                <div class="product-group-item">
                    <a href="<?php echo $menu_links["buy-sms"]; ?>">
                        <span><i class="fa fa-envelope" aria-hidden="true"></i> <?php echo __("website/account/text21"); ?></span>
                        <p class="product-group-item-desc">
                        <?php echo ___("constants/category-sms/sub_title"); ?>
                        </p>
                    </a>
                </div>
                <?php
            }

            if($s_groups = Products::special_groups()){
                foreach($s_groups AS $group){
                    ?>
                    <div class="product-group-item">
                        <a href="<?php echo Controllers::$init->CRLink("products",[$group["route"]]); ?>">
                            <span><i class="fa fa-cube	
" aria-hidden="true"></i> <?php echo $group["title"]; ?></span>
                            <p class="product-group-item-desc">
                            <?php echo $group["sub_title"]; ?>
                            </p>
                        </a>
                    </div>
                    <?php
                }
            }


        ?>

    </div>

</div>