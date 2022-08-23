<?php defined('CORE_FOLDER') OR exit('You can not get in here!');
    $hoptions = [
        'page' => "basket",
    ];

    $currency_symbols = [];
    foreach(Money::getCurrencies() AS $currency){
        $symbol = $currency["prefix"] != '' ? trim($currency["prefix"]) : trim($currency["suffix"]);
        if(!$symbol) $symbol = $currency["code"];
        $currency_symbols[$symbol] = $currency["id"];
    }
?>
<script type="text/javascript">
    var currency_symbols = <?php echo Utility::jencode($currency_symbols); ?>;
    var ns_details      = {};
    var whois_details   = {};
    var domain_names    = {};

    function amount_divider(str){
        var visible_amount      = str;
        var split_amount        = visible_amount.split(" ");
        var amount_symbol       = '';
        var amount_symbol_pos   = '';
        var split_amount_last   = split_amount.length-1;

        if(currency_symbols[split_amount[0]]){
            amount_symbol_pos   = 'left';
            amount_symbol       = split_amount[0];
            split_amount.shift();
            visible_amount      = split_amount.join(" ");
        }else if(currency_symbols[split_amount[split_amount_last]]){
            amount_symbol_pos   = 'right';
            amount_symbol       = split_amount[split_amount_last];
            split_amount.pop();
            visible_amount      = split_amount.join(" ");
        }
        return {
            amount      : visible_amount,
            symbol_pos  : amount_symbol_pos,
            symbol      : amount_symbol
        };
    }

    function set_wprivacy(element,id){
        var check = $(element).prop("checked");
        var request = MioAjax({
            action:"<?php echo ($links["bring"] ?? '')."set-wprivacy"; ?>",
            method: "POST",
            data:{id:id,check:check}
        },true,true);
        request.done(function(){
            OrderSummary();
        });
    }
    function change_selection_period(element,id){
        var value = $(element).val();
        var request = MioAjax({
            action:"<?php echo $links["bring"]."change-selection-period"; ?>",
            method: "POST",
            data:{id:id,selection:value}
        },true,true);
        request.done(function(){
            ItemList();
            OrderSummary();
        });
    }
    function change_selection_year(element,id){
        var value = $(element).val();
        var request = MioAjax({
            action:"<?php echo $links["bring"]."change-selection-year"; ?>",
            method: "POST",
            data:{id:id,selection:value}
        },true,true);
        request.done(function(){
            ItemList();
            OrderSummary();
        });
    }
    function ItemList(){
        $("#item_list").html('');
        $("#basket_loader").fadeIn(200);
        var request = MioAjax({
            action: "<?php echo $links["bring"]."item-list"; ?>",
            method: "POST"
        },true,true);

        request.done(function(result){
            var solve = false,content = '';
            if(result){
                $("#basket_loader").fadeOut(1);
                solve = getJson(result);
                if(solve){
                    if(solve.status == "none"){
                        $("#item_list").fadeOut(400).html('');
                        $("#empty_list").fadeIn(400);
                        $(".basket-count").html('0');
                        $("#coupon_code").attr("disabled",true);
                    }else if(solve.status == "listing"){
                        $("#coupon_code").attr("disabled",false);
                        if(solve.count != undefined) $(".basket-count").html(solve.count);
                        if(solve.data != undefined){
                            $("#item_list").fadeOut(1).html('');
                            var size = solve.data.length;
                            var rank = 0;
                            var selection_period = '';

                            $(solve.data).each(function(key,item){
                                rank++;
                                selection_period = '';

                                if(item.ns_details !== undefined) ns_details[item.id] = item.ns_details;
                                if(item.whois_details !== undefined) whois_details[item.id] = item.whois_details;
                                if(item.product_type !== undefined && item.product_type === "domain")
                                    domain_names[item.id] = item.name;

                                if(item.selection_period !== undefined){
                                    selection_period +=
                                        '<select onchange="change_selection_period(this,'+item.id+');">';
                                    $(item.selection_period).each(function(k,v){
                                        selection_period += '<option value="'+k+'"';
                                        if(item.selected_period !== undefined && item.selected_period === v.id)
                                            selection_period += ' selected';
                                        selection_period += '>';
                                        selection_period += v.period;
                                        selection_period +='</option>';
                                    });
                                    selection_period += '</select>';
                                }
                                else if(item.selection_year !== undefined){
                                    selection_period +=
                                        '<select onchange="change_selection_year(this,'+item.id+');">';
                                    $(item.selection_year).each(function(k,v){
                                        var k_i = k+1;
                                        selection_period += '<option value="'+v.year+'"';
                                        if(item.year !== undefined && parseInt(v.year) === parseInt(item.year))
                                            selection_period += ' selected';
                                        selection_period += '>';
                                        selection_period += v.period;
                                        selection_period +='</option>';
                                    });
                                    selection_period += '</select>';
                                }

                                content  = '<div class="sepetlist" id="basket-item-'+key+'">';
                                if(item.reduced != undefined && item.reduced != 0)
                                    content += '<div class="row-label green-label"><?php echo __("website/basket/reduced"); ?></div>';
                                if(item.promotion_applied != undefined)
                                    content += '<div class="row-label green-label"><?php echo __("website/basket/promotion-applied"); ?></div>';
                                content += '<div class="sepetlistcon">';
                                content += '<div class="uhinfo">';
                                content += '<h5><strong>'+item.name+'</strong></h5>';
                                if(item.category != undefined && item.category_route != undefined)
                                    content += '<h4><a href="'+item.category_route+'" target="_blank">'+item.category+'</a></h4>';
                                if(item.domain != undefined && item.domain != '')
                                    content += '<div class="clear"></div>('+item.domain+')';
                                else if(item.ip != undefined && item.ip != '')
                                    content += '<div class="clear"></div>('+item.ip+')';

                                if(item.visible_wprivacy != undefined && item.visible_wprivacy){
                                    var wprivacy_active = '';
                                    if(item.wprivacy != undefined && item.wprivacy)
                                        wprivacy_active = ' checked';

                                    content += '<div class="cart-additional-details">';

                                    content += '<input'+wprivacy_active+' class="checkbox-custom" type="checkbox" id="whois_privacy_'+key+'" onchange="set_wprivacy(this,'+item.id+');"><label for="whois_privacy_'+key+'" class="checkbox-custom-label" style="font-size:14px;"><?php echo __("website/basket/whois-privacy"); ?> (<strong>'+item.wprivacy_price+'</strong>)</label>';

                                    content += '</div>';

                                }

                                if(item.product_type !== undefined && item.product_type === 'domain')
                                {
                                    if(item.event_name !== undefined && (item.event_name === 'DomainNameRegisterOrder' || item.event_name === 'DomainNameTransferRegisterOrder'))
                                    {
                                        content += '<div class="cart-additional-details">';
                                        content += '<a href="javascript:open_whois_info('+item.id+');"><?php echo __("website/basket/whois-tx3"); ?> <i class="fa fa-pencil-square-o" aria-hidden="true"></i></a>';
                                        content += '<a href="javascript:open_ns_info('+item.id+');"><?php echo __("website/basket/dns-tx3"); ?> <i class="fa fa-pencil-square-o" aria-hidden="true"></i></a>';
                                        content += '</div>';
                                    }
                                }

                                if(item.adds != undefined && item.adds.length){
                                    content += "<div class='clear'></div><p>";
                                    $(item.adds).each(function(a_key,a_item){
                                        var pername = a_item.period;
                                        pername =  pername != '' ? " | "+pername : '';
                                        content += '- '+a_item.name+' <span>'+a_item.amount+''+pername+'</span><br>';
                                    });
                                    content += "</p>";
                                }

                                content += '</div>';
                                content += '<div class="uhperiyod">';
                                if(selection_period !== '')
                                    content += selection_period;
                                else
                                    content += '<H5>'+item.period_name+'</H5>';

                                if(item.reduced != undefined && item.reduced != 0){
                                    var replace1 = '<?php echo __("website/basket/reduced2"); ?>';
                                    replace1 = replace1.replace('{rate}',item.reduced);
                                    content += '<span STYLE="color:#81bc00;font-weight:bold;">('+replace1+')</span>';
                                }
                                content += '</div>';

                                content += '<div class="uhtutar">';
                                if(item.amount !== undefined) {
                                    var is_free = '';

                                    if(item.amount === '<?php echo ___("needs/free-amount"); ?>')
                                        is_free = ' style="color:#81bc00;"';

                                    var amount_info = amount_divider(item.amount);
                                    content += '<h4'+is_free+' class="amount_spot_view"><strong><i class="currpos'+amount_info.symbol_pos+'">'+amount_info.symbol+'</i>' + amount_info.amount + '</strong></h4>';
                                }
                                content += '</div>';

                                content += '<div class="uhsil">';
                                content += '<a title="<?php echo __("website/basket/delete-item"); ?>" href="javascript:deleteItem('+key+','+item.id+');void 0;"><i class="fa fa-trash" aria-hidden="true"></i></a>';
                                content += '</div>';

                                content += '<div class="clear"></div>';
                                content += '</div>';
                                content += '</div>';
                                $("#item_list").append(content);
                            });

                            if(size == rank){
                                $("#empty_list").slideUp(400);
                                $("#item_list").fadeIn(500);
                            }

                        }else $("#item_list").fadeIn(100).html("Empty List");
                    }
                }else console.log("Can not resolved : "+result);

            }else $("#basket_loader").addClass("error").html("Failed loaded in basket to item list");
        });

    }

    function deleteItem(index,id){
        var item = $("#basket-item-"+index);
        item.animate({opacity: 4}, 300);
        var request = MioAjax({
            action: "<?php echo $links["bring"]."delete-item"; ?>",
            method: "POST",
            data: {id:id}
        },true,true);

        request.done(function(result){
            if(result){
                var solve = getJson(result);
                if(solve){
                    if(solve.status == "successful"){

                        item.animate({backgroundColor:'#EEE',opacity:0}, 500,function () {
                            item.remove();
                            if($(".sepetlist").length==0){

                                $("#item_list").fadeOut(400).html('');
                                $("#empty_list").fadeIn(400);
                                $(".basket-count").html('0');
                                $("#coupon_code").attr("disabled",true);
                            }
                        });

                        OrderSummary();
                    }else if(solve.status == "error"){
                        swal('<?php echo __("website/basket/modal-error"); ?>',solve.message,'error');
                    }
                }else console.log("Result cannot resolved.");
            }else console.log("Basket item not deleted.");
        });
    }

    function OrderSummary() {

        $("#OrderSummary_loader").css("display","block");
        $("#OrderSummaryContent").css("display","none");

        var request = MioAjax({
            action: "<?php echo $links["bring"]."order-summary"; ?>",
            method: "POST"
        },true,true);

        request.done(function (result) {

            $("#OrderSummary_loader").fadeOut(500,function(){
                $("#OrderSummaryContent").fadeIn();
            });

            var solve = false,content = '';
            if(result){
                solve = getJson(result);
                if(solve){

                    if(solve.total_amount != undefined){
                        var amount_info = amount_divider(solve.total_amount);
                        $("#total-amount").html('<div class="amount_spot_view"><i class="currpos'+amount_info.symbol_pos+'">'+amount_info.symbol+'</i> '+amount_info.amount+'</div>');
                    }
                    else $("#total-amount").html('-');

                    if(solve.dealership_discounts != undefined && solve.dealership_discounts.length){
                        $("#dealership_discounts").html('').fadeIn(1);
                        var d_content = '';
                        var d_see = '<?php echo __("website/basket/dealership-discount", ['']); ?>';
                        var d_seee = '';
                        $(solve.dealership_discounts).each(function(dkey,ditem){
                            var amount_info = amount_divider(ditem.amount);
                            d_content  = '<tr>';
                            d_seee     = d_see.replace('{rate}',ditem.rate);
                            d_content += '<td><strong>'+d_seee+'</strong>'+(ditem.name !== null ? '<br>('+ditem.name+')' : '')+' </td>';
                            d_content += '<td align="right"><h5><div class="amount_spot_view"><i class="currpos'+amount_info.symbol_pos+'">'+amount_info.symbol+'</i> -'+amount_info.amount+'</div></h5></td>';
                            d_content += '</tr>';
                            $("#dealership_discounts").append(d_content);
                        });
                    }else $("#dealership_discounts").html('').fadeOut(1);

                    if(solve.coupon_discounts != undefined && solve.coupon_discounts.length){
                        $("#coupon_discounts").html('').fadeIn(1);
                        var d_content = '';
                        var d_see = '<?php echo __("website/basket/coupon-discount", ['']); ?>';
                        var d_seee = '';
                        $(solve.coupon_discounts).each(function(dkey,ditem){
                            d_content  = '<tr>';
                            d_seee     = d_see.replace('{value}',ditem.dvalue);
                            d_content += '<td><strong>'+d_seee+'</strong><br>('+ditem.name+') <a style="color:#777;margin-left:5px;" title="<?php echo __("website/basket/delete-coupon"); ?>" href="javascript:deleteCoupon('+ditem.id+');void 0;"><i class="fa fa-trash" aria-hidden="true"></i></a></td>';
                            var amount_info = amount_divider(ditem.amount);
                            d_content += '<td align="right"><h5><div class="amount_spot_view"><i class="currpos'+amount_info.symbol_pos+'">'+amount_info.symbol+'</i> -'+amount_info.amount+'</div></h5></td>';
                            d_content += '</tr>';
                            $("#coupon_discounts").append(d_content);
                        });
                    }else $("#coupon_discounts").html('').fadeOut(1);


                    if(solve.taxation != undefined && solve.taxation){
                        $("#tax_content").fadeIn(1);
                        var see,see_text;
                        see     = $("#tax-see");
                        see_text = see.html();
                        see_text = see_text.replace('{rates}',solve.tax_rates ?? '');
                        see_text = see_text.replace('{rate}',solve.tax_rate);
                        see.html(see_text);
                        if(solve.total_tax_amount != undefined){
                            var amount_info = amount_divider(solve.total_tax_amount);
                            $("#tax-amount").html('<div class="amount_spot_view"><i class="currpos'+amount_info.symbol_pos+'">'+amount_info.symbol+'</i> '+amount_info.amount+'</div>');
                        }else $("#tax-amount").html('-');
                    }else $("#tax_content").fadeOut(1);

                    if(solve.total_amount_payable != undefined){
                        $("#continue_block").fadeOut(100,function(){
                            $("#continue_go").fadeIn(100);
                        });
                        var amount_info = amount_divider(solve.total_amount_payable);
                        $("#total-amount-payable").html('<div class="amount_spot_view"><i class="currpos'+amount_info.symbol_pos+'">'+amount_info.symbol+'</i> '+amount_info.amount+'</div>');
                    }else{
                        $("#continue_go").fadeOut(100,function(){
                            $("#continue_block").fadeIn(100);
                        });
                        $("#total-amount-payable").html('-');
                    }

                    if(solve.use_coupon != undefined){
                        if(solve.use_coupon) $("#use_coupon").fadeIn(1);
                        else $("#use_coupon").fadeOut(1);
                    }else $("#use_coupon").fadeOut(1);


                }else console.log("Can not resolved : "+result);
            }else console.log("Failed loaded in basket to order summary");
        });
    }

    function coupon_check(value){
        if(value != '' && value.length>=3){
            var request = MioAjax({
                action: "<?php echo $links["bring"]."coupon-check"; ?>",
                method: "POST",
                data:{code:value}
            },true,true);

            request.done(function (result) {
                if(result){
                    var solve = getJson(result);
                    if(solve){

                        if(solve.status == "error"){

                            $("#coupon_result").html(solve.message).fadeIn(200);

                        }else if(solve.status == "successful"){

                            $("#coupon_result").html('').fadeOut(1);
                            $("#kuponkodu").slideUp(400,function(){
                                $("#coupon_code").val('');
                                OrderSummary();
                            });
                        }else{
                            $("#coupon_result").html('').fadeOut(1);
                        }
                    }else{
                        $("#coupon_result").html('').fadeOut(1);
                        console.log(result);
                    }
                }else{
                    $("#coupon_result").html('').fadeOut(1);
                    console.log("Coupon check result is empty");
                }
            });
        }
    }

    function deleteCoupon(id){
        if(id != 0 && id != null){

            var request = MioAjax({
                action: "<?php echo $links["bring"]."delete-coupon"; ?>",
                method: "POST",
                data:{coupon_id:id}
            },true,true);

            request.done(function (result) {
                if(result){
                    var solve = getJson(result);
                    if(solve){
                        if(solve.status == "successful"){
                            OrderSummary();
                        }
                    }else console.log(result);
                }else console.log("Coupon check result is empty");
            });
        }
    }

    function open_ns_info(id)
    {

        var _title = '<?php echo __("website/basket/dns-tx1"); ?>';

        var ns_info = ns_details[id];

        open_modal('cart-ns-details',{
            width:'800px',
            title: _title.replace("{domain}",domain_names[id]),
        });

        $("#cart-ns-details input[name=item_id]").val(id);


        for(var i = 1; i <= 4; i++)
        {
            if(ns_info["ns"+i] !== undefined)
            {
                var x = i-1;
                $("#cart-ns-details input[name='dns[]']").eq(x).val(ns_info["ns"+i]);
            }
        }

    }

    function open_whois_info(id)
    {

        var _title = '<?php echo __("website/basket/whois-tx1"); ?>';

        $("#cart-whois-details input[name=item_id]").val(id);

        $("#cart-whois-details .iziModal-header-title").html(_title.replace("{domain}",domain_names[id]));
        $("#cart-whois-details").iziModal('open');

        var info            = whois_details[id];
        var contact_types   = Object.keys(info);

        $(contact_types).each(function(k,contact_type){
            var whois_info_keys = Object.keys(info[contact_type]);
            $(whois_info_keys).each(function(k,w_name){
                var w_value = info[contact_type][w_name];

                if(w_name === "profile_id")
                {
                    if(parseInt(w_value) > 0)
                    {
                        $("select[name='profile_id["+contact_type+"]']").val(w_value);
                    }
                    console.log(w_value);
                }
                else $(".whois-"+contact_type+"-"+w_name).val(w_value);
            });
        });


    }

    $(document).ready(function(){
        var tab_ct;
        ItemList();
        OrderSummary();

        $("#cart-whois-details").iziModal({
            title: '???',
            width:800,
            restoreDefaultContent:false,
            transitionIn: 'fadeInDown',
            transitionOut: 'fadeOutDown',
            bodyOverflow: true,
            history:false,
            appendTo:false
        });

        $("#coupon_code").keyup(function(e){
            var ithis = this;
            var isBackspaceOrDelete = (e.keyCode == 8 || e.keyCode == 9 || e.keyCode == 46 || e.keyCode == 32);
            var check = isBackspaceOrDelete || (e.keyCode>=33 && e.keyCode < 254);
            var inputValue = $(ithis).val();
            if(inputValue.length<3) $("#coupon_result").html('').fadeOut(1);
            if(check && inputValue.length>=3){
                var ithis = this;
                var ie    = e;
                setTimeout(function(){
                    coupon_check(inputValue);
                },600);
            }
        });

        $("#coupon_code").bind("paste", function(e){
            var pastedData = e.originalEvent.clipboardData.getData('text');
            coupon_check(pastedData);
        });

        $("#cart-ns-details").on("click","#DomainDnsChangeForm_submit",function(){
            MioAjaxElement($(this),{
                waiting_text: "<?php echo addslashes(__("website/others/button1-pending")); ?>",
                result: "DomainDnsChangeForm_handler"
            });
        });

        $("#cart-whois-details").on("click","#DomainWhoisChangeForm_submit",function(){
            MioAjaxElement($(this),{
                waiting_text: "<?php echo addslashes(__("website/others/button1-pending")); ?>",
                result: "DomainWhoisChangeForm_handler"
            });
        });

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

    function DomainDnsChangeForm_handler(result)
    {
        if(result !== ''){
            var solve = getJson(result);
            if(solve !== false){
                if(solve.status === "error")
                    alert_error(solve.message,{timer:3000});
                else if(solve.status === "successful")
                {
                    alert_success(solve.message,{timer:2000});
                    ItemList();
                    OrderSummary();
                }
            }else
                console.log(result);
        }
    }

    function DomainWhoisChangeForm_handler(result)
    {
        if(result !== ''){
            var solve = getJson(result);
            if(solve !== false){
                if(solve.status === "error")
                    alert_error(solve.message,{timer:3000});
                else if(solve.status === "successful")
                {
                    $("#cart-whois-details").iziModal('close');

                    alert_success(solve.message,{timer:2000});
                    ItemList();
                }
            }else
                console.log(result);
        }
    }
</script>
<div id="wrapper">

    <div class="sepet">

        <div class="sepetleft">

            <div class="sepetbaslik">
                <div style="padding:0px 15px;">
                    <div class="uhinfo"><?php echo __("website/basket/name2"); ?></div>
                    <div class="uhperiyod"><?php echo __("website/basket/period"); ?></div>
                    <div class="uhtutar"><?php echo __("website/basket/amount"); ?></div>
                </div>
            </div>

            <div class="clear"></div>
            <div class="info" id="empty_list">
                <i class="fa fa-info-circle" aria-hidden="true"></i>
                <h4><?php echo __("website/basket/empty-list"); ?></h4>
            </div>
            <div id="basket_loader" style="margin-top: 7%;    margin-bottom: 40px; text-align: center;">
                <div class="spinner"></div>
            </div>
            <div class="clear"></div>
            <div id="item_list" style="display: none;"></div>

            <div align="center"><a class="lbtn gonderbtn" id="continueshopbtn"  href="<?php echo $home_link ?? ''; ?>"><i class="fa fa-angle-double-left" aria-hidden="true"></i> <?php echo __("website/basket/continue-to-shopping"); ?></a></div>

            <div class="paymentlogos">
                <img class="plogos1" src="<?php echo $tadress ?? ''; ?>images/credit-cards.png">
                <img class="plogos2" src="<?php echo $tadress ?? '';?>images/ssl-secure.svg">
                <div class="clear"></div>
                <span><?php echo __("website/basket/basketsecnotice"); ?></span>
            </div>


        </div>

        <div class="sepetright">
            <div class="sepetrightshadow">
                <div class="sepetbaslik">
                    <div style="padding:0px 12px;text-align:right;">
                        <?php echo __("website/basket/order-summary"); ?>
                    </div>
                </div>

                <div class="sepetrightcon" id="OrderSummaryContent" style="display: none;">

                    <table class="sepetsipinfo" width="100%" border="0">
                        <tr>
                            <td><strong><?php echo __("website/basket/total-order-amount"); ?></strong></td>
                            <td align="right"><h5 id="total-amount">0</h5></td>
                        </tr>

                        <tbody id="dealership_discounts"></tbody>

                        <tbody id="coupon_discounts" style="display: none;"></tbody>

                        <tr id="tax_content" style="display:none;">
                            <td><strong id="tax-see"><?php echo __("website/basket/tax-amount",['']); ?></strong></td>
                            <td align="right"><h5 id="tax-amount">0</h5></td>
                        </tr>

                        <tr id="use_coupon" style="display: none;">
                            <td colspan="2" align="center">
                                <a href="javascript:$('#kuponkodu').slideToggle();void 0"><i class="fa fa-ticket" aria-hidden="true"></i> <?php echo __("website/basket/use-coupon-code"); ?></a>
                                <div class="kuponkodu" id="kuponkodu" style="display: none; transition-property: all; transition-duration: 0s; transition-timing-function: ease; opacity: 1;">
                                    <input id="coupon_code" name="coupon_code" type="text" placeholder="<?php echo __("website/basket/coupon-code-pholder"); ?>" onchange="coupon_check($(this).val());">
                                    <div style="text-align: center; margin-top: 5px; display: none;" class="error" id="coupon_result"></div>
                                </div>
                            </td>
                        </tr>

                        <tr>
                            <td class="totalamountinfo" align="center" colspan="2">
                                <strong><?php echo __("website/basket/total-amount-payable"); ?></strong><br>
                                <h5 id="total-amount-payable">0</h5>
                            </td>
                        </tr>

                    </table>



                    <div class="clear"></div>
                </div>

                <div class="clear"></div>

                <div id="OrderSummary_loader">
                    <div class="spinner"></div>
                    <div class="clear"></div>
                    <br>
                </div>
                <div class="clear"></div>


            </div>
            <a href="<?php echo $links["payment"]; ?>" style="display: none;" class="gonderbtn" id="continue_go"><?php echo __("website/basket/continue-button"); ?></a>
            <a class="graybtn gonderbtn" id="continue_block" style="background: #CCCCCC; cursor: no-drop;"><?php echo __("website/basket/continue-button"); ?></a>
        </div>

    </div>
</div>

<div id="cart-ns-details" style="display: none">
    <form action="<?php echo $links["bring"]; ?>change-domain-ns" method="post" id="DomainDnsChangeForm">
        <input type="hidden" name="item_id" value="">

        <div class="padding20">
            <div class="red-info">
                <div class="padding15">
                    <i class="fas fa-exclamation-circle"></i>
                    <p><?php echo __("website/basket/dns-tx2"); ?></p>
                </div>
            </div>

            <div style="width:100%;text-align:center;">
                <div class="clear"></div>
                <?php
                    for($i=1;$i<=4;$i++)
                    {
                        ?>
                        <input name="dns[]" value="" type="text" class="" placeholder="ns<?php echo $i; ?>.example.com">
                        <div class="clear"></div>
                        <?php
                    }
                ?>
            </div>
        </div>
        <div class="modal-foot-btn">
            <a href="javascript:void(0);" id="DomainDnsChangeForm_submit" class="green lbtn"><?php echo ___("needs/button-update"); ?></a>
        </div>
    </form>
</div>

<div id="cart-whois-details" style="display: none">

    <form action="<?php echo $links["bring"]; ?>change-domain-whois" method="post" id="DomainWhoisChangeForm">
        <input type="hidden" name="item_id" value="">


        <div class="padding20">
            <div class="blue-info">
                <div class="padding15">
                    <i class="fa fa-info-circle" aria-hidden="true"></i>
                    <p><?php echo __("website/basket/whois-tx2"); ?></p>
                </div>
            </div>


            <div id="tab-contact-type">
                <ul class="tab">
                    <?php
                        if(isset($contact_types) && $contact_types)
                        {
                            foreach($contact_types AS $k => $v)
                            {
                                ?>
                                <li><a href="javascript:void 0;" class="tablinks<?php echo $k == "registrant" ? ' active' : ''; ?>" onclick="open_tab(this,'<?php echo $k; ?>','contact-type');" data-tab="<?php echo $k; ?>"><?php echo $v; ?></a></li>
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
                            <div id="contact-type-<?php echo $k; ?>" class="tabcontent" style="<?php echo $k == "registrant" ? 'display:block;' : ''; ?>">

                                <div class="formcon">
                                    <div class="yuzde30"><?php echo __("website/account_products/domain-whois-tx18"); ?></div>
                                    <div class="yuzde70">

                                        <?php if(isset($udata) && $udata): ?>
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
                                        <?php endif; ?>

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
                                <input name="info[<?php echo $k; ?>][Address]" type="text" value="<?php echo $whois[$k]["Address"] ?? ''; ?>" class="yuzde33 whois-<?php echo $k; ?>-Address" placeholder="<?php echo __("website/account_products/whois-address"); ?>">
                                <input name="info[<?php echo $k; ?>][Country]" type="text" value="<?php echo $whois[$k]["Country"] ?? ''; ?>" class="yuzde33 whois-<?php echo $k; ?>-Country" placeholder="<?php echo __("website/account_products/whois-CountryCode"); ?>">
                                <input name="info[<?php echo $k; ?>][ZipCode]" type="text" value="<?php echo $whois[$k]["ZipCode"] ?? ''; ?>" class="yuzde33 whois-<?php echo $k; ?>-ZipCode" placeholder="<?php echo __("website/account_products/whois-zipcode"); ?>">
                            </div>
                            <?php
                        }
                    }
                ?>

            </div>


        </div>
        <div class="modal-foot-btn">
            <a href="javascript:void(0);" id="DomainWhoisChangeForm_submit" class="green lbtn"><?php echo ___("needs/button-update"); ?></a>
        </div>

    </form>
</div>