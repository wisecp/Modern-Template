<?php defined('CORE_FOLDER') OR exit('You can not get in here!');
    $wide_content   = true;
    $confirm_text = __("website/account_products/server-upgrade-confirm-text");

    if(isset($server) && $server){
        $updowngrade_remove = $server["updowngrade_remove_server"];
        if(substr($updowngrade_remove,0,4) == "then")
            $confirm_text = __("website/account_products/server-upgrade-confirm-text2",[
                '{day}' => substr($updowngrade_remove,5,4),
            ]);
        elseif($updowngrade_remove == "now")
            $confirm_text = __("website/account_products/server-upgrade-confirm-text3");
    }

    include $tpath."common-needs.php";
    $hoptions = ["datatables","jquery-ui"];
?>

<div id="template-loader" style="display: none;">
    <div id="block_module_loader">
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

<script type="text/javascript">
function openTab(evt, tabName) {
    var gtab,dtab,link,tab;
    $(".tabcontent").css("display","none");
    $(".tablinks").removeClass("active");
    $("#"+tabName).css("display","block");
    $(evt).addClass("active");
    gtab     = gGET("tab");
    dtab     = $(evt).attr("data-tab");
    if((gtab == '' || gtab == null || gtab == undefined) && dtab == 1){
        link    = window.location.href;
    }else{
        link     = sGET("tab",dtab);
    }
    window.history.pushState("object or string", $("title").html(),link);
}

$(document).ready(function(){
    var tab = gGET("tab");
    if(tab == '' || tab == undefined){
        $(".tablinks:eq(0)").click();
    }else{
        $(".tablinks[data-tab='"+tab+"']").click();
    }

    <?php if(isset($server) && $proanse["status"] == "active"): ?>
    $("#block_module_details_con").html($("#template-loader").html());
    setTimeout(reload_module_content,500);
    <?php endif; ?>
});

function run_transaction(btn_k,btn_el,post_fields){
    var data1   = {inc: "panel_operation_method",method:btn_k};
    var data2   = $(btn_el).data("fields");
    if(typeof data2 !== 'object' && data2 !== undefined && data2.length > 0) data2 = getJson(data2);
    if(typeof data2 !== 'object' || data2 === undefined || data2 === false) data2 = {};
    var data3   = post_fields === undefined || post_fields === false ? {} : post_fields;
    var _data   = Object.assign(data1,...data2,...data3);

    var request = MioAjax({
        button_element:btn_el,
        waiting_text: '<?php echo __("website/others/button1-pending"); ?>',
        action:"<?php echo $links["controller"]; ?>",
        method:"POST",
        data:_data,
    },true,true);
    request.done(t_form_handle);
}
function reload_module_content(page){
    if(page === undefined) page = "<?php echo isset($m_page) ? $m_page : false; ?>";
    else $("#block_module_details_con").html($("#template-loader").html());

    if(page !== ''){
        window.history.pushState("object or string", $("title").html(),'<?php echo $links["controller"]; ?>?m_page='+page);
    }

    var request = MioAjax({
        action:"<?php echo $links["controller"]; ?>",
        method:"POST",
        data:{
            inc:        "get_server_informations",
            m_page:     page,
        },
    },true,true);

    request.done(function(result){
        if(result != ''){
            var solve = getJson(result);
            if(solve !== false){

                $("#server_status_loader").css("display","none");

                if(solve.status !== undefined){
                    if(solve.status) $("#server_status_online").css("display","block");
                    else $("#server_status_offline").css("display","block");
                }
                if(solve.panel !== undefined) $("#block_module_details_con").html(solve.panel);
            }else
                console.log(result);
        }
    });
}
function t_form_handle(result){
    if(result !== ''){
        var solve = getJson(result);
        if(solve !== false){
            if(solve.status === "error"){
                alert_error(solve.message,{timer:3000});
            }
            else if(solve.status === "successful"){
                alert_success(solve.message,{timer:3000});
            }
            if(solve.timeRedirect !== undefined){
                setTimeout(function(){
                    window.location.href = solve.timeRedirect.url === undefined ? location.href : solve.timeRedirect.url;
                },solve.timeRedirect.duration);
            }
            else if(solve.redirect !== undefined){
                window.location.href = solve.redirect;
            }
        }else
            console.log(result);
    }
}
</script>
<link rel="stylesheet" type="text/css" href="<?php echo $sadress; ?>assets/style/progress-circle.css">
<style type="text/css">
    .hostbtn{width:150px;padding:10px 20px;background:#eee;display:inline-block;margin:5px;vertical-align:top;border-radius:3px}
    .hostbtn:hover {background:#dbdbdb;}
    #nserverinfo {
        font-size: 15px;
        padding: 12px;
        background: none;
        border-bottom: 1px solid #eee;
        color: #009595;
        background: #efefef;
        background: -moz-linear-gradient(top,#efefef 0%,#fff 100%);
        background: -webkit-linear-gradient(top,#efefef 0%,#fff 100%);
        background: linear-gradient(to bottom,#efefef 0%,#fff 100%);
        filter: progid:DXImageTransform.Microsoft.gradient(startColorstr='#efefef',endColorstr='#ffffff',GradientType=0);
    }
    #hostserverblok {width:48%;text-align:center;border:none;}

    .load-wrapp{width:150px;margin:90px auto;text-align:center;color:#777}
    .square{width:12px;height:12px;border-radius:4px;background-color:#777}
    .spinner{position:relative;width:45px;height:45px;margin:0 auto}
    .load-7{margin-left:-70px;display:inline-block}
    .l-1{animation-delay:.48s}
    .l-2{animation-delay:.6s}
    .l-3{animation-delay:.72s}
    .l-4{animation-delay:.84s}
    .l-5{animation-delay:.96s}
    .l-6{animation-delay:1.08s}
    .l-7{animation-delay:1.2s}
    .l-8{animation-delay:1.32s}
    .l-9{animation-delay:1.44s}
    .l-10{animation-delay:1.56s}
    .load-7 .square{animation:loadingG 1.5s cubic-bezier(.17,.37,.43,.67) infinite}
    @keyframes loadingA {
        50%{height:15px 35px}
        100%{height:15px}
    }
    @keyframes loadingB {
        50%{width:15px 35px}
        100%{width:15px}
    }
    @keyframes loadingC {
        50%{transform:translate(0,0) translate(0,15px)}
        100%{transform:translate(0,0)}
    }
    @keyframes loadingD {
        50%{transform:rotate(0deg) rotate(180deg)}
        100%{transform:rotate(360deg)}
    }
    @keyframes loadingE {
        100%{transform:rotate(0deg) rotate(360deg)}
    }
    @keyframes loadingF {
        0%{opacity:0}
        100%{opacity:1}
    }
    @keyframes loadingG {
        0%{transform:translate(0,0) rotate(0deg)}
        50%{transform:translate(70px,0) rotate(360deg)}
        100%{transform:translate(0,0) rotate(0deg)}
    }
    @keyframes loadingH {
        0%{width:15px}
        50%{width:35px;padding:4px}
        100%{width:15px}
    }
    @keyframes loadingI {
        100%{transform:rotate(360deg)}
    }
    @keyframes bounce {
        0%,100%{transform:scale(0.0)}
        50%{transform:scale(1.0)}
    }
    @keyframes loadingJ {
        0%,100%{transform:translate(0,0)}
        50%{transform:translate(80px,0);background-color:#f5634a;width:25px}
    }
    @media only screen and (min-width:320px) and (max-width:1025px) {
        #hostserverblok {width:100%;    margin-bottom: 20px;}
    }
    .serverblokbtn a {width:18%;font-size:14px;     margin: 2px;   padding: 12px 0px;background:#eee;    display: inline-block;}
    .serverblokbtn a:hover {background:#ccc;  }
    #vpsreboot:hover {background:#607d8b;color:white;}
    #vpsShutdown:hover {background:#f44336;color:white;}
    #vpsPowerOff:hover {background:#f44336;color:white;}
    #vpsPowerOnn:hover {background:#8bc34a;color:white;}
</style>


<div class="mpanelrightcon">

    <div class="mpaneltitle">
        <div class="sayfayolu"><?php include  $tpath."inc".DS."panel-breadcrumb.php"; ?></div>
        <h4><strong><i class="fa fa-server" aria-hidden="true"></i> <?php echo $proanse["name"]; echo isset($options["category_name"]) ? " (".$options["category_name"].")" : NULL; ?></strong></h4>
    </div>


    <ul class="tab">
        <li><a href="javascript:void(0)" class="tablinks" onclick="openTab(this, 'ozet')" data-tab="1"><i class="fa fa-server" aria-hidden="true"></i> <?php echo __("website/account_products/server-details"); ?></a></li>

        <?php if(isset($addons) && $addons): ?>
            <li><a href="javascript:void(0)" class="tablinks" onclick="openTab(this, 'addons')" data-tab="addons"><i class="fa fa-rocket" aria-hidden="true"></i> <?php echo __("website/account_products/tab-addons"); ?></a></li>
        <?php endif; ?>

        <?php if(isset($requirements) && $requirements): ?>
            <li><a href="javascript:void(0)" class="tablinks" onclick="openTab(this, 'requirements')" data-tab="requirements"><i class="fa fa-check-square" aria-hidden="true"></i> <?php echo __("website/account_products/tab-requirements"); ?></a></li>
        <?php endif; ?>

        <?php if($proanse["status"] == "active" && $upgrade): ?>
            <li><a href="javascript:void(0)" class="tablinks" onclick="openTab(this, 'upgrade')" data-tab="upgrade"><i class="ion-speedometer" aria-hidden="true"></i> <?php echo __("website/account_products/hosting-tab-upgrade"); ?></a></li>
        <?php endif; ?>

        <?php if($proanse["status"] == "active" && $ctoc_service_transfer): ?>
            <li><a href="javascript:void(0)" class="tablinks" onclick="openTab(this, 'transfer-service')" data-tab="transfer-service"><i class="fa fa-exchange" aria-hidden="true"></i> <?php echo __("website/account_products/transfer-service"); ?></a></li>
        <?php endif; ?>

        <?php if($proanse["status"] == "active" && $proanse["period"] != "none"): ?>
            <li><a href="javascript:void(0)" class="tablinks" onclick="openTab(this, 'iptaltalebi')" data-tab="cancellation"><i class="fa fa-ban" aria-hidden="true"></i> <?php echo __("website/account_products/cancellation-request"); ?></a></li>
        <?php endif; ?>

        <div class="orderidno"><span><?php echo __("website/account_products/table-ordernum"); ?></span><strong>#<?php echo $proanse["id"]; ?></strong></div>
    </ul>


    <div id="ozet" class="tabcontent">

        <div class="hizmetblok" id="<?php echo isset($server) && $proanse["status"] == "active" ? "block_modulewidth50" : ''; ?>">
            <div class="cpanelebmail">

                <div class="whoisgizlenmis" style="margin-bottom: -25px;margin-top: 10px;background: #8bc34a;width: 15%;display: none;" id="server_status_online">Online</div>
                <div class="whoisgizlenmis" style="margin-bottom: -25px;margin-top: 10px;background: #f44336;width: 15%;display:none;" id="server_status_offline">Offline</div>
                <?php if(isset($server) && $proanse["status"] == "active"): ?>
                    <div class="whoisgizlenmis" style="margin-bottom: -25px;margin-top: 10px;background: #c0c5ce;width: 15%;display: block;height: 20px;" id="server_status_loader"><?php echo ___("needs/processing"); ?>...</div>
                <?php endif; ?>

                <?php if(isset($options["product_image"])): ?>
                    <img  src="<?php echo $options["product_image"]; ?>" width="200" height="auto">
                <?php else: ?>
                    <img style="width:140px;" src="<?php echo $tadress."images/image47.png"; ?>" width="auto" height="auto">
                <?php endif; ?>
                <?php if(isset($options["hostname"])): ?>
                    <h5 style="margin:15px 0px;"><?php echo $options["hostname"]; ?><br><span style="font-weight:500;"><?php echo isset($options["ip"]) ? $options["ip"] : ''; ?></span></h5>

                <?php endif; ?>
                <?php if(isset($options["panel_type"]) && $options["panel_type"] == "WHM" && $options["panel_link"]): ?>
                    <a target="_blank" href="<?php echo isset($options["panel_link"]) ? $options["panel_link"] : NULL; ?>" class="turuncbtn gonderbtn"><?php echo __("website/account_products/login-whm"); ?></a>
                <?php elseif(isset($options["panel_type"]) && $options["panel_type"] == "Plesk"): ?>
                    <a target="_blank" href="<?php echo isset($options["panel_link"]) ? $options["panel_link"] : NULL; ?>" class="mavibtn gonderbtn"><?php echo __("website/account_products/login-plesk"); ?></a>
                <?php elseif(isset($options["panel_type"]) && $options["panel_type"] && $options["panel_link"]): ?>
                    <a target="_blank" href="<?php echo isset($options["panel_link"]) ? $options["panel_link"] : NULL; ?>" class="mavibtn gonderbtn"><?php echo __("website/account_products/login-panel"); ?></a>
                <?php endif; ?>

                <?php
                    if(isset($product) && $product && $proanse["period"] != "none" && ($proanse["status"] == "active" || $proanse["status"] == "suspended") && !isset($proanse["disable_renewal"])){
                        ?>
                        <div class="clear"></div>
                        <div id="renewal_list" style="display:none;">
                            <select id="selection_renewal">
                                <option value=""><?php echo __("website/account_products/renewal-list-option"); ?></option>
                                <?php
                                    if(isset($product["price"])){
                                        foreach($product["price"] AS $k=>$v){
                                            ?>
                                            <option value="<?php echo $k; ?>"><?php
                                                    echo View::period($v["time"],$v["period"]);
                                                    echo " ";
                                                    echo Money::formatter_symbol($v["amount"],$v["cid"],true);
                                                ?></option>
                                            <?php
                                        }
                                    }
                                ?>
                            </select>
                            <script type="text/javascript">
                                $(document).ready(function () {
                                    $("#selection_renewal").change(function () {
                                        var selection = $(this).val();
                                        if (selection != '') {
                                            var result = MioAjax({
                                                action: "<?php echo $links["controller"]; ?>",
                                                method: "POST",
                                                data: {operation: "order_renewal", period: selection}
                                            }, true);

                                            if (result) {
                                                var solve = getJson(result);
                                                if (solve) {
                                                    if (solve.status == "successful") {
                                                        window.location.href = solve.redirect;
                                                    }
                                                }
                                            }
                                        }
                                    });
                                });
                            </script>
                        </div>
                        <a href="javascript:$('#renewal_list').slideToggle(400);void 0;"
                           class="mavibtn gonderbtn"><?php echo __("website/account_products/renewal-now-button"); ?></a>
                        <div class="clear"></div>
                        <?php
                    }
                ?>

            </div>
        </div>

        <div id="<?php echo isset($server) && $proanse["status"] == "active" ? "block_modulewidth50" : ''; ?>" class="hizmetblok">
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
                    <td><?php echo isset($options["group_name"]) ? $options["group_name"] : __("website/others/none"); ?></td>
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
                ?>
                <tr>
                    <td><strong><?php echo __("website/account_products/payment-period"); ?></strong></td>
                    <td><?php echo View::period($proanse["period_time"],$proanse["period"]); ?></td>
                </tr>

                <?php if(substr($proanse["renewaldate"],0,4) != "1881" && substr($proanse["renewaldate"],0,4) != "1970"): ?>
                    <tr>
                        <td><strong><?php echo __("website/account_products/renewal-date"); ?></strong></td>
                        <td><?php echo DateManager::format(Config::get("options/date-format"),$proanse["renewaldate"]); ?></td>
                    </tr>
                <?php endif; ?>

                <?php if(substr($proanse["duedate"],0,4) != "1881" && substr($proanse["duedate"],0,4) != "1970"): ?>
                    <tr>
                        <td><strong><?php echo __("website/account_products/due-date"); ?></strong></td>
                        <td><?php echo DateManager::format(Config::get("options/date-format"),$proanse["duedate"]); ?></td>
                    </tr>
                <?php endif; ?>

                <tr align="center" class="tutartd">
                    <td colspan="2"><strong><?php echo __("website/account_products/amount"); ?> : <?php echo $amount; ?></strong></td>
                </tr>
            </table>

        </div>


        <?php if(isset($server) && $proanse["status"] == "active"): ?>
            <div class="clear"></div>

            <div class="block_module_details" id="get_details_module_content">
                <div class="hizmetblok" id="block_module_details_con"></div>
            </div>

            <div class="clear"></div>
        <?php endif; ?>


        <?php if(isset($hoptions["server_features"]) || (isset($options["ip"]) && $options["ip"] != '')): ?>
            <div class="hizmetblok" STYLE="min-height:310px;">
                <table width="100%" border="0">
                    <tr>
                        <td colspan="2" bgcolor="#ebebeb" ><strong><?php echo __("website/account_products/server-features"); ?></strong></td>
                    </tr>

                    <?php if(isset($options["server_features"]["processor"]) && $options["server_features"]["processor"] != ''): ?>
                        <tr>
                            <td><strong><?php echo __("website/account_products/server-processor"); ?></strong></td>
                            <td><?php echo $options["server_features"]["processor"]; ?></td>
                        </tr>
                    <?php endif; ?>

                    <?php if(isset($options["server_features"]["ram"]) && $options["server_features"]["ram"] != ''): ?>
                        <tr>
                            <td><strong><?php echo __("website/account_products/server-ram"); ?></strong></td>
                            <td><?php echo $options["server_features"]["ram"]; ?></td>
                        </tr>
                    <?php endif; ?>

                    <?php if(isset($options["server_features"]["disk"]) && $options["server_features"]["disk"] != ''): ?>
                        <tr>
                            <td><strong><?php echo __("website/account_products/server-disk"); ?></strong></td>
                            <td><?php echo $options["server_features"]["disk"]; ?></td>
                        </tr>
                    <?php endif; ?>

                    <?php if(isset($options["server_features"]["bandwidth"]) && $options["server_features"]["bandwidth"] != ''): ?>
                        <tr>
                            <td><strong><?php echo __("website/account_products/server-bandwidth"); ?></strong></td>
                            <td><?php echo $options["server_features"]["bandwidth"]; ?></td>
                        </tr>
                    <?php endif; ?>

                    <?php if(isset($options["server_features"]["location"]) && $options["server_features"]["location"] != ''): ?>
                        <tr>
                            <td><strong><?php echo __("website/account_products/server-location"); ?></strong></td>
                            <td><?php echo $options["server_features"]["location"]; ?></td>
                        </tr>
                    <?php endif; ?>

                    <?php if(isset($options["server_features"]["raid"]) && $options["server_features"]["raid"] != ''): ?>
                        <tr>
                            <td><strong><?php echo __("website/account_products/server-raid"); ?></strong></td>
                            <td><?php echo $options["server_features"]["raid"]; ?></td>
                        </tr>
                    <?php endif; ?>


                </table>
            </div>
        <?php endif; ?>

        <?php if(isset($options["login"])): ?>
            <div class="hizmetblok">
                <table width="100%" border="0">
                    <tr>
                        <td colspan="2" bgcolor="#ebebeb" ><strong><?php echo __("website/account_products/server-login-informations"); ?></strong></td>
                    </tr>

                    <?php if(isset($options["ip"]) && $options["ip"] != ''): ?>
                        <tr>
                            <td><strong><?php echo __("website/account_products/server-ip"); ?></strong></td>
                            <td><?php echo $options["ip"]; ?></td>
                        </tr>
                    <?php endif; ?>

                    <?php if(isset($options["login"]["username"]) && $options["login"]["username"] != ''): ?>
                        <tr>
                            <td><strong><?php echo __("website/account_products/server-login-username"); ?></strong></td>
                            <td><?php echo $options["login"]["username"]; ?></td>
                        </tr>
                    <?php endif; ?>

                    <?php if(isset($options["login"]["password"]) && $options["login"]["password"] != ''): ?>
                        <tr>
                            <td><strong><?php echo __("website/account_products/server-login-password"); ?></strong></td>
                            <td><?php echo $options["login"]["password"]; ?></td>
                        </tr>
                    <?php endif; ?>

                </table>
            </div>
        <?php endif; ?>

        <?php if((isset($options["hostname"]) && $options["hostname"]) || (isset($options["ns1"]) && $options["ns1"])  || (isset($options["ns2"]) && $options["ns2"]) || (isset($options["ns3"]) && $options["ns3"]) || (isset($options["ns4"]) && $options["ns4"])): ?>
            <div class="hizmetblok">
                <table width="100%" border="0">
                    <tr>
                        <td bgcolor="#ebebeb" ><strong><?php echo __("website/account_products/server-information"); ?></strong></td>
                    </tr>

                    <?php if(isset($options["hostname"])): ?>
                        <tr>
                            <td><strong><?php echo __("website/account_products/server-info-hostname"); ?>: </strong><span><?php echo $options["hostname"]; ?></span></td>
                        </tr>
                    <?php endif; ?>

                    <?php if(isset($options["ns1"]) && $options["ns1"]): ?>
                        <tr>
                            <td><strong><?php echo __("website/account_products/server-info-ns1"); ?></strong>: <?php echo $options["ns1"]; ?></td>
                        </tr>
                    <?php endif; ?>

                    <?php if(isset($options["ns2"]) && $options["ns2"]): ?>
                        <tr>
                            <td><strong><?php echo __("website/account_products/server-info-ns2"); ?></strong>: <?php echo $options["ns2"]; ?></td>
                        </tr>
                    <?php endif; ?>

                    <?php if(isset($options["ns3"]) && $options["ns3"]): ?>
                        <tr>
                            <td><strong><?php echo __("website/account_products/server-info-ns3"); ?></strong>: <?php echo $options["ns3"]; ?></td>
                        </tr>
                    <?php endif; ?>

                    <?php if(isset($options["ns4"]) && $options["ns4"]): ?>
                        <tr>
                            <td><strong><?php echo __("website/account_products/server-info-ns4"); ?></strong>: <?php echo $options["ns4"]; ?></td>
                        </tr>
                    <?php endif; ?>

                </table>
            </div>
        <?php endif; ?>

        <?php if(isset($options["assigned_ips"]) && $options["assigned_ips"]): ?>
            <div class="hizmetblok">
                <table width="100%" border="0">
                    <tr>
                        <td bgcolor="#ebebeb" ><strong><?php echo __("website/account_products/server-assigned-ips"); ?></strong></td>
                    </tr>

                    <tr>
                        <td colspan="2">
                            <?php echo nl2br($options["assigned_ips"]); ?>
                        </td>
                    </tr>

                </table>
            </div>
        <?php endif; ?>
        <?php if(isset($options["descriptions"]) && $options["descriptions"]): ?>
            <div class="hizmetblok">
                <table width="100%" border="0">
                    <tr>
                        <td bgcolor="#ebebeb" ><strong><?php echo __("website/account_products/server-descriptions"); ?></strong></td>
                    </tr>

                    <tr>
                        <td colspan="2">
                            <?php echo Filter::link_convert(nl2br($options["descriptions"])); ?>
                        </td>
                    </tr>

                </table>
            </div>
        <?php endif; ?>

        <?php
            if(isset($options["blocks"]) && $options["blocks"]){
                foreach($options["blocks"] AS $block){
                    ?>
                     <div class="hizmetblok block-item">
                        <div class="block_module_details-title formcon"><h4><?php echo $block["title"]; ?></h4></div>
                        <div class="block-item-desc"><div class="padding10"><?php echo Filter::link_convert(nl2br($block["description"])); ?></div></div>
                    </div>
                    <?php
                }
            }
        ?>

    </div>

    <?php if(isset($addons) && $addons): ?>
        <div id="addons" class="tabcontent">

            <script type="text/javascript">
                $(document).ready(function(){

                    $('#addons_table').DataTable({
                        "columnDefs": [
                            {
                                "targets": [0],
                                "visible":false,
                                "searchable": false
                            },
                        ],
                        paging: false,
                        info:     false,
                        searching: false,
                        responsive: true,
                        "language":{
                            "url":"<?php echo APP_URI."/".___("package/code")."/datatable/lang.json";?>"
                        }
                    });
                });
            </script>

            <table width="100%" id="addons_table" class="table table-striped table-borderedx table-condensed nowrap">
                <thead style="background:#ebebeb;">
                <tr>
                    <th align="left">#</th>
                    <th align="left"><?php echo __("website/account_products/addons-table-addon-info"); ?></th>
                    <th align="center"><?php echo __("website/account_products/addons-table-date"); ?></th>
                    <th align="center"><?php echo __("website/account_products/addons-table-amount"); ?></th>
                    <th align="center"><?php echo __("website/account_products/addons-table-status"); ?></th>
                </tr>
                </thead>
                <tbody align="center" style="border-top:none;">
                <?php
                    foreach($addons AS $k=>$row){
                        $list_cdatetime     = substr($row["cdate"],0,4)!="1881" ? DateManager::format(Config::get("options/date-format"),$row["cdate"]) : '-';
                        $list_rdatetime     = substr($row["renewaldate"],0,4)!="1881" ? DateManager::format(Config::get("options/date-format"),$row["renewaldate"]) : '-';
                        $list_duedatetime   = substr($row["duedate"],0,4)!="1881" ? DateManager::format(Config::get("options/date-format"),$row["duedate"]) : '-';
                        $status = $product_situations[$row["status"]];

                        $amount = $row["amount"]>0 ? Money::formatter_symbol($row["amount"],$row["cid"]) : ___("needs/free-amount");
                        $period = NULL;

                        if($row["amount"]>0){
                            $period = View::period($row["period_time"],$row["period"]);
                        }
                        $amount_period  = "<strong>".$amount."</strong>";
                        if($period) $amount_period.= "<br>".$period."";

                        ?>
                        <tr>
                            <td align="left"><?php echo $k; ?></td>
                            <td align="left"><?php echo $row["addon_name"]."<br>".$row["option_name"]; ?></td>
                            <td align="center"><?php echo $list_rdatetime."<br>".$list_duedatetime; ?></td>
                            <td align="center"><?php echo $amount_period; ?></td>
                            <td align="center"><?php echo $status; ?></td>
                        </tr>
                        <?php
                    }
                ?>
                </tbody>
            </table>



        </div>
    <?php endif; ?>

    <?php if(isset($requirements) && $requirements): ?>
        <div id="requirements" class="tabcontent">

            <?php
                foreach($requirements AS $requirement){
                    $response = $requirement["response"];
                    $rtype    = $requirement["response_type"];

                    if($rtype == "select" || $rtype == "radio" || $rtype == "checkbox" || $rtype == "file")
                        $response_j   = Utility::jdecode($response,true);

                    if(($rtype == "select" || $rtype == "radio") && is_array($response_j))
                        $response = htmlentities($response_j[0], ENT_QUOTES);
                    else
                        $response = htmlentities($response,ENT_QUOTES);


                    ?>
                    <div class="formcon">
                        <div class="yuzde30"><?php echo $requirement["requirement_name"]; ?></div>
                        <div class="yuzde70">
                            <?php
                                if($rtype == "file"){
                                    foreach($response_j AS $k=>$re){
                                        $link = $links["controller"]."?operation=requirement-file-download&rid=".$requirement["id"]."&key=".$k;
                                        ?><a href="<?php echo $link; ?>" target="_blank" class="lbtn"><i class="fa fa-external-link" aria-hidden="true"></i> <?php echo Utility::short_text($re["file_name"],0,30,true); ?></a> <?php
                                    }
                                }elseif($rtype == "checkbox"){
                                    if($response_j) echo implode(" , ",$response_j);
                                }else
                                    echo nl2br(Filter::link_convert($response));
                            ?>
                        </div>
                    </div>
                    <?php
                }
            ?>


        </div>
    <?php endif; ?>

    <?php if($proanse["status"] == "active" && $proanse["period"] != "none" && $upgrade): ?>
        <div id="upgrade" class="tabcontent">

            <div class="tabcontentcon content-updown">

                <div class="green-info" style="margin-bottom:25px;">
                    <div class="padding20">
                        <i class="ion-speedometer" aria-hidden="true"></i>
                        <?php echo __("website/account_products/hosting-upgrade-info"); ?>
                    </div>
                </div>

                <div class="formcon">
                    <div class="yuzde30" style="vertical-align:top;"><?php echo __("website/account_products/upgrade-order-statistics"); ?>:
                        <div class="clear"></div>
                        <span class="kinfo"><?php echo __("website/account_products/upgrade-order-statistics-info"); ?></span>
                    </div>
                    <div class="yuzde70">

                        <div class="yuzde50">
                            <div class="formcon">
                                <span><?php echo __("website/account_products/upgrade-product-name"); ?>:</span>
                                <strong><?php echo $proanse["name"]; ?></strong>
                            </div>

                            <div class="formcon">
                                <span><?php echo __("website/account_products/upgrade-renewaldate"); ?>:</span>
                                <strong><?php echo DateManager::format(Config::get("options/date-format"),$proanse["renewaldate"]); ?></strong>
                            </div>

                            <div class="formcon">
                                <span><?php echo __("website/account_products/upgrade-duedate"); ?>:</span>
                                <strong> <?php echo DateManager::format(Config::get("options/date-format"),$proanse["duedate"]); ?></strong>
                            </div>

                            <div class="formcon">
                                <span><?php echo __("website/account_products/upgrade-times-used"); ?>:</span>
                                <strong><?php echo $upgrade_times_used ." ". ___("date/day"); ?></strong> <!--<?php echo $upgrade_times_used_amount; ?>)-->
                            </div>

                            <div class="formcon">
                                <span><?php echo __("website/account_products/upgrade-remaining-day"); ?>:</span>
                                <strong><?php echo $upgrade_remaining_day ." ". ___("date/day"); ?></strong> <!--(<strong style="color:red;"><?php echo $upgrade_remaining_amount; ?></strong>)-->
                            </div>
                        </div>

                    </div>
                </div>


                <?php if(isset($upgproducts) && $upgproducts["products"]): ?>

                    <div id="upgradeConfirm" data-izimodal-title="<?php echo __("website/account_products/hosting-tab-upgrade"); ?>" style="display: none">
                        <div class="padding20">
                            <div align="center">
                                <span id="upgradeConfirm_text"></span>

                                <div class="clear"></div>

                                <a style="float:none" href="javascript:void(0);" id="upgradeConfirm_ok" class="gonderbtn yesilbtn"><i class="fa fa-check"></i> <?php echo ___("needs/iconfirm"); ?></a>

                                <div class="clear"></div>

                            </div>

                        </div>

                    </div>

                    <script type="text/javascript">
                        function selected_category(id){
                            $(".upgrade-products").fadeOut(300);
                            $("#category_"+id).fadeIn(300);
                        }
                        function upgradeProduct(id){
                            var selectedPrice = $("#product_"+id+"_price").val();
                            var name          = $("#product_"+id+"_name").val();
                            var payable       = $("#product_"+id+"_price option:selected").data("payable");

                            open_modal("upgradeConfirm");

                            var confirm_text = '<?php echo str_replace("\n","<br>",$confirm_text); ?>';
                            confirm_text     = confirm_text.replace("{name}",name);
                            confirm_text     = confirm_text.replace("{payable}",payable);

                            $("#upgradeConfirm_text").html(confirm_text);

                            $("#upgradeConfirm_ok").on("click",function(){

                                var request       = MioAjax({
                                    button_element:$("#upgradeConfirm_ok"),
                                    waiting_text: '<?php echo addslashes(__("website/others/button5-pending")); ?>',
                                    action:"<?php echo $links["controller"]; ?>",
                                    method:"POST",
                                    data:{operation:"set_upgrade_product",product_id:id,pirce_id:selectedPrice},
                                },true,true);

                                request.done(function(result){
                                    if(result != ''){
                                        var solve = getJson(result);
                                        if(solve !== false){
                                            if(solve.status == "error"){
                                                if(solve.message != undefined && solve.message != '')
                                                    alert_error(solve.message,{timer:5000});
                                            }else if(solve.status == "successful"){
                                                if(solve.message != undefined) alert_success(solve.message,{timer:2000});
                                                if(solve.redirect != undefined && solve.redirect != ''){
                                                    window.location.href = solve.redirect;
                                                }
                                            }
                                        }else
                                            console.log(result);
                                    }
                                });

                            });

                        }

                        $(document).ready(function(){
                            <?php
                            if($upgproducts["products"]){
                            $keys = array_keys($upgproducts["products"]);
                            ?>$('#upgrade_selected_product option[value=<?php echo $keys[0]; ?>]').attr("selected",true).trigger("change");<?php
                            }
                            ?>
                        });
                    </script>

                    <div class="formcon">
                        <div class="yuzde30"><?php echo __("website/account_products/upgrade-select-packages"); ?>:
                            <div class="clear"></div>
                        </div>
                        <div class="yuzde70">
                            <select name="product" id="upgrade_selected_product" onchange="selected_category(this.options[this.selectedIndex].value);">
                                <?php
                                    if(isset($upgproducts) && $upgproducts){
                                        if($upgproducts["categories"]){
                                            foreach($upgproducts["categories"] AS $caid=>$val){
                                                ?>
                                                <option<?php echo isset($val["non-product"]) ? ' disabled' : ''; ?> value="<?php echo $caid; ?>"><?php echo $val["title"]; ?></option>
                                                <?php
                                            }
                                        }
                                    }
                                ?>
                            </select>

                        </div>
                    </div>

                    <div class="tablopaketler" style="background: none;">

                        <?php
                            foreach($upgproducts["categories"] AS $caid=>$val){
                                $products   = isset($upgproducts["products"][$caid]) ? $upgproducts["products"][$caid] : false;
                                ?>
                                <div id="category_<?php echo $caid; ?>" style="display:none;" class="upgrade-products">
                                    <?php
                                        foreach($products AS $product){
                                            $prices     = $upgproducts["prices"][$product["id"]];

                                            $opt  = Utility::jdecode($product["options"],true);
                                            $optl = Utility::jdecode($product["options_lang"],true);
                                            $isCurrent = $product["id"] == $proanse["product_id"];
                                            if($isCurrent) continue;

                                            $popular            = false;

                                            if(isset($opt["popular"]) && $opt["popular"]) $popular = true;
                                            ?>
                                            <div class="tablepaket<?php echo $popular ? ' active' : '' ?>">
                                                <?php
                                                    if($popular){
                                                        ?>
                                                        <div class="tablepopular"><?php echo __("website/products/popular"); ?></div>
                                                        <?php
                                                    }
                                                ?>
                                                <div class="tpakettitle"><?php echo $product["title"]; ?></div>
                                                <div class="paketline"></div>

                                                <?php
                                                    $features = $product["features"];
                                                ?>
                                                <div class="clear"></div>
                                                <div class="products_features">
                                                    <?php echo nl2br($features); ?>
                                                </div>
                                                <div class="clear"></div>
                                                <?php
                                                    if($features){
                                                        ?>
                                                        <div class="paketline"></div>
                                                        <?php
                                                    }
                                                ?>

                                                <input type="hidden" id="product_<?php echo $product["id"]; ?>_name" value="<?php echo $product["title"]; ?>">
                                                <select id="product_<?php echo $product["id"]; ?>_price">
                                                    <?php
                                                        foreach($prices AS $k=>$price){

                                                            if($foreign_user) $payable =$price["payable"];
                                                            else $payable = $price["taxed_payable"];
                                                            $payable = Money::formatter_symbol($payable,$price["cid"]);

                                                            if($price["amount"]>0){
                                                                $period = View::period($price["time"],$price["period"]);
                                                                $amount     = Money::formatter_symbol($price["amount"],$price["cid"],!$product["override_usrcurrency"]);
                                                            }else{
                                                                $amount = ___("needs/free-amount");
                                                                $period = NULL;
                                                            }
                                                            $show_price = $amount;
                                                            if($period) $show_price .= " (".$period.")";
                                                            ?>
                                                            <option value="<?php echo $k; ?>" data-payable="<?php echo $payable; ?>"><?php echo $show_price; ?></option>
                                                            <?php
                                                        }
                                                    ?>
                                                </select>
                                                <?php if($product["haveStock"]): ?>
                                                    <a href="javascript:upgradeProduct(<?php echo $product["id"]; ?>);void 0;" class="gonderbtn" id="product_upgrade_<?php echo $product["id"]; ?>"><i class="fa fa-shopping-cart" aria-hidden="true"></i> <?php echo __("website/account_products/button-upgrade"); ?></a>
                                                <?php else: ?>
                                                    <a id="sunucutukenbtn" class="gonderbtn"><i class="fa fa-ban" aria-hidden="true"></i> <?php echo __("website/products/out-of-stock2"); ?></a>
                                                <?php endif; ?>
                                            </div>
                                            <?php
                                        }
                                    ?>
                                </div>
                                <?php
                            }
                        ?>

                    </div>
                <?php else: ?>
                    <div class="clear"></div>
                    <div id="upgrade-product-none">
                        <?php echo __("website/account_products/ugrade-package-none"); ?>
                    </div>
                <?php endif; ?>

            </div>

        </div>
    <?php endif; ?>

    <?php if($proanse["status"] == "active" && $ctoc_service_transfer): ?>
        <div id="transfer-service" class="tabcontent">
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
                        <div class="formcon">
                            <div class="yuzde30"><?php echo __("website/account_products/limit-info"); ?></div>
                            <div class="yuzde70" style="color: #F44336;"><?php echo ($ctoc_limit - $ctoc_used); ?></div>
                        </div>
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
                        <thead>
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

    <?php if($proanse["status"] == "active" && $proanse["period"] != "none"): ?>
        <div id="iptaltalebi" class="tabcontent">
            <div class="tabcontentcon">
                <div class="red-info" style="margin-bottom:20px;">
                    <div class="padding15">
                        <i class="fa fa-meh-o" aria-hidden="true"></i>
                        <p><?php echo __("website/account_products/canceled-desc"); ?></p>
                    </div>
                </div>
                <form action="<?php echo $links["controller"]; ?>" method="post" id="CanceledProduct">
                    <input type="hidden" name="operation" value="canceled_product">

                    <textarea name="reason" cols="" rows="3" placeholder="<?php echo __("website/account_products/canceled-reason"); ?>"></textarea>
                    <select name="urgency">
                        <option value="now"><?php echo __("website/account_products/canceled-urgency-now"); ?></option>
                        <option value="period-ending"><?php echo __("website/account_products/canceled-urgency-period-ending"); ?></option>
                    </select>
                    <a href="javascript:void(0);" class="redbtn gonderbtn mio-ajax-submit" mio-ajax-options='{"result":"CanceledProduct_submit","waiting_text":"<?php echo addslashes(__("website/others/button5-pending")); ?>"}'><?php echo __("website/account_products/canceled-button"); ?></a>
                    <div class="clear"></div>
                </form>
                <div id="CanceledProduct_success" style="display: none;">
                    <div style="margin-top:30px;margin-bottom:70px;text-align:center;">
                        <i style="font-size:80px;color:green;" class="fa fa-check"></i>
                        <h4><?php echo __("website/account_products/canceled-sent"); ?></h4>
                        <br>
                    </div>
                </div>
                <script type="text/javascript">
                    function CanceledProduct_submit(result) {
                        if(result != ''){
                            var solve = getJson(result);
                            if(solve !== false){
                                if(solve.status == "error"){
                                    if(solve.for != undefined && solve.for != ''){
                                        $("#CanceledProduct "+solve.for).focus();
                                        $("#CanceledProduct "+solve.for).attr("style","border-bottom:2px solid red; color:red;");
                                        $("#CanceledProduct "+solve.for).change(function(){
                                            $(this).removeAttr("style");
                                        });
                                    }
                                    if(solve.message != undefined && solve.message != '')
                                        alert_error(solve.message,{timer:3000});
                                }else if(solve.status == "successful"){
                                    $("#CanceledProduct").fadeOut(400,function(){
                                        $("#CanceledProduct_success").fadeIn(400);
                                        $("html,body").animate({scrollTop:200},600);
                                    });
                                }
                            }else
                                console.log(result);
                        }
                    }
                </script>
            </div>
        </div>
    <?php endif; ?>

    <div class="clear"></div>
</div>