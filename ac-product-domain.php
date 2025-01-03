<?php defined('CORE_FOLDER') OR exit('You can not get in here!');
    $wide_content = true;
    include $tpath."common-needs.php";
    $hoptions = ["datatables","jquery-ui"];
    $bring      = Filter::init("GET/bring");

    $ttl_times      = [
        60          => '1 min',
        120          => '2 min',
        300          => '5 min',
        600          => '10 min',
        900          => '15 min',
        1800         => '30 min',
        3600         => '1 hr',
        7200         => '2 hr',
        18000        => '5 hr',
        43200        => '12 hr',
        86400        => '1 day',
    ];

    $tab_verification       = isset($require_verification) && $require_verification;

    if($product) $options["dns_manage"] = $product["dns_manage"];


    $allow_dns_cns          = (isset($module_con) && method_exists($module_con,'CNSList'));
    $allow_dns_records      = (isset($module_con) && method_exists($module_con,'getDnsRecords'));
    $allow_dns_sec_records  = (isset($module_con) && method_exists($module_con,'getDnsSecRecords'));
    $allow_forwarding_dmn   = (isset($module_con) && method_exists($module_con,'getForwardingDomain'));
    $allow_forwarding_eml   = (isset($module_con) && method_exists($module_con,'getEmailForwards'));

    if($bring)
    {
        if($bring == "forwarding-domain" && $allow_forwarding_dmn)
        {
            $getForwarding      = $module_con->getForwardingDomain();

            if(!$getForwarding)
            {
                ?>
                <div class="red-info">
                    <div class="padding20">
                        <i class="fa fa-warning"></i>
                        <p><?php echo $module_con->error; ?></p>
                    </div>
                </div>
                <?php
            }
            else
            {
                if($getForwarding["status"])
                {
                    ?>
                    <div class="domainforwarding">

                        <div class="formcon">
                            <div class="yuzde30"><?php echo __("website/account_products/domain-forwarding-tx9"); ?></div>
                            <div class="yuzde70">
                                <select disabled style="width: 100px;" id="forward_protocol">
                                    <option<?php echo $getForwarding["protocol"] == "http" ? ' selected' : ''; ?>>http://</option>
                                    <option<?php echo $getForwarding["protocol"] == "https" ? ' selected' : ''; ?>>https://</option>
                                </select>
                                <input disabled style="width:78%;" id="forward_domain" value="<?php echo $getForwarding["domain"]; ?>" type="text" placeholder="<?php echo __("website/account_products/domain-forwarding-tx4"); ?>"></div>
                        </div>
                        <div class="formcon">
                            <div class="yuzde30"><?php echo __("website/account_products/domain-forwarding-tx10"); ?></div>
                            <div class="yuzde70">
                                <div class="yuzde75">

                                    <input<?php echo $getForwarding["method"] == 301 ? ' checked' : ''; ?> disabled id="method_301" class="radio-custom" name="method" value="301" type="radio">
                                    <label for="method_301" class="radio-custom-label" style="margin-right: 28px;"><span class="checktext">301 (<?php echo __("website/account_products/domain-forwarding-tx11"); ?>)</span></label>

                                    <input<?php echo $getForwarding["method"] == 302 ? ' checked' : ''; ?> disabled id="method_302" class="radio-custom" name="method" value="302" type="radio">
                                    <label for="method_302" class="radio-custom-label"><span class="checktext">302 (<?php echo __("website/account_products/domain-forwarding-tx12"); ?>)</span></label>

                                </div>
                            </div>
                        </div>


                        <div class="line"></div>
                        <a style="width:240px;float:right;font-weight: 600;" href="javascript:void(0);" onclick="cancel_forward_domain(this);" class="yesilbtn gonderbtn"><?php echo __("website/account_products/domain-forwarding-tx6"); ?></a>
                    </div>
                    <?php
                }
                else
                {
                    ?>
                    <div class="domainforwarding">

                        <div class="formcon">
                            <div class="yuzde30"><?php echo __("website/account_products/domain-forwarding-tx9"); ?></div>
                            <div class="yuzde70">
                                <select style="width: 100px;" id="forward_protocol">
                                    <option>http://</option>
                                    <option>https://</option>
                                </select>
                                <input style="width:78%;" id="forward_domain" value="" type="text" placeholder="<?php echo __("website/account_products/domain-forwarding-tx4"); ?>"></div>
                        </div>
                        <div class="formcon">
                            <div class="yuzde30"><?php echo __("website/account_products/domain-forwarding-tx10"); ?></div>
                            <div class="yuzde70">
                                <div class="yuzde75">

                                    <input checked id="method_301" class="radio-custom" name="method" value="301" type="radio">
                                    <label for="method_301" class="radio-custom-label" style="margin-right: 28px;"><span class="checktext">301 (<?php echo __("website/account_products/domain-forwarding-tx11"); ?>)</span></label>

                                    <input id="method_302" class="radio-custom" name="method" value="302" type="radio">
                                    <label for="method_302" class="radio-custom-label"><span class="checktext">302 (<?php echo __("website/account_products/domain-forwarding-tx12"); ?>)</span></label>

                                </div>
                            </div>
                        </div>


                        <div class="line"></div>
                        <a style="width:240px;float:right;font-weight: 600;" href="javascript:void(0);" onclick="set_forward_domain(this);" class="yesilbtn gonderbtn"><?php echo __("website/account_products/domain-forwarding-tx5"); ?></a>
                    </div>
                    <?php
                }
            }
            exit();
        }
        elseif($bring == "cns-list" && $allow_dns_cns)
        {
            $cns_list = $module_con->CNSList($options);
            if(is_array($cns_list) && $cns_list)
            {
                foreach($cns_list AS $id=>$row){
                    ?>
                    <form action="<?php echo $links["controller"]; ?>" method="post" id="ModifyCns<?php echo $id; ?>">
                        <input type="hidden" name="operation" value="domain_modify_cns">
                        <input type="hidden" name="id" value="<?php echo $id; ?>">

                        <div class="yuzde33"><input name="ns" type="text" class="" placeholder="ns1.<?php echo $proanse["options"]["domain"] ?? $proanse["name"]; ?>" value="<?php echo $row["ns"]; ?>"></div>
                        <div class="yuzde33"> <input name="ip" type="text" class="" placeholder="192.168.1.1" value="<?php echo $row["ip"]; ?>"></div>
                        <div class="yuzde33">
                            <a href="javascript:void(0);" class="sbtn mio-ajax-submit" mio-ajax-options='{"result":"ModifyCNS","waiting_text":"<?php echo addslashes(__("website/others/button4-pending")); ?>"}'><i class="fa fa-pencil-square-o" aria-hidden="true"></i></a>
                            <a href="javascript:void(0);" class="sbtn mio-ajax-submit" mio-ajax-options='{"action":"<?php echo $links["controller"];?>","method":"POST","data":{"operation":"domain_delete_cns","id":"<?php echo $id;?>"},"type":"direct","result":"DeleteCNS","waiting_text":"<?php echo addslashes(__("website/others/button4-pending")); ?>","before_function":"delete_confirm"}'><i class="fa fa-trash-o" aria-hidden="true"></i></a>
                        </div>

                    </form>
                    <?php
                }
            }
            else{
                ?><div align="center" style="margin-top:20px;"><?php echo __("website/account_products/none-cns"); ?></div><?php
            }

            exit;
        }
        elseif($bring == "dns-records")
        {
            if(!$allow_dns_records) return false;

            $getRecords = $module_con->getDnsRecords();
            if($getRecords)
            {
                foreach($getRecords AS $k => $r)
                {
                    $ttl_text   = $r["ttl"]." sec";
                    $minute      = round($r["ttl"] / 60);
                    $hour        = round($minute / 60);

                    if($hour > 0) $ttl_text = $hour.' hr';
                    elseif($minute > 0) $ttl_text = $minute.' min';

                    ?>
                    <tr id="DnsRecord_<?php echo $k; ?>">
                        <input type="hidden" name="type" value="<?php echo $r["type"]; ?>">
                        <input type="hidden" name="identity" value="<?php echo $r["identity"]; ?>">
                        <input type="hidden" name="name" value="<?php echo $r["name"]; ?>">
                        <input type="hidden" name="value" value="<?php echo htmlentities($r["value"],ENT_QUOTES); ?>">
                        <td align="left" class="dns-record-type"><?php echo $r["type"]; ?></td>
                        <td align="left" class="dns-record-name">
                            <div class="edit-wrap" style="display: none"><input type="text" value=""></div>
                            <div class="show-wrap"><?php echo $r["name"]; ?></div>
                        </td>
                        <td align="left" class="dns-record-value">
                            <div class="edit-wrap" style="display: none;"><input type="text" value=""></div>
                            <div class="show-wrap"><?php echo $r["value"]; ?></div>
                        </td>
                        <td align="center" class="dns-record-ttl">
                            <div class="edit-wrap-ttl" style="width:80px; display: none;">
                                <select>
                                    <option value="">Auto</option>
                                    <?php
                                        if($ttl_times)
                                        {
                                            foreach($ttl_times AS $ttl_k => $ttl_v)
                                            {
                                                ?>
                                                <option<?php echo $ttl_text == $ttl_v ? ' selected' : ''; ?> value="<?php echo $ttl_k; ?>"><?php echo $ttl_v; ?></option>
                                                <?php
                                            }
                                        }
                                    ?>
                                </select>
                            </div>

                            <?php if($r["type"] == "MX"): ?>
                                <div class="edit-wrap-priority" style="width: 80px;display: none;">
                                    <input type="number" name="priority" value="<?php echo $r["priority"]; ?>">
                                </div>
                            <?php endif; ?>

                            <div class="show-wrap-ttl">
                                <?php
                                    echo $ttl_text;
                                ?>
                            </div>
                            <?php if($r["type"] == "MX"): ?>
                                <div class="show-wrap-priority"> - Priority <?php echo $r["priority"]; ?></div>
                            <?php endif; ?>
                        </td>
                        <td align="center">
                            <div class="edit-content" style="display: none;">
                                <a data-tooltip="<?php echo ___("needs/button-save"); ?>" href="javascript:void 0;" onclick="saveDnsRecord(<?php echo $k; ?>,this);" class="sbtn green"><i class="fa fa-check"></i></a>
                                <a data-tooltip="<?php echo __("website/account_products/preview-turn-back"); ?>" href="javascript:void 0;" onclick="cancelDnsRecord(<?php echo $k; ?>);" class="sbtn"><i class="fa fa-reply"></i></a>
                            </div>
                            <div class="no-edit-content">
                                <a data-tooltip="<?php echo ___("needs/button-edit"); ?>" href="javascript:void 0;" onclick="editDnsRecord(<?php echo $k; ?>);" class="sbtn"><i class="fa fa-pencil"></i></a>
                                <a data-tooltip="<?php echo ___("needs/button-delete"); ?>" href="javascript:void 0;" onclick="removeDnsRecord(<?php echo $k; ?>,this);" class="sbtn red"><i class="fa fa-trash"></i></a>
                            </div>
                        </td>
                    </tr>
                    <?php
                }
            }
            else
            {
                ?>
                <tr style="background: none;box-shadow: none;">
                    <td colspan="5" style="box-shadow: none;">
                        <div align="center"><?php echo __("website/account_products/domain-dns-records-6"); ?></div>
                    </td>
                </tr>
                <?php
            }

            exit();
        }
        elseif($bring == "dns-sec-records")
        {
            if(!$allow_dns_sec_records) return false;

            $getRecords = $module_con->getDnsSecRecords();
            if($getRecords)
            {
                foreach($getRecords AS $k => $r)
                {
                    ?>
                    <tr id="DnsRecord_<?php echo $k; ?>">
                        <input type="hidden" name="identity" value="<?php echo $r["identity"]; ?>">
                        <input type="hidden" name="digest" value="<?php echo $r["digest"]; ?>">
                        <input type="hidden" name="key_tag" value="<?php echo $r["key_tag"]; ?>">
                        <input type="hidden" name="digest_type" value="<?php echo $r["digest_type"]; ?>">
                        <input type="hidden" name="algorithm" value="<?php echo $r["algorithm"]; ?>">
                        <td align="left" class="dns-sec-record-digest"><?php echo $r["digest"]; ?></td>
                        <td align="left" class="dns-sec-record-key_tag"><?php echo $r["key_tag"]; ?></td>
                        <td align="left" class="dns-sec-record-digest-type"><?php echo $module_con->config["settings"]["dns-digest-types"][$r["digest_type"]] ?? $r["digest_type"]; ?></td>
                        <td align="left" class="dns-sec-record-algorithm"><?php echo $module_con->config["settings"]["dns-algorithms"][$r["algorithm"]] ?? $r["algorithm"]; ?></td>
                        <td align="center">
                            <a data-tooltip="<?php echo ___("needs/button-delete"); ?>" href="javascript:void 0;" onclick="removeDnsSecRecord(<?php echo $k; ?>,this);" class="sbtn red"><i class="fa fa-trash"></i></a>
                        </td>
                    </tr>
                    <?php
                }
            }
            else
            {
                ?>
                <tr style="background: none;box-shadow: none;">
                    <td colspan="5" style="box-shadow: none;">
                        <div align="center"><?php echo __("website/account_products/domain-dns-records-6"); ?></div>
                    </td>
                </tr>
                <?php
            }

            exit();
        }
        elseif($bring == "email-forwards")
        {
            if(!$allow_forwarding_eml) return false;

            $getRecords = $module_con->getEmailForwards();
            if($getRecords)
            {
                foreach($getRecords AS $k => $r)
                {
                    ?>
                    <tr id="EmailForward_<?php echo $k; ?>">
                        <input type="hidden" name="identity" value="<?php echo $r["identity"] ?? ''; ?>">
                        <input type="hidden" name="prefix" value="<?php echo $r["prefix"]; ?>">
                        <input type="hidden" name="target" value="<?php echo $r["target"]; ?>">
                        <td align="left" class="email-forward-prefix"><?php echo $r["prefix"]; ?></td>
                        <td><i class="fas fa-long-arrow-alt-right" style="font-size: 30px;"></i></td>
                        <td align="left" class="email-forward-target">
                            <div class="edit-wrap" style="display: none"><input type="text" value="" placeholder="email@example.com"></div>
                            <div class="show-wrap"><?php echo $r["target"]; ?></div>
                        </td>
                        <td align="center">
                            <div class="edit-content" style="display: none;">
                                <a data-tooltip="<?php echo ___("needs/button-save"); ?>" href="javascript:void 0;" onclick="saveEmailForward(<?php echo $k; ?>,this);" class="sbtn green"><i class="fa fa-check"></i></a>
                                <a data-tooltip="<?php echo __("website/account_products/preview-turn-back"); ?>" href="javascript:void 0;" onclick="cancelEmailForward(<?php echo $k; ?>);" class="sbtn"><i class="fa fa-reply"></i></a>
                            </div>
                            <div class="no-edit-content">
                                <a data-tooltip="<?php echo ___("needs/button-edit"); ?>" href="javascript:void 0;" onclick="editEmailForward(<?php echo $k; ?>);" class="sbtn"><i class="fa fa-pencil"></i></a>
                                <a data-tooltip="<?php echo ___("needs/button-delete"); ?>" href="javascript:void 0;" onclick="removeEmailForward(<?php echo $k; ?>,this);" class="sbtn red"><i class="fa fa-trash"></i></a>
                            </div>
                        </td>
                    </tr>
                    <?php
                }
            }
            else
            {
                ?>
                <tr style="background: none;box-shadow: none;">
                    <td colspan="3" style="box-shadow: none;">
                        <div align="center"><?php echo __("website/account_products/domain-forwarding-tx19"); ?></div>
                    </td>
                </tr>
                <?php
            }

            exit();
        }
    }
?>
<style type="text/css"></style>
<script type="text/javascript">
    function password_check(id){
        var value = $("#"+id).val();
        var check = checkStrength(value);
        $("#strength_"+id+" div").css("display","none");
        $("#strength_"+id+" #"+check).css("display","block");
    }



    $(document).ready(function(){
        var tab = gGET("tab");
        if (tab != '' && tab != undefined) {
            $("#tab-tab .tablinks[data-tab='" + tab + "']").click();
        }
        else
        {
            $("#tab-tab .tablinks:eq(0)").addClass("active");
            $("#tab-tab .tabcontent:eq(0)").css("display", "block");
        }
    });
</script>

<table style="display: none;">
    <tbody id="template-loader">
    <tr style="background: none;box-shadow: none;">
        <td colspan="5" style="box-shadow: none;">
            <div id="template-loaderx">
                <div id="block_module_loader" align="center" style="width:100%">
                    <div class="load-wrapp">
                        <p style="margin-bottom:20px;font-size:17px;"><strong><?php echo ___("needs/processing"); ?>...</strong><br><?php echo ___("needs/please-wait"); ?></p>
                        <div class="load-7">
                            <div class="square-holder">
                                <div class="square"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </td>
    </tr>
    </tbody>
    <tbody id="template-loader2">
    <tr style="background: none;box-shadow: none;">
        <td colspan="4" style="box-shadow: none;">
            <div id="block_module_loader" align="center" style="width:100%">
                <div class="load-wrapp">
                    <p style="margin-bottom:20px;font-size:17px;"><strong><?php echo ___("needs/processing"); ?>...</strong><br><?php echo ___("needs/please-wait"); ?></p>
                    <div class="load-7">
                        <div class="square-holder">
                            <div class="square"></div>
                        </div>
                    </div>
                </div>
            </div>
        </td>
    </tr>
    </tbody>
</table>

<div id="info_doc_modal" style="display: none;" data-izimodal-title="<?php echo __("website/account_products/domain-doc-5"); ?>">
    <div class="padding20">
        <?php
            echo $required_docs_info ?? '';
        ?>
    </div>
</div>


<div class="mpanelrightcon">

    <div class="mpaneltitle">
        <div class="sayfayolu"><?php include $tpath."inc".DS."panel-breadcrumb.php"; ?></div>
        <h4><strong><i class="fa fa-globe" aria-hidden="true"></i> <?php echo $proanse["name"]; ?> (<?php echo __("website/account_products/service-groups/domain"); ?>)</strong></h4>
    </div>


    <?php /*if(isset($require_verification) && $require_verification): ?>
        <div class="red-info" id="domain-doc-required-info">
            <div class="padding15">
                <i class="fa fa-exclamation-triangle" aria-hidden="true"></i>
                <p style="font-size: 18px;"><strong><?php echo __("website/account_products/domain-doc-1"); ?></strong></p>
                <p><?php echo __("website/account_products/domain-doc-2"); ?></p>
                <a href="javascript:void 0;" onclick="$('a[data-tab=verification]').click();" class="lbtn"><?php echo __("website/account_products/domain-doc-3"); ?></a>
            </div>
        </div>
    <?php endif;*/ ?>


    <div id="tab-tab">

        <ul class="tab">
            <?php if(!$tab_verification): ?>
                <li><a href="javascript:void(0)" class="tablinks" onclick="open_tab(this, 'summary','tab')" data-tab="summary"><i class="fa fa-info" aria-hidden="true"></i> <?php echo __("website/account_products/detail"); ?></a></li>
            <?php endif; ?>

            <?php if($proanse["status"] == "active"): ?>
                <li><a href="javascript:void(0)" class="tablinks" onclick="open_tab(this, 'whois','tab')" data-tab="whois"><i class="far fa-id-card" aria-hidden="true"></i> <?php echo __("website/account_products/whois-manager"); ?></a></li>
            <?php endif; ?>

            <?php if($proanse["status"] == "active" && isset($options["dns_manage"]) && $options["dns_manage"]): ?>
                <li><a href="javascript:void(0)" class="tablinks" onclick="open_tab(this, 'dns','tab')" data-tab="dns"><i class="fas fa-server" aria-hidden="true"></i> <?php echo __("website/account_products/dns-manager"); ?></a></li>
            <?php endif; ?>

            <?php if($proanse["status"] == "active" && ($allow_forwarding_dmn || $allow_forwarding_eml)): ?>
                <li><a href="javascript:void(0)" class="tablinks" onclick="open_tab(this, 'forwarding','tab')" data-tab="forwarding"><i class="fa fa-share" aria-hidden="true"></i> <?php echo __("website/account_products/forwarding"); ?></a></li>
            <?php endif; ?>

            <?php if($tab_verification): ?>
                <li><a href="javascript:void(0)" class="tablinks" onclick="open_tab(this, 'verification','tab')" data-tab="verification"><i class="fas fa-gavel" aria-hidden="true"></i> <?php echo __("website/account_products/domain-verification-tx1"); ?></a></li>
            <?php endif; ?>

            <?php if($proanse["status"] == "active"): ?>
                <li><a href="javascript:void(0)" class="tablinks" onclick="open_tab(this, 'security','tab')" data-tab="security"><i class="fa fa-shield" aria-hidden="true"></i> <?php echo __("website/account_products/security-manager"); ?></a></li>
            <?php endif; ?>

            <?php if($proanse["status"] == "active" && $ctoc_service_transfer): ?>
                <li><a href="javascript:void(0)" class="tablinks" onclick="open_tab(this, 'transfer-service','tab')" data-tab="transfer-service"><i class="fa fa-exchange" aria-hidden="true"></i> <?php echo __("website/account_products/transfer-service"); ?></a></li>
            <?php endif; ?>

            <?php if($proanse["status"] == "active"): ?>
                <li><a href="javascript:void(0)" class="tablinks" onclick="open_tab(this, 'transfer','tab')" data-tab="transfer"><i class="fa fa-random" aria-hidden="true"></i> <?php echo __("website/account_products/transfer-manager"); ?></a></li>
            <?php endif; ?>

            <div class="orderidno"><span><?php echo __("website/account_products/table-ordernum"); ?></span><strong>#<?php echo $proanse["id"]; ?></strong></div>

        </ul>

        <?php if(!$tab_verification): ?>
            <div id="tab-summary" class="tabcontent">
                <div class="hizmetblok">
                    <div class="service-first-block" style="border:none;min-height:auto; padding:30px 0px;">

                        <div id="order_image" style="    position: relative;display: inline-block;margin-right: 32px;">
                            <img style="width:130px;" src="<?php echo $tadress."images/domain-order-cover.jpg"; ?>" width="auto" height="auto">
                        </div>

                        <div style="display: inline-block;vertical-align: top;margin-top: 10px;   position:relative;">

                            <h4 style="font-size: 24px;"><strong><?php echo $options["domain"]; ?></strong></h4>
                            <?php if($proanse["status"] == "active" || $proanse["status"] == "suspended" || $proanse["status"] == "inprocess"){ ?>
                                <h4 style="margin-bottom:10px;"><?php echo __("website/account_products/remaining-day"); ?>: <strong><?php echo $remaining_day; ?></strong></h4>
                            <?php } ?>


                            <?php if(isset($options["whois_privacy"]) && $options["whois_privacy"]): ?>
                                <div class="service-status-con" style="display:block;" id="server_status_other"><span class="statusother"><i class="fa fa-shield" aria-hidden="true" style="margin-right: 5px;"></i> <?php echo __("website/account_products/whois-hidden"); ?></span>
                                </div>
                            <?php endif; ?>

                        </div>

                    </div>
                </div>

                <div class="hizmetblok" style="border:none;min-height:auto;padding-bottom:25px;">

                    <div id="order-service-detail-btns">

                        <div id="renewal_list" style="display:none;">
                            <select id="selection_renewal">
                                <option value=""><?php echo __("website/account_products/renewal-list-option"); ?></option>
                                <?php
                                    if(isset($renewal_list) && $renewal_list){
                                        foreach($renewal_list AS $year=>$cash){
                                            ?>
                                            <option value="<?php echo $year; ?>">(<?php echo $year." ".__("website/account_products/registration-year");?>) <?php echo $cash; ?></option>
                                            <?php
                                        }
                                    }
                                ?>
                            </select>
                            <script type="text/javascript">
                                $(document).ready(function(){
                                    $("#selection_renewal").change(function(){
                                        var selection = $(this).val();
                                        if(selection != ''){
                                            var result = MioAjax({
                                                action: "<?php echo $links["controller"]; ?>",
                                                method: "POST",
                                                data:{operation:"domain_renewal",period:selection}
                                            },true);

                                            if(result){
                                                var solve = getJson(result);
                                                if(solve){
                                                    if(solve.status == "error")
                                                        alert_error(solve.message,{timer:5000});
                                                    else if(solve.status == "successful"){
                                                        window.location.href = solve.redirect;
                                                    }
                                                }
                                            }
                                        }
                                    });
                                });
                            </script>
                        </div>

                        <?php if($proanse["status"] == "active" || $proanse["status"] == "suspended"): ?>
                        <a href="javascript:$('#renewal_list').slideToggle(400);void 0;" class="metalbtn gonderbtn"><i class="fa fa-refresh"></i> <?php echo __("website/account_products/renewal-now-button"); ?></a>

                        <a href="javascript:void(0)" onclick="$('a[data-tab=dns]').click();" class="turuncbtn gonderbtn"><i class="fas fa-server" aria-hidden="true"></i> <?php echo __("website/account_products/dns-manager"); ?></a>

                        <a href="javascript:void(0)" onclick="$('a[data-tab=whois]').click();" class="mavibtn gonderbtn"><i class="far fa-id-card" aria-hidden="true"></i> <?php echo __("website/account_products/whois-manager"); ?></a>
                        <?php endif; ?>

                    </div>
                </div>

                <div class="hizmetblok">
                    <table width="100%" border="0">
                        <tr>
                            <td colspan="2" bgcolor="#ebebeb">
                                <strong style="float: left;"><?php echo __("website/account_products/general-info"); ?></strong>
                                <?php
                                    if(isset($invoice) && $invoice)
                                    {
                                        ?>
                                        <span style="float:right;"><?php echo __("website/account_invoices/invoice-num"); ?> <a href="<?php echo $invoice["detail_link"]; ?>" target="_blank"><strong><?php echo $invoice["number"] ? $invoice["number"] : "#".$invoice["id"]; ?></strong></a></span>
                                        <?php
                                    }
                                ?>
                            </td>
                        </tr>
                        <tr>
                            <td><strong><?php echo __("website/account_products/service-group"); ?></strong></td>
                            <td><?php echo __("website/account_products/service-groups/domain"); ?></td>
                        </tr>

                        <tr>
                            <td><strong><?php echo __("website/account_products/service-name"); ?></strong></td>
                            <td><?php echo $proanse["name"]; ?></td>
                        </tr>
                        <tr>
                            <td><strong><?php echo __("website/account_products/service-status"); ?></strong></td>
                            <td><?php echo $product_situations[$proanse["status"]]; ?></td>
                        </tr>

                        <?php

                            if(isset($subscription) && $subscription["status"] != "cancelled")
                            {
                                ?>
                                <tr>
                                    <td><strong><?php echo __("website/account_products/auto-pay-1"); ?></strong></td>
                                    <td>
                                        <div id="subscription_status">
                                            <div class="lds-ellipsis"><div></div><div></div><div></div><div></div></div>
                                        </div>
                                        <script type="text/javascript">
                                            $(document).ready(function(){
                                                $.get("<?php echo $links["controller"]; ?>?operation=subscription_detail",function(data){
                                                    $("#subscription_status").html(data);
                                                });
                                            });
                                        </script>
                                    </td>
                                </tr>
                                <?php
                            }

                            if(!isset($subscription) || $subscription["status"] == "cancelled")
                            {
                                $c_s_m = Config::get("modules/card-storage-module");
                                if($c_s_m && $c_s_m != "none")
                                {
                                    $o_a_p = isset($proanse["auto_pay"]) ? $proanse["auto_pay"]  : 0;
                                    ?>
                                    <tr>
                                        <td><strong><?php echo __("website/account_products/auto-pay-1"); ?></strong></td>
                                        <td>
                                            <input onchange="change_auto_pay_status(this);" type="checkbox" class="sitemio-checkbox" id="auto_pay" value="1"<?php echo $o_a_p ? ' checked' : ''; ?>>
                                            <label class="sitemio-checkbox-label" for="auto_pay"></label>

                                            <script type="text/javascript">
                                                function change_auto_pay_status(el){
                                                    let status = $(el).prop('checked') ? 1 : 0;

                                                    if(status === 1 && '<?php echo isset($stored_cards) && $stored_cards ? "true" : "false"; ?>' === "false")
                                                    {
                                                        alert_error("<?php echo __("website/account_products/auto-pay-3"); ?>",{timer:5000});
                                                        $(el).prop('checked',false);
                                                        return false;
                                                    }

                                                    MioAjax({
                                                        action:"<?php echo $links["controller"]; ?>",
                                                        method:"POST",
                                                        data:{operation:"set_auto_pay_status",status:status}
                                                    },true,true);
                                                    alert_success("<?php echo __("website/account_products/auto-pay-4"); ?>",{timer:3000});
                                                }
                                            </script>

                                        </td>
                                    </tr>
                                    <?php
                                }
                            }
                        ?>

                        <tr>
                            <td><strong><?php echo __("website/account_products/payment-period"); ?></strong></td>
                            <td><?php echo View::period($proanse["period_time"],$proanse["period"]); ?></td>
                        </tr>
                        <tr align="center" class="tutartd">
                            <td colspan="2"><strong><?php echo __("website/account_products/register-amount"); ?> : <?php echo $amount; ?></strong></td>
                        </tr>
                    </table>
                </div>


                <div class="hizmetblok">
                    <table width="100%" border="0">
                        <tr>
                            <td colspan="2" bgcolor="#ebebeb" ><strong><?php echo __("website/account_products/payment-details"); ?></strong></td>
                        </tr>

                        <tr>
                            <td><strong><?php echo __("website/account_products/registration-date"); ?></strong></td>
                            <td><?php echo DateManager::format(Config::get("options/date-format"),$proanse["cdate"]); ?></td>
                        </tr>

                        <?php if(substr($proanse["duedate"],0,4) != "1881" && substr($proanse["duedate"],0,4) != "1970"): ?>
                            <tr>
                                <td><strong><?php echo __("website/account_products/due-date"); ?></strong></td>
                                <td><?php echo DateManager::format(Config::get("options/date-format"),$proanse["duedate"]); ?></td>
                            </tr>
                        <?php endif; ?>

                        <?php if(substr($proanse["renewaldate"],0,4) != "1881" && substr($proanse["renewaldate"],0,4) != "1970"): ?>
                            <tr>
                                <td><strong><?php echo __("website/account_products/last-paid-date"); ?></strong></td>
                                <td><?php echo DateManager::format(Config::get("options/date-format"),$proanse["renewaldate"]); ?></td>
                            </tr>
                        <?php endif; ?>

                        <?php if($proanse["period_time"]): ?>
                            <tr>
                                <td><strong><?php echo __("website/account_products/registration-period"); ?></strong></td>
                                <td><?php echo $proanse["period_time"]." ".__("website/account_products/registration-year"); ?></td>
                            </tr>
                        <?php endif; ?>

                        <tr align="center" class="tutartd">
                            <td colspan="2"><strong><?php echo __("website/account_products/renewal-amount"); ?> : <?php echo $renewal_amount; ?></strong></td>
                        </tr>
                        </tr>

                    </table>

                </div>



            </div>
        <?php endif; ?>

        <?php if($proanse["status"] == "active"){ ?>
            <div id="tab-whois" class="tabcontent">
                <div class="tabcontentcon">

                    <script type="text/javascript">
                        $(document).ready(function(){
                            $("#whois_privacy_status").change(function(){
                                var status = $(this).data("status");
                                var request = MioAjax({
                                    action:'<?php echo $links["controller"]; ?>',
                                    data:{
                                        operation:"domain_whois_privacy",
                                        status:status,
                                    },
                                    method:"POST",
                                },true,true);

                                request.done(function (result){
                                    if(result != ''){
                                        var solve = getJson(result);
                                        if(solve !== false){
                                            if(solve.status == "error"){
                                                if(solve.message != undefined && solve.message != '')
                                                    alert_error(solve.message,{timer:5000});
                                            }else if(solve.status == "successful"){

                                                if(solve.message != undefined && solve.message != '' && solve.message != 'go-to-basket')
                                                    alert_success(solve.message,{timer:3000});

                                                if(solve.redirect != undefined && solve.redirect != ''){
                                                    setTimeout(function(){
                                                        window.location.href = solve.redirect;
                                                    },solve.message == "go-to-basket" ? 1 : 3000);
                                                }
                                            }
                                        }else
                                            console.log(result);
                                    }
                                });
                            });

                            var tab_whois = gGET("whois");
                            if (tab_whois != '' && tab_whois != undefined) {
                                $("#tab-whois .tablinks[data-tab='" + tab_whois + "']").click();
                            } else {
                                $("#tab-whois .tablinks:eq(0)").addClass("active");
                                $("#tab-whois .tabcontent:eq(0)").css("display", "block");
                            }


                            var tab_ct = gGET("contact-type");
                            if (tab_ct != '' && tab_ct != undefined) {
                                $("#tab-contact-type .tablinks[data-tab='" + tab_ct + "']").click();
                            } else {
                                $("#tab-contact-type .tablinks:eq(0)").addClass("active");
                                $("#tab-contact-type .tabcontent:eq(0)").css("display", "block");
                            }

                            $(".select-whois-profile").change(function(){
                                tab_ct              = gGET("contact-type");

                                if(tab_ct === "" || tab_ct === null) tab_ct = "registrant";

                                var profile         =  $(this).val();
                                var profile_o       = $("option[value="+profile+"]",$(this));
                                var wrap            = $("#contact-type-"+tab_ct);

                                $(".profile-name-wrap",wrap).css("display","none");

                                if(profile === "new")
                                {
                                    $(".profile-name-wrap",wrap).css("display","block");
                                    $(".profile-name-wrap input",wrap).focus();
                                }
                                else
                                {
                                    var info            = profile_o.data("information");
                                    var info_keys       = Object.keys(info);

                                    $(info_keys).each(function(k,v){
                                        $(".whois-"+tab_ct+"-"+v).val(info[v]);
                                    });
                                }

                            });


                        });
                        function ModifyWhois_submit(result) {
                            if(result != ''){
                                var solve = getJson(result);
                                if(solve !== false){
                                    if(solve.status == "error"){
                                        if(solve.for != undefined && solve.for != ''){
                                            $("#ModifyWhois "+solve.for).focus();
                                            $("#ModifyWhois "+solve.for).attr("style","border-bottom:2px solid red; color:red;");
                                            $("#ModifyWhois "+solve.for).change(function(){
                                                $(this).removeAttr("style");
                                            });
                                        }
                                        if(solve.message != undefined && solve.message != '')
                                            alert_error(solve.message,{timer:5000});
                                    }else if(solve.status == "successful"){
                                        alert_success(solve.message,{timer:4000});
                                        setTimeout(function(){
                                            window.location.href = location.href;
                                        },2000);
                                    }
                                }else
                                    console.log(result);
                            }
                        }

                    </script>

                    <div id="tab-whois" class="subtab">
                        <ul class="tab">
                            <li><a href="javascript:void(0)" class="tablinks" onclick="open_tab(this, 'information','whois')" data-tab="information"><i class="far fa-id-card" aria-hidden="true"></i> <?php echo __("website/account_products/current-whois"); ?></a></li>

                            <li><a href="javascript:void(0)" class="tablinks" onclick="open_tab(this, 'protection','whois')" data-tab="protection"><i class="fas fa-user-shield"></i> <?php echo __("website/account_products/domain-whois-tx1"); ?></a></li>
                        </ul>

                        <div id="whois-information" class="tabcontent">
                            <div class="blue-info">
                                <div class="padding15">
                                    <i class="fa fa-info-circle" aria-hidden="true"></i>
                                    <p><?php echo __("admin/orders/update-whois-info-desc"); ?></p>
                                </div>
                            </div>
                            <div class="line"></div>

                            <form action="<?php echo $links["controller"]; ?>" method="post" id="ModifyWhois">
                                <input type="hidden" name="operation" value="domain_modify_whois">

                                <div id="tab-contact-type">
                                    <ul class="tab">
                                        <?php
                                            if(isset($contact_types) && $contact_types)
                                            {
                                                foreach($contact_types AS $k => $v)
                                                {
                                                    ?>
                                                    <li><a href="javascript:void 0;" class="tablinks" onclick="open_tab(this,'<?php echo $k; ?>','contact-type');" data-tab="<?php echo $k; ?>"><?php echo $v; ?></a></li>
                                                    <?php
                                                }
                                            }
                                        ?>
                                        <a style="float:right;margin: 14px 15px 0 0;" href="<?php echo $links["whois-profiles"]; ?>" class="green lbtn"><i style="margin-right: 5px;" class="far fa-id-card"></i> <?php echo __("website/account_products/domain-whois-tx6"); ?></a>
                                    </ul>

                                    <?php
                                        if(isset($contact_types) && $contact_types)
                                        {
                                            foreach($contact_types AS $k => $v)
                                            {
                                                ?>
                                                <div id="contact-type-<?php echo $k; ?>" class="tabcontent">

                                                    <div class="formcon">
                                                        <div class="yuzde30"><?php echo __("website/account_products/domain-whois-tx18"); ?></div>
                                                        <div class="yuzde70">
                                                            <select class="select-whois-profile" name="profile_id[<?php echo $k; ?>]">
                                                                <option data-information='<?php echo Utility::jencode($user_whois_info ?? []); ?>' value="0"><?php echo __("website/account_products/domain-whois-tx20"); ?></option>
                                                                <?php
                                                                    if(isset($whois_profiles) && $whois_profiles)
                                                                    {
                                                                        foreach($whois_profiles AS $pf)
                                                                        {
                                                                            $s_pf = $whois[$k]["profile_id"] ?? 0;
                                                                            $pf_s = $s_pf == $pf["id"];
                                                                            ?>
                                                                            <option<?php echo $pf_s ? ' selected' : ''; ?> data-information='<?php echo $pf["information"]; ?>' value="<?php echo $pf["id"]; ?>"><?php echo $pf["name"].' - '.$pf["person_name"]." - ".$pf["person_email"]." - ".$pf["person_phone"];  ?></option>
                                                                            <?php
                                                                        }
                                                                    }
                                                                ?>
                                                                <option value="new">+ <?php echo __("website/account_products/domain-whois-tx19"); ?></option>
                                                            </select>

                                                            <div class="formcon profile-name-wrap" style="display: none;">
                                                                <div class="yuzde30"><?php echo __("website/account_products/domain-whois-tx7"); ?></div>
                                                                <div class="yuzde70">
                                                                    <input name="profile_name[<?php echo $k; ?>]" value="" type="text" placeholder="<?php echo __("website/account_products/domain-whois-tx7"); ?>" style="padding: 8px;width: 100%;">
                                                                </div>
                                                            </div>

                                                            <div style="margin-top: 15px;display: inline-block;">
                                                                    <input type="checkbox" name="apply_to_all[<?php echo $k; ?>]" value="1" class="checkbox-custom" id="apply_to_all_<?php echo $k; ?>">
                                                                    <label class="checkbox-custom-label" for="apply_to_all_<?php echo $k; ?>"><?php echo __("website/account_products/domain-whois-tx21"); ?></label>
                                                                </div>

                                                        </div>
                                                    </div>


                                                    <input name="info[<?php echo $k; ?>][Name]" value="<?php echo $whois[$k]["Name"] ?? ''; ?>" type="text" class="yuzde33 whois-<?php echo $k; ?>-Name" placeholder="<?php echo __("website/account_products/whois-full_name"); ?>">
                                                    <input name="info[<?php echo $k; ?>][Company]" value="<?php echo $whois[$k]["Company"] ?? ''; ?>" type="text" class="yuzde33 whois-<?php echo $k; ?>-Company" placeholder="<?php echo __("website/account_products/whois-company_name"); ?>">
                                                    <input name="info[<?php echo $k; ?>][EMail]" value="<?php echo $whois[$k]["EMail"] ?? ''; ?>" type="text" class="yuzde33 whois-<?php echo $k; ?>-EMail" placeholder="<?php echo __("website/account_products/whois-email"); ?>">
                                                    <input name="info[<?php echo $k; ?>][PhoneCountryCode]" value="<?php echo $whois[$k]["PhoneCountryCode"] ?? ''; ?>" type="text" class="yuzde33 whois-<?php echo $k; ?>-PhoneCountryCode" placeholder="<?php echo __("website/account_products/whois-phoneCountryCode"); ?>">
                                                    <input name="info[<?php echo $k; ?>][Phone]" type="text" value="<?php echo $whois[$k]["Phone"] ?? ''; ?>" class="yuzde33 whois-<?php echo $k; ?>-Phone" placeholder="<?php echo __("website/account_products/whois-phone"); ?>">
                                                    <input name="info[<?php echo $k; ?>][FaxCountryCode]" type="text" value="<?php echo $whois[$k]["FaxCountryCode"] ?? ''; ?>" class="yuzde33 whois-<?php echo $k; ?>-FaxCountryCode" placeholder="<?php echo __("website/account_products/whois-faxCountryCode"); ?>">
                                                    <input name="info[<?php echo $k; ?>][Fax]" type="text" value="<?php echo $whois[$k]["Fax"] ?? ''; ?>" class="yuzde33 whois-<?php echo $k; ?>-Fax" placeholder="<?php echo __("website/account_products/whois-fax"); ?>">
                                                    <input name="info[<?php echo $k; ?>][City]" type="text" value="<?php echo $whois[$k]["City"] ?? ''; ?>" class="yuzde33 whois-<?php echo $k; ?>-City" placeholder="<?php echo __("website/account_products/whois-city"); ?>">
                                                    <input name="info[<?php echo $k; ?>][State]" type="text" value="<?php echo $whois[$k]["State"] ?? ''; ?>" class="yuzde33 whois-<?php echo $k; ?>-State" placeholder="<?php echo __("website/account_products/whois-state"); ?>">
                                                    <input name="info[<?php echo $k; ?>][Address]" type="text" value="<?php echo $whois[$k]["AddressLine1"] ?? 'unknown'; ?>" class="yuzde33 whois-<?php echo $k; ?>-Address" placeholder="<?php echo __("website/account_products/whois-address"); ?>">
                                                    <input name="info[<?php echo $k; ?>][Country]" type="text" value="<?php echo $whois[$k]["Country"] ?? ''; ?>" class="yuzde33 whois-<?php echo $k; ?>-Country" placeholder="<?php echo __("website/account_products/whois-CountryCode"); ?>">
                                                    <input name="info[<?php echo $k; ?>][ZipCode]" type="text" value="<?php echo $whois[$k]["ZipCode"] ?? ''; ?>" class="yuzde33 whois-<?php echo $k; ?>-ZipCode" placeholder="<?php echo __("website/account_products/whois-zipcode"); ?>">
                                                </div>
                                                <?php
                                            }
                                        }
                                    ?>

                                </div>

                                <div class="line"></div>

                                <?php if($module_con && method_exists($module_con,'resend_verification_mail')): ?>
                                    <div style="float: left;">
                                        <a href="javascript:void(0);" class="yesilbtn gonderbtn mio-ajax-submit" mio-ajax-options='{"action":"<?php echo $links["controller"];?>","method":"POST","data":{"operation":"domain_resend_verification_mail"},"type":"direct","result":"ResendVerificationMail","waiting_text":"<?php echo addslashes(__("website/others/button4-pending")); ?>"}'><i class="fa fa-paper-plane" aria-hidden="true"></i> <?php echo __("website/account_products/domain-resend-verification"); ?></a>
                                    </div>

                                <?php endif; ?>

                                <div style="float: right;">
                                    <a href="javascript:void(0);" class="yesilbtn gonderbtn mio-ajax-submit" mio-ajax-options='{"result":"ModifyWhois_submit","waiting_text":"<?php echo addslashes(__("website/others/button1-pending")); ?>"}'><?php echo __("website/account_products/whois-modify-button"); ?></a>
                                </div>
                                <div class="clear"></div>


                            </form>
                            <div class="clear"></div>

                        </div>

                        <div id="whois-protection" class="tabcontent">

                            <div class="whois-protection-area">

                                <div class="blue-info">
                                    <div class="padding15">
                                        <i class="fa fa-shield" aria-hidden="true"></i>
                                        <p><?php echo __("website/account_products/domain-whois-tx2"); ?></p>
                                    </div>
                                </div>
                                <div class="line"></div>

                                <div class="formcon">
                                    <div class="yuzde30"><?php echo __("website/account_products/domain-whois-tx3"); ?></div>
                                    <div class="yuzde70">
                                        <input<?php echo isset($wprivacy) && $wprivacy ? ' checked' : ''; ?> type="checkbox" class="sitemio-checkbox" id="whois_privacy_status" value="1" data-status="<?php echo isset($wprivacy) && $wprivacy ? 'disable' : 'enable'; ?>">
                                        <label class="sitemio-checkbox-label" for="whois_privacy_status"></label>
                                    </div>
                                </div>

                                <?php if(isset($wprivacy_price) && $wprivacy_price != ''): ?>
                                    <div class="formcon">
                                        <div class="yuzde30"><?php echo __("website/account_products/domain-whois-tx4"); ?></div>
                                        <div class="yuzde70">
                                            <strong><?php echo $wprivacy_price; ?></strong> / <?php echo ___("date/period-year"); ?>
                                        </div>
                                    </div>
                                <?php endif; ?>

                                <?php if(isset($wprivacy_endtime) && $wprivacy_endtime): ?>
                                    <div class="formcon">
                                        <div class="yuzde30"><?php echo __("website/account_products/domain-whois-tx5"); ?></div>
                                        <div class="yuzde70">
                                            <?php echo $wprivacy_endtime; ?>
                                        </div>
                                    </div>
                                <?php endif; ?>

                            </div>


                        </div>

                    </div>


                </div>
            </div>
        <?php } ?>


        <?php  if($proanse["status"] == "active" && isset($options["dns_manage"]) && $options["dns_manage"]){ ?>
            <div id="tab-dns" class="tabcontent">
                <div class="tabcontentcon">

                    <script type="text/javascript">
                        var record_k,record_type,record_name,record_value,record_identity,record_ttl,record_priority,record_names = [],record_values = [],record_identities = [];
                        var record_digest,record_key_tag,record_digest_type,record_algorithm;

                        $(document).ready(function(){
                            var tab_dns = gGET("dns");
                            if (tab_dns != '' && tab_dns != undefined) {
                                $("#tab-dns .tablinks[data-tab='" + tab_dns + "']").click();
                            }
                            else
                            {
                                $("#tab-dns .tablinks:eq(0)").addClass("active");
                                $("#tab-dns .tabcontent:eq(0)").css("display", "block");
                            }

                            <?php if($allow_dns_records): ?>
                            $("#getDnsRecords_tbody").html($("#template-loader").html());
                            setTimeout(function(){
                                reload_dns_records();
                            },500);
                            <?php endif; ?>

                            <?php if($allow_dns_cns): ?>
                            $("#cns-wrap").html($("#template-loaderx").html());
                            setTimeout(function(){
                                reload_dns_cns();
                            },500);
                            <?php endif; ?>

                            <?php if($allow_dns_sec_records): ?>
                            $("#getDnsSecRecords_tbody").html($("#template-loader").html());
                            setTimeout(function(){
                                reload_dns_sec_records();
                            },500);
                            <?php endif; ?>


                            $("#DnsRecord_type").change(function(){
                                var type = $(this).val();

                                $("#DnsRecord_priority").css("display","none");

                                if(type === "MX")
                                {
                                    $("#DnsRecord_priority").css("display","inline-block");
                                }

                                if(type === "A") $("#DnsRecord_value").attr("placeholder","The IPV4 Address");
                                else if(type === "AAAA") $("#DnsRecord_value").attr("placeholder","The IPV6 Address");
                                else if(type === "CNAME" || type === "MX") $("#DnsRecord_value").attr("placeholder","The Target Hostname");
                                else if(type === "TXT") $("#DnsRecord_value").attr("placeholder","The Text");
                                else
                                    $("#DnsRecord_value").attr("placeholder","");
                            });

                            $("#cns-wrap").on("click",".mio-ajax-submit",function(){
                                MioAjaxElement(this);
                            });

                        });

                        function reload_dns_cns(add_loader)
                        {
                            if(add_loader) $("#cns-wrap").html($("#template-loaderx").html());

                            $.get("<?php echo $links["controller"]; ?>?bring=cns-list",function(result){
                                $("#cns-wrap").html(result);
                                $("#addCNS input[type=text]").val('');
                            });
                        }

                        function reload_dns_records(add_loader)
                        {
                            if(add_loader) $("#getDnsRecords_tbody").html($("#template-loader").html());

                            $.get("<?php echo $links["controller"]; ?>?bring=dns-records",function(result){
                                $("#getDnsRecords_tbody").html(result);
                            });
                        }

                        function editDnsRecord(k)
                        {
                            record_k        = k;
                            record_type     = $("#DnsRecord_"+k+" input[name=type]").val();
                            record_name     = $("#DnsRecord_"+k+" .dns-record-name .show-wrap").html();
                            record_value    = $("#DnsRecord_"+k+" .dns-record-value .show-wrap").html();

                            record_names[k]         = record_name;
                            record_values[k]        = record_value;


                            $("#getDnsRecords_tbody .edit-wrap").css("display","none");
                            $("#getDnsRecords_tbody .edit-wrap-ttl").css("display","none");
                            $("#getDnsRecords_tbody .edit-wrap-priority").css("display","none");
                            $("#getDnsRecords_tbody .edit-content").css("display","none");
                            $("#getDnsRecords_tbody .show-wrap").css("display","block");
                            $("#getDnsRecords_tbody .show-wrap-ttl").css("display","block");
                            $("#getDnsRecords_tbody .show-wrap-priority").css("display","inline-block");
                            $("#getDnsRecords_tbody .no-edit-content").css("display","block");
                            $("#getDnsRecords_tbody tr").removeClass("editing-active");

                            $("#DnsRecord_"+k+" .dns-record-name input").val(record_name);
                            $("#DnsRecord_"+k+" .dns-record-value input").val(record_value);

                            $("#DnsRecord_"+k+" .edit-content").css("display","block");
                            $("#DnsRecord_"+k+" .edit-wrap").css("display","block");
                            $("#DnsRecord_"+k+" .edit-wrap-ttl").css("display","block");
                            $("#DnsRecord_"+k+" .edit-wrap-priority").css("display","inline-block");
                            $("#DnsRecord_"+k+" .no-edit-content").css("display","none");
                            $("#DnsRecord_"+k+" .show-wrap").css("display","none");
                            $("#DnsRecord_"+k+" .show-wrap-ttl").css("display","none");
                            $("#DnsRecord_"+k+" .show-wrap-priority").css("display","none");
                            $("#DnsRecord_"+k).addClass('editing-active');
                        }

                        function cancelDnsRecord(k)
                        {
                            $("#getDnsRecords_tbody .edit-wrap").css("display","none");
                            $("#getDnsRecords_tbody .edit-wrap-ttl").css("display","none");
                            $("#getDnsRecords_tbody .edit-wrap-priority").css("display","none");
                            $("#getDnsRecords_tbody .edit-content").css("display","none");
                            $("#getDnsRecords_tbody .show-wrap").css("display","block");
                            $("#getDnsRecords_tbody .show-wrap-ttl").css("display","block");
                            $("#getDnsRecords_tbody .show-wrap-priority").css("display","inline-block");
                            $("#getDnsRecords_tbody .no-edit-content").css("display","block");
                            $("#getDnsRecords_tbody tr").removeClass("editing-active");
                        }

                        function saveDnsRecord(k,el)
                        {
                            record_type         = $("#DnsRecord_"+k+" input[name=type]").val();
                            record_identity     = $("#DnsRecord_"+k+" input[name=identity]").val();
                            record_name         = $("#DnsRecord_"+k+" .dns-record-name input").val();
                            record_value        = $("#DnsRecord_"+k+" .dns-record-value input").val();
                            record_ttl          = $("#DnsRecord_"+k+" .dns-record-ttl .edit-wrap-ttl select").val();
                            record_priority     = $("#DnsRecord_"+k+" .dns-record-ttl .edit-wrap-priority input").val();

                            $("#DnsRecord_"+k+" .dns-record-name .show-wrap").html(record_name);
                            $("#DnsRecord_"+k+" .dns-record-value .show-wrap").html(record_value);

                            var request = MioAjax({
                                button_element:$(el),
                                waiting_text: '<i style="-webkit-animation:fa-spin 2s infinite linear;animation: fa-spin 2s infinite linear;" class="fa fa-spinner" aria-hidden="true"></i>',
                                action: "<?php echo $links["controller"]; ?>",
                                method: "POST",
                                data:
                                    {
                                        operation: "update_dns_record",
                                        type:record_type,
                                        name:record_name,
                                        value:record_value,
                                        identity:record_identity,
                                        ttl:record_ttl,
                                        priority:record_priority,
                                    }
                            },true,true);

                            request.done(function(result){
                                if(result !== ''){
                                    var solve = getJson(result);
                                    if(solve !== false){
                                        if(solve.status === "error")
                                            alert_error(solve.message,{timer:3000});
                                        else if(solve.status === "successful")
                                        {
                                            alert_success("<?php echo __("website/account_products/domain-dns-records-8"); ?>",{timer:3000});
                                            reload_dns_records(true);
                                        }
                                    }else
                                        console.log(result);
                                }
                            });

                        }
                        function removeDnsRecord(k,el)
                        {
                            if(!confirm("<?php echo ___("needs/delete-are-you-sure"); ?>")) return false;

                            record_type         = $("#DnsRecord_"+k+" input[name=type]").val();
                            record_identity     = $("#DnsRecord_"+k+" input[name=identity]").val();
                            record_name         = $("#DnsRecord_"+k+" input[name=name]").val();
                            record_value        = $("#DnsRecord_"+k+" input[name=value]").val();


                            var request = MioAjax({
                                button_element:$(el),
                                waiting_text: "<?php echo addslashes(__("website/others/button1-pending")); ?>",
                                action: "<?php echo $links["controller"]; ?>",
                                method: "POST",
                                data:
                                    {
                                        operation: "delete_dns_record",
                                        type:record_type,
                                        name:record_name,
                                        value:record_value,
                                        identity:record_identity
                                    }
                            },true,true);

                            request.done(function(result){
                                if(result !== ''){
                                    var solve = getJson(result);
                                    if(solve !== false){
                                        if(solve.status === "error")
                                            alert_error(solve.message,{timer:3000});
                                        else if(solve.status === "successful")
                                        {
                                            $("#DnsRecord_"+k).remove();

                                            alert_success("<?php echo __("website/account_products/domain-dns-records-9"); ?>",{timer:3000});
                                            //reload_dns_records(true);
                                        }
                                    }else
                                        console.log(result);
                                }
                            });
                        }

                        function addDnsRecord(el)
                        {
                            record_type         = $("#DnsRecord_type").val();
                            record_name         = $("#DnsRecord_name").val();
                            record_value        = $("#DnsRecord_value").val();
                            record_ttl          = $("#DnsRecord_ttl").val();
                            record_priority     = $("#DnsRecord_priority").val();

                            var request = MioAjax({
                                button_element:$(el),
                                waiting_text: "<?php echo addslashes(__("website/others/button1-pending")); ?>",
                                action: "<?php echo $links["controller"]; ?>",
                                method: "POST",
                                data:
                                    {
                                        operation: "add_dns_record",
                                        type:record_type,
                                        name:record_name,
                                        value:record_value,
                                        ttl:record_ttl,
                                        priority:record_priority,
                                    }
                            },true,true);

                            request.done(function(result){
                                if(result !== ''){
                                    var solve = getJson(result);
                                    if(solve !== false){
                                        if(solve.status === "error")
                                            alert_error(solve.message,{timer:3000});
                                        else if(solve.status === "successful")
                                        {
                                            $("#DnsRecord_type").val('');
                                            $("#DnsRecord_name").val('');
                                            $("#DnsRecord_value").val('');


                                            alert_success("<?php echo __("website/account_products/domain-dns-records-7"); ?>",{timer:3000});
                                            reload_dns_records(true);
                                        }
                                    }else
                                        console.log(result);
                                }
                            });
                        }

                        function reload_dns_sec_records(add_loader)
                        {
                            if(add_loader) $("#getDnsSecRecords_tbody").html($("#template-loader").html());

                            $.get("<?php echo $links["controller"]; ?>?bring=dns-sec-records",function(result){
                                $("#getDnsSecRecords_tbody").html(result);
                            });
                        }

                        function addDnsSecRecord(el)
                        {
                            record_digest       = $("#DnsSecRecord_digest").val();
                            record_key_tag       = $("#DnsSecRecord_key_tag").val();
                            record_digest_type  = $("#DnsSecRecord_digest_type").val();
                            record_algorithm    = $("#DnsSecRecord_algorithm").val();

                            var request = MioAjax({
                                button_element:$(el),
                                waiting_text: "<?php echo addslashes(__("website/others/button1-pending")); ?>",
                                action: "<?php echo $links["controller"]; ?>",
                                method: "POST",
                                data:
                                    {
                                        operation: "add_dns_sec_record",
                                        digest:record_digest,
                                        key_tag:record_key_tag,
                                        digest_type:record_digest_type,
                                        algorithm:record_algorithm,
                                    }
                            },true,true);

                            request.done(function(result){
                                if(result !== ''){
                                    var solve = getJson(result);
                                    if(solve !== false){
                                        if(solve.status === "error")
                                            alert_error(solve.message,{timer:3000});
                                        else if(solve.status === "successful")
                                        {
                                            $("#DnsSecRecord_digest").val('');
                                            $("#DnsSecRecord_key_tag").val('');
                                            $("#DnsSecRecord_digest_type").val('');
                                            $("#DnsSecRecord_algorithm").val('');


                                            alert_success("<?php echo __("website/account_products/domain-dns-records-7"); ?>",{timer:3000});
                                            reload_dns_sec_records(true);
                                        }
                                    }else
                                        console.log(result);
                                }
                            });
                        }

                        function removeDnsSecRecord(k,el)
                        {
                            if(!confirm("<?php echo ___("needs/delete-are-you-sure"); ?>")) return false;

                            record_identity         = $("#DnsSecRecord_"+k+" input[name=identity]").val();
                            record_digest           = $("#DnsSecRecord_"+k+" input[name=digest]").val();
                            record_key_tag           = $("#DnsSecRecord_"+k+" input[name=key_tag]").val();
                            record_digest_type      = $("#DnsSecRecord_"+k+" input[name=digest_type]").val();
                            record_algorithm        = $("#DnsSecRecord_"+k+" input[name=algorithm]").val();


                            var request = MioAjax({
                                button_element:$(el),
                                waiting_text: "<?php echo addslashes(__("website/others/button1-pending")); ?>",
                                action: "<?php echo $links["controller"]; ?>",
                                method: "POST",
                                data:
                                    {
                                        operation: "delete_dns_sec_record",
                                        identity:record_identity,
                                        digest:record_digest,
                                        key_tag:record_key_tag,
                                        digest_type:record_digest_type,
                                        algorithm:record_algorithm
                                    }
                            },true,true);

                            request.done(function(result){
                                if(result !== ''){
                                    var solve = getJson(result);
                                    if(solve !== false){
                                        if(solve.status === "error")
                                            alert_error(solve.message,{timer:3000});
                                        else if(solve.status === "successful")
                                        {
                                            $("#DnsSecRecord_"+k).remove();

                                            alert_success("<?php echo __("website/account_products/domain-dns-records-9"); ?>",{timer:3000});
                                            //reload_dns_records(true);
                                        }
                                    }else
                                        console.log(result);
                                }
                            });
                        }
                    </script>


                    <div id="tab-dns" class="subtab">

                        <ul class="tab">

                            <li><a href="javascript:void(0)" class="tablinks" onclick="open_tab(this, 'current','dns')" data-tab="current"><i class="fas fa-angle-right"></i> <?php echo __("website/account_products/domain-current-dns"); ?></a></li>

                            <?php if($allow_dns_cns): ?>
                                <li><a href="javascript:void(0)" class="tablinks" onclick="open_tab(this, 'cns','dns')" data-tab="cns"><i class="fas fa-angle-right"></i> <?php echo __("admin/orders/domain-cns-management"); ?></a></li>
                            <?php endif; ?>

                            <?php if($allow_dns_records): ?>
                                <li><a href="javascript:void(0)" class="tablinks" onclick="open_tab(this, 'records','dns')" data-tab="records"><i class="fas fa-angle-right"></i> <?php echo __("website/account_products/domain-dns-records-1"); ?></a></li>
                            <?php endif; ?>

                            <?php if($allow_dns_sec_records): ?>
                                <li><a href="javascript:void(0)" class="tablinks" onclick="open_tab(this, 'sec-records','dns')" data-tab="sec-records"><i class="fas fa-angle-right"></i> <?php echo __("website/account_products/domain-dns-records-13"); ?></a></li>
                            <?php endif; ?>


                        </ul>

                        <div id="dns-current" class="tabcontent">
                            <div class="blue-info">
                                <div class="padding15">
                                    <i class="fa fa-info-circle" aria-hidden="true"></i>
                                    <p><?php echo __("website/account_products/domain-dns-tx1"); ?></p>
                                </div>
                            </div>

                            <div class="padding20">
                                <script type="text/javascript">
                                    function ModifyDns_submit(result) {
                                        if(result != ''){
                                            var solve = getJson(result);
                                            if(solve !== false){
                                                if(solve.status == "error"){
                                                    if(solve.for != undefined && solve.for != ''){
                                                        $("#ModifyDns "+solve.for).focus();
                                                        $("#ModifyDns "+solve.for).attr("style","border-bottom:2px solid red; color:red;");
                                                        $("#ModifyDns "+solve.for).change(function(){
                                                            $(this).removeAttr("style");
                                                        });
                                                    }
                                                    if(solve.message != undefined && solve.message != '')
                                                        alert_error(solve.message,{timer:5000});
                                                }else if(solve.status == "successful"){
                                                    alert_success(solve.message,{timer:3000});
                                                    if(solve.redirect != undefined && solve.redirect != ''){
                                                        setTimeout(function(){
                                                            window.location.href = solve.redirect;
                                                        },3000);
                                                    }
                                                }
                                            }else
                                                console.log(result);
                                        }
                                    }
                                </script>
                                <form action="<?php echo $links["controller"]; ?>" method="post" id="ModifyDns">

                                    <input type="hidden" name="operation" value="domain_modify_dns">

                                    <div class="formcon">
                                        <div class="yuzde30" style="text-align:center;">#1</div>
                                        <div class="yuzde70"><input name="dns[]" value="<?php echo isset($options["ns1"]) ? $options["ns1"] : false; ?>" type="text" class="" placeholder="<?php echo"ns.".($proanse["options"]["domain"] ?? $proanse["name"]); ?>">
                                        </div>
                                    </div>

                                    <div class="formcon">
                                        <div class="yuzde30" style="text-align:center;">#2</div>
                                        <div class="yuzde70">
                                            <input name="dns[]" value="<?php echo isset($options["ns2"]) ? $options["ns2"] : false; ?>" type="text" class="" placeholder="<?php echo"ns.".($proanse["options"]["domain"] ?? $proanse["name"]); ?>">
                                        </div>
                                    </div>

                                    <div class="formcon">
                                        <div class="yuzde30" style="text-align:center;">#3</div>
                                        <div class="yuzde70">
                                            <input name="dns[]" value="<?php echo isset($options["ns3"]) ? $options["ns3"] : false; ?>" type="text" class="" placeholder="<?php echo"ns.".($proanse["options"]["domain"] ?? $proanse["name"]); ?>">
                                        </div>
                                    </div>

                                    <div class="formcon">
                                        <div class="yuzde30" style="text-align:center;">#4</div>
                                        <div class="yuzde70">
                                            <input name="dns[]" value="<?php echo isset($options["ns4"]) ? $options["ns4"] : false; ?>" type="text" class="" placeholder="<?php echo"ns.".($proanse["options"]["domain"] ?? $proanse["name"]); ?>">
                                        </div>
                                    </div>

                                    <a style="float:right;" href="javascript:void(0);" class="mavibtn gonderbtn mio-ajax-submit" mio-ajax-options='{"result":"ModifyDns_submit","waiting_text":"<?php echo addslashes(__("website/others/button1-pending")); ?>"}'><?php echo __("website/account_products/domain-dns-update"); ?></a>

                                </form>
                                <div class="clear"></div>
                            </div>
                        </div>

                        <?php if($allow_dns_cns): ?>
                            <div id="dns-cns" class="tabcontent" style="display: none">
                                <div class="blue-info">
                                    <div class="padding15">
                                        <i class="fa fa-info-circle" aria-hidden="true"></i>
                                        <p><?php echo __("website/account_products/domain-dns-tx2"); ?></p>
                                    </div>
                                </div>
                                <div class="padding20">
                                    <form action="<?php echo $links["controller"]; ?>" method="post" id="addCNS">
                                        <input type="hidden" name="operation" value="domain_add_cns">

                                        <div class="yuzde33"><input name="ns" type="text" class="" placeholder="ns1.<?php echo $proanse["options"]["domain"] ?? $proanse["name"]; ?>"></div>
                                        <div class="yuzde33"><input name="ip" type="text" class="" placeholder="192.168.1.1"></div>
                                        <div class="yuzde33"> <a style="width:240px;float:none;" href="javascript:void(0);" class="yesilbtn gonderbtn mio-ajax-submit" mio-ajax-options='{"result":"addCNS_submit","waiting_text":"<?php echo addslashes(__("website/others/button1-pending")); ?>"}'><?php echo __("website/account_products/add-ns-button"); ?></a></div>

                                    </form>
                                    <script type="text/javascript">
                                        function addCNS_submit(result) {
                                            if(result != ''){
                                                var solve = getJson(result);
                                                if(solve !== false){
                                                    if(solve.status == "error"){
                                                        if(solve.for != undefined && solve.for != ''){
                                                            $("#addCNS "+solve.for).focus();
                                                            $("#addCNS "+solve.for).attr("style","border-bottom:2px solid red; color:red;");
                                                            $("#addCNS "+solve.for).change(function(){
                                                                $(this).removeAttr("style");
                                                            });
                                                        }
                                                        if(solve.message != undefined && solve.message != '')
                                                            alert_error(solve.message,{timer:5000});
                                                    }else if(solve.status == "successful"){
                                                        alert_success(solve.message,{timer:3000});
                                                        if(solve.redirect != undefined && solve.redirect != ''){
                                                            setTimeout(function(){
                                                                reload_dns_cns(true);
                                                            },3000);
                                                        }
                                                    }
                                                }else
                                                    console.log(result);
                                            }
                                        }
                                    </script>

                                    <div class="line"></div>

                                    <div id="cns-wrap"></div>

                                    <script type="text/javascript">
                                        function delete_confirm() {
                                            var co = confirm("<?php echo __("website/account_products/delete-are-you-sure"); ?>");
                                            return co;
                                        }
                                        function DeleteCNS(result) {
                                            if(result != ''){
                                                var solve = getJson(result);
                                                if(solve !== false){
                                                    if(solve.status == "error"){
                                                        swal(
                                                            '<?php echo __("website/account_products/modal-error-title"); ?>',
                                                            solve.message,
                                                            'error'
                                                        )
                                                    }else if(solve.status == "successful"){
                                                        swal({
                                                            type: 'success',
                                                            title: '<?php echo __("website/account_products/modal-success-title"); ?>',
                                                            text: '<?php echo __("website/account_products/deleted-domain-cns"); ?>',
                                                            showConfirmButton: false,
                                                            timer: 1500
                                                        });
                                                        setTimeout(function(){
                                                            reload_dns_cns(true);
                                                        },1500);
                                                    }
                                                }else
                                                    console.log(result);
                                            }
                                        }
                                        function ModifyCNS(result) {
                                            if(result != ''){
                                                var solve = getJson(result);
                                                if(solve !== false){
                                                    if(solve.status == "error"){
                                                        swal(
                                                            '<?php echo __("website/account_products/modal-error-title"); ?>',
                                                            solve.message,
                                                            'error'
                                                        )
                                                    }else if(solve.status == "successful"){
                                                        swal({
                                                            type: 'success',
                                                            title: '<?php echo __("website/account_products/modal-success-title"); ?>',
                                                            text: '<?php echo __("website/account_products/changed-domain-cns"); ?>',
                                                            showConfirmButton: false,
                                                            timer: 1500
                                                        });
                                                        setTimeout(function(){
                                                            reload_dns_cns(true);
                                                        },1500);
                                                    }
                                                }else
                                                    console.log(result);
                                            }
                                        }
                                    </script>


                                </div>
                            </div>
                        <?php endif; ?>

                        <?php if($allow_dns_records): ?>
                            <div id="dns-records" class="tabcontent" style="display: none">
                                <div class="blue-info">
                                    <div class="padding15">
                                        <i class="fa fa-info-circle" aria-hidden="true"></i>
                                        <p><?php echo __("website/account_products/domain-dns-tx3"); ?></p>
                                    </div>
                                </div>
                                <div class="DomainModuleChildPage">
                                    <table width="100%" id="getDnsRecords" border="0" cellpadding="0" cellspacing="0">
                                        <thead>
                                        <tr>
                                            <th data-orderable="false" align="left" width="15%"><?php echo __("website/account_products/domain-dns-records-2"); ?></th>
                                            <th data-orderable="false" align="left"><?php echo __("website/account_products/domain-dns-records-3"); ?></th>
                                            <th data-orderable="false" align="left"><?php echo __("website/account_products/domain-dns-records-4"); ?></th>
                                            <th data-orderable="false" align="center"><?php echo __("website/account_products/domain-dns-records-10"); ?></th>
                                            <th data-orderable="false" align="center"></th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        <tr class="addDnsRecord_table">
                                            <td align="left">
                                                <select id="DnsRecord_type">
                                                    <option value=""><?php echo __("website/account_products/domain-dns-records-2"); ?></option>
                                                    <?php
                                                        foreach($module_con->config["settings"]["dns-record-types"] AS $t)
                                                        {
                                                            ?>
                                                            <option><?php echo $t; ?></option>
                                                            <?php
                                                        }
                                                    ?>
                                                </select>
                                            </td>
                                            <td align="left">
                                                <input type="text" id="DnsRecord_name" placeholder="@">
                                            </td>
                                            <td align="left"><input type="text" id="DnsRecord_value" placeholder=""></td>
                                            <td align="center">
                                                <select id="DnsRecord_ttl" style="width: 80px;">
                                                    <option value="">Auto</option>
                                                    <?php
                                                        foreach($ttl_times AS $ttl_k => $ttl_v)
                                                        {
                                                            ?>
                                                            <option value="<?php echo $ttl_k; ?>"><?php echo $ttl_v; ?></option>
                                                            <?php
                                                        }
                                                    ?>
                                                </select>
                                                <input type="number" style="width: 80px;display:none;" id="DnsRecord_priority" placeholder="Priority" value="">
                                            </td>
                                            <td align="center">
                                                <a href="javascript:void 0;" class="sbtn green add-dns-record" onclick="addDnsRecord(this);"><i class="fas fa-plus"></i> <?php echo __("website/account_products/domain-dns-records-5"); ?></a>
                                            </td>
                                        </tr>
                                        </tbody>
                                        <tbody id="getDnsRecords_tbody"></tbody>
                                    </table>
                                </div>
                            </div>
                        <?php endif; ?>

                        <?php if($allow_dns_sec_records): ?>
                            <div id="dns-sec-records" class="tabcontent" style="display: none">
                                <div class="blue-info">
                                    <div class="padding15">
                                        <i class="fa fa-info-circle" aria-hidden="true"></i>
                                        <p><?php echo __("website/account_products/domain-dns-tx4"); ?></p>
                                    </div>
                                </div>
                                <div class="DomainModuleChildPage">
                                    <table width="100%" id="getDnsRecords" border="0" cellpadding="0" cellspacing="0">
                                        <thead>
                                        <tr>
                                            <th data-orderable="false" align="left">Digest</th>
                                            <th data-orderable="false" align="left">Key Tag</th>
                                            <th data-orderable="false" align="left">Digest Type</th>
                                            <th data-orderable="false" align="center">Algorithm</th>
                                            <th data-orderable="false" align="center"></th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        <tr class="addDnsSecRecord_table">
                                            <td align="left">
                                                <input type="text" id="DnsSecRecord_digest" placeholder="Digest">
                                            </td>
                                            <td align="left">
                                                <input type="text" id="DnsSecRecord_key_tag" placeholder="Key Tag">
                                            </td>
                                            <td align="left">
                                                <select id="DnsSecRecord_digest_type">
                                                    <option value=""><?php echo ___("needs/select-your"); ?></option>
                                                    <?php
                                                        foreach($module_con->config["settings"]["dns-digest-types"] AS $k => $t)
                                                        {
                                                            ?>
                                                            <option value="<?php echo $k; ?>"><?php echo $t; ?></option>
                                                            <?php
                                                        }
                                                    ?>
                                                </select>
                                            </td>
                                            <td align="center">
                                                <select id="DnsSecRecord_algorithm">
                                                    <option value=""><?php echo ___("needs/select-your"); ?></option>
                                                    <?php
                                                        foreach($module_con->config["settings"]["dns-algorithms"] AS $k => $t)
                                                        {
                                                            ?>
                                                            <option value="<?php echo $k; ?>"><?php echo $t; ?></option>
                                                            <?php
                                                        }
                                                    ?>
                                                </select>
                                            </td>
                                            <td align="center">
                                                <a href="javascript:void 0;" class="sbtn green add-dns-sec-record" onclick="addDnsSecRecord(this);"><i class="fas fa-plus"></i> <?php echo __("website/account_products/domain-dns-records-5"); ?></a>
                                            </td>
                                        </tr>
                                        </tbody>
                                        <tbody id="getDnsSecRecords_tbody"></tbody>
                                    </table>
                                </div>
                            </div>
                        <?php endif; ?>

                    </div>

                </div>
            </div>
        <?php } ?>

        <?php  if($proanse["status"] == "active" && ($allow_forwarding_dmn || $allow_forwarding_eml)): ?>
            <div id="tab-forwarding" class="tabcontent">

                <div class="tabcontentcon">

                    <script type="text/javascript">
                        $(document).ready(function(){
                            var tab_forwarding = gGET("forwarding");
                            if (tab_forwarding != '' && tab_forwarding != undefined) {
                                $("#tab-forwarding .tablinks[data-tab='" + tab_forwarding + "']").click();
                            }
                            else
                            {
                                $("#tab-forwarding .tablinks:eq(0)").addClass("active");
                                $("#tab-forwarding .tabcontent:eq(0)").css("display", "block");
                            }

                            $("#forwarding-domain-wrap").html($("#template-loaderx").html());
                            reload_forwarding_domain();

                        });

                        function set_forward_domain(el)
                        {
                            var request = MioAjax({
                                button_element:el,
                                waiting_text: "<?php echo addslashes(__("website/others/button1-pending")); ?>",
                                action:"<?php echo $links["controller"]; ?>",
                                method: "POST",
                                data:{
                                    operation:"set_forward_domain",
                                    protocol : $("#forward_protocol").val(),
                                    domain : $("#forward_domain").val(),
                                    method : $(".domainforwarding input[name=method]:checked").val(),
                                },
                            },true,true);

                            request.done(function(result){
                                if(result != ''){
                                    var solve = getJson(result);
                                    if(solve !== false){
                                        if(solve.status === "error")
                                            alert_error(solve.message,{timer:3000});
                                        else if(solve.status === "successful"){
                                            alert_success(solve.message,{timer:2000});
                                            setTimeout(function(){
                                                reload_forwarding_domain(true);
                                            },2000);
                                        }
                                    }else
                                        console.log(result);
                                }
                            });
                        }
                        function cancel_forward_domain(el)
                        {
                            var request = MioAjax({
                                button_element:el,
                                waiting_text: "<?php echo addslashes(__("website/others/button1-pending")); ?>",
                                action:"<?php echo $links["controller"]; ?>",
                                method: "POST",
                                data:{
                                    operation:"cancel_forward_domain"
                                },
                            },true,true);

                            request.done(function(result){
                                if(result != ''){
                                    var solve = getJson(result);
                                    if(solve !== false){
                                        if(solve.status === "error")
                                            alert_error(solve.message,{timer:3000});
                                        else if(solve.status === "successful"){
                                            alert_success(solve.message,{timer:2000});
                                            setTimeout(function(){
                                                reload_forwarding_domain(true);
                                            },2000);
                                        }
                                    }else
                                        console.log(result);
                                }
                            });
                        }

                        function reload_forwarding_domain(add_loader)
                        {
                            if(add_loader) $("#forwarding-domain-wrap").html($("#template-loaderx").html());

                            $.get("<?php echo $links["controller"]; ?>?bring=forwarding-domain",function(result){
                                $("#forwarding-domain-wrap").html(result);
                            });
                        }

                    </script>


                    <div id="tab-forwarding" class="subtab">

                        <ul class="tab">

                            <?php if($allow_forwarding_dmn): ?>
                                <li><a href="javascript:void(0)" class="tablinks" onclick="open_tab(this, 'domain','forwarding')" data-tab="domain"><i class="fas fa-angle-right"></i> <?php echo __("website/account_products/domain-forwarding-tx1"); ?></a></li>
                            <?php endif; ?>

                            <?php if($allow_forwarding_eml): ?>
                                <li><a href="javascript:void(0)" class="tablinks" onclick="open_tab(this, 'email','forwarding')" data-tab="cns"><i class="fas fa-angle-right"></i> <?php echo __("website/account_products/domain-forwarding-tx2"); ?></a></li>
                            <?php endif; ?>
                        </ul>

                        <?php if($allow_forwarding_dmn): ?>
                            <div id="forwarding-domain" class="tabcontent">

                                <div class="blue-info">
                                    <div class="padding15">
                                        <i class="fa fa-info-circle" aria-hidden="true"></i>
                                        <p><?php echo __("website/account_products/domain-forwarding-tx3"); ?></p>
                                    </div>
                                </div>

                                <div id="forwarding-domain-wrap"></div>


                            </div>
                        <?php endif; ?>

                        <?php if($allow_forwarding_eml): ?>
                            <script type="text/javascript">
                                var email_forward_prefix,email_forward_target,email_forward_target_new,email_forward_identity;
                                $(document).ready(function(){
                                    $("#getEmailForwards_tbody").html($("#template-loader2").html());
                                    setTimeout(function(){
                                        reload_email_forwards();
                                    },500);
                                });

                                function reload_email_forwards(add_loader)
                                {
                                    if(add_loader) $("#getEmailForwards_tbody").html($("#template-loader2").html());

                                    $.get("<?php echo $links["controller"]; ?>?bring=email-forwards",function(result){
                                        $("#getEmailForwards_tbody").html(result);
                                    });
                                }

                                function editEmailForward(k)
                                {
                                    email_forward_prefix        = $("#EmailForward_"+k+" input[name=prefix]").val();
                                    email_forward_target        = $("#EmailForward_"+k+" input[name=target]").val();
                                    email_forward_identity      = $("#EmailForward_"+k+" input[name=identity]").val();


                                    $("#getEmailForwards_tbody .edit-wrap").css("display","none");
                                    $("#getEmailForwards_tbody .edit-content").css("display","none");
                                    $("#getEmailForwards_tbody .show-wrap").css("display","block");
                                    $("#getEmailForwards_tbody .no-edit-content").css("display","block");
                                    $("#getEmailForwards_tbody tr").removeClass("editing-active");
                                    
                                    $("#EmailForward_"+k+" .email-forward-target input").val(email_forward_target);

                                    $("#EmailForward_"+k+" .edit-content").css("display","block");
                                    $("#EmailForward_"+k+" .edit-wrap").css("display","block");
                                    $("#EmailForward_"+k+" .no-edit-content").css("display","none");
                                    $("#EmailForward_"+k+" .show-wrap").css("display","none");
                                    $("#EmailForward_"+k).addClass('editing-active');
                                }

                                function cancelEmailForward(k)
                                {
                                    $("#getEmailForwards_tbody .edit-wrap").css("display","none");
                                    $("#getEmailForwards_tbody .edit-content").css("display","none");
                                    $("#getEmailForwards_tbody .show-wrap").css("display","block");
                                    $("#getEmailForwards_tbody .no-edit-content").css("display","block");
                                    $("#getEmailForwards_tbody tr").removeClass("editing-active");
                                }

                                function saveEmailForward(k,el)
                                {
                                    email_forward_prefix        = $("#EmailForward_"+k+" input[name=prefix]").val();
                                    email_forward_target        = $("#EmailForward_"+k+" input[name=target]").val();
                                    email_forward_target_new    = $("#EmailForward_"+k+" .email-forward-target input").val();
                                    email_forward_identity  = $("#EmailForward_"+k+" input[name=identity]").val();


                                    $("#EmailForward_"+k+" .email-forward-target .show-wrap").html(email_forward_target);

                                    var request = MioAjax({
                                        button_element:$(el),
                                        waiting_text: '<i style="-webkit-animation:fa-spin 2s infinite linear;animation: fa-spin 2s infinite linear;" class="fa fa-spinner" aria-hidden="true"></i>',
                                        action: "<?php echo $links["controller"]; ?>",
                                        method: "POST",
                                        data:
                                            {
                                                operation: "update_email_forward",
                                                prefix:email_forward_prefix,
                                                target:email_forward_target,
                                                target_new:email_forward_target_new,
                                                identity:email_forward_identity,
                                            }
                                    },true,true);

                                    request.done(function(result){
                                        if(result !== ''){
                                            var solve = getJson(result);
                                            if(solve !== false){
                                                if(solve.status === "error")
                                                    alert_error(solve.message,{timer:3000});
                                                else if(solve.status === "successful")
                                                {
                                                    alert_success("<?php echo __("website/account_products/domain-dns-records-8"); ?>",{timer:3000});
                                                    reload_email_forwards(true);
                                                }
                                            }else
                                                console.log(result);
                                        }
                                    });

                                }
                                function removeEmailForward(k,el)
                                {
                                    if(!confirm("<?php echo ___("needs/delete-are-you-sure"); ?>")) return false;

                                    email_forward_prefix    = $("#EmailForward_"+k+" input[name=prefix]").val();
                                    email_forward_target    = $("#EmailForward_"+k+" input[name=target]").val();
                                    email_forward_identity  = $("#EmailForward_"+k+" input[name=identity]").val();


                                    var request = MioAjax({
                                        button_element:$(el),
                                        waiting_text: "<?php echo addslashes(__("website/others/button1-pending")); ?>",
                                        action: "<?php echo $links["controller"]; ?>",
                                        method: "POST",
                                        data:
                                            {
                                                operation: "delete_email_forward",
                                                prefix:email_forward_prefix,
                                                target:email_forward_target,
                                                identity:email_forward_identity
                                            }
                                    },true,true);

                                    request.done(function(result){
                                        if(result !== ''){
                                            var solve = getJson(result);
                                            if(solve !== false){
                                                if(solve.status === "error")
                                                    alert_error(solve.message,{timer:3000});
                                                else if(solve.status === "successful")
                                                {
                                                    $("#EmailForward_"+k).remove();

                                                    alert_success("<?php echo __("website/account_products/domain-forwarding-tx17"); ?>",{timer:3000});
                                                }
                                            }else
                                                console.log(result);
                                        }
                                    });
                                }

                                function addEmailForward(el)
                                {
                                    email_forward_prefix      = $("#EmailForward_prefix").val();
                                    email_forward_target      = $("#EmailForward_target").val();

                                    var request = MioAjax({
                                        button_element:$(el),
                                        waiting_text: "<?php echo addslashes(__("website/others/button1-pending")); ?>",
                                        action: "<?php echo $links["controller"]; ?>",
                                        method: "POST",
                                        data:
                                            {
                                                operation: "add_email_forward",
                                                prefix:email_forward_prefix,
                                                target:email_forward_target,
                                            }
                                    },true,true);

                                    request.done(function(result){
                                        if(result !== ''){
                                            var solve = getJson(result);
                                            if(solve !== false){
                                                if(solve.status === "error")
                                                    alert_error(solve.message,{timer:3000});
                                                else if(solve.status === "successful")
                                                {
                                                    $("#EmailForward_prefix").val('');
                                                    $("#EmailForward_target").val('');

                                                    alert_success("<?php echo __("website/account_products/domain-forwarding-tx16"); ?>",{timer:3000});
                                                    reload_email_forwards(true);
                                                }
                                            }else
                                                console.log(result);
                                        }
                                    });
                                }
                            </script>
                            <div id="forwarding-email" class="tabcontent">
                                <table width="100%" id="getEmailForwards" border="0" cellpadding="0" cellspacing="0">
                                    <thead>
                                    <tr>
                                        <th data-orderable="false" align="left"><?php echo __("website/account_products/domain-forwarding-tx14"); ?></th>
                                        <th data-orderable="false" align="center"></th>
                                        <th data-orderable="false" align="left"><?php echo __("website/account_products/domain-forwarding-tx15"); ?></th>
                                        <th data-orderable="false" align="center"></th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <tr class="addEmailForward_table">
                                        <td align="left">
                                            <input type="text" id="EmailForward_prefix" value="" placeholder="Prefix" style="width: 150px;float:left;"><strong style="float: left;">@<?php echo $options["domain"]; ?></strong>
                                        </td>
                                        <td align="left">
                                            <i class="fas fa-long-arrow-alt-right" style="font-size: 30px;"></i>
                                        </td>
                                        <td align="left">
                                            <input type="text" id="EmailForward_target" placeholder="email@example.com">
                                        </td>
                                        <td align="center">
                                            <a href="javascript:void 0;" class="sbtn green add-email-forward" onclick="addEmailForward(this);"><i class="fas fa-plus"></i> <?php echo ___("needs/button-create"); ?></a>
                                        </td>
                                    </tr>
                                    </tbody>
                                    <tbody id="getEmailForwards_tbody"></tbody>
                                </table>
                            </div>
                        <?php endif; ?>

                    </div>


                </div>

            </div>
        <?php endif; ?>


        <?php if($tab_verification): ?>
            <div id="tab-verification" class="tabcontent">

                <script type="text/javascript">
                    var doc_opt,doc_type,doc_id;
                    function changed_doc(el)
                    {
                        doc_id          = $(el).val();
                        doc_opt         = $("option:selected",$(el));
                        doc_type        = doc_opt.data("type");

                        $("#addDoc_file").css("display",doc_type === "file" ? "inline-block" : "none");
                        $("#addDoc_text").css("display",doc_type === "text" ? "inline-block" : "none");
                        $("#addDoc_select").css("display",doc_type === "select" ? "inline-block" : "none");
                        $("#addDoc_select select").prop('disabled',true).css("display","none");

                        if(doc_type === "select")
                            $("#doc_"+doc_id+"_select").prop('disabled',false).css("display","inline-block");


                    }
                    function addDoc_submit(el)
                    {
                        MioAjaxElement(el,{
                            "result":"addDoc_handle",
                            "waiting_text":"<?php echo addslashes(__("website/others/button1-pending")); ?>",
                            "progress_text":"<?php echo addslashes(__("website/others/button1-upload")); ?>",
                        });
                    }

                    function addDoc_handle(result)
                    {
                        if(result !== ''){
                            var solve = getJson(result);
                            if(solve !== false){
                                if(solve.status == "error"){
                                    if(solve.message != undefined && solve.message != '')
                                        alert_error(solve.message,{timer:3000});

                                }else if(solve.status === "successful"){
                                    alert_success(solve.message,{timer:3000});
                                    setTimeout(function(){
                                        window.location.reload();
                                    },3000);
                                }
                            }else
                                console.log(result);
                        }
                    }

                    function sent_domain_doc(el)
                    {
                        MioAjaxElement(el,{
                            "result":"sentDoc_handle",
                            "waiting_text":"<?php echo addslashes(__("website/others/button1-pending")); ?>",
                            "progress_text":"<?php echo addslashes(__("website/others/button1-upload")); ?>",
                        });
                    }

                    function sentDoc_handle(result)
                    {
                        if(result !== ''){
                            var solve = getJson(result);
                            if(solve !== false){
                                if(solve.status == "error"){
                                    if(solve.message != undefined && solve.message != '')
                                        alert_error(solve.message,{timer:3000});

                                }else if(solve.status === "successful"){
                                    alert_success(solve.message,{timer:3000});
                                    setTimeout(function(){
                                        window.location.reload();
                                    },3000);
                                }
                            }else
                                console.log(result);
                        }
                    }
                </script>

                <div class="tabcontentcon">

                    <h4 style="float: left;"><strong><?php echo __("website/account_products/domain-doc-4"); ?></strong></h4>

                    <?php if(isset($required_docs_info) && $required_docs_info): ?>
                        <a href="javascript:void 0;" onclick="open_modal('info_doc_modal');" class="sbtn gray" style="float: right;font-weight: 600;"><i class="fa fa-question-circle" aria-hidden="true"></i> <?php echo __("website/account_products/domain-doc-5"); ?></a>
                    <?php endif; ?>


                    <div class="line"></div>

                    <div class="blue-info">
                        <div class="padding15">
                            <i class="fa fa-info-circle" aria-hidden="true"></i>
                            <p><?php echo __("website/account_products/domain-doc-6"); ?></p>
                        </div>
                    </div>


                    <?php if(isset($options["verification_operator_note"]) && $options["verification_operator_note"]): ?>
                        <div class="red-info" id="domain-doc-required-info">
                            <div class="padding15">
                                <i class="fa fa-exclamation-triangle" aria-hidden="true"></i>
                                <p style="margin:0;">
                                    <?php echo __("website/account_products/domain-doc-7"); ?>
                                    <br>---<br>
                                    <strong><?php echo nl2br($options["verification_operator_note"]); ?></strong>
                                </p>

                            </div>
                        </div>
                    <?php endif; ?>


                    <?php if($proanse["status"] == "active"): ?>
                        <div class="green-info">
                            <div class="padding15">
                                <i class="fa fa-check-circle" aria-hidden="true"></i>
                                <p><strong><?php echo __("website/account_products/domain-doc-8"); ?></strong></p>
                            </div>
                        </div>
                    <?php endif; ?>


                    <div class="domain-docs-area">

                        <div class="line"></div>


                        <?php if($proanse["status"] != "active"): ?>
                            <form action="<?php echo $links["controller"]; ?>" method="post" id="addDomainDoc" enctype="multipart/form-data">
                                <input type="hidden" name="operation" value="add_domain_doc">

                                <div class="formcon" style="margin-bottom: 25px;">
                                    <div class="yuzde30"><?php echo __("website/account_products/domain-doc-16"); ?></div>
                                    <div class="yuzde30">
                                        <select name="doc_id" onchange="changed_doc(this);">
                                            <option value="0"><?php echo __("website/account_products/domain-doc-13"); ?></option>
                                            <?php
                                                if(isset($info_docs) && $info_docs)
                                                {
                                                    foreach($info_docs AS $d_id => $d)
                                                    {
                                                        ?>
                                                        <option data-type="<?php echo $d["type"]; ?>" value="<?php echo $d_id; ?>"><?php echo $d["name"]; ?></option>
                                                        <?php
                                                    }
                                                }
                                            ?>
                                        </select>
                                    </div>
                                    <div class="yuzde40" style="display: none;" id="addDoc_text">
                                        <input name="text" value="" type="text" placeholder="<?php echo __("website/account_products/domain-doc-15"); ?>" style="width: 65%;">
                                        <a class="sbtn green" href="javascript:void 0;" onclick="addDoc_submit(this);"><strong><i class="fas fa-plus"></i> <?php echo __("website/account_products/domain-doc-17"); ?></strong></a>
                                        <div class="clear"></div>
                                    </div>
                                    <div class="yuzde40" style="display: none;" id="addDoc_select">
                                        <?php
                                            if(isset($info_docs) && $info_docs)
                                            {
                                                foreach($info_docs AS $d_k => $d)
                                                {
                                                    if($d["type"] != "select") continue;
                                                    ?>
                                                    <select name="select" id="doc_<?php echo $d_k; ?>_select" style="display:none; width: 65%;" disabled>
                                                        <option value="-1"><?php echo ___("needs/select-your"); ?></option>
                                                        <?php
                                                            foreach($d["options"] AS $op_k => $op)
                                                            {
                                                                ?>
                                                                <option value="<?php echo $op_k; ?>"><?php echo $op; ?></option>
                                                                <?php
                                                            }
                                                        ?>

                                                    </select>
                                                    <?php
                                                }
                                            }
                                        ?>

                                        <a class="sbtn green" href="javascript:void 0;" onclick="addDoc_submit(this);"><strong><i class="fas fa-plus"></i> <?php echo __("website/account_products/domain-doc-17"); ?></strong></a>
                                        <div class="clear"></div>
                                    </div>
                                    <div class="yuzde40" style="display: none;" id="addDoc_file">
                                        <input type="file" name="file" style="width: 65%;">
                                        <a class="sbtn green" href="javascript:void 0;" onclick="addDoc_submit(this);"><strong><i class="fas fa-plus"></i> <?php echo __("website/account_products/domain-doc-17"); ?></strong></a>
                                        <div class="clear"></div>
                                    </div>
                                </div>


                            </form>
                        <?php endif; ?>

                        <form action="<?php echo $links["controller"] ?? ''; ?>" method="post" id="sentForm">
                            <input type="hidden" name="operation" value="sent_domain_doc">
                            <table width="100%" border="0" cellpadding="0" cellspacing="0">
                                <thead>
                                <tr>
                                    <th align="left"><?php echo __("website/account_products/domain-doc-9"); ?></th>
                                    <th align="left"><?php echo __("website/account_products/domain-doc-10"); ?></th>
                                    <th align="center"><?php echo __("website/account_products/domain-doc-11"); ?></th>
                                    <th align="center"></th>
                                </tr>
                                </thead>
                                <tbody>

                                <?php
                                    $sent_button = false;

                                    if(isset($uploaded_docs) && sizeof($uploaded_docs) > 0)
                                    {
                                        $statues = [
                                            'unsent'        => '<span>'.__("website/account_products/domain-doc-20").'</span>',
                                            'pending'        => '<span class="process">'.__("website/account_products/domain-doc-23").'</span>',
                                            'declined'    => '<span class="wait">'.__("website/account_products/domain-doc-21").'</span>',
                                            'verified'      => '<span class="active">'.__("website/account_products/domain-doc-22").'</span>',
                                        ];
                                        foreach($uploaded_docs AS $ud)
                                        {
                                            if($ud["status"] == "unsent") $sent_button = true;
                                            ?>
                                            <tr>
                                                <td><?php echo $ud["name"]; ?></td>
                                                <td>
                                                    <?php
                                                        if($ud["file"])
                                                        {
                                                            ?>
                                                            <?php echo $ud["file"]["name"]; ?> <a style="padding: 3px 5px;font-size: 13px;" class="sbtn" target="_blank" href="<?php echo $links["controller"]; ?>?operation=download_domain_doc_file&id=<?php echo $ud["id"]; ?>" title="<?php echo __("website/account_products/domain-doc-24"); ?>"><i class="fa fa-cloud-download" aria-hidden="true"></i></a>
                                                            <?php
                                                        }
                                                        else
                                                            echo $ud["value"];
                                                    ?>
                                                </td>
                                                <td align="center">
                                                    <div class="listingstatus"><?php echo $statues[$ud["status"]]; ?></div>
                                                    <?php if($ud["status_msg"]): ?>
                                                        <span class="domain-docs-op-message">(<?php echo $ud["status_msg"]; ?>)</span>
                                                    <?php endif; ?>
                                                    <?php if($ud["status"] == "verified"): ?>
                                                        <span class="domain-docs-op-message">(<?php echo DateManager::format(Config::get("options/date-format")." - H:i",$ud["updated_at"]); ?>)</span>
                                                    <?php endif; ?>
                                                </td>
                                                <td></td>
                                            </tr>
                                            <?php
                                        }
                                    }
                                    else
                                    {
                                        ?>
                                        <tr>
                                            <td colspan="4" align="center">
                                                <div class="red-info">
                                                    <div class="padding15">
                                                        <i class="fa fa-exclamation-triangle" aria-hidden="true"></i>
                                                        <p><?php echo __("website/account_products/domain-doc-18"); ?></p>
                                                    </div>
                                                </div>
                                                <?php if(isset($required_docs_info) && $required_docs_info): ?>
                                                    <a href="javascript:void 0;" onclick="open_modal('info_doc_modal');" class="sbtn gray" style="font-weight: 600;"><i class="fa fa-question-circle" aria-hidden="true"></i> <?php echo __("website/account_products/domain-doc-5"); ?></a>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                        <?php
                                    }
                                ?>
                                </tbody>
                            </table>

                            <?php if($sent_button): ?>
                                <div class="line"></div>

                                <a href="javascript:void(0);" onclick="sent_domain_doc(this);" class="yesilbtn gonderbtn"><?php echo __("website/account_products/domain-doc-19"); ?></a>
                            <?php endif; ?>

                        </form>

                    </div>

                    <div class="clear"></div>
                </div>
            </div>
        <?php endif; ?>

        <?php if($proanse["status"] == "active" && $ctoc_service_transfer): ?>
            <div id="tab-transfer-service" class="tabcontent">
                <div class="tabcontentcon">
                    <div class="blue-info" style="margin-bottom:20px;">
                        <div class="padding15">
                            <i class="fa fa-info-circle" aria-hidden="true"></i>
                            <p><?php echo __("website/account_products/transfer-service-desc"); ?></p>
                        </div>
                    </div>

                    <?php
                        if(isset($ctoc_limit) && strlen($ctoc_limit) > 0){
                            ?>
                            <div class="clear"></div>
                            <div class="formcon">
                                <div class="yuzde30"><?php echo __("website/account_products/limit-info"); ?></div>
                                <div class="yuzde70" style="color: #F44336;"><?php echo ($ctoc_limit - $ctoc_used); ?></div>
                            </div>
                            <div class="clear"></div>
                            <?php
                        }
                    ?>

                    <div id="TransferService_wrap" style="<?php echo $ctoc_has_expired ? 'display:none;' : ''; ?>">

                        <form action="<?php echo $links["controller"]; ?>" method="post" id="TransferService">
                            <input type="hidden" name="operation" value="transfer_service">

                            <input type="text" name="email" placeholder="<?php echo __("website/account_products/transfer-service-client-email"); ?>">

                            <input type="password" name="password" placeholder="<?php echo __("website/account_products/transfer-service-your-account-password"); ?>">

                            <a href="javascript:void(0);" class="yesilbtn gonderbtn mio-ajax-submit" mio-ajax-options='{"result":"TransferService_handle","waiting_text":"<?php echo addslashes(__("website/others/button5-pending")); ?>"}'><?php echo __("website/account_products/transfer-button"); ?></a>
                            <div class="clear"></div>
                        </form>


                        <h4 style="margin-bottom:7px;    font-size: 18px;"><strong><?php echo __("website/account_products/transfer-service-pending-list"); ?></strong></h4>

                        <table id="ctoc_s_t_list" width="100%" border="0" style="<?php echo isset($ctoc_s_t_list) && is_array($ctoc_s_t_list) && sizeof($ctoc_s_t_list)>0 ? '' : 'display:none;'; ?>">
                            <thead style="background:#ebebeb;">
                            <tr>
                                <th align="left"><?php echo __("website/account_products/transfer-service-list-th-user"); ?></th>
                                <th align="center"><?php echo __("website/account_products/transfer-service-list-th-email"); ?></th>
                                <th align="center"><?php echo __("website/account_products/transfer-service-list-th-date"); ?></th>
                                <th align="center"> </th>
                            </tr>
                            </thead>

                            <tbody align="center" style="border-top:none;">
                            <?php
                                if(isset($ctoc_s_t_list) && is_array($ctoc_s_t_list) && sizeof($ctoc_s_t_list)>0){
                                    foreach($ctoc_s_t_list AS $ctoc_s_t_r){
                                        $ctoc_s_t_r["data"]     = Utility::jdecode($ctoc_s_t_r["data"],true);
                                        $evt_data               = $ctoc_s_t_r["data"];
                                        $full_name              = $evt_data["to_full_name"];
                                        $name_length            = Utility::strlen($full_name);
                                        $name_length            -= 2;
                                        $full_name              = Utility::substr($full_name,0,2).str_repeat("*",$name_length);
                                        ?>
                                        <tr style="background:none;" id="ctoc_s_t_<?php echo $ctoc_s_t_r["id"]; ?>">
                                            <td align="left"><?php echo $full_name; ?></td>
                                            <td align="center"><?php echo $evt_data["to_email"]; ?></td>
                                            <td align="center"><?php echo DateManager::format(Config::get("options/date-format")." H:i",$ctoc_s_t_r["cdate"]); ?></td>
                                            <td align="center" width="140">
                                                <a href="javascript:void 0;" onclick="remove_ctoc_s_t(<?php echo $ctoc_s_t_r["id"]; ?>,this);" class="sbtn red" data-tooltip="<?php echo ___("needs/button-delete"); ?>"><i class="fa fa-times"></i></a>
                                            </td>
                                        </tr>
                                        <?php
                                    }
                                }
                            ?>
                            </tbody>
                        </table>
                        <div id="ctoc_s_t_no_list" class="error" style="<?php echo !(isset($ctoc_s_t_list) && is_array($ctoc_s_t_list) && sizeof($ctoc_s_t_list)>0) ? '' : 'display:none;' ?>"><?php echo __("website/account_products/transfer-service-no-list"); ?></div>


                    </div>
                    <div id="TransferService_success" style="display: none;">
                        <div style="margin-top:30px;margin-bottom:70px;text-align:center;">
                            <i style="font-size:80px;" class="fa fa-check"></i>
                            <h4><?php echo __("website/account_products/transfer-service-successful"); ?></h4>
                            <br>
                        </div>
                    </div>
                    <script type="text/javascript">
                        function TransferService_handle(result){
                            if(result != ''){
                                var solve = getJson(result);
                                if(solve !== false){
                                    if(solve.status == "error"){
                                        if(solve.for != undefined && solve.for != ''){
                                            $("#TransferService "+solve.for).focus();
                                            $("#TransferService "+solve.for).attr("style","border-bottom:2px solid red; color:red;");
                                            $("#TransferService "+solve.for).change(function(){
                                                $(this).removeAttr("style");
                                            });
                                        }
                                        if(solve.message != undefined && solve.message != '')
                                            alert_error(solve.message,{timer:3000});
                                    }else if(solve.status == "successful"){
                                        $("#TransferService_wrap").fadeOut(400,function(){
                                            $("#TransferService_success").fadeIn(400);
                                            $("html,body").animate({scrollTop:200},600);
                                        });
                                        if(solve.reload !== undefined){
                                            setTimeout(function(){
                                                location.href = '<?php echo $links["controller"]; ?>?tab=transfer-service';
                                            },3000);
                                        }
                                    }
                                }else
                                    console.log(result);
                            }
                        }
                        function remove_ctoc_s_t(id,btn){
                            swal({
                                title: '<?php echo ___("needs/delete-are-you-sure"); ?>',
                                text: "",
                                icon: 'warning',
                                showCancelButton: true,
                                confirmButtonColor: '#3085d6',
                                cancelButtonColor: '#d33',
                                confirmButtonText: '<?php echo __("website/others/notification-confirm"); ?>',
                                cancelButtonText: '<?php echo __("website/others/notification-cancel"); ?>',
                            }).then(function(value){
                                if(value){
                                    var request = MioAjax({
                                        waiting_text: '<i style="-webkit-animation:fa-spin 2s infinite linear;animation: fa-spin 2s infinite linear;" class="fa fa-spinner" aria-hidden="true"></i>',
                                        button_element: btn,
                                        action: "<?php echo $links["controller"]; ?>",
                                        method: "POST",
                                        data:{
                                            operation: "remove_transfer_service",
                                            id: id
                                        },
                                    },true,true);
                                    request.done(function(result){
                                        if(result !== ''){
                                            var solve = getJson(result);
                                            if(solve.status === 'error')
                                                Swal.fire(
                                                    '<?php echo __("website/others/notification-error"); ?>',
                                                    solve.message,
                                                    'error'
                                                );
                                            else if(solve.status === 'successful'){
                                                swal(
                                                    '<?php echo __("website/others/notification-success"); ?>',
                                                    solve.message,
                                                    'success'
                                                );
                                                $(btn).parent().parent().remove();
                                                if($("#ctoc_s_t_list tbody tr").length < 1){
                                                    $("#ctoc_s_t_list").css("display","none");
                                                    $("#ctoc_s_t_no_list").css("display","block");
                                                }
                                            }
                                        }
                                    });
                                }
                            });
                        }
                    </script>
                </div>
            </div>
        <?php endif; ?>

        <?php
            if($proanse["status"] == "active"){
                ?>
                <div id="tab-security" class="tabcontent">
                    <div class="tabcontentcon">


                        <div class="green-info">
                            <div class="padding15">
                                <i class="fa fa-info-circle" aria-hidden="true"></i>
                                <p><?php echo __("website/account_products/transferlock-note"); ?></p>
                            </div>
                        </div>
                        <div class="line"></div>
                        <?php if(isset($options["transferlock"]) && $options["transferlock"]): ?>
                            <h5><strong><?php echo __("website/account_products/transferlock"); ?></strong> <strong style="color:#8bc34a;margin-left:10px;"><i class="fa fa-lock" aria-hidden="true"></i> <?php echo __("website/account_products/transferlock-active"); ?></strong></h5>

                            <div class="line"></div>
                            <a style="float:left;" href="javascript:void(0);" class="redbtn gonderbtn mio-ajax-submit" mio-ajax-options='{"data":{"operation":"domain_modify_transferlock"},"action":"<?php echo $links["controller"]; ?>","method":"POST","type":"direct","result":"Transferlock_submit","waiting_text":"<?php echo addslashes(__("website/others/button1-pending")); ?>"}'><?php echo __("website/account_products/transferlock-passive-button"); ?></a>

                        <?php else: ?>
                            <h5><strong><?php echo __("website/account_products/transferlock"); ?></strong> <strong style="color:#E21D1D;margin-left:10px;"><i class="fa fa-unlock" aria-hidden="true"></i> <?php echo __("website/account_products/transferlock-passive"); ?></strong></h5>

                            <div class="line"></div>
                            <a style="float:left;" href="javascript:void(0);" class="yesilbtn gonderbtn mio-ajax-submit" mio-ajax-options='{"data":{"operation":"domain_modify_transferlock"},"action":"<?php echo $links["controller"]; ?>","method":"POST","type":"direct","result":"Transferlock_submit","waiting_text":"<?php echo addslashes(__("website/others/button1-pending")); ?>"}'><?php echo __("website/account_products/transferlock-active-button"); ?></a>
                        <?php endif; ?>

                        <script type="text/javascript">
                            function Transferlock_submit(result) {
                                if(result != ''){
                                    var solve = getJson(result);
                                    if(solve !== false){
                                        if(solve.status == "error"){
                                            swal(
                                                '<?php echo __("website/account_products/modal-error-title"); ?>',
                                                solve.message,
                                                'error'
                                            )
                                        }else if(solve.status == "successful"){
                                            swal({
                                                type: 'success',
                                                title: '<?php echo __("website/account_products/modal-success-title"); ?>',
                                                text: '<?php echo __("website/account_products/changed-transferlock"); ?>',
                                                showConfirmButton: false,
                                                timer: 1500
                                            });
                                            setTimeout(function(){
                                                var link = "<?php echo $links["controller"]; ?>";
                                                link = sGET("tab","security",link);
                                                window.location.href = link;
                                            },1500);
                                        }
                                    }else
                                        console.log(result);
                                }

                            }
                        </script>

                    </div>
                    <div class="clear"></div>
                </div>
                <div id="tab-transfer" class="tabcontent">
                    <div class="tabcontentcon">

                        <div class="green-info">
                            <div class="padding15">
                                <i class="fa fa-info-circle" aria-hidden="true"></i>
                                <p><?php echo __("website/account_products/transfer-code-note"); ?></p>
                            </div>
                        </div>

                        <div class="line"></div>

                        <div id="transfercode_submit">

                            <?php echo __("website/account_products/transfer-code-note2"); ?>
                            <div class="line"></div>
                            <a style="width:250px;" href="javascript:void(0);" class="mavibtn gonderbtn mio-ajax-submit" mio-ajax-options='{"data":{"operation":"domain_transfer_code_submit"},"action":"<?php echo $links["controller"]; ?>","method":"POST","type":"direct","result":"TransferCode_submit","waiting_text":"<?php echo addslashes(__("website/others/button1-pending")); ?>"}'><?php echo __("website/account_products/transfer-code-submit"); ?></a>
                        </div>

                        <div id="TransferCode_success1" style="display: none;">
                            <div style="margin-top:30px;margin-bottom:70px;text-align:center;">
                                <i style="font-size:70px;" class="fa fa-check"></i>
                                <h2 style="color:green;font-weight:bold;"><?php echo __("website/account_products/transfer-code-sent1"); ?></h2>
                                <br/>
                                <h4><?php echo __("website/account_products/transfer-code-sent2"); ?></h4>
                            </div>
                        </div>

                        <div id="TransferCode_success2" style="display: none;">
                            <div style="margin-top:30px;margin-bottom:70px;text-align:center;">
                                <i style="font-size:80px;color:green;" class="fa fa-check"></i>
                                <h2 style="color:green;font-weight:bold;"><?php echo __("website/account_products/transfer-code-sent1"); ?></h2>
                                <br/>
                                <h4><?php echo __("website/account_products/transfer-code-sent3"); ?></h4>
                            </div>
                        </div>



                        <script type="text/javascript">
                            function TransferCode_submit(result) {
                                if(result != ''){
                                    var solve = getJson(result);
                                    if(solve !== false){
                                        if(solve.status == "error"){
                                            swal(
                                                '<?php echo __("website/account_products/modal-error-title"); ?>',
                                                solve.message,
                                                'error'
                                            )
                                        }else if(solve.status == "successful"){

                                            $("#transfercode_submit").fadeOut(400,function(){
                                                if(solve.type != undefined && solve.type == "request")
                                                    $("#TransferCode_success2").fadeIn(400);
                                                else
                                                    $("#TransferCode_success1").fadeIn(400);
                                                $("html,body").animate({scrollTop:200},600);
                                            });
                                        }
                                    }else
                                        console.log(result);
                                }
                            }
                        </script>



                    </div>
                    <div class="clear"></div>
                </div>
                <?php
            }
        ?>

    </div>


    <div class="clear"></div>
</div>