<?php defined('CORE_FOLDER') OR exit('You can not get in here!');
    $master_content_none = true;
    $logo            = Utility::image_link_determiner(Config::get("theme/notifi-header-logo"));
    if(!$logo) $logo = Utility::image_link_determiner(Config::get("theme/header-logo"));
    $status         = __("website/account_invoices/status-".$invoice["status"]);
    $status         = Utility::strtoupper($status);
    $cdate          = DateManager::format("d/m/Y",$invoice["cdate"]);
    $duedate        = DateManager::format("d/m/Y",$invoice["duedate"]);
    $datepaid       = substr($invoice["datepaid"],0,4) == "1881" ? '' : DateManager::format("d/m/Y H:i",$invoice["datepaid"]);
    $refunddate     = substr($invoice["refunddate"],0,4) == "1881" ? '' : DateManager::format("d/m/Y H:i",$invoice["refunddate"]);
    $sharing        = isset($sharing) ? $sharing : false;
    if($sharing) $censored = isset($udata) && $udata["id"] == $invoice["user_id"] ? false : true;
    else $censored       = false;
    if(isset($admin)) $censored = false;

    $GLOBALS["censured"] = $censored;

    function censored($type='',$data=''){
        $data     = trim($data);
        $censored = $GLOBALS["censured"];
        if($censored){
            $str_arr    = Utility::str_split($data);
            $str        = NULL;
            $size       = sizeof($str_arr)-1;

            if($type == "company_tax_number" || $type == "identity"){
                $lastCharC = $size-8;
            }elseif($type == "company_tax_office"){
                $lastCharC = $size <5 ? $size-3 : $size-4;
            }elseif($type == "phone"){
                $lastCharC = $size-4;
            }

            if($type == "email"){
                $split      = explode("@",$data);
                $prefix     = $split[0];
                $suffix     = $split[1];
                $dots       = explode(".",$suffix);
                $str_arr    = str_split($prefix);
                $size       = sizeof($str_arr)-1;
                $charC      = $size < 5 ? $size-3 : $size-6;
                for($i=0; $i<=$size; $i++){
                    $char = isset($str_arr[$i]) ? $str_arr[$i] : '';;
                    if($i>$charC) $str .= '*';
                    else $str .= $char;
                }
                $str    .= "@";

                $str_arr    = str_split($dots[0]);
                $size       = sizeof($str_arr);
                $str        .= str_repeat("*",$size);
                unset($dots[0]);
                $str .= ".".implode(".",$dots);
            }elseif(isset($firstCharC)){
                for($i=0; $i<=$size; $i++){
                    $char = isset($str_arr[$i]) ? $str_arr[$i] : '';;
                    if($i<$firstCharC) $str .= '*';
                    else $str .= $char;
                }
            }elseif(isset($lastCharC)){
                for($i=0; $i<=$size; $i++){
                    $char = isset($str_arr[$i]) ? $str_arr[$i] : '';;
                    if($i>$lastCharC) $str .= '*';
                    else $str .= $char;
                }
            }else return $data;

            return $str;
        }else
            return $data;
    }

?>
<!DOCTYPE html>
<html>
<head>
    <?php
        $hoptions = ["jquery-ui","jsPDF"];
        include __DIR__.DS."inc/ac-head.php";
    ?>

    <script type="text/javascript">
        $(document).ready(function(){
            if(gGET("status") != null){
                var url = sGET("status",'');
                history.pushState(false, "", url);
            }
        });

        var base64Img = null;
        margins = {
            top: 70,
            bottom: 40,
            left: 30,
            width: 550
        };

        function dataPrint(type){

            if(type == "normal"){

                window.print();

            }else if(type == "pdf"){
                var pdf_name    = '<?php echo __("website/account_invoices/invoice"); ?>-<?php echo $invoice["id"]; ?>.pdf';
            }
        }
    </script>
    <style type="text/css">
        #muspanel .header {
            z-index:99;
        }
    </style>
</head>
<body id="muspanel" style="background:#eee;">

<?php if(Filter::GET("status") == "success"): ?>
    <script>
        swal(
            '<?php echo __("website/account_invoices/successx"); ?>',
            '<?php echo __("website/account_invoices/success"); ?>',
            'success'
        )
    </script>

<?php elseif(Filter::GET("status") == "fail"): ?>
    <script>
        swal(
            '<?php echo __("website/account_invoices/errorx"); ?>',
            '<?php echo __("website/account_invoices/error2"); ?>',
            'error'
        )
    </script>
<?php endif; ?>

<div class="invoicex">
    <div class="padding">

        <div id="exportData">

            <div class="companybillinfo">
                <img title="Logo" alt="Logo" src="<?php echo $logo; ?>" width="200" height="auto">
                <span style="margin-bottom:10px;display: inline-block;">
                <?php
                    echo str_replace(EOL,"<br>",$informations);
                ?>
                    <br><?php echo $address; ?><br>
                    <?php echo isset($pnumbers[0]) ? $pnumbers[0] : '*'; ?> - <?php echo isset($eaddresses[0]) ? $eaddresses[0] : '*'; ?>
            </span>
            </div>

            <div class="custbillinfo">
                <h5><?php echo __("website/account_invoices/invoice-owner"); ?></h5>
                <div class="line"></div>
                <span style="margin-bottom:10px;display: inline-block;">
                <?php if($invoice["user_data"]["kind"] == "individual"): ?>

                    <strong><?php echo $invoice["user_data"]["full_name"]; ?></strong>
                    <?php if($invoice["user_data"]["address"]["country_id"] == 227 && isset($invoice["user_data"]["identity"]) && $invoice["user_data"]["identity"]): ?>
                        <br>
                        <?php echo __("website/account_invoices/uinfo-identity").": ".censored('identity',$invoice["user_data"]["identity"]); ?>
                    <?php endif; ?>

                <?php elseif($invoice["user_data"]["kind"] == "corporate"): ?>

                    <strong><?php echo $invoice["user_data"]["company_name"]; ?></strong>

                    <?php if(isset($invoice["user_data"]["company_tax_office"]) && $invoice["user_data"]["company_tax_office"]): ?>
                        <br><?php echo __("website/account_invoices/uinfo-company_tax_office").": ".censored("company_tax_office",$invoice["user_data"]["company_tax_office"]); ?>
                    <?php endif; ?>

                    <?php if(isset($invoice["user_data"]["company_tax_number"]) && $invoice["user_data"]["company_tax_number"]): ?>
                        <?php echo !$invoice["user_data"]["company_tax_office"] ? '<br>' : ''; ?>
                        <?php echo __("website/account_invoices/uinfo-company_tax_number").": ".censored("company_tax_number",$invoice["user_data"]["company_tax_number"]); ?>
                    <?php endif; ?>

                <?php endif; ?>

                    <?php
                        if(isset($invoice["user_data"]["address"]) && $invoice["user_data"]["address"]){
                            $adrs = $invoice["user_data"]["address"];
                            echo "<br>";
                            if(!$censored) echo $adrs["address"]." ";
                            echo isset($adrs["zipcode"]) && $adrs["zipcode"] ? " ".$adrs["zipcode"]." / " : '';
                            echo isset($adrs["counti"]) && $adrs["counti"] ? $adrs["counti"]." / " : '';
                            echo isset($adrs["city"]) && $adrs["city"] ? $adrs["city"]." / " : '';
                            echo AddressManager::get_country_name($adrs["country_code"]);
                        }
                    ?>

                    <?php
                        $phone = NULL;
                        if($invoice["user_data"]["kind"] == "corporate")
                            if(isset($invoice["user_data"]["landline_phone"]) && $invoice["user_data"]["landline_phone"])
                                $phone  = $invoice["user_data"]["landline_phone"];

                        if($invoice["user_data"]["kind"] == "individual"){

                            if(isset($invoice["user_data"]["landline_phone"]) && $invoice["user_data"]["landline_phone"])
                                $phone  = $invoice["user_data"]["landline_phone"];

                            if(!$phone && isset($invoice["user_data"]["gsm"]) && $invoice["user_data"]["gsm"])
                                $phone  = "+".$invoice["user_data"]["gsm_cc"].$invoice["user_data"]["gsm"];
                        }

                        echo "<br>";
                        echo $phone ? censored('phone',$phone)." - " : '';
                        echo censored('email',$invoice["user_data"]["email"]);
                    ?>

            </span>

                <?php if($censored): ?>
                    <div class="gray-info" style="margin-bottom: 15px;text-align: center;">
                        <div class="padding10"><?php echo __("website/account_invoices/censored-info"); ?></div>
                    </div>
                <?php endif; ?>

            </div>

            <div class="clear"></div>

            <div class="invoicestatus">
                <div class="padding20">
                    <?php if($invoice["status"] == "waiting"): ?>
                        <span class="invwait"><?php echo $status; ?></span>
                    <?php elseif($invoice["status"] == "paid"): ?>
                        <span class="invpaid"><?php echo $status; ?></span>
                    <?php elseif($invoice["status"] == "unpaid" || $invoice["status"] == "refund" || $invoice["status"] = "cancelled"): ?>
                        <span class="invnopay"><?php echo $status; ?></span>
                    <?php endif; ?>

                    <?php if($invoice["status"] == "refund"): ?>
                        <span class="invoicepaymethod">(<?php echo $refunddate; ?>)</span>
                    <?php endif; ?>

                    <?php
                        if(!isset($pmethod_name) && $invoice["pmethod"] != "none") $pmethod_name = $invoice["pmethod"];
                    ?>

                    <?php if(isset($pmethod_name) && $pmethod_name): ?>
                        <span class="invoicepaymethod">(<?php echo $pmethod_name; ?>)</span>
                    <?php endif; ?>

                    <div class="clear"></div>
                </div>
            </div>

            <div class="invoicetimes">
                <div class="formcon">
                    <div class="yuzde50"><?php echo __("website/account_invoices/creation-date"); ?>:</div>
                    <div class="yuzde50"><?php echo $cdate; ?></div>
                </div>
                <div class="formcon">
                    <div class="yuzde50"><?php echo __("website/account_invoices/due-date"); ?>:</div>
                    <div class="yuzde50"><?php echo $duedate; ?></div>
                </div>

                <?php if($datepaid): ?>
                    <div class="formcon">
                        <div class="yuzde50"><?php echo __("website/account_invoices/paid-date"); ?>:</div>
                        <div class="yuzde50"><?php echo $datepaid; ?></div>
                    </div>
                <?php endif; ?>
            </div>


            <div class="invoiceidx">
                <?php echo __("website/account_invoices/invoice-num"); ?>: <?php echo $invoice["id"]; ?>
            </div>

            <div class="clear"></div>

            <div class="invoicedesc">


                <div class="formcon" style="background:#eee;">
                    <div class="yuzde70"><span><strong><?php echo __("website/account_invoices/description"); ?></strong></span></div>
                    <div class="yuzde30"><span><strong><?php echo __("website/account_invoices/amount"); ?></strong></span></div>
                </div>

                <?php

                    if(isset($items) && $items){
                        foreach($items AS $item){
                            $amount = $item["amount"];
                            $cid    = isset($item["currency"]) ? $item["currency"] : $item["cid"];
                            ?>
                            <div class="formcon">
                                <div class="yuzde70"><span><?php echo nl2br($item["description"]); ?></span></div>
                                <div class="yuzde30"><span><?php echo Money::formatter_symbol($amount,$cid); ?></span></div>
                            </div>
                            <?php
                        }
                    }

                ?>


                <div id="sendbta_wrap" class="formcon" style="<?php echo $invoice["sendbta"] ? '' : 'display:none;'; ?>">
                    <div class="yuzde70"><span><?php echo __("website/account_invoices/sendbta"); ?></span></div>
                    <div class="yuzde30"><span id="sendbta_fee"><?php echo Money::formatter_symbol($invoice["sendbta_amount"],$invoice["currency"]); ?></span></div>
                </div>

                <div id="pmethod_commission_wrap" class="formcon" style="<?php echo $invoice["pmethod_commission"]>0 ? '' : 'display:none;'; ?>">
                    <div class="yuzde70"><span id="pmethod_commission_label"><?php echo __("website/account_invoices/pmethod_commission",['{method}' => $invoice["pmethod"]]); ?> (%<?php echo $invoice["pmethod_commission_rate"]; ?>)</span></div>
                    <div class="yuzde30"><span id="pmethod_commission_fee"><?php echo Money::formatter_symbol($invoice["pmethod_commission"],$invoice["currency"]); ?></span></div>
                </div>


                <div class="formcon" style="background:#eee;padding: 0px;">
                    <div class="yuzde70" style="text-align:right;"><span><?php echo __("website/account_invoices/subtotal"); ?></span></div>
                    <div class="yuzde30"><span><strong id="subtotal_fee"><?php echo Money::formatter_symbol($invoice["subtotal"],$invoice["currency"]); ?></strong></span></div>
                </div>

                <?php
                    if($invoice["discounts"]){
                        $discounts = $invoice["discounts"] ? Utility::jdecode($invoice["discounts"],true) : [];
                        if($discounts){
                            $total_discount_amount = 0;
                            $items  = $discounts["items"];

                            if(isset($items["coupon"]) && $items["coupon"]){
                                foreach($items["coupon"] AS $item){
                                    $name   = $item["name"]." - ".$item["dvalue"];
                                    $total_discount_amount += $item["amountd"];
                                    ?>
                                    <div class="formcon" style="background:#eee;padding: 0px;">
                                        <div class="yuzde70" style="text-align:right;"><span><?php echo $name; ?></span></div>
                                        <div class="yuzde30"><span><strong>-<?php echo $item["amount"]; ?></strong></span></div>
                                    </div>
                                    <?php
                                }
                            }

                            if(isset($items["dealership"]) && $items["dealership"]){
                                foreach($items["dealership"] AS $item){
                                    $name   = $item["name"]." - %".$item["rate"];
                                    $total_discount_amount += $item["amountd"];
                                    ?>
                                    <div class="formcon" style="background:#eee;padding: 0px;">
                                        <div class="yuzde70" style="text-align:right;"><span><?php echo $name; ?></span></div>
                                        <div class="yuzde30"><span><strong>-<?php echo $item["amount"]; ?></strong></span></div>
                                    </div>
                                    <?php
                                }
                            }

                            if($total_discount_amount){
                                $discounted_total_amount = $invoice["subtotal"] - $total_discount_amount;
                                ?>
                                <div class="formcon" style="background:#eee;padding: 0px;font-weight: bold;color: #4caf50;">
                                    <div class="yuzde70" style="text-align:right;"><span><?php echo __("website/account_invoices/total-discount-amount"); ?></span></div>
                                    <div class="yuzde30"><span style="color: #4caf50;"><strong>-<?php echo Money::formatter_symbol($total_discount_amount,$invoice["currency"]); ?></strong></span></div>
                                </div>

                                <div class="formcon" style="background:#eee;padding: 0px;">
                                    <div class="yuzde70" style="text-align:right;"><span><?php echo __("website/account_invoices/discounted-total"); ?></span></div>
                                    <div class="yuzde30"><span><strong><?php echo Money::formatter_symbol($discounted_total_amount,$invoice["currency"]); ?></strong></span></div>
                                </div>
                                <?php
                            }

                        }
                    }
                ?>

                <div id="tax_wrap" class="formcon" style="background:#eee;padding: 0px;<?php echo $invoice["tax"]>0 && $invoice["taxrate"]>0 ? '' : 'display:none;'; ?>">
                    <div class="yuzde70" style="text-align:right;"><span><?php echo __("website/account_invoices/tax-amount",['{rate}' => str_replace(".00","",$invoice["taxrate"]),]); ?></span></div>
                    <div class="yuzde30"><span><strong id="tax_fee"><?php echo Money::formatter_symbol($invoice["tax"],$invoice["currency"]); ?></strong></span></div>
                </div>

                <div id="total_wrap" class="formcon" style="background:#eee;border:none;padding: 0px;<?php echo $invoice["total"]>0 ? '' : 'display:none;'; ?>">
                    <div class="yuzde70" style="text-align:right;"><span style="font-weight: bold;font-size: 16px;"><?php echo __("website/account_invoices/total-amount"); ?></span></div>
                    <div class="yuzde30"><span><strong id="total_fee" style="font-size: 16px;"><?php echo Money::formatter_symbol($invoice["total"],$invoice["currency"]); ?></strong></span></div>
                </div>

                <div class="clear"></div>

            </div>

        </div>

        <div class="otherincoivebtns">
            <?php if($permission_share): ?>
                <a href="javascript:open_modal('share-invoice');void 0;" class="sbtn"><i class="fa fa-share-alt"></i> <?php echo __("website/account_invoices/share"); ?></a>
            <?php endif; ?>

            <a href="javascript:dataPrint('normal');void 0;" class="sbtn"><i class="fa fa-print"></i> <?php echo __("website/account_invoices/print"); ?></a>
            <!--
            <a href="javascript:dataPrint('pdf');" class="sbtn" title="<?php echo __("website/account_invoice/download-pdf"); ?>"><i class="fa fa-cloud-download" aria-hidden="true"></i></a>
            -->
        </div>
        <div class="clear"></div>

        <?php if($invoice["status"] == "paid"): ?>
            <div class="faturaodenmis">
                <h4><strong><?php echo __("website/account_invoices/text1"); ?></strong></h4>


                <?php if(!$sharing && $invoice["legal"]): ?>
                    <br>
                    <h5><?php echo __("website/account_invoices/text2"); ?></h5>
                    <div class="yuzde30" style="margin-top:15px;">

                        <?php if($invoice["taxed_file"] != ''): ?>
                            <a href="<?php echo $links["download"]; ?>" class="yesilbtn gonderbtn"><i class="fa fa-cloud-download" aria-hidden="true"></i> <?php echo __("website/account_invoices/download-invoice"); ?></a>
                        <?php else: ?>
                            <a class="graybtn gonderbtn"><i class="fa fa-cloud-download" aria-hidden="true"></i> <?php echo __("website/account_invoices/download-invoice"); ?></a>
                        <?php endif; ?>

                    </div>
                <?php endif; ?>
            </div>
        <?php endif; ?>

        <?php if($invoice["status"] == "unpaid"): ?>

            <script type="text/javascript">
                var selected_payment_method;
                $(function(){

                    $("#accordion").accordion({
                        heightStyle: "content",
                    });

                    setTimeout(function(){
                        reload_selection_result();
                    },200);

                    $("#accordion").on("change","input",function(){
                        reload_selection_result();
                    });

                    $("#continue_button").click(function(){
                        var selected_pmethod = $("#payment_methods input[name=pmethod]:checked").val();
                        var selected_sendbta = $("#selected_sendbta").prop("checked");

                        var request = MioAjax({
                            button_element:this,
                            waiting_text: '<?php echo __("website/others/button2-pending"); ?>',
                            action:"<?php echo $links["controller"]; ?>",
                            method:"POST",
                            data:{
                                operation:"payment-screen",
                                sendbta:selected_sendbta ? 1 : 0,
                                pmethod:selected_pmethod,
                            },
                        },true,true);

                        request.done(function(result){
                            if(result != ''){
                                var solve = getJson(result);
                                if(solve !== false){
                                    if(solve.status == "error"){
                                        if(solve.for != undefined && solve.for != ''){
                                            $("#detailForm "+solve.for).focus();
                                            $("#detailForm "+solve.for).attr("style","border-bottom:2px solid red; color:red;");
                                            $("#detailForm "+solve.for).change(function(){
                                                $(this).removeAttr("style");
                                            });
                                        }
                                        if(solve.message != undefined && solve.message != '')
                                            alert_error(solve.message,{timer:5000});
                                    }else if(solve.status == "successful"){

                                        if(solve.content != undefined){
                                            $("#payment-screen-content").html(solve.content);
                                            $("#selection-methods").fadeOut(500,function () {
                                                $("#payment-screen").fadeIn(500);
                                            });
                                        }

                                        if(solve.message != undefined) alert_success(solve.message,{timer:4000});
                                        if(solve.redirect != undefined && solve.redirect != ''){
                                            setTimeout(function(){
                                                window.location.href = solve.redirect;
                                            },4000);
                                        }
                                    }
                                }else
                                    console.log(result);
                            }
                        });

                    });

                });

                function reload_selection_result(){
                    $("#pmethods_loader").fadeOut(200);

                    var selected_pmethod = $("#payment_methods input[name=pmethod]:checked").val();
                    var selected_sendbta = $("#selected_sendbta").prop("checked");

                    var request         = MioAjax({
                        action: "<?php echo $links["controller"]; ?>",
                        method: "POST",
                        data:{
                            operation:"selection-result",
                            pmethod:selected_pmethod ? selected_pmethod : '',
                            sendbta:selected_sendbta ? 1 : 0,

                        },
                    },true,true);

                    request.done(function(result){
                        if(result != ''){
                            var solve = getJson(result);
                            if(solve !== false){

                                $("#payment_methods").fadeIn(200).html('');
                                var content,amount,see_fee,checked_status;
                                $(solve.pmethods).each(function(key,item){
                                    if(item.balance != undefined){
                                        see_fee =  ' ('+item.balance+')';
                                    }else if(item.commission_fee != undefined){
                                        see_fee = ' (+'+item.commission_fee+')';
                                    }else
                                        see_fee = '';

                                    if(item.selected != undefined){
                                        selected_payment_method = item.option_name;
                                        checked_status = ' checked';
                                    }
                                    else
                                        checked_status = '';

                                    content = '<input id="method-'+key+'" class="checkbox-custom" name="pmethod" value="'+item.method+'" type="radio"'+checked_status+'>';
                                    content += '<label for="method-'+key+'" class="checkbox-custom-label"><strong>'+item.option_name+'</strong>'+see_fee+'</label>';
                                    $("#payment_methods").append(content);
                                });

                                if(solve.tax != undefined && solve.tax != 0){
                                    $("#tax_wrap").css("display","block");
                                    $("#tax_fee").html(solve.tax);
                                }else
                                    $("#tax_wrap").css("display","none");

                                if(solve.total != undefined){
                                    $("#total_wrap").css("display","block");
                                    $("#total_fee").html(solve.total);
                                }else
                                    $("#total_wrap").css("display","none");

                                if(solve.subtotal != undefined) $("#subtotal_fee").html(solve.subtotal);

                                if(solve.visible_sendbta){

                                    if(selected_sendbta){
                                        $("#sendbta_wrap").css("display","block");
                                        $("#sendbta_fee").html(solve.sendbta_fee);
                                    }else
                                        $("#sendbta_wrap").css("display","none");

                                    $("#selected_sendbta_wrap").css("display","block");
                                    var selected_sendbta_text = '<?php echo __("website/account_invoices/sendbta-amount"); ?>';
                                    $("#selected_sendbta_fee").html(selected_sendbta_text.replace('{amount}',solve.sendbta_fee));
                                }else{
                                    $("#selected_sendbta_wrap").css("display","none");
                                    $("#selected_sendbta").prop("checked",false);
                                    $("#sendbta_wrap").css("display","none");
                                }

                                if(solve.pcommission != 0){
                                    $("#pmethod_commission_wrap").css("display","block");
                                    var label_text = '<?php echo __("website/account_invoices/pmethod_commission"); ?>';
                                    label_text = label_text.replace("{method}",selected_payment_method);
                                    label_text += " (%"+solve.pcommission_rate+")";
                                    $("#pmethod_commission_label").html(label_text);
                                    $("#pmethod_commission_fee").html(solve.pcommission);
                                }else
                                    $("#pmethod_commission_wrap").css("display","none");


                            }else
                                console.log(result);
                        }
                    });
                }

                function turnBack(){
                    $("#payment-screen").fadeOut(500,function(){
                        $("#selection-methods").fadeIn(500);
                    });
                }
            </script>

            <div id="payment-screen" style="display: none;">
                <div id="payment-screen-content"></div>
                <div class="clear"></div>
                <a class="lbtn" href="javascript:turnBack();void 0;"><?php echo __("website/account_invoices/payment-turn-back"); ?></a>
            </div>

            <div id="selection-methods" class="tabcontentcon" style="width:100%;">
                <div class="clear"></div>
                <div id="accordion">

                    <h3><?php echo __("website/account_invoices/pay-the-bill"); ?></h3>
                    <div>
                        <div class="odemeyontem">
                            <h5><strong><?php echo __("website/account_invoices/payment-method"); ?></strong></h5>
                            <div class="clear"></div>

                            <div id="pmethods_loader" style="margin-top: 10px; text-align: center;">
                                <div class="spinner"></div>
                            </div>
                            <div id="payment_methods" style="display:none;"></div>

                            <div class="clear"></div>
                        </div>

                        <div class="clear"></div>

                        <div id="selected_sendbta_wrap" class="odemeyontem" style="margin-top:10px;margin-bottom:0px;display:none;">
                            <h5><strong><?php echo __("website/account_invoices/sendbta"); ?></strong></h5>
                            <div class="clear"></div>

                            <input id="selected_sendbta" class="checkbox-custom" name="sendbta" value="1" type="checkbox" style="width:25px;">
                            <label style="float:left;margin-bottom:3px;" for="selected_sendbta" class="checkbox-custom-label"><span class="checktext" id="selected_sendbta_fee"></span></label>
                            <div class="clear"></div>

                        </div>
                        <div class="line"></div>
                        <a href="javascript:void(0);" id="continue_button" class="yesilbtn gonderbtn"><?php echo __("website/account_invoices/continue-button"); ?></a>
                        <div class="clear"></div>
                    </div>
                </div>
            </div>
        <?php endif; ?>



        <div class="clear"></div>
    </div>
</div>

<?php if(!isset($admin)): ?>
    <?php if(isset($udata)): ?>
        <div style="text-align:center;margin-bottom:25px;"><a href="<?php echo $links["invoices"]; ?>"><i class="fa fa-angle-double-left" aria-hidden="true"></i> <?php echo __("website/account_invoices/go-back"); ?></a></div>
    <?php else: ?>
        <div style="text-align:center;margin-bottom:25px;"><a href="<?php echo $login_link; ?>"><i class="fa fa-angle-double-left" aria-hidden="true"></i> <?php echo __("website/account_invoices/sign-in"); ?></a></div>
    <?php endif; ?>
<?php endif; ?>

<?php if($permission_share): ?>
    <div id="share-invoice" style="display: none;" data-izimodal-title="<?php echo __("website/account_invoices/share-invoice"); ?>">
        <div class="padding30">


            <div class="green-info" style="margin-bottom:25px;text-align:center;font-size:14px;">
                <div class="padding20">
                    <i class="fa fa-check-circle-o" style="float:none;    margin: 0px;    margin-bottom: 10px;"></i><div class="clear"></div>
                    <?php echo __("website/account_invoices/share-invoice-info"); ?>                   </div>
            </div>


            <div class="clear"></div>
            <div class="share-invoice-link">
                <strong><?php echo __("website/account_invoice/share-url"); ?></strong><br>
                

                <textarea rows="3" style="resize: none;padding:5px;" type="search" onclick="this.focus();this.select()" readonly="readonly"><?php echo $links["share"]; ?></textarea>
            </div>
            <div class="clear"></div>

        </div>
    </div>
<?php endif; ?>

<?php
    View::main_script();
?>


<div class="clear"></div>
<a href="#0" class="cd-top">Top</a>
</body>
</html>