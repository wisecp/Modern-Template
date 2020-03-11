<?php defined('CORE_FOLDER') OR exit('You can not get in here!');
    if(!isset($gtype)) $gtype = false;
?><div class="mpanelleft">

    <?php
        if(isset($visibility_invoice) && $visibility_invoice && $statistic3>0 && $pname != "account_dashboard" && !isset($unpaid_invoices)){
            if(!isset($get_unpaid_invoice_total)){
                Helper::Load(["Invoices","Money"]);
                $u_data = UserManager::LoginData("member");
                $get_unpaid_invoice_total  = Invoices::get_total_unpaid_invoices_amount($u_data["id"]);
            }
            ?>
            <style>
                .invoicereminleft{color:#F44336;margin-bottom:15px;position:relative;overflow:hidden;}
                .invoicereminleft h4{font-size:16px;font-weight:700;margin-bottom:5px}
                .invoicereminleft span{font-weight:300}
                .invoicereminleft .lbtn{margin-top:15px;color:#F44336;border:1px solid #F44336;font-weight:400;font-size:14px}
                .invoicereminleft .lbtn:hover {background:#F44336;color:white;}
                .invoicereminleft img {height: 170px;position:absolute;right: -45px;bottom: -30px;opacity: 0.2;filter: alpha(opacity=20);}
            </style>

            <div class="invoicereminleft">
                <img src="https://image.flaticon.com/icons/svg/179/179386.svg">
                <div class="padding20">
                    <h4><?php echo __("website/account/remind-invoice-text5"); ?></h4>
                    <span><?php echo __("website/account/remind-invoice-text6",[
                            '{count}' => $statistic3,
                            '{total}' => Money::formatter_symbol($get_unpaid_invoice_total,Money::getUCID()),
                        ]); ?></span><div class="clear"></div>
                    <a href="<?php echo Controllers::$init->CRLink("ac-ps-invoices-p",["bulk-payment"]); ?>" class="lbtn"><?php echo $statistic3>1 ? __("website/account/remind-invoice-text3") : __("website/account/remind-invoice-text4"); ?></a>
                </div>
            </div>
            <?php
        }
    ?>


    <div class="mpanelbtns">
        <a <?php echo ($pname == "account_dashboard") ? 'id="mpanelbtnsaktif" ' : ''; ?>href="<?php echo $acsidebar_links['menu-0']; ?>"><span><i class="fa fa-home" aria-hidden="true"></i><?php echo __("website/account/sidebar-menu-0"); ?></span></a>

        <?php if($visibility_ticket): ?>
            <a <?php echo ($pname == "account_tickets") ? 'id="mpanelbtnsaktif" ' : ''; ?>href="<?php echo $acsidebar_links['menu-1']; ?>"><span><i class="fa fa-life-ring" aria-hidden="true"></i><?php echo __("website/account/sidebar-menu-1"); ?></span></a>
        <?php endif; ?>


        <a <?php echo ($pname == "account_products" && $gtype != "domain") ? 'id="mpanelbtnsaktif" ' : ''; ?>href="<?php echo ($statistic1 > 0) ? "javascript:$('#menuurunlink').slideToggle(); void 0" : $acsidebar_links['menu-2']; ?>"><span><i class="fa fa-rocket" aria-hidden="true"></i><?php echo __("website/account/sidebar-menu-2"); ?></span></a>
        <?php if($statistic1>0): ?>
        <div class="menuurunlink" id="menuurunlink" style="float: left; transition-property: all; transition-duration: 0s; transition-timing-function: ease; opacity: 1;<?php echo $pname != "account_products" || $gtype == "domain" ? ' display: none;' : ''; ?>">

            <?php if($software_total>0): ?>
            <a <?php echo ($gtype == "software") ? 'id="mpanelbtnsaktif" ' : ''; ?>href="<?php echo $acsidebar_links['menu-2-0']; ?>"><span><i class="fa fa-angle-double-right" aria-hidden="true"></i><?php echo __("website/account/sidebar-menu-2-sub-software"); ?></span></a>
            <?php endif; ?>

            <?php if($sms_total>0): ?>
                <a <?php echo ($gtype == "sms") ? 'id="mpanelbtnsaktif" ' : ''; ?>href="<?php echo $acsidebar_links['menu-2-1'];?>"><span><i class="fa fa-angle-double-right" aria-hidden="true"></i><?php echo __("website/account/sidebar-menu-2-sub-sms"); ?></span></a>
            <?php endif; ?>

            <?php if($hosting_total>0): ?>
            <a <?php echo ($gtype == "hosting") ? 'id="mpanelbtnsaktif" ' : ''; ?>href="<?php echo $acsidebar_links['menu-2-2'];?>"><span><i class="fa fa-angle-double-right" aria-hidden="true"></i><?php echo __("website/account/sidebar-menu-2-sub-hosting"); ?></span></a>
            <?php endif; ?>

            <?php if($server_total>0): ?>
                <a <?php echo ($gtype == "server") ? 'id="mpanelbtnsaktif" ' : ''; ?>href="<?php echo $acsidebar_links['menu-2-3'];?>"><span><i class="fa fa-angle-double-right" aria-hidden="true"></i><?php echo __("website/account/sidebar-menu-2-sub-server"); ?></span></a>
            <?php endif; ?>

            <?php if($special_total>0 && $special_list && is_array($special_list)): ?>
            <?php
            foreach($special_list AS $group){
                ?>
                <a <?php echo ($gtype == "special" && $group["id"] == $category["id"]) ? 'id="mpanelbtnsaktif" ' : ''; ?>href="<?php echo $acsidebar_links['menu-2-4'];?>?category=<?php echo $group["id"]; ?>"><span><i class="fa fa-angle-double-right" aria-hidden="true"></i><?php echo $group["name"]; ?></span></a>
                <?php
            }
            ?>
            <?php endif; ?>

        </div>
        <?php endif; ?>

        <?php if(Config::get("options/pg-activation/domain")): ?>
            <a <?php echo ($pname == "account_products" && $gtype == "domain") ? 'id="mpanelbtnsaktif" ' : ''; ?>href="<?php echo $acsidebar_links['menu-3'];?>"><span><i class="fa fa-globe" aria-hidden="true"></i><?php echo __("website/account/sidebar-menu-3"); ?></span></a>
        <?php endif; ?>

        <?php if(Config::get("options/international-sms-service") && Config::get("options/pg-activation/international-sms")): ?>
        <a <?php echo ($pname == "account_sms") ? 'id="mpanelbtnsaktif" ' : ''; ?>href="<?php echo $acsidebar_links["menu-sms"]; ?>"><span><i class="fa fa-envelope-o" aria-hidden="true"></i><?php echo __("website/account/sidebar-menu-sms"); ?></span></a>
        <?php endif; ?>

        <?php if($visibility_invoice): ?>
            <a <?php echo ($pname == "account_invoices") ? 'id="mpanelbtnsaktif" ' : ''; ?>href="<?php echo $acsidebar_links["menu-4"]; ?>"><span><i class="fa fa-file-text-o" aria-hidden="true"></i><?php echo __("website/account/sidebar-menu-4"); ?></span></a>
        <?php endif; ?>

        <a <?php echo ($pname == "account_info") ? 'id="mpanelbtnsaktif" ' : ''; ?>href="<?php echo (isset($acsidebar_links['menu-5'])) ? $acsidebar_links['menu-5'] : NULL;?>"><span><i class="fa fa-user" aria-hidden="true"></i><?php echo __("website/account/sidebar-menu-5"); ?></span></a>

        <a <?php echo ($pname == "account_messages") ? 'id="mpanelbtnsaktif" ' : ''; ?>href="<?php echo (isset($acsidebar_links['menu-6'])) ? $acsidebar_links['menu-6'] : NULL;?>"><span><i class="fa fa-envelope-o" aria-hidden="true"></i><?php echo __("website/account/sidebar-menu-6"); ?></span></a>

    </div>
</div>