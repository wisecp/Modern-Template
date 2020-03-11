<?php defined('CORE_FOLDER') OR exit('You can not get in here!');
    if(!isset($gtype)) $gtype = false;
    if(!isset($pname)) $pname = false;
    if(!isset($acheader_info)){
        $statistic1 = 0;
    }
    if(class_exists("Products")) Helper::Load(["Products"]);

    $menu_links = [
        'menu-0' => Controllers::$init->CRLink("my-account"),
        'menu-1' => Controllers::$init->CRLink("ac-ps-tickets"),
        'menu-2' => Controllers::$init->CRLink("ac-ps-products"),
        'menu-2-0' => Controllers::$init->CRLink("ac-ps-products-t",["software"]),
        'menu-2-1' => Controllers::$init->CRLink("ac-ps-products-t",["sms"]),
        'menu-2-2' => Controllers::$init->CRLink("ac-ps-products-t",["hosting"]),
        'menu-2-3' => Controllers::$init->CRLink("ac-ps-products-t",["server"]),
        'menu-2-4' => Controllers::$init->CRLink("ac-ps-products-t",["special"]),
        'menu-3' => Controllers::$init->CRLink("ac-ps-products-t",["domain"]),
        'menu-4' => Controllers::$init->CRLink("ac-ps-invoices"),
        'menu-5' => Controllers::$init->CRLink("ac-ps-info"),
        'menu-6' => Controllers::$init->CRLink("ac-ps-messages"),
        'balance-link' => Controllers::$init->CRLink("ac-ps-balance"),
        'menu-sms' => Controllers::$init->CRLink("ac-ps-sms"),
        'buy-domain'  => Controllers::$init->CRLink("domain"),
        'buy-hosting' => Controllers::$init->CRLink("products",["hosting"]),
        'buy-server'  => Controllers::$init->CRLink("products",["server"]),
        'buy-software' => Controllers::$init->CRLink("softwares"),
        'buy-sms'      => Controllers::$init->CRLink("products",["sms"]),
    ];
?>
<script type="text/javascript">
    $(document).ready(function(){
        $('.toggle').click(function(e) {
            var href = $(this).attr("href");
            if(href === 'javascript:void 0;' || href === '')
                e.preventDefault();

            var $this = $(this);

            if ($this.next().hasClass('show')) {
                $this.next().removeClass('show');
                $this.next().slideUp(350);
            } else {
                $this.parent().parent().find('li .inner').removeClass('show');
                $this.parent().parent().find('li .inner').slideUp(350);
                $this.next().toggleClass('show');
                $this.next().slideToggle(350);
            }
        });
    });
</script>

<div class="leftbar" style="background-color:#<?php echo Config::get("theme/color2"); ?>;">

    <div class="companylogo">
        <a href="<?php echo $home_link; ?>">
            <img src="<?php echo APP_URI."/".Config::get("theme/clientArea-logo"); ?>"/>
        </a>
    </div>

    <?php
        if(isset($acheader_info)){
            ?>
            <span class="wclientwelcome"><?php echo __("website/account/welcome-board",[
                    '{name}' => $acheader_info["name"],
                    '{surname}' => $acheader_info["surname"],
                    '{full_name}' => $acheader_info["full_name"],
                ]); ?></span>
            <?php
        }
    ?>

    <div class="modernclientmenu">

        <a onclick="dashboard_style_toggle();" style="display:none" href="javascript:void 0;" id="dashboard_button_close" class="mclientmobbtn"><i class="fa fa-close" aria-hidden="true"></i></a>

        <ul>
            <li class="neworderbtn">
                <a class="toggle" href="<?php echo $home_link; ?>"><span><i class="fa fa-shopping-cart" aria-hidden="true"></i><strong><?php echo __("website/account/text16"); ?></strong></span></a>
            </li>

            <?php
                if(isset($acsidebar_links) && $acsidebar_links){
                    ?>
                    <li>
                        <a class="toggle"<?php echo ($pname == "account_dashboard") ? ' id="active" ' : ''; ?>href="<?php echo $menu_links['menu-0']; ?>"><span><i class="fa fa-home" aria-hidden="true"></i><?php echo __("website/account/sidebar-menu-0"); ?></span></a>
                    </li>


                    <?php if($visibility_ticket): ?>
                        <li>
                            <a class="toggle"<?php echo ($pname == "account_tickets") ? ' id="active" ' : ''; ?>href="<?php echo $menu_links['menu-1']; ?>"><span><i class="fa fa-life-ring" aria-hidden="true"></i><?php echo __("website/account/sidebar-menu-1"); ?></span></a>
                        </li>
                    <?php endif; ?>

                    <li>
                        <a class="toggle"<?php echo ($pname == "account_products" && $gtype != "domain") ? ' id="active" ' : ''; ?>href="<?php echo $statistic1 ? "javascript:void 0;" : $menu_links['menu-2']; ?>"><span><i class="fa fa-rocket" aria-hidden="true"></i><?php echo __("website/account/sidebar-menu-2"); ?></span></a>
                        <?php if($statistic1>0): ?>
                            <ul class="inner">

                                <?php if($software_total>0): ?>
                                    <li>
                                        <a class="toggle" <?php echo ($gtype == "software") ? 'id="active" ' : ''; ?>href="<?php echo $menu_links['menu-2-0']; ?>"><span><i class="fa fa-angle-double-right" aria-hidden="true"></i><?php echo __("website/account/sidebar-menu-2-sub-software"); ?></span></a>
                                    </li>
                                <?php endif; ?>

                                <?php if($sms_total>0): ?>
                                    <li>
                                        <a class="toggle" <?php echo ($gtype == "sms") ? 'id="active" ' : ''; ?>href="<?php echo $menu_links['menu-2-1'];?>"><span><i class="fa fa-angle-double-right" aria-hidden="true"></i><?php echo __("website/account/sidebar-menu-2-sub-sms"); ?></span></a>
                                    </li>
                                <?php endif; ?>

                                <?php if($hosting_total>0): ?>
                                    <li>
                                        <a class="toggle" <?php echo ($gtype == "hosting") ? 'id="active" ' : ''; ?>href="<?php echo $menu_links['menu-2-2'];?>"><span><i class="fa fa-angle-double-right" aria-hidden="true"></i><?php echo __("website/account/sidebar-menu-2-sub-hosting"); ?></span></a>
                                    </li>
                                <?php endif; ?>

                                <?php if($server_total>0): ?>
                                    <li>
                                        <a class="toggle" <?php echo ($gtype == "server") ? 'id="active" ' : ''; ?>href="<?php echo $menu_links['menu-2-3'];?>"><span><i class="fa fa-angle-double-right" aria-hidden="true"></i><?php echo __("website/account/sidebar-menu-2-sub-server"); ?></span></a>
                                    </li>
                                <?php endif; ?>

                                <?php if($special_total>0 && $special_list && is_array($special_list)): ?>
                                    <?php
                                    foreach($special_list AS $group){
                                        ?>
                                        <li>
                                            <a class="toggle" <?php echo ($gtype == "special" && $group["id"] == $category["id"]) ? 'id="active" ' : ''; ?>href="<?php echo $menu_links['menu-2-4'];?>?category=<?php echo $group["id"]; ?>"><span><i class="fa fa-angle-double-right" aria-hidden="true"></i><?php echo $group["name"]; ?></span></a>
                                        </li>
                                        <?php
                                    }
                                    ?>
                                <?php endif; ?>
                            </ul>
                        <?php endif; ?>
                    </li>

                    <?php if(Config::get("options/pg-activation/domain")): ?>
                        <li>
                            <a class="toggle" <?php echo ($pname == "account_products" && $gtype == "domain") ? 'id="active" ' : ''; ?>href="<?php echo $menu_links['menu-3'];?>"><span><i class="fa fa-globe" aria-hidden="true"></i><?php echo __("website/account/sidebar-menu-3"); ?></span></a>
                        </li>
                    <?php endif; ?>

                    <?php if(Config::get("options/international-sms-service") && Config::get("options/pg-activation/international-sms")): ?>
                        <li>
                            <a class="toggle" <?php echo ($pname == "account_sms") ? 'id="active" ' : ''; ?>href="<?php echo $menu_links["menu-sms"]; ?>"><span><i class="fa fa-envelope-o" aria-hidden="true"></i><?php echo __("website/account/sidebar-menu-sms"); ?></span></a>
                        </li>
                    <?php endif; ?>

                    <?php if($visibility_invoice): ?>
                        <li>
                            <a class="toggle" <?php echo ($pname == "account_invoices") ? 'id="active" ' : ''; ?>href="<?php echo $menu_links["menu-4"]; ?>"><span><i class="fa fa-file-text-o" aria-hidden="true"></i><?php echo __("website/account/sidebar-menu-4"); ?></span></a>
                        </li>
                    <?php endif; ?>

                    <li>
                        <a class="toggle" <?php echo ($pname == "account_info") ? 'id="active" ' : ''; ?>href="<?php echo (isset($menu_links['menu-5'])) ? $menu_links['menu-5'] : NULL;?>"><span><i class="fa fa-user" aria-hidden="true"></i><?php echo __("website/account/sidebar-menu-5"); ?></span></a>
                    </li>

                    <li>
                        <a class="toggle" <?php echo ($pname == "account_messages") ? 'id="mpanelbtnsaktif" ' : ''; ?>href="<?php echo (isset($menu_links['menu-6'])) ? $menu_links['menu-6'] : NULL;?>"><span><i class="fa fa-envelope-o" aria-hidden="true"></i><?php echo __("website/account/sidebar-menu-6"); ?></span></a>
                    </li>
                    <?php
                }else{
                    ?>
                    <?php if($sign_in): ?>
                        <li>
                            <a class="toggle" href="<?php echo $login_link; ?>"><span><i class="fa fa-sign-in" aria-hidden="true"></i><?php echo __("website/sign/in");?></span></a>
                        </li>
                    <?php endif; ?>

                    <?php if($sign_up): ?>
                        <li>
                            <a class="toggle" href="<?php echo $register_link; ?>"><span><i class="fa fa-user-plus" aria-hidden="true"></i><?php echo __("website/sign/up");?></span></a>
                        </li>
                    <?php endif; ?>
                    <?php
                }


                if(Config::get("options/kbase-system")){
                    ?>
                    <li>
                        <a class="toggle" href="<?php echo Controllers::$init->CRLink("knowledgebase"); ?>"><span><i class="fa fa-info-circle" aria-hidden="true"></i><?php echo __("website/knowledgebase/page-title");?></span></a>
                    </li>
                    <?php
                }
            ?>

            <li>
                <a class="toggle" href="<?php echo Controllers::$init->CRLink("contact"); ?>"><span><i class="fa fa-phone" aria-hidden="true"></i><?php echo __("website/contact/page-title");?></span></a>
            </li>



        </ul>

    </div>

</div>