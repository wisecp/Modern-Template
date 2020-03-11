<?php
    defined('CORE_FOLDER') OR exit('You can not get in here!');
    $hoptions = ["jquery-ui"];
?>
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

            var request = MioAjax({
                button_element:this,
                waiting_text: '<?php echo __("website/others/button2-pending"); ?>',
                action:"<?php echo $links["controller"]; ?>",
                method:"POST",
                data:{
                    operation:"payment-screen",
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

        var request         = MioAjax({
            action: "<?php echo $links["controller"]; ?>",
            method: "POST",
            data:{
                operation:"selection-result",
                pmethod:selected_pmethod ? selected_pmethod : '',

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

                    if(solve.tax != undefined && parseInt(solve.tax) != 0){
                        $("#tax_wrap").css("display","table-row");
                        $("#tax_fee").html(solve.tax);
                    }else
                        $("#tax_wrap").css("display","none");

                    if(solve.total != undefined){
                        $("#total_wrap").css("display","table-row");
                        $("#total_fee").html(solve.total);
                    }else
                        $("#total_wrap").css("display","none");

                    if(solve.subtotal != undefined) $("#subtotal_fee").html(solve.subtotal);

                    if(solve.pcommission != 0){
                        $("#pmethod_commission_wrap").css("display","table-row");
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
<div class="mpanelrightcon">

    <div class="mpaneltitle">
        <div class="sayfayolu"><?php include __DIR__.DS."inc/panel-breadcrumb.php"; ?></div>
        <h4><strong><i class="fa fa-file-text-o"></i> <?php echo __("website/account_invoices/page-bulk-payment"); ?></strong></h4>
    </div>

    <div class="bulkpayment">
        <style>
            .bulkpayment table tr td {padding:10px;border-bottom:1px solid #ccc;}
        </style>

        <table width="100%" border="0" cellpadding="0" cellspacing="0">
            <tr>
                <td width="75%" bgcolor="#eee"><strong><?php echo __("website/account_invoices/bulk-payment-text1"); ?></strong></td>
                <td width="25%" align="center" bgcolor="#eee"><strong><?php echo __("website/account_invoices/bulk-payment-text2"); ?></strong></td>
            </tr>

            <?php
                $currency       = Money::getUCID();
                $subtotal       = 0;
                $tax            = 0;
                if(isset($unpaid_invoices) && $unpaid_invoices){
                    foreach($unpaid_invoices AS $invoice){
                        $us_data    = Utility::jdecode($invoice["user_data"],true);

                        if($invoice["local"] && $invoice["legal"]){
                            $invoice_tax = Money::get_tax_amount($invoice["subtotal"],$invoice["taxrate"]);
                            $invoice_tax = Money::exChange($invoice_tax,$invoice["currency"],$currency);
                            $tax         += $invoice_tax;
                        }

                        $items      = Invoices::item_listing($invoice["id"]);
                        $subtotal   += Money::exChange($invoice["subtotal"],$invoice["currency"],$currency);
                        $bill_link  = Controllers::$init->CRLink("ac-ps-detail-invoice",[$invoice["id"]]);
                        ?>
                        <tr>
                            <td>
                                <strong>
                                    <a href="<?php echo $bill_link; ?>" target="_blank"><?php echo __("website/account_invoices/invoice-num"); ?> #<?php echo $invoice["id"]; ?></a>
                                </strong>
                                <br />
                                <?php
                                    foreach($items AS $item){
                                        $amount = $item["amount"];
                                        $cid    = isset($item["currency"]) ? $item["currency"] : $item["cid"];
                                        echo $item["description"]."<br>";
                                    }
                                ?>
                            </td>
                            <td align="center"><?php echo Money::formatter_symbol($invoice["subtotal"],$invoice["currency"]); ?></td>
                        </tr>
                        <?php
                    }
                }

                $total = $subtotal + $tax;
            ?>

            <tr id="pmethod_commission_wrap" style="display: none;">
                <td align="right" bgcolor="#EEE"><strong id="pmethod_commission_label"><?php echo __("website/account_invoices/pmethod_commission"); ?> (%0)</strong></td>
                <td align="center" bgcolor="#EEE"><strong id="pmethod_commission_fee">0</strong></td>
            </tr>

            <tr id="subtotal_wrap">
                <td align="right" bgcolor="#EEE"><strong><?php echo __("website/account_invoices/bulk-payment-text3"); ?></strong></td>
                <td align="center" bgcolor="#EEE"><strong id="subtotal_fee" <?php echo Money::formatter_symbol($subtotal,$currency); ?></strong></td>
            </tr>


            <tr id="tax_wrap" style="<?php echo $tax > 0 ? '' : 'display: none;'; ?>">
                <td align="right" bgcolor="#EEE"><strong><?php echo ___("needs/tax"); ?></strong></td>
                <td align="center" bgcolor="#EEE"><strong id="tax_fee"><?php echo Money::formatter_symbol($tax,$currency)?></strong></td>
            </tr>
            <tr id="total_wrap">
                <td align="right" bgcolor="#4CAF50"><strong style="font-size:16px;color:white;"><?php echo __("website/account_invoices/bulk-payment-text5"); ?></strong></td>
                <td align="center" bgcolor="#4CAF50"><strong style="font-size:18px;color:white;" id="total_fee"><?php echo Money::formatter_symbol($total,$currency)?></strong></td>
            </tr>
        </table>

        <div class="clear"></div>

        <div id="payment-screen" style="display: none; margin-top:10px;">
            <div id="payment-screen-content"></div>
            <div class="clear"></div>
            <a class="lbtn" href="javascript:turnBack();void 0;"><?php echo __("website/account_invoices/payment-turn-back"); ?></a>
        </div>

        <div id="selection-methods" class="tabcontentcon" style="width:100%;">
            <div class="clear"></div>
            <div id="accordion" style="margin-top: 25px;">

                <h3><?php echo __("website/account_invoices/bulk-payment-text6"); ?></h3>
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
                    <div class="line"></div>
                    <a href="javascript:void(0);" id="continue_button" class="yesilbtn gonderbtn"><?php echo __("website/account_invoices/continue-button"); ?></a>
                    <div class="clear"></div>
                </div>
            </div>
        </div>


    </div>

    <div class="clear"></div>
</div>