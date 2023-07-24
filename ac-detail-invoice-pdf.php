<?php
    $pnumbers           = Config::get("contact/phone-numbers");
    $eaddresses         = Config::get("contact/email-addresses");
    $address            = Config::get("contact/address");
    $logo            = Utility::image_link_determiner(Config::get("theme/notifi-header-logo"));
    if(!$logo) $logo = Utility::image_link_determiner(Config::get("theme/header-logo"));
    if(Config::get("theme/invoice-detail-logo")) $logo = Utility::image_link_determiner(Config::get("theme/invoice-detail-logo"));
    $status         = __("website/account_invoices/status-".$invoice["status"]);
    $status         = Utility::strtoupper($status);
    $cdate          = DateManager::format(Config::get("options/date-format"),$invoice["cdate"]);
    $duedate        = DateManager::format(Config::get("options/date-format"),$invoice["duedate"]);
    $datepaid       = substr($invoice["datepaid"],0,4) == "1881" ? '' : DateManager::format(Config::get("options/date-format")." - H:i",$invoice["datepaid"]);
    $refunddate     = substr($invoice["refunddate"],0,4) == "1881" ? '' : DateManager::format(Config::get("options/date-format")." - H:i",$invoice["refunddate"]);
    $sharing        = isset($sharing) ? $sharing : false;
    /*
    if($sharing) $censored = isset($udata) && $udata["id"] == $invoice["user_id"] ? false : true;
    else $censored       = false;
    */
    if(isset($admin)) $censored = false;
    if(!($invoice["user_data"]["kind"] ?? '')) $invoice["user_data"]["kind"] = "individual";

    $censored = false;
    $GLOBALS["censured"] = false;

    if(!function_exists("censored"))
    {
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
    }
?>
<style>
    .line {
        background-color: #ddd;
        float: left;
        height: 0.5px;
        width:300px;
    }
    table tr td {border-bottom:1px solid #ddd;padding:12px;}
</style>
<page backtop="10mm" backbottom="0mm" backleft="10mm" backright="12mm">

    <table style="color: #345a6c;" width="100%" border="0" align="center" cellpadding="25" cellspacing="0">
        <tr>
            <td valign="top" style="line-height:20px;font-size:12px;" width="300">

              <h4><?php echo __("website/account_invoices/invoice-owner"); ?></h4>

                <div class="line"></div>

                <?php if($invoice["user_data"]["kind"] == "individual"): ?>
                    <p style="margin:5px 0px;font-size:13px;">
                        <strong><?php echo $invoice["user_data"]["full_name"]; ?></strong>
                    </p>

                    <?php if($invoice["user_data"]["address"]["country_id"] == 227 && isset($invoice["user_data"]["identity"]) && $invoice["user_data"]["identity"]): ?>
                        <?php echo __("website/account_invoices/uinfo-identity").": ".censored('identity',$invoice["user_data"]["identity"]); ?>
                    <?php endif; ?>

                <?php elseif($invoice["user_data"]["kind"] == "corporate"): ?>
                    <p style="margin:5px 0px;font-size:13px;">
                        <strong><?php echo $invoice["user_data"]["company_name"]; ?></strong>
                    </p>
                    <?php
                        $add_br = false;
                    ?>
                    <?php if(isset($invoice["user_data"]["company_tax_office"]) && $invoice["user_data"]["company_tax_office"]): ?>
                        <?php $add_br = true; echo __("website/account_invoices/uinfo-company_tax_office").": ".censored("company_tax_office",$invoice["user_data"]["company_tax_office"]); ?>
                    <?php endif; ?>

                    <?php if(isset($invoice["user_data"]["company_tax_number"]) && $invoice["user_data"]["company_tax_number"]): ?>
                        <?php $add_br = true; echo __("website/account_invoices/uinfo-company_tax_number").": ".censored("company_tax_number",$invoice["user_data"]["company_tax_number"]); ?>
                    <?php endif; ?>
                    <?php echo $add_br ? '<br>' : ''; ?>
                <?php endif; ?>

                <?php
                    if(isset($invoice["user_data"]["address"]) && $invoice["user_data"]["address"]){
                        $adrs = $invoice["user_data"]["address"];

                        $address_info = $adrs["address"]." / ";

                        $address_info .= isset($adrs["counti"]) && $adrs["counti"] ? $adrs["counti"]." / " : '';

                        $address_info .= isset($adrs["city"]) && $adrs["city"] ? $adrs["city"]." / " : '';
                        $address_info .= AddressManager::get_country_name($adrs["country_code"]);
                        $address_info .= isset($adrs["zipcode"]) && $adrs["zipcode"] ? " / ".$adrs["zipcode"] : '';
                        if($censored)
                            echo Filter::censored($address_info);
                        else
                            echo $address_info;

                        echo "<br>";
                    }

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

                    if(strlen($phone) > 2)
                    {
                        echo $phone ? censored('phone',$phone)." - " : '';
                    }
                    echo censored('email',$invoice["user_data"]["email"]);

                    if(isset($custom_fields) && $custom_fields)
                    {
                        echo '<br>';
                        $custom_field_keys = array_keys($custom_fields);
                        $end_f = end($custom_field_keys);
                        foreach($custom_fields AS $f_id => $field)
                        {
                            echo '<span>'.$field["name"].'</span> : ';
                            if($censored)
                                echo Filter::censored($field["value"]);
                            else
                                echo $field["value"];
                            if($f_id != $end_f)
                                echo '<br>';
                        }
                    }

                ?>

                <?php if($censored): ?>
                    <br>
                    <span style="color:#F44336;"><?php echo __("website/account_invoices/censored-info"); ?></span>
                <?php endif; ?>

               <h3 style="margin:25px 0;"> <?php echo __("website/account_invoices/invoice-num"); ?> <?php echo $invoice["number"] ? $invoice["number"] : "#".$invoice["id"]; ?></h3>



            </td>
            <td valign="top" style="line-height:20px;font-size:12px;" colspan="2" align="right" width="300">

                <img style="margin:10px 0;" title="Logo" alt="Logo" src="<?php echo $logo; ?>" width="175" height="auto">
                <?php
                    $informations       = ___("constants/informations");
                    $informations       = explode(EOL,$informations);
                    $company_name       = $informations[0];
                    unset($informations[0]);
                    $informations       = array_values($informations);
                    $informations       = implode(EOL,$informations);
                ?>
                <p style="margin:5px 0px;font-size:13px;">
                    <?php echo stristr($company_name,'<strong>') ? $company_name : '<strong>'.$company_name.'</strong>'; ?>
                </p>
                <?php
                    echo str_replace(EOL,"<br>",$informations);
                ?>
                <br><?php echo $address; ?>
                <br><?php echo implode(" - ",array_filter([isset($pnumbers[0]) ? $pnumbers[0] : '' ,isset($eaddresses[0]) ? $eaddresses[0] : ''])); ?>
                <br>
                <br>
                <?php echo __("website/account_invoices/creation-date"); ?>: <?php echo $cdate; ?><br>
                <?php echo __("website/account_invoices/due-date"); ?>: <?php echo $duedate; ?>
            </td>
        </tr>
        <tr>
            <td colspan="2" bgcolor="#EEE"><strong><?php echo __("website/account_invoices/description"); ?></strong></td>
            <td width="25%" align="center" bgcolor="#EEE"><strong><?php echo __("website/account_invoices/amount"); ?></strong></td>
        </tr>

        <?php
            if(isset($items) && $items){
                foreach($items AS $item){
                    $amount = $item["total_amount"];
                    $cid    = isset($item["currency"]) ? $item["currency"] : $item["cid"];
                    ?>
                    <tr>
                        <td style="font-size:12px;width:450px;" colspan="2"><?php echo implode("<br>- ",explode(EOL,$item["description"])); ?></td>
                        <td align="center"><?php echo Money::formatter_symbol($amount,$cid); ?></td>
                    </tr>
                    <?php
                }
            }

            if($invoice["sendbta"] > 0.00)
            {
                ?>
                <tr>
                    <td style="font-size:12px;" colspan="2"><?php echo __("website/account_invoices/sendbta"); ?></td>
                    <td align="center"><?php echo Money::formatter_symbol($invoice["sendbta_amount"],$invoice["currency"]); ?></td>
                </tr>
                <?php
            }

            if($invoice["pmethod_commission"] > 0.00)
            {
                ?>
                <tr>
                    <td style="font-size:12px;" colspan="2"><?php echo __("website/account_invoices/pmethod_commission",['{method}' => $pmethod_name]); ?> (%<?php echo $invoice["pmethod_commission_rate"]; ?>)</td>
                    <td align="center"><?php echo Money::formatter_symbol($invoice["pmethod_commission"],$invoice["currency"]); ?></td>
                </tr>
                <?php
            }
        ?>


        <tr>
            <td colspan="2" align="right" bgcolor="#EEE"><?php echo __("website/account_invoices/subtotal"); ?></td>
            <td align="center" bgcolor="#EEE"><?php echo Money::formatter_symbol($invoice["subtotal"],$invoice["currency"]); ?></td>
        </tr>

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
                            <tr>
                                <td style="font-size:12px;" colspan="2"><?php echo $name; ?></td>
                                <td align="center">-<?php echo $item["amount"]; ?></td>
                            </tr>
                            <?php
                        }
                    }

                    if(isset($items["promotions"]) && $items["promotions"]){
                        foreach($items["promotions"] AS $item){
                            $name   = $item["name"]." - ".$item["dvalue"];
                            $total_discount_amount += $item["amountd"];

                            ?>
                            <tr>
                                <td style="font-size:12px;" colspan="2"><?php echo $name; ?></td>
                                <td align="center">-<?php echo $item["amount"]; ?></td>
                            </tr>
                            <?php
                        }
                    }


                    if(isset($items["dealership"]) && $items["dealership"]){
                        foreach($items["dealership"] AS $item){
                            $name   = $item["name"]." - %".$item["rate"];
                            $total_discount_amount += $item["amountd"];

                            ?>
                            <tr>
                                <td style="font-size:12px;" colspan="2"><?php echo $name; ?></td>
                                <td align="center">-<?php echo $item["amount"]; ?></td>
                            </tr>
                            <?php
                        }
                    }

                    if($total_discount_amount){
                        $discounted_total_amount = $invoice["subtotal"] - $total_discount_amount;
                        ?>

                        <tr>
                            <td style="font-size:12px;font-weight: bold;color: #4caf50;" colspan="2"><?php echo __("website/account_invoices/total-discount-amount"); ?></td>
                            <td align="center" style="font-weight: bold;color: #4caf50;">-<?php echo Money::formatter_symbol($total_discount_amount,$invoice["currency"]); ?></td>
                        </tr>

                        <tr>
                            <td style="font-size:12px;" colspan="2"><?php echo __("website/account_invoices/discounted-total"); ?></td>
                            <td align="center"><?php echo Money::formatter_symbol($discounted_total_amount,$invoice["currency"]); ?></td>
                        </tr>
                        <?php
                    }

                }
            }

            if(Config::get("options/taxation"))
            {
                ?>
                <tr>
                    <td colspan="2" align="right" bgcolor="#EEE"><?php echo __("website/account_invoices/tax-amount",['{rate}' => str_replace(".00","",$invoice["taxrate"]),'{rates}' => $tax_rates ?? '']); ?></td>
                    <td align="center" bgcolor="#EEE"><?php echo Money::formatter_symbol($invoice["tax"],$invoice["currency"]); ?></td>
                </tr>
                <?php
            }
        ?>
        <tr>
            <td colspan="2" align="right" bgcolor="#EEE"><strong><?php echo __("website/account_invoices/total-amount"); ?></strong></td>
            <td align="center" bgcolor="#EEE"><strong><?php echo Money::formatter_symbol($invoice["total"],$invoice["currency"]); ?></strong></td>
        </tr>

        <?php
            $invoice_qr_codes = Hook::run("AddQRCodetoInvoiceDetailinClientArea",$invoice);
            if($invoice_qr_codes){
                foreach($invoice_qr_codes AS $qr_code)
                {
                    if($qr_code){
                        $image      = $qr_code['image'] ?? '';
                        $title      = $qr_code['title'] ?? '';
                        $desc       = $qr_code['description'] ?? '';
                        if(!$image) continue;
                        ?>

                        <tr>
                            <td colspan="3" align="left" style="padding:20px 0px;">
                                <img style="width:80px;float:left;margin-right:20px;" src="<?php echo $image; ?>">
                                <?php if($desc): ?>
                                    <strong><?php echo $title; ?></strong>
                                    <p><?php echo $desc; ?></p>
                                <?php endif; ?>
                            </td>
                        </tr>

                        <?php
                    }
                }
            }
        ?>

        <tr>
            <td colspan="3" align="center" style="border:none;padding-top:50px;">

                <?php if($invoice["status"] == "unpaid"): ?>
                    <h1 style="font-size:70px;margin-top:25px;color:#ee9892"><?php echo $status; ?></h1>
                    <SPAN><?php echo __("website/account_invoices/due-date"); ?>: <?php echo $duedate; ?></SPAN>
                <?php elseif($invoice["status"] == "paid"): ?>
                    <h1 style="font-size:70px;margin-top:25px;color:#add77c"><?php echo $status; ?></h1>
                    <SPAN><?php echo $pmethod_name; ?> (<?php echo $datepaid; ?>)</SPAN><br><br>
                    <strong><?php echo __("website/account_invoices/text1"); ?></strong>

                <?php elseif($invoice["status"] == "waiting"): ?>
                    <h1 style="font-size:40px;margin-top:25px;"><?php echo $status; ?></h1>
                    <SPAN><?php echo $pmethod_name; ?> (<?php echo $datepaid; ?>)</SPAN>

                <?php elseif($invoice["status"] == "cancelled"): ?>
                    <h1 style="font-size:40px;margin-top:25px;"><?php echo $status; ?></h1>

                <?php elseif($invoice["status"] == "refund"): ?>
                    <h1 style="font-size:40px;margin-top:25px;"><?php echo $status; ?></h1>
                    <SPAN><?php echo $pmethod_name; ?> (<?php echo $datepaid; ?>)</SPAN><br><br>
                    <SPAN><?php echo __("website/account_invoices/refunded"); ?> (<?php echo $refunddate; ?>)</SPAN>
                <?php endif; ?>

               
            </td>
        </tr>



        <?php if(Filter::html_clear(Config::get("options/invoice_special_note/".View::$init->ui_lang))): ?>
            <tr>
                <td colspan="3" align="left" style="border-top: 2px solid #eee;border-bottom:none;">
                    <p>
                        <?php
                            echo Utility::text_replace(Config::get("options/invoice_special_note/".View::$init->ui_lang),[
                                '{CLIENT_ID}' => $invoice["user_data"]["id"],
                                '{CLIENT_TAX_NUMBER}' => $invoice["user_data"]["company_tax_number"],
                                '{CLIENT_TAX_OFFICE}' => $invoice["user_data"]["company_tax_office"],
                            ]);
                        ?>
                    </p>
                </td>
            </tr>
        <?php endif; ?>
    </table>

</page>
