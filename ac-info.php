<?php defined('CORE_FOLDER') OR exit('You can not get in here!');

    $wide_content = true;

    $inc        = Filter::GET("inc");

    if($inc){

        if($inc == "address-list"){
            if(isset($acAddresses) && $acAddresses){
                foreach($acAddresses AS $addr){
                    ?>
                    <div class="adresbilgisi" id="address_<?php echo $addr["id"];?>">
                        <input type="hidden" name="full_name" value="<?php echo $addr["full_name"]; ?>">
                        <input type="hidden" name="kind" value="<?php echo $addr["kind"]; ?>">
                        <input type="hidden" name="company_name" value="<?php echo $addr["company_name"]; ?>">
                        <input type="hidden" name="company_tax_office" value="<?php echo $addr["company_tax_office"]; ?>">
                        <input type="hidden" name="company_tax_number" value="<?php echo $addr["company_tax_number"]; ?>">
                        <input type="hidden" name="email" value="<?php echo $addr["email"]; ?>">
                        <input type="hidden" name="phone" value="<?php echo $addr["phone"]; ?>">
                        <input type="hidden" name="identity" value="<?php echo $addr["identity"]; ?>">
                        <input type="hidden" name="country_id" value="<?php echo $addr["country_id"]; ?>">
                        <input type="hidden" name="detouse" value="<?php echo $addr["detouse"] ? 1 : 0; ?>">
                        <input type="hidden" name="zipcode" value="<?php echo $addr["zipcode"]; ?>">
                        <input type="hidden" name="city" value="<?php echo $addr["city"]; ?>">
                        <input type="hidden" name="counti" value="<?php echo $addr["counti"]; ?>">
                        <input type="hidden" name="address" value="<?php echo htmlentities($addr["address"],ENT_QUOTES); ?>">
                        <div class="yuzde80">
                            <strong><?php echo $addr["name"]; ?></strong><br>
                            <?php echo $addr["address_line"]; ?>
                            <?php if($addr["detouse"]): ?>
                                <strong>(<?php echo __("website/account_info/default-address"); ?>)</strong>
                            <?php endif; ?>
                        </div>
                        <div class="yuzde20" style="float:right;text-align:right;">
                            <a href="javascript:editAddress(<?php echo $addr["id"]; ?>);void 0;" style="margin-left:5px;" title="<?php echo ___("needs/button-edit") ?>" class="sbtn"><i class="fa fa-pencil" aria-hidden="true"></i></a>
                            <a href="javascript:deleteAddress(<?php echo $addr["id"]; ?>);void 0;" style="margin-left:5px;" title="<?php echo ___("needs/button-delete") ?>" class="red sbtn"><i class="fa fa-trash" aria-hidden="true"></i></a>
                        </div>
                    </div>
                    <?php
                }
            }
        }

        die();
    }

    $email_disabled = !$editable["email"];
    if($email_verify_status){
        if(!$udata["verified-email"]) $email_disabled = false;
        if(!$editable["email"] && User::ActionCount($udata["id"],"alteration","changed-email-address")) $email_disabled = true;
    }
    $gsm_disabled   = !$editable["gsm"];
    if($gsm_disabled && $udata["gsm"] == '') $gsm_disabled = false;
    if($gsm_verify_status){
        if(!$udata["verified-gsm"]) $gsm_disabled = false;
        if(!$editable["gsm"] && User::ActionCount($udata["id"],"alteration","changed-gsm-number")) $gsm_disabled = true;
    }

    $hoptions = ["jquery-ui",'jquery-mask','voucher_codes'];

?>
<link rel="stylesheet" href="<?php echo $sadress;?>assets/plugins/phone-cc/new/css/intlTelInput.css">
<script src="<?php echo $sadress;?>assets/plugins/phone-cc/new/js/intlTelInput.js"></script>
<script type="text/javascript">
    var default_country,city_request = false,counti_request=false;
    var tel1;
    var tel2;
    var telInput;
    var telInput2;
    var countryCode;
    $(document).ready(function(){

        $("#password_primary,#password_again").bind('paste keypress keyup keydown change',function(){
            var pw1 = $("#password_primary").val();
            var pw2 = $("#password_again").val();

            var level = checkStrength(pw1);

            if(pw1 !== pw2) level = 'weak';

            $('.level-block').css("display","none");
            $("#"+level).css("display","block");
        });

        telInput = document.getElementById("gsm");
        telInput2 = document.getElementById("gsm2");

        countryCode         = '<?php if($ipInfo = UserManager::ip_info()) echo $ipInfo["countryCode"]; else echo 'us'; ?>';
        default_country     = countryCode;

        var object_code = {
            geoIpLookup: function(callback) {
                callback(countryCode);
            },
            autoPlaceholder: "on",
            formatOnDisplay: true,
            initialCountry: "auto",
            nationalMode: false,
            placeholderNumberType: "MOBILE",
            preferredCountries: ['us', 'gb', 'ch', 'ca', 'de', 'it'],
            separateDialCode: true,
            utilsScript: "<?php echo $sadress;?>assets/plugins/phone-cc/new/js/utils.js"
        };

        tel1    = intlTelInput(telInput,object_code);
        tel2    = intlTelInput(telInput2,object_code);

        $("#gsm").change(function(){
            $("#ModifyAccountInfo input[name=gsm]").val(tel1.getNumber());
        });

        telInput.addEventListener("countrychange", function() {
            $("#ModifyAccountInfo input[name=gsm]").val(tel1.getNumber());
        });


        $("#gsm2").change(function(){
            $("#manageAddressForm input[name=gsm]").val(tel2.getNumber());
        });

        telInput2.addEventListener("countrychange", function() {
            $("#manageAddressForm input[name=gsm]").val(tel2.getNumber());
        });

        $("#manageAddressForm input[name='kind']").change(function(){
            var value       = $("#manageAddressForm input[name='kind']:checked").val();

            if(value === "corporate")
            {
                $("#manageAddressForm .corporate-info").css("display","block");
                $("#manageAddressForm .individual-info").css("display","none");
            }
            else
            {
                $("#manageAddressForm .individual-info").css("display","block");
                $("#manageAddressForm .corporate-info").css("display","none");
            }

        });

        $("input[name='kind']").trigger("change");



        let tab = gGET("tab");

        if(tab !== "null" && tab !== 'NULL' && tab !== null)
        {
            if(tab === "1")
                tab = 'general';
            else if(tab === "2")
                tab = 'billing';
            else if(tab === "3")
                tab = 'preferences';
            else if(tab === "4")
                tab = 'password';
            else if(tab === "5")
                tab = 'verification';
        }

        if(tab !== '' && tab !== undefined && typeof tab !== 'object' && tab !== "null" && tab !== 'NULL' && tab !== null)
            $('.tablinks[data-k='+tab+']').click();
        else
            $(".tablinks:eq(0)").click();


        var accordionx = gGET("accordion");
        if(accordionx == null){
            accordionx = 0;
        }else
            accordionx -=1;

        $( "#accordion" ).accordion({
            heightStyle: "content",
            active:accordionx,
            collapsible: false,
        });

        $("#ModifyAccountInfo input[name='kind']").change(function(){
            var value       = $("#ModifyAccountInfo input[name='kind']:checked").val();
            var cfieldsz    = $("#ModifyAccountInfo .custom-field--content").length;

            if(value == "corporate"){
                $("#ModifyAccountInfo .corporate-info").css("display","block");
                $("#ModifyAccountInfo .digerbilgiler").css("display","block");
                $("#ModifyAccountInfo .kisiselbilgiler").removeAttr("style");
            }else{
                $("#ModifyAccountInfo .corporate-info").fadeOut(1);
                if(cfieldsz==0){
                    $("#ModifyAccountInfo .digerbilgiler").css("display","none");
                    $("#ModifyAccountInfo .kisiselbilgiler").attr("style","width:100%;");
                }
            }

        });




        <?php
        if(isset($requiredFields) && $requiredFields){

        }
        elseif($remainingVerifications["force"] > 0){
        ?>$('.tablinks[data-k=verification]').click();<?php
        }
        ?>

    });

    function getCities(country,call_request){

        $("#manageAddressForm select[name=city]").html('').css("display","none").attr("disabled",true);
        $("#manageAddressForm input[name=city]").val('').css("display","block").attr("disabled",false);

        $("#manageAddressForm select[name=counti]").html('').css("display","none").attr("disabled",true);
        $("#manageAddressForm input[name=counti]").val('').css("display","block").attr("disabled",false);

        if(call_request) city_request = false;

        var request = MioAjax({
            action:"<?php echo $operation_link; ?>",
            method:"POST",
            data:{operation:"getCities",country:country},
        },true,true);

        request.done(function(result){
            if(call_request) city_request = "done";

            if(result || result !== undefined){
                if(result != ''){
                    var solve = getJson(result);
                    if(solve !== false){
                        if(solve.status == "successful"){
                            $("#manageAddressForm select[name=city]").html('');
                            $("#manageAddressForm select[name='city']").append($('<option>', {
                                value: '',
                                text: "<?php echo ___("needs/select-your"); ?>",
                            }));
                            $(solve.data).each(function (index,elem) {
                                $("#manageAddressForm select[name='city']").append($('<option>', {
                                    value: elem.id,
                                    text: elem.name
                                }));
                                if(index === 0) def_val = elem.id;
                            });
                            $("#manageAddressForm select[name='city']").css("display","block").attr("disabled",false);
                            $("#manageAddressForm input[name='city']").css("display","none").attr("disabled",true);
                        }else{
                            $("#manageAddressForm select[name='city']").css("display","none").attr("disabled",true);
                            $("#manageAddressForm input[name='city']").css("display","block").attr("disabled",false);
                            //$("#manageAddressForm input[name='city']").focus();
                        }
                    }else
                        console.log(result);
                }
            }
        });
    }
    function getCounties(city,call_request){

        if(call_request) counti_request = false;

        if(city !== ''){
            var request = MioAjax({
                action:"<?php echo $operation_link; ?>",
                method:"POST",
                data:{operation:"getCounties",city:city},
            },true,true);

            request.done(function(result){
                if(call_request) counti_request = "done";
                if(result || result != undefined){
                    if(result != ''){
                        var solve = getJson(result);
                        if(solve !== false){
                            if(solve.status == "successful"){
                                $("#manageAddressForm select[name=counti]").html('');
                                $("#manageAddressForm select[name='counti']").append($('<option>', {
                                    value: '',
                                    text: "<?php echo ___("needs/select-your"); ?>",
                                }));
                                $(solve.data).each(function (index,elem) {
                                    $("#manageAddressForm select[name='counti']").append($('<option>', {
                                        value: elem.id,
                                        text: elem.name
                                    }));
                                });
                                $("#manageAddressForm select[name=counti]").css("display","block").attr("disabled",false);
                                $("#manageAddressForm input[name=counti]").val('').css("display","none").attr("disabled",true);
                            }else{
                                $("#manageAddressForm select[name=counti]").css("display","none").attr("disabled",true);
                                $("#manageAddressForm input[name=counti]").val('').css("display","block").attr("disabled",false);
                                //$("#manageAddressForm input[name='counti']").focus();
                            }
                        }else
                            console.log(result);
                    }
                }
            });
        }
        else{
            $("#manageAddressForm select[name=counti]").html('').css("display","none").attr("disabled",true);
            $("#manageAddressForm input[name=counti]").val('').css("display","block").attr("disabled",false);
            if(call_request) counti_request = "done";
        }
    }
    function manageAddressForm_handler(result){
        if(result != ''){
            var solve = getJson(result);
            if(solve !== false){
                if(solve.status == "error"){
                    if(solve.for != undefined && solve.for != ''){
                        $("#manageAddressForm "+solve.for).focus();
                        $("#manageAddressForm "+solve.for).attr("style","border-bottom:2px solid red; color:red;");
                        $("#manageAddressForm "+solve.for).change(function(){
                            $(this).removeAttr("style");
                        });
                    }
                    if(solve.message != undefined && solve.message != '')
                        alert_error(solve.message,{timer:5000});
                }else if(solve.status == "successful"){
                    alert_success(solve.message,{timer:2000});

                    turn_back();

                    $.get( "<?php echo $operation_link."?inc=address-list"; ?>", function( data ) {
                        $( "#AddressList" ).html( data );
                        $("#empty_content").css("display","none");
                    });
                }
            }else
                console.log(result);
        }
    }
    function addAddress(){
        $("html, body").animate({ scrollTop: - ($('#accordion2').offset().top + 20)  },200);
        $("#address_list_wrap,#defaultAddress_wrap").fadeOut(100,function(){
            $("#manageAddress").fadeIn(100);
        });

        $('#manageAddress #manage_title_1').css('display','block');
        $('#manageAddress #manage_title_2').css('display','none');

        $("#manageAddressForm input[name=operation]").val('addNewAddress');
        $("#manageAddressForm input[name=id]").val('0');



        $("#manageAddressForm input[name=kind]").prop('checked',false);
        $("#xkind_1").prop('checked',true).trigger('change');

        $("#manageAddressForm input[name=full_name]").val('');
        $("#manageAddressForm input[name=company_name]").val('');
        $("#manageAddressForm input[name=company_tax_number]").val('');
        $("#manageAddressForm input[name=company_tax_office]").val('');
        $("#manageAddressForm input[name=email]").val('');
        tel2.setNumber('');
        $('#manageAddressForm input[name=gsm]').val('');

        $("#manageAddressForm input[name=identity]").val('');

        $("#manageAddressForm select[name=country]").val('');

        $("#manageAddressForm select[name=city]").html('').css("display","none").attr("disabled",true);
        $("#manageAddressForm input[name=city]").val('').css("display","block").attr("disabled",false);

        $("#manageAddressForm select[name=counti]").html('').css("display","none").attr("disabled",true);
        $("#manageAddressForm input[name=counti]").val('').css("display","block").attr("disabled",false);

        $("#manageAddressForm_submit").html('+ <?php echo htmlspecialchars(__("website/account_info/add-address-submit-button"),ENT_QUOTES); ?>');

        $('select[name=country] option[data-code='+(default_country.toUpperCase())+']').prop("selected",true).trigger('change');

    }
    function editAddress(id){

        $("html, body").animate({ scrollTop: - ($('#accordion2').offset().top + 20)  },200);

        $("#address_list_wrap,#defaultAddress_wrap").fadeOut(100,function(){
            $("#manageAddress").fadeIn(100);
        });

        $('#manageAddress #manage_title_2').css('display','block');
        $('#manageAddress #manage_title_1').css('display','none');


        $("#manageAddressForm input[name=operation]").val('editAddress');
        $("#manageAddressForm input[name=id]").val(id);

        $("#manageAddressForm_submit").html('<?php echo htmlspecialchars(__("website/account_info/edit-address-submit-button"),ENT_QUOTES); ?>');

        var kind            = $("#address_"+id+" input[name=kind]").val();
        var full_name       = $("#address_"+id+" input[name=full_name]").val();
        var comp_name       = $("#address_"+id+" input[name=company_name]").val();
        var comp_tax_off    = $("#address_"+id+" input[name=company_tax_office]").val();
        var comp_tax_num    = $("#address_"+id+" input[name=company_tax_number]").val();
        var email           = $("#address_"+id+" input[name=email]").val();
        var phone           = $("#address_"+id+" input[name=phone]").val();
        var identity        = $("#address_"+id+" input[name=identity]").val();
        var city            = $("#address_"+id+" input[name=city]").val();
        var counti          = $("#address_"+id+" input[name=counti]").val();
        var zipcode         = $("#address_"+id+" input[name=zipcode]").val();
        var address         = $("#address_"+id+" input[name=address]").val();
        var detouse         = $("#address_"+id+" input[name=detouse]").val();
        var country_id      = $("#address_"+id+" input[name=country_id]").val();


        $("#manageAddressForm input[name=kind]").prop('checked',false);
        $("#manageAddressForm input[value="+kind+"]").prop('checked',true).trigger('change');

        $("#manageAddressForm input[name=full_name]").val(full_name);
        $("#manageAddressForm input[name=company_name]").val(comp_name);
        $("#manageAddressForm input[name=company_tax_number]").val(comp_tax_num);
        $("#manageAddressForm input[name=company_tax_office]").val(comp_tax_off);
        $("#manageAddressForm input[name=email]").val(email);
        if(phone.length > 1) tel2.setNumber("+"+phone);
        $('#manageAddressForm input[name=gsm]').val(phone);
        $("#manageAddressForm input[name=identity]").val(identity);


        $("#manageAddressForm input[name=zipcode]").val(zipcode);
        $("#manageAddressForm input[name=address]").val(address);
        $("#manageAddressForm input[name=detouse]").prop("checked",detouse == 1);

        $("#manageAddressForm select[name=country]").val(country_id);

        getCities(country_id,true);

        var cityInterval = setInterval(function(){
            if(city_request === "done"){
                $("#manageAddressForm *[name=city]").val(city);
                clearInterval(cityInterval);
                city_request = false;

                getCounties(city,true);

                var countiInterval = setInterval(function(){
                    if(counti_request === "done"){
                        $("#manageAddressForm *[name=counti]").val(counti);
                        clearInterval(countiInterval);
                        counti_request = false;
                    }
                },100);

            }
        },100);
    }
    function openTab(evt, tabName)
    {
        let k,link,tab;
        $(".tabcontent").css("display","none");
        $(".tablinks").removeClass("active");
        $("#"+tabName).css("display","block");
        $(evt).addClass("active");
        k     = $(evt).attr("data-k");
        link     = window.location.href;
        tab      = gGET("tab");
        if(tab || (!tab && k.length > 1)){
            link = sGET("tab",k);
        }
        window.history.pushState("object or string", $("title").html(),link);
    }

    function turn_back()
    {
        $("html, body").animate({ scrollTop: - ($('#accordion2').offset().top + 20)  },200);
        $("#manageAddress").fadeOut(100,function(){
            $("#address_list_wrap,#defaultAddress_wrap").fadeIn(100);
        });
    }
</script>


<div id="contract1_modal" style="display: none;" data-izimodal-title="<?php echo htmlentities(__("website/sign/contract1-title"),ENT_QUOTES); ?>">
    <div class="padding20">
        <?php echo ___("constants/contract1"); ?>
    </div>
</div>

<div id="contract2_modal" style="display: none;" data-izimodal-title="<?php echo htmlentities(__("website/sign/contract2-title"),ENT_QUOTES); ?>">
    <div class="padding20">
        <?php echo ___("constants/contract2"); ?>
    </div>
</div>

<div class="mpanelrightcon">

    <div class="mpaneltitle">
        <div class="sayfayolu"><?php include __DIR__.DS."inc".DS."panel-breadcrumb.php"; ?></div>
        <h4><strong><i class="fa fa-user"></i> <?php echo __("website/account_info/page-title"); ?></strong></h4>
    </div>


    <ul class="tab">
        <li><a href="javascript:void(0)" class="tablinks" data-k="general" onclick="openTab(this, 'general')"><i class="fa fa-info" aria-hidden="true"></i> <?php echo __("website/account_info/info-tab1"); ?></a></li>
        <li><a href="javascript:void(0)" class="tablinks" data-k="billing" onclick="openTab(this, 'billing')"><i class="fa fa-file-text-o" aria-hidden="true"></i> <?php echo __("website/account_info/info-tab2"); ?></a></li>
        <li><a href="javascript:void(0)" class="tablinks" data-k="preferences" onclick="openTab(this, 'preferences')"><i class="fa fa-star-o" aria-hidden="true"></i> <?php echo __("website/account_info/info-tab3"); ?></a></li>
        <?php
            if($c_s_m && $c_s_m != "none")
            {
                ?>
                <li><a href="javascript:void(0)" class="tablinks" data-k="csm" onclick="openTab(this, 'csm')"><i class="fa fa-credit-card-alt" aria-hidden="true"></i> <?php echo __("website/account_info/info-tab-c-s-m"); ?></a></li>

                <?php
            }
        ?>
        <li><a href="javascript:void(0)" class="tablinks" data-k="password" onclick="openTab(this, 'password')"><i class="fa fa-key" aria-hidden="true"></i> <?php echo __("website/account_info/info-tab4"); ?></a></li>
        <li><a href="javascript:void(0)" class="tablinks" data-k="verification" onclick="openTab(this, 'verification')"><i class="fa fa-check" aria-hidden="true"></i> <?php echo __("website/account_info/info-tab5"); ?></a></li>
       <?php if(Config::get("options/gdpr-status")): ?>
           <li><a href="javascript:void(0)" class="tablinks" data-k="gdpr" onclick="openTab(this, 'gdpr')"><i class="fa fa-user-shield" aria-hidden="true"></i> <?php echo __("website/account_info/info-tab-gdpr"); ?></a></li>
       <?php endif; ?>
    </ul>


    <div id="general" class="tabcontent">
        <div class="tabcontentcon" style="margin-top:25px;">


            <?php
                if($requiredFields["general"] ?? [])
                {
                    ?>
                    <div class="red-info">
                        <div class="padding15">
                            <i class="fa fa-info-circle" aria-hidden="true"></i>
                            <h5><strong><?php echo __("website/account_info/required-field-title"); ?></strong></h5>
                            <p style="margin:10px 0px;"><?php echo __("website/account_info/required-field"); ?></p>
                            <?php
                                foreach($requiredFields["general"] AS $key => $fv)
                                {
                                    $message        = $fv["message"];
                                    $element_id     = $fv["element_id"];

                                    ?>
                                    <p><strong>- <?php echo $message; ?></strong></p>

                                <?php if($element_id): ?>
                                    <script type="text/javascript">
                                        $(document).ready(function(){
                                            $("<?php echo $element_id; ?>").attr("style","border-bottom:2px solid red; color:red;");
                                            $("<?php echo $element_id; ?>").closest("tr").attr("style","color:red;");
                                            $("<?php echo $element_id; ?>").change(function(){
                                                $(this).removeAttr("style");
                                                $(this).closest("tr").removeAttr("style");
                                            });
                                        });
                                    </script>
                                <?php endif; ?>
                                    <?php
                                }
                            ?>
                            <div class="clear"></div>
                        </div>

                    </div>
                    <?php
                }
            ?>

            <div class="hesapbilgilerim">


                <form id="ModifyAccountInfo" method="POST" action="<?php echo $operation_link; ?>">
                    <?php echo Validation::get_csrf_token('account'); ?>

                    <input type="hidden" name="operation" value="ModifyAccountInfo">


                    <div class="kisiselbilgiler">

                        <h4 class="hesapinfobloktitle"><?php echo __("website/account_info/personal-informations"); ?></h4>

                        <?php if($kind_status): ?>
                            <div class="hesapbilgisi">
                                <div class="yuzde25">
                                    <div class="hesapbilgititle"><?php echo __("website/sign/up-form-kind"); ?></div>
                                </div>
                                <div class="yuzde75">

                                    <?php if($editable["kind"] || $udata["kind"] == "individual"): ?>
                                        <input id="kind_1" class="radio-custom" name="kind" value="individual" type="radio" <?php echo ($udata["kind"] == "individual") ? ' checked' : NULL;?>>
                                        <label for="kind_1" class="radio-custom-label" style="margin-right: 28px;"><span class="checktext"><?php echo __("website/sign/up-form-kind-1"); ?></span></label>
                                    <?php endif; ?>

                                    <?php if($editable["kind"] || $udata["kind"] == "corporate"): ?>
                                        <input id="kind_2" class="radio-custom" name="kind" value="corporate" type="radio" <?php echo($udata["kind"] == "corporate") ? ' checked' : NULL; ?>>
                                        <label for="kind_2" class="radio-custom-label" style="margin-right: 28px;"><span class="checktext"><?php echo __("website/sign/up-form-kind-2"); ?></span></label>
                                    <?php endif; ?>

                                </div>
                            </div>
                        <?php endif; ?>

                        <div class="hesapbilgisi">
                            <div class="yuzde25"><div class="hesapbilgititle"><?php echo __("website/sign/up-form-full_name"); ?></div></div>
                            <div class="yuzde75">
                                <input<?php echo !$editable["full_name"] && $udata["full_name"] != '' ? ' disabled' : NULL; ?> name="full_name" class="accountinputs" type="text" value="<?php echo $udata["full_name"];?>">
                            </div>
                        </div>

                        <div class="hesapbilgisi">
                            <div class="yuzde25"><div class="hesapbilgititle"><?php echo __("website/sign/up-form-email"); ?> <?php if($udata["verified-email"]){ ?><a data-tooltip="<?php echo __("website/account_info/verified"); ?>"><i style="color:#8bc34a" class="fa fa-check-circle-o" aria-hidden="true"></i></a><?php } ?></div></div>
                            <div class="yuzde75">
                                <input<?php echo $email_disabled ? ' disabled' : NULL; ?> name="email" type="text" value="<?php echo $udata["email"];?>">
                            </div>
                        </div>

                        <?php if($gsm_status || $udata["gsm"] != ''): ?>
                            <div class="hesapbilgisi">
                                <div class="yuzde25"><div class="hesapbilgititle"><?php echo __("website/sign/up-form-gsm"); ?> <?php if($udata["verified-gsm"]){ ?><a data-tooltip="<?php echo __("website/account_info/verified"); ?>"><i style="color:#8bc34a" class="fa fa-check-circle-o" aria-hidden="true"></i></a><?php } ?> </div></div>
                                <div class="yuzde75">
                                    <input<?php echo $gsm_disabled ? ' disabled' : NULL; ?> id="gsm" type="text" style="width:100%;"  value="<?php echo (isset($udata["gsm"])) ? "+".$udata["gsm_cc"].$udata["gsm"] : NULL; ?>">
                                    <input type="hidden" name="gsm" value="<?php echo (isset($udata["gsm"])) ? "+".$udata["gsm_cc"].$udata["gsm"] : NULL; ?>">
                                </div>
                            </div>
                        <?php else: ?>
                            <div style="display: none;"><input id="gsm" type="text" style="width:100%;"  value=""></div>
                        <?php endif; ?>


                        <?php if($landlinep_status || $udata["landline_phone"]): ?>
                            <div class="hesapbilgisi">
                                <div class="yuzde25"><div class="hesapbilgititle"><?php echo __("website/sign/up-form-landline-phone"); ?></div></div>
                                <div class="yuzde75">
                                    <input<?php echo !$editable["landline_phone"] && $udata["landline_phone"] != '' ? ' disabled' : NULL; ?> name="landline_phone" id="clandline_phone" class="accountinputs" type="text" value="<?php echo (isset($udata["landline_phone"])) ? $udata["landline_phone"] : NULL; ?>">
                                </div>
                            </div>
                        <?php endif; ?>


                        <?php if($birthday_status || $udata["birthday"]): ?>
                            <div class="hesapbilgisi">
                                <div class="yuzde25">
                                    <div class="hesapbilgititle">
                                        <?php echo __("website/sign/up-form-birthday"); ?>
                                    </div>
                                </div>
                                <div class="yuzde75">
                                    <input<?php echo !$editable["birthday"] && $udata["birthday"] != '' ? ' disabled' : NULL; ?> type="date" name="birthday" class="accountinputs" id="birthday" value="<?php echo $udata["birthday"] == NULL ? NULL : $udata["birthday"]; ?>" placeholder="YYYY-MM-DD">
                                    <?php
                                        if($birthday_adult_verify){
                                            ?>
                                            <span class="kinfo"><?php echo __("website/account_info/birthday-adult-verify-note"); ?></span>
                                            <?php
                                        }
                                    ?>
                                </div>
                            </div>
                        <?php endif; ?>

                        <?php if(($udata["country"] == 227 || $udata["force_identity"]) && ($identity_status || $udata["identity"] != '')): ?>
                            <div class="hesapbilgisi">
                                <div class="yuzde25"><div class="hesapbilgititle"><?php echo __("website/sign/up-form-identity"); ?></div></div>
                                <div class="yuzde75">
                                    <input<?php echo !$editable["identity"] && $udata["identity"] != '' ? ' disabled' : NULL; ?> name="identity" class="accountinputs" id="identity"  maxlength="11" type="text" value="<?php echo (isset($udata["identity"])) ? $udata["identity"] : NULL; ?>" onkeypress='return event.charCode>= 48 &&event.charCode<= 57'>
                                </div>
                            </div>
                        <?php endif; ?>

                    </div>

                    <?php
                        $other_info = $udata["kind"] == "corporate";
                        if($cfields) $other_info = true;
                        if(Config::get("options/sign/security-question/status")) $other_info = true;
                    ?>
                    <div class="digerbilgiler"<?php echo !$other_info ? ' style="display:none;"' : ''; ?>>

                        <h4 class="hesapinfobloktitle"><?php echo __("website/account_info/other-informations"); ?></h4>

                        <?php if($kind_status): ?>
                            <div class="hesapbilgisi corporate-info" style="<?php echo $udata["kind"] != "corporate" ? 'display:none;' : ''; ?>">
                                <div class="yuzde25"><div class="hesapbilgititle"><?php echo __("website/sign/up-form-cname"); ?></div></div>
                                <div class="yuzde75">
                                    <input id="company_name" name="company_name" type="text" placeholder="<?php echo __("website/sign/up-form-cname"); ?>" value="<?php echo (isset($udata["company_name"])) ? $udata["company_name"] : NULL; ?>">
                                </div>
                            </div>

                            <?php if(Config::get("options/sign/up/kind/corporate/company_tax_number")): ?>
                                <div class="hesapbilgisi">
                                    <div class="yuzde25"><div class="hesapbilgititle"><?php echo __("website/sign/up-form-ctaxno"); ?></div></div>
                                    <div class="yuzde75">
                                        <input id="company_tax_number" name="company_tax_number" type="text" placeholder="<?php echo __("website/sign/up-form-ctaxno"); ?>" value="<?php echo (isset($udata["company_tax_number"])) ? $udata["company_tax_number"] : NULL; ?>">
                                    </div>
                                </div>
                            <?php endif; ?>

                            <?php if(Config::get("options/sign/up/kind/corporate/company_tax_office")): ?>
                                <div class="hesapbilgisi">
                                    <div class="yuzde25"><div class="hesapbilgititle"><?php echo __("website/sign/up-form-ctaxoff"); ?></div></div>
                                    <div class="yuzde75">
                                        <input id="company_tax_office" name="company_tax_office" type="text" placeholder="<?php echo __("website/sign/up-form-ctaxoff"); ?>" value="<?php echo (isset($udata["company_tax_office"])) ? $udata["company_tax_office"] : NULL; ?>">
                                    </div>
                                </div>
                            <?php endif; ?>
                        <?php endif; ?>
                        <?php
                            if($cfields){
                                foreach($cfields AS $field){
                                    $isDisable = $udata["field_".$field["id"]] != NULL && $field["uneditable"] ? true : false;
                                    ?>
                                    <div class="hesapbilgisi custom-field--content" id="cfield_<?php echo $field["id"]; ?>_wrap">
                                        <div class="yuzde25"><div class="hesapbilgititle"><?php echo $field["name"]; ?></div></div>
                                        <div class="yuzde75">

                                            <?php
                                                $disabled = $isDisable ? ' disabled' : NULL;
                                                $value = $udata["field_".$field["id"]];
                                                if($field["type"] == "text"){
                                                    ?>
                                                    <input<?php echo $disabled; ?> id="cfield_<?php echo $field["id"]; ?>" type="text" name="cfields[<?php echo $field["id"]; ?>]" value="<?php echo $value; ?>">
                                                    <?php
                                                }elseif($field["type"] == "textarea"){
                                                    ?>
                                                    <textarea<?php echo $disabled; ?> rows="3" id="cfield_<?php echo $field["id"]; ?>" name="cfields[<?php echo $field["id"]; ?>]"><?php echo $value; ?></textarea>
                                                    <?php
                                                }elseif($field["type"] == "select"){
                                                    ?>
                                                    <select<?php echo $disabled; ?> id="cfield_<?php echo $field["id"]; ?>" class="accountinputs" name="cfields[<?php echo $field["id"]; ?>]">
                                                        <option value=""><?php echo __("website/account_info/select-field-option"); ?></option>
                                                        <?php
                                                            $parse = explode(",",$field["options"]);
                                                            foreach($parse AS $p){
                                                                $selected = $value == $p ? " selected" : NULL;
                                                                ?>
                                                                <option<?php echo $selected; ?>><?php echo  $p; ?></option>
                                                                <?php
                                                            }
                                                        ?>
                                                    </select>
                                                    <?php
                                                }elseif($field["type"] == "radio"){
                                                    $parse = explode(",",$field["options"]);
                                                    foreach($parse AS $k=>$p){
                                                        $checked = $value == $p ? " checked" : NULL;
                                                        if(($disabled && $checked) || !$disabled) {
                                                            ?>
                                                            <input<?php echo $disabled . $checked; ?>
                                                                    name="cfields[<?php echo $field["id"]; ?>]"
                                                                    value="<?php echo $p; ?>"
                                                                    class="radio-custom"
                                                                    id="cfield_<?php echo $field["id"] . "_" . $k; ?>"
                                                                    type="radio">
                                                            <label style="margin-right:15px;"
                                                                   for="cfield_<?php echo $field["id"] . "_" . $k; ?>"
                                                                   class="radio-custom-label"><?php echo $p; ?></label>
                                                            <?php
                                                        }
                                                    }
                                                }elseif($field["type"] == "checkbox"){
                                                    $parsev = explode(",",$value);
                                                    $parse  = explode(",",$field["options"]);
                                                    foreach($parse AS $k=>$p){
                                                        $checked = in_array($p,$parsev) ? " checked" : NULL;
                                                        if(($disabled && $checked) || !$disabled){
                                                            ?>
                                                            <input<?php echo $disabled.$checked;?> name="cfields[<?php echo $field["id"]; ?>][]" value="<?php echo $p;?>" class="checkbox-custom" id="cfield_<?php echo $field["id"]."_".$k; ?>" type="checkbox">
                                                            <label style="margin-right:15px;" for="cfield_<?php echo $field["id"]."_".$k; ?>" class="checkbox-custom-label"><?php echo $p; ?></label>
                                                            <?php
                                                        }
                                                    }
                                                }
                                            ?>

                                        </div>
                                    </div>
                                    <?php
                                }
                            }
                        ?>

                        <?php
                            if(Config::get("options/sign/security-question/status")){
                                ?>
                                <div class="hesapbilgisi">
                                    <div class="yuzde25"><div class="hesapbilgititle"><?php echo __("website/account/field-security_question"); ?></div></div>
                                    <div class="yuzde75">
                                        <input id="security_question" name="security_question" type="text" placeholder="<?php echo __("website/account_info/security-question-ex"); ?>" value="<?php echo (isset($udata["security_question"])) ? $udata["security_question"] : NULL; ?>">
                                        <span class="kinfo"><?php echo __("website/account_info/security-question-info"); ?></span>
                                    </div>
                                </div>

                                <div class="hesapbilgisi">
                                    <div class="yuzde25"><div class="hesapbilgititle"><?php echo __("website/account/field-security_question_answer"); ?></div></div>
                                    <div class="yuzde75">
                                        <input id="security_question_answer" name="security_question_answer" type="password" placeholder="<?php echo __("website/account_info/security-question-answer-ex"); ?>" value="<?php echo (isset($udata["security_question_answer"])) ? str_repeat("*",Utility::strlen($udata["security_question_answer"])) : NULL; ?>">
                                    </div>
                                </div>
                                <?php
                            }
                        ?>


                    </div>


                    <div class="clear"></div>
                    <div class="line"></div>

                    <a href="javascript:void(0);" class="yesilbtn gonderbtn mio-ajax-submit" mio-ajax-options='{"result":"ModifyAccount_submit","waiting_text":"<?php echo addslashes(__("website/others/button1-pending")); ?>"}'><?php echo __("website/account_info/update-button"); ?></a>

                    <div id="FormOutput" class="error" style="display: none; margin-top: 25px;    text-align: right;    float: right;    margin-right: 25px;"></div>
                </form>
                <script type="text/javascript">
                    function ModifyAccount_submit(result) {
                        if(result != ''){
                            var solve = getJson(result);
                            if(solve !== false){
                                if(solve.status == "error"){
                                    if(solve.for != undefined && solve.for != ''){
                                        $("#ModifyAccountInfo "+solve.for).focus();
                                        $("#ModifyAccountInfo "+solve.for).attr("style","border-bottom:2px solid red; color:red;");
                                        $("#ModifyAccountInfo "+solve.for).change(function(){
                                            $(this).removeAttr("style");
                                        });
                                    }

                                    if(solve.message != undefined && solve.message != ''){
                                        swal('<?php echo __("website/account_info/modal-error-title"); ?>',solve.message,'error');
                                    }
                                }else if(solve.status == "successful"){
                                    swal('<?php echo __("website/account_info/modal-success-title"); ?>',solve.message,'success');
                                    if(solve.refresh != undefined && solve.refresh){
                                        setTimeout(function(){
                                            window.location.href = window.location.href;
                                        },2000);
                                    }
                                }
                            }else
                                console.log(result);
                        }
                    }
                </script>
                <div class="clear"></div>
            </div>
        </div>

    </div>

    <div id="billing" class="tabcontent">
        <style>
            #billing .sbtn {    padding: 7px 10px;}
            #billing .ui-accordion .ui-accordion-header {cursor: text;}
            #csm .ui-accordion .ui-accordion-header {cursor: text;}
        </style>
        <div class="tabcontentcon"  style="margin-top:25px;">

            <?php
                if($requiredFields["billing"] ?? [])
                {
                    ?>
                    <div class="red-info">
                        <div class="padding15">
                            <i class="fa fa-info-circle" aria-hidden="true"></i>
                            <h5><strong><?php echo __("website/account_info/required-field-title"); ?></strong></h5>
                            <p style="margin:10px 0px;"><?php echo __("website/account_info/required-field"); ?></p>
                            <?php
                                foreach($requiredFields["billing"] AS $key => $fv)
                                {
                                    $message        = $fv["message"];
                                    $element_id     = $fv["element_id"];

                                    ?>
                                    <p><strong>- <?php echo $message; ?></strong></p>

                                <?php if($element_id): ?>
                                    <script type="text/javascript">
                                        $(document).ready(function(){
                                            $("<?php echo $element_id; ?>").attr("style","border-bottom:2px solid red; color:red;");
                                            $("<?php echo $element_id; ?>").closest("tr").attr("style","color:red;");
                                            $("<?php echo $element_id; ?>").change(function(){
                                                $(this).removeAttr("style");
                                                $(this).closest("tr").removeAttr("style");
                                            });
                                        });
                                    </script>
                                <?php endif; ?>
                                    <?php
                                }
                            ?>
                            <div class="clear"></div>
                        </div>

                    </div>
                    <?php
                }
            ?>

            <div id="accordion2" style="margin-top:25px;" class="ui-accordion ui-widget ui-helper-reset" role="tablist">

              
                <!-- Add/Edit Billing Address -->
                <div id="manageAddress" style="display: none">
                    <h3 class="ui-accordion-header ui-corner-top ui-state-default ui-accordion-icons" role="tab" id="manage_title_1" aria-controls="ui-id-2" aria-selected="true" aria-expanded="true" tabindex="0"><span class="ui-accordion-header-icon ui-icon ui-icon-triangle-1-s"></span><strong><?php echo __("website/account_info/add-new-address"); ?></strong></h3>
                    <h3 class="ui-accordion-header ui-corner-top ui-state-default ui-accordion-icons" role="tab" id="manage_title_2" aria-controls="ui-id-2" aria-selected="true" aria-expanded="true" tabindex="0"><span class="ui-accordion-header-icon ui-icon ui-icon-triangle-1-s"></span><strong><?php echo __("website/account_info/edit-address"); ?></strong></h3>


                    <div class="ui-accordion-content ui-corner-bottom ui-helper-reset ui-widget-content ui-accordion-content-active" id="ui-id-2" aria-labelledby="ui-id-1" role="tabpanel" aria-hidden="false" style="display: block;    margin-bottom: 20px;">
                        <script type="text/javascript">
                            $(document).ready(function(){
                                $("#manageAddressForm_submit").on("click",function(){
                                    MioAjaxElement($(this),{
                                        waiting_text: '<?php echo __("website/others/button1-pending"); ?>',
                                        result:"manageAddressForm_handler",
                                    });
                                });
                            });
                        </script>

                        <form action="<?php echo $operation_link; ?>" method="post" id="manageAddressForm">
                            <?php echo Validation::get_csrf_token('account'); ?>

                            <input type="hidden" name="operation" value="addNewAddress">
                            <input type="hidden" name="id" value="0">

                            <?php if($kind_status): ?>
                                <div class="hesapbilgisi">
                                    <div class="yuzde25">
                                        <div class="hesapbilgititle"><?php echo __("website/sign/up-form-kind"); ?></div>
                                    </div>
                                    <div class="yuzde75">

                                        <input id="xkind_1" class="radio-custom" name="kind" value="individual" type="radio" checked>
                                        <label for="xkind_1" class="radio-custom-label" style="margin-right: 28px;"><span class="checktext"><?php echo __("website/sign/up-form-kind-1"); ?></span></label>

                                        <input id="xkind_2" class="radio-custom" name="kind" value="corporate" type="radio">
                                        <label for="xkind_2" class="radio-custom-label" style="margin-right: 28px;"><span class="checktext"><?php echo __("website/sign/up-form-kind-2"); ?></span></label>

                                    </div>
                                </div>
                            <?php endif; ?>

                            <div class="hesapbilgisi">
                                <div class="yuzde25"><div class="hesapbilgititle"><?php echo __("website/account_info/address-profile-tx1"); ?></div></div>
                                <div class="yuzde75">
                                    <input name="full_name" type="text" placeholder="<?php echo __("website/account_info/address-profile-tx1"); ?>" value="">
                                </div>
                            </div>

                            <!--Individual -->
                            <?php if($udata["country"] == 227 && $identity_status): ?>
                                <div class="hesapbilgisi">
                                    <div class="yuzde25"><div class="hesapbilgititle"><?php echo __("website/sign/up-form-identity"); ?></div></div>
                                    <div class="yuzde75">
                                        <input placeholder="<?php echo __("website/sign/up-form-identity"); ?>" name="identity" maxlength="11" type="text" value="" onkeypress='return event.charCode>= 48 &&event.charCode<= 57'>
                                    </div>
                                </div>
                            <?php endif; ?>

                            <!-- Individual end-->

                            <!-- corporate start -->
                            <div class="hesapbilgisi corporate-info" style="display: none;">
                                <div class="yuzde25"><div class="hesapbilgititle"><?php echo __("website/sign/up-form-cname"); ?></div></div>
                                <div class="yuzde75">
                                    <input name="company_name" type="text" placeholder="<?php echo __("website/sign/up-form-cname"); ?>" value="">
                                </div>
                            </div>

                            <?php if(Config::get("options/sign/up/kind/corporate/company_tax_number")): ?>
                                <div class="hesapbilgisi">
                                    <div class="yuzde25"><div class="hesapbilgititle"><?php echo __("website/sign/up-form-ctaxno"); ?></div></div>
                                    <div class="yuzde75">
                                        <input name="company_tax_number" type="text" placeholder="<?php echo __("website/sign/up-form-ctaxno"); ?>" value="">
                                    </div>
                                </div>
                            <?php endif; ?>

                            <?php if(Config::get("options/sign/up/kind/corporate/company_tax_office")): ?>
                                <div class="hesapbilgisi">
                                    <div class="yuzde25"><div class="hesapbilgititle"><?php echo __("website/sign/up-form-ctaxoff"); ?></div></div>
                                    <div class="yuzde75">
                                        <input name="company_tax_office" type="text" placeholder="<?php echo __("website/sign/up-form-ctaxoff"); ?>" value="">
                                    </div>
                                </div>
                            <?php endif; ?>
                            <!--corporate end-->

                            <div class="hesapbilgisi">
                                <div class="yuzde25"><div class="hesapbilgititle"><?php echo __("website/sign/up-form-email"); ?></div></div>
                                <div class="yuzde75">
                                    <input name="email" type="email" placeholder="<?php echo __("website/sign/up-form-email"); ?>" value="">
                                </div>
                            </div>
                            <div class="hesapbilgisi">
                                <div class="yuzde25"><div class="hesapbilgititle"><?php echo __("website/sign/up-form-gsm"); ?></div></div>
                                <div class="yuzde75">
                                    <input id="gsm2" type="text" style="width:100%;"  value="">
                                    <input type="hidden" name="gsm">
                                </div>
                            </div>

                            <div class="clear" style="margin-bottom: 20px;"></div>

                            <?php
                                if(isset($countryList) && $countryList){
                                    ?>
                                    <div class="yuzde25">
                                        <strong><?php echo __("website/account_info/country"); ?></strong>
                                        <select name="country" onchange="getCities(this.options[this.selectedIndex].value);">
                                            <option value=""><?php echo __("website/account_info/select-your"); ?></option>
                                            <?php
                                                foreach($countryList as $country){
                                                    ?><option value="<?php echo $country["id"];?>" data-code="<?php echo $country["code"]; ?>"><?php echo $country["name"];?></option><?php
                                                }
                                            ?>
                                        </select>
                                    </div>
                                    <?php
                                }
                            ?>

                            <div id="cities" class="yuzde25">
                                <strong><?php echo __("website/account_info/city"); ?></strong>
                                <select name="city" onchange="getCounties($(this).val());" disabled style="display: none;"></select>
                                <input type="text" name="city" placeholder="<?php echo __("admin/users/create-city-placeholder"); ?>">
                            </div>
                            <div id="counti" class="yuzde25" >
                                <strong><?php echo __("website/account_info/counti"); ?></strong>
                                <select name="counti" disabled style="display: none;"></select>
                                <input type="text" name="counti" placeholder="<?php echo __("admin/users/create-counti-placeholder"); ?>">
                            </div>
                            <div id="zipcode" class="yuzde25">
                                <strong><?php echo __("website/account_info/zipcode"); ?></strong>
                                <input name="zipcode" type="text" placeholder="<?php echo __("admin/users/create-zipcode-placeholder"); ?>">
                            </div>
                            <div id="address" class="yuzde100" style="margin-top:20px;">
                                <strong><?php echo __("website/account_info/address"); ?></strong>
                                <input name="address" type="text" placeholder="<?php echo __("admin/users/create-address-placeholder"); ?>">
                            </div>
                            <div class="line"></div>
                            <div style="margin-top: 5px;display: inline-block;">
                                <input type="checkbox" name="detouse" value="1" class="checkbox-custom" id="detouse">
                                <label class="checkbox-custom-label" for="detouse"><?php echo __("website/account_info/stored-cards-6"); ?></label>
                            </div>
                            <div>
                                <input type="checkbox" name="overwritenadoninv" value="1" class="checkbox-custom" id="overwritenadoninv">
                                <label class="checkbox-custom-label" for="overwritenadoninv"><?php echo __("website/account_info/address-profile-tx2"); ?></label>
                            </div>

                            <div class="line"></div>

                            <a style="float:left;" href="javascript:void(0);" onclick="turn_back();" class="lbtn"><?php echo __("website/basket/turn-back"); ?></a>

                            <a style="float:right;" href="javascript:void(0);" id="manageAddressForm_submit" class="green lbtn"><?php echo __("website/account_info/add-address-submit-button"); ?></a>

                        </form>
                    </div>
                </div>
                <!-- Add/Edit Billing Address end-->


                <!-- Accordion Start -->
                <div id="defaultAddress_wrap">
                    <h3 class="ui-accordion-header ui-corner-top ui-state-default ui-accordion-icons" role="tab" id="accordion_el2" aria-controls="ui-id-2" aria-selected="true" aria-expanded="true" tabindex="0"><span class="ui-accordion-header-icon ui-icon ui-icon-triangle-1-s"></span><strong><?php echo __("website/account_info/address-profile-tx3"); ?></strong></h3>
                    <div class="ui-accordion-content ui-corner-bottom ui-helper-reset ui-widget-content ui-accordion-content-active" id="ui-id-2" aria-labelledby="ui-id-1" role="tabpanel" aria-hidden="false" style="display: block;    margin-bottom: 20px;">

                        <script type="text/javascript">
                            $(document).ready(function(){
                                $("#updateDefaultAddressForm_submit").on("click",function(){
                                    MioAjaxElement($(this),{
                                        waiting_text: '<?php echo __("website/others/button1-pending"); ?>',
                                        result:"manageAddressForm_handler",
                                    });
                                });
                            });
                        </script>
                        <form action="<?php echo $operation_link; ?>" method="post" id="updateDefaultAddressForm">
                            <input type="hidden" name="operation" value="updateDefaultAddress">

                            <?php echo Validation::get_csrf_token('account'); ?>

                            <div class="adresbilgisi">
                                <div class="yuzde100">
                                    <select name="address_id">
                                        <?php
                                            if(isset($acAddresses) && $acAddresses){
                                                foreach($acAddresses AS $addr)
                                                {
                                                    ?>
                                                    <option value="<?php echo $addr["id"]; ?>"><?php echo $addr["name"]; ?> - <?php echo $addr["address_line"]; ?></option>
                                                    <?php
                                                }
                                            }
                                            else
                                            {
                                                ?>
                                                <option value="0"><?php echo ___("needs/none"); ?></option>
                                                <?php
                                            }
                                        ?>
                                    </select>

                                </div>
                            </div>
                            <div style="margin-top: 20px;display: inline-block;width: 100%;">
                                <input type="checkbox" name="overwritenadoninv" value="1" class="checkbox-custom" id="overwritenadoninv2">
                                <label class="checkbox-custom-label" for="overwritenadoninv2"><?php echo __("website/account_info/address-profile-tx2"); ?></label>
                            </div>
                            <div class="line"></div>
                            <a href="javascript:void 0;" class="green lbtn" id="updateDefaultAddressForm_submit"><?php echo ___("needs/button-save"); ?></a>
                            <a style="float:right;" href="javascript:addAddress();void 0;" class="blue lbtn">+ <?php echo __("website/account_info/add-address-submit-button"); ?></a>
                        </form>

                    </div>
                </div>
                <!-- Accordion End -->


                <!-- Accordion Start -->
                <div id="address_list_wrap">
                    <h3 class="ui-accordion-header ui-corner-top ui-state-default ui-accordion-icons" role="tab" id="accordion_el3" aria-controls="ui-id-2" aria-selected="true" aria-expanded="true" tabindex="0"><span class="ui-accordion-header-icon ui-icon ui-icon-triangle-1-s"></span><strong><?php echo __("website/account_info/current-addresses"); ?></strong></h3>
                    <div class="ui-accordion-content ui-corner-bottom ui-helper-reset ui-widget-content ui-accordion-content-active" id="ui-id-2" aria-labelledby="ui-id-1" role="tabpanel" aria-hidden="false" style="display: block;">
                        <div id="AddressList">
                            <?php
                                if(isset($acAddresses) && $acAddresses){
                                    foreach($acAddresses AS $addr){
                                        ?>
                                        <div class="adresbilgisi" id="address_<?php echo $addr["id"];?>">
                                            <input type="hidden" name="full_name" value="<?php echo $addr["full_name"]; ?>">
                                            <input type="hidden" name="kind" value="<?php echo $addr["kind"]; ?>">
                                            <input type="hidden" name="company_name" value="<?php echo $addr["company_name"]; ?>">
                                            <input type="hidden" name="company_tax_office" value="<?php echo $addr["company_tax_office"]; ?>">
                                            <input type="hidden" name="company_tax_number" value="<?php echo $addr["company_tax_number"]; ?>">
                                            <input type="hidden" name="email" value="<?php echo $addr["email"]; ?>">
                                            <input type="hidden" name="phone" value="<?php echo $addr["phone"]; ?>">
                                            <input type="hidden" name="identity" value="<?php echo $addr["identity"]; ?>">

                                            <input type="hidden" name="country_id" value="<?php echo $addr["country_id"]; ?>">
                                            <input type="hidden" name="detouse" value="<?php echo $addr["detouse"] ? 1 : 0; ?>">
                                            <input type="hidden" name="zipcode" value="<?php echo $addr["zipcode"]; ?>">
                                            <input type="hidden" name="city" value="<?php echo $addr["city"]; ?>">
                                            <input type="hidden" name="counti" value="<?php echo $addr["counti"]; ?>">
                                            <input type="hidden" name="address" value="<?php echo htmlentities($addr["address"],ENT_QUOTES); ?>">
                                            <div class="yuzde80">
                                                <strong><?php echo $addr["name"]; ?></strong><br>
                                                <?php echo $addr["address_line"]; ?>
                                                <?php if($addr["detouse"]): ?>
                                                    <strong>(<?php echo __("website/account_info/default-address"); ?>)</strong>
                                                <?php endif; ?>
                                            </div>
                                            <div class="yuzde20" style="float:right;text-align:right;">
                                                <a href="javascript:editAddress(<?php echo $addr["id"]; ?>);void 0;" title="<?php echo ___("needs/button-edit") ?>" class="sbtn"><i class="fa fa-pencil" aria-hidden="true"></i></a>
                                                <a href="javascript:deleteAddress(<?php echo $addr["id"]; ?>);void 0;"  title="<?php echo ___("needs/button-delete") ?>" class="red sbtn"><i class="fa fa-trash" aria-hidden="true"></i></a>
                                            </div>
                                        </div>
                                        <?php
                                    }
                                }
                            ?>
                        </div>
                        <center><div class="" id="empty_content" style="<?php echo isset($acAddresses) && $acAddresses ? 'display: none;' : ''; ?>"><?php echo __("website/account_info/address-none"); ?></div></center>
                        <script type="text/javascript">
                            function deleteAddress(id){
                                if(confirm('<?php echo htmlspecialchars(__("website/account_info/delete-address-are-you-sure"),ENT_QUOTES); ?>')){
                                    $("#address_"+id).animate({opacity: 4}, 300);

                                    var request = MioAjax({
                                        action:"<?php echo $operation_link; ?>",
                                        method:"POST",
                                        data:{
                                            operation:"DeleteAddress",
                                            id:id,
                                        }
                                    },true,true);

                                    request.done(function(result){
                                        if(result != ''){
                                            var solve = getJson(result);
                                            if(solve !== false){
                                                if(solve.status == "error"){
                                                    alert_error(solve.message,{timer:3000});
                                                }else if(solve.status == "successful"){
                                                    $("#address_"+id).animate({backgroundColor:'#4E1402',opacity:0}, 500,function () {
                                                        $("#address_"+id).remove();
                                                        if(solve.total<1){
                                                            $("#empty_content").slideDown(300);
                                                        }
                                                    });
                                                }
                                            }else
                                                console.log(result);
                                        }
                                    });
                                }
                            }
                        </script>
                    </div>
                </div>
                <!-- Accordion End -->


            </div>
        </div>
    </div>



    <div id="preferences" class="tabcontent">
        <div class="tabcontentcon">

            <?php
                if($requiredFields["preferences"] ?? [])
                {
                    ?>
                    <div class="red-info">
                        <div class="padding15">
                            <i class="fa fa-info-circle" aria-hidden="true"></i>
                            <h5><strong><?php echo __("website/account_info/required-field-title"); ?></strong></h5>
                            <p style="margin:10px 0px;"><?php echo __("website/account_info/required-field"); ?></p>
                            <?php
                                foreach($requiredFields["preferences"] AS $key => $fv)
                                {
                                    $message        = $fv["message"];
                                    $element_id     = $fv["element_id"];

                                    ?>
                                    <p><strong>- <?php echo $message; ?></strong></p>

                                <?php if($element_id): ?>
                                    <script type="text/javascript">
                                        $(document).ready(function(){
                                            $("<?php echo $element_id; ?>").attr("style","border-bottom:2px solid red; color:red;");
                                            $("<?php echo $element_id; ?>").closest("tr").attr("style","color:red;");
                                            $("<?php echo $element_id; ?>").change(function(){
                                                $(this).removeAttr("style");
                                                $(this).closest("tr").removeAttr("style");
                                            });
                                        });
                                    </script>
                                <?php endif; ?>
                                    <?php
                                }
                            ?>
                            <div class="clear"></div>
                        </div>

                    </div>
                    <?php
                }
            ?>

            <form action="<?php echo $operation_link; ?>" method="post" id="ModifyPreferences">
                <?php echo Validation::get_csrf_token('account'); ?>

                <input type="hidden" name="operation" value="ModifyPreferences">

                <?php
                    if(Config::get("options/two-factor-verification")){
                        ?>
                        <div class="formcon">
                            <div class="yuzde70">
                                <?php echo __("website/account_info/two-factor"); ?>
                                <div class="clear"></div>
                                <span class="kinfo"><?php echo __("website/account_info/two-factor-info"); ?></span>
                            </div>
                            <div class="yuzde30">
                                <input id="two_factor" class="sitemio-checkbox" name="two_factor" value="1" type="checkbox"<?php echo ($udata["two_factor"] == 1) ? ' checked' : ''; ?>>
                                <label for="two_factor" class="sitemio-checkbox-label" style="margin-right: 28px;"></label>
                            </div>
                        </div>
                        <?php
                    }
                ?>

                <div class="formcon">
                    <div class="yuzde70"><?php echo __("website/account_info/email-notifications"); ?></div>
                    <div class="yuzde30">
                        <input id="email_notifications" class="checkbox-custom" name="email_notifications" value="1" type="checkbox"<?php echo ($udata["email_notifications"] == 1) ? ' checked' : ''; ?>>
                        <label for="email_notifications" class="checkbox-custom-label" style="margin-right: 28px;"></label>
                    </div>
                </div>


                <div class="formcon">
                    <div class="yuzde70"><?php echo __("website/account_info/sms-notifications"); ?></div>
                    <div class="yuzde30">
                        <input id="sms_notifications" class="checkbox-custom" name="sms_notifications" value="1" type="checkbox"<?php echo ($udata["sms_notifications"] == 1) ? ' checked' : ''; ?>>
                        <label for="sms_notifications" class="checkbox-custom-label" style="margin-right: 28px;"></label>
                    </div>
                </div>

                <div class="formcon">
                    <div class="yuzde70"><?php echo __("website/account_info/select-currency"); ?></div>
                    <div class="yuzde30">
                        <select name="currency">
                            <?php
                                if($currencies = Money::getCurrencies()){
                                    foreach($currencies AS $currency){
                                        ?><option value="<?php echo $currency["id"]; ?>"<?php echo $currency["id"] == $udata["currency"] ? " selected" : ''; ?>><?php echo $currency["name"]." (".$currency["code"].")"; ?></option><?php
                                    }
                                }
                            ?>
                        </select>
                    </div>
                </div>

                <div class="formcon">
                    <div class="yuzde70"><?php echo __("website/account_info/select-language"); ?></div>
                    <div class="yuzde30">
                        <select name="lang">
                            <?php
                                if(isset($lang_list) && is_array($lang_list)){
                                    foreach($lang_list AS $lang){
                                        ?><option value="<?php echo $lang["key"]; ?>" <?php echo $udata["lang"] == $lang["key"] ? " selected" : ''; ?>><?php echo $lang["cname"]." (".$lang["name"].")"; ?></option><?php
                                    }
                                }
                            ?>
                        </select>
                    </div>
                </div>

                <div class="clear"></div>
                <a href="javascript:void(0);" class="yesilbtn gonderbtn mio-ajax-submit" mio-ajax-options='{"waiting_text":"<?php echo addslashes(__("website/others/button1-pending")); ?>","result":"ModifyPreferences_submit"}'><?php echo __("website/account_info/update-button"); ?></a>
                <div id="result" class="error" style="display: none; margin-top: 10px; text-align: center;"></div>
            </form>
            <div id="ModifyPreferences_success" style="display:none;">
                <div style="margin-top:30px;margin-bottom:70px;text-align:center;">
                    <i style="font-size:80px;color:green;" class="fa fa-check"></i>
                    <h4 style="color:green;font-weight:bold;"><?php echo __("website/account_info/changed-successfully"); ?></h4>
                    <br>
                </div>
            </div>
            <script type="text/javascript">
                function ModifyPreferences_submit(result) {
                    if(result != ''){
                        var solve = getJson(result);
                        if(solve !== false){
                            if(solve.status == "error"){
                                if(solve.for != undefined && solve.for != ''){
                                    $("#ModifyPreferences "+solve.for).focus();
                                    $("#ModifyPreferences "+solve.for).attr("style","border-bottom:2px solid red; color:red;");
                                    $("#ModifyPreferences "+solve.for).change(function(){
                                        $(this).removeAttr("style");
                                    });
                                }

                                if(solve.message != undefined && solve.message != ''){
                                    swal('<?php echo __("website/account_info/modal-error-title"); ?>',solve.message,'error');
                                }
                            }else if(solve.status == "successful"){
                                swal('<?php echo __("website/account_info/modal-success-title"); ?>',"<?php echo __("website/account_info/changed-successfully"); ?>",'success');
                            }
                            if(solve.redirect != undefined && solve.redirect){
                                window.location.href = solve.redirect;
                            }
                        }else
                            console.log(result);
                    }
                }
            </script>
            <div class="clear"></div>
        </div>
    </div>

    <div id="password" class="tabcontent">
        <div class="tabcontentcon">

            <?php
                if($requiredFields["password"] ?? [])
                {
                    ?>
                    <div class="red-info">
                        <div class="padding15">
                            <i class="fa fa-info-circle" aria-hidden="true"></i>
                            <h5><strong><?php echo __("website/account_info/required-field-title"); ?></strong></h5>
                            <p style="margin:10px 0px;"><?php echo __("website/account_info/required-field"); ?></p>
                            <?php
                                foreach($requiredFields["password"] AS $key => $fv)
                                {
                                    $message        = $fv["message"];
                                    $element_id     = $fv["element_id"];

                                    ?>
                                    <p><strong>- <?php echo $message; ?></strong></p>

                                <?php if($element_id): ?>
                                    <script type="text/javascript">
                                        $(document).ready(function(){
                                            $("<?php echo $element_id; ?>").attr("style","border-bottom:2px solid red; color:red;");
                                            $("<?php echo $element_id; ?>").closest("tr").attr("style","color:red;");
                                            $("<?php echo $element_id; ?>").change(function(){
                                                $(this).removeAttr("style");
                                                $(this).closest("tr").removeAttr("style");
                                            });
                                        });
                                    </script>
                                <?php endif; ?>
                                    <?php
                                }
                            ?>
                            <div class="clear"></div>
                        </div>

                    </div>
                    <?php
                }
            ?>


            <form action="<?php echo $operation_link; ?>" method="post" id="ModifyPassword">
                <?php echo Validation::get_csrf_token('account'); ?>

                <input type="hidden" name="operation" value="ModifyPassword">
                <h5 style="margin:20px 0px;"><?php echo __("website/account_info/info-tab4-text1"); ?></h5>

                <!--
                <div class="formcon"><strong class="yuzde30"><?php echo __("website/account_info/set-a-password"); ?></strong><input class="yuzde50inpt" type="password" name="password" placeholder="******"></div>

                <div class="formcon"><strong class="yuzde30"><?php echo __("website/account_info/set-a-password-again"); ?></strong><input class="yuzde50inpt" type="password" name="password_again" placeholder="******"></div>
                -->

                <div class="formcon">
                    <strong class="yuzde30"><?php echo __("website/account_info/set-a-password"); ?></strong>
                    <input class="yuzde50inpt" name="password" type="password" id="password_primary" placeholder="******" required>
                </div>
                <div class="formcon">
                    <strong class="yuzde30"><?php echo __("website/account_info/set-a-password-again"); ?></strong>
                    <input class="yuzde50inpt" name="password_again" type="password" id="password_again" placeholder="******" required>
                </div>

                <div class="formcon">
                    <div class="yuzde30">

                    </div>
                    <div class="yuzde70">
                        <a class="lbtn" href="javascript:void 0;" onclick="$('#password_primary').attr('type','text'); $('#password_primary,#password_again').val(voucher_codes.generate({length:16,charset: 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$%&*()-_=+[]\|;:,./?'})).trigger('change');"><i class="fa fa-refresh"></i> <?php echo __("website/account_products/new-random-password"); ?></a>

                        <div style="width: 200px;display: inline-block;">
                            <div id="weak" style="display:block;" class="level-block"><?php echo __("website/sign/up-form-password-level"); ?>: <strong><?php echo __("website/sign/up-form-password-level1"); ?></strong></div>
                            <div id="good" class="level-block" style="display:none"><?php echo __("website/sign/up-form-password-level"); ?>: <strong><?php echo __("website/sign/up-form-password-level2"); ?></strong></div>
                            <div id="strong" class="level-block" style="display: none;"><?php echo __("website/sign/up-form-password-level"); ?>: <strong><?php echo __("website/sign/up-form-password-level3"); ?></strong></div>
                        </div>
                    </div>
                </div>


                <span style="    font-size: 14px;    margin: 15px 0px;    float: left;"><?php echo __("website/account_info/info-tab4-text2"); ?></span>
                <div class="clear"></div>
                <a href="javascript:void(0);" class="yesilbtn gonderbtn mio-ajax-submit" mio-ajax-options='{"waiting_text":"<?php echo addslashes(__("website/others/button1-pending")); ?>","result":"ModifyPassword_submit"}'><?php echo __("website/account_info/change-password-button"); ?></a>
                <div id="FormOutput" class="error" style="display: none; margin-top: 10px; "></div>
            </form>
            <div id="ModifyPassword_success" style="display:none;">
                <div style="margin-top:30px;margin-bottom:70px;text-align:center;">
                    <i style="font-size:80px;color:green;" class="fa fa-check"></i>
                    <h4 style="color:green;font-weight:bold;"><?php echo __("website/account_info/changed-password-successfully"); ?></h4>
                    <br>
                </div>
            </div>
            <script type="text/javascript">
                function ModifyPassword_submit(result) {
                    if(result != ''){
                        var solve = getJson(result);
                        if(solve !== false){
                            if(solve.status == "error"){
                                if(solve.for != undefined && solve.for != ''){
                                    $("#ModifyPassword "+solve.for).focus();
                                    $("#ModifyPassword "+solve.for).attr("style","border-bottom:2px solid red; color:red;");
                                    $("#ModifyPassword "+solve.for).change(function(){
                                        $(this).removeAttr("style");
                                    });
                                }

                                if(solve.message != undefined && solve.message != ''){
                                    $("#ModifyPassword #FormOutput").fadeIn(200).html(solve.message);
                                }
                            }else if(solve.status == "successful"){
                                $("#ModifyPassword #FormOutput").fadeOut(200).html('');
                                $("#ModifyPassword").fadeOut(200,function(){
                                    $("#ModifyPassword_success").fadeIn(200);
                                });
                            }else
                                $("#ModifyPassword #FormOutput").fadeOut(200).html('');
                        }else
                            console.log(result);
                    }
                }
            </script>
            <div class="clear"></div>
        </div>
    </div>

    <div id="verification" class="tabcontent">
        <div class="tabcontentcon">


            <?php
                if($requiredFields["verification"] ?? [])
                {
                    ?>
                    <div class="red-info">
                        <div class="padding15">
                            <i class="fa fa-info-circle" aria-hidden="true"></i>
                            <h5><strong><?php echo __("website/account_info/required-field-title"); ?></strong></h5>
                            <p style="margin:10px 0px;"><?php echo __("website/account_info/required-field"); ?></p>
                            <?php
                                foreach($requiredFields["verification"] AS $key => $fv)
                                {
                                    $message        = $fv["message"];
                                    $element_id     = $fv["element_id"];

                                    ?>
                                    <p><strong>- <?php echo $message; ?></strong></p>

                                <?php if($element_id): ?>
                                    <script type="text/javascript">
                                        $(document).ready(function(){
                                            $("<?php echo $element_id; ?>").attr("style","border-bottom:2px solid red; color:red;");
                                            $("<?php echo $element_id; ?>").closest("tr").attr("style","color:red;");
                                            $("<?php echo $element_id; ?>").change(function(){
                                                $(this).removeAttr("style");
                                                $(this).closest("tr").removeAttr("style");
                                            });
                                        });
                                    </script>
                                <?php endif; ?>
                                    <?php
                                }
                            ?>
                            <div class="clear"></div>
                        </div>

                    </div>
                    <?php
                }
            ?>


            <div class="userverification" style="display:block">

                <?php

                    if($remainingVerifications["force"] > 0)
                    {
                        ?>
                        <div class="userverification-headinfo"><i class="fa fa-shield" aria-hidden="true"></i>
                            <h4 style="color: #F44336;"><strong><?php echo __("website/account_info/verification-text1"); ?></strong></h4>
                            <h5><?php echo __("website/account_info/verification-text2"); ?></h5>
                        </div>
                        <?php
                    }
                    else
                    {
                        ?>
                        <div class="userverification-headinfo"><i class="fa fa-shield" aria-hidden="true" style="color:#4caf50;"></i>
                            <h4 style="color: #4caf50;"><strong><?php echo __("website/account_info/verification-text3"); ?></strong></h4>
                            <h5><?php echo __("website/account_info/verification-text4"); ?></h5>
                        </div>
                        <?php
                    }

                    if(isset($remainingVerifications["verified-email"]))
                    {
                        ?>
                        <div class="hesapbilgisi">
                            <div class="yuzde50">
                                <div class="hesapbilgititle"><?php echo __("website/account_info/please-verify-your-email"); ?></div>
                            </div>
                            <div class="yuzde50">
                                <?php
                                    $sendBlocked = User::CheckBlocked("verify-code-send-email",$udata["id"],[
                                        'ip' => UserManager::GetIP(),
                                        'email' => $udata["email"],
                                    ]);
                                ?>
                                <form action="<?php echo $operation_link; ?>" method="post" id="verifyEmail" onsubmit="return false;">
                                    <?php echo Validation::get_csrf_token('account'); ?>
                                    <input type="hidden" name="operation" value="verifyEmail">
                                    <input type="hidden" name="send" value="0">
                                    <input name="code" id="email_verify_code" type="text" class="yuzde30" placeholder="<?php echo __("website/account_info/verify-code-input-label"); ?>" style="border-bottom:2px solid red; color:red;">
                                    <a style="margin-right:5px;" href="javascript:verifyCodeCheckEmail();void 0;" id="verify-code-check-email" class="lbtn"><i class="fa fa-check" aria-hidden="true"></i> <?php echo __("website/account_info/verify-button"); ?></a>
                                    <a<?php echo $sendBlocked ? ' style="display:none;"' : ''; ?> href="javascript:verifyCodeSendEmail();void 0;" id="verify-code-send-email" class="lbtn"><i class="fa fa-refresh" aria-hidden="true"></i> <?php echo __("website/account_info/verify-code-send"); ?></a>
                                    <a style="opacity: 0.3;background: none;color: #777;<?php echo !$sendBlocked ? 'display:none;' : ''; ?>" id="verifyEmailSendBlockedButton" class="lbtn"><i class="fa fa-refresh" aria-hidden="true"></i> <?php echo __("website/account_info/verify-code-send"); ?></a>

                                    <div class="clear"></div>
                                    <div id="verify_email_result" class="error" style="display: none;     margin-top: 5px; margin-bottom:5px; font-size: 14px;font-weight:normal;"></div>

                                </form>
                                <script type="text/javascript">
                                    function verifyCodeCheckEmail(){
                                        $("#verifyEmail input[name='send']").val("0");
                                        MioAjaxElement($("#verify-code-check-email"),{
                                            form:$("#verifyEmail"),
                                            waiting_text:"<?php echo addslashes(__("website/others/button1-pending")); ?>",
                                            result:"verifyEmail_handler",
                                        });
                                    }

                                    function verifyCodeSendEmail(){
                                        $("#verifyEmail input[name='send']").val("1");
                                        MioAjaxElement($("#verify-code-send-email"),{
                                            form:$("#verifyEmail"),
                                            waiting_text:"<?php echo addslashes(__("website/others/button1-pending")); ?>",
                                            result:"verifyEmail_handler",
                                        });
                                    }

                                    function verifyEmail_handler(result) {
                                        if(result != ''){
                                            var solve = getJson(result);
                                            if(solve !== false){
                                                if(solve.status == "error"){
                                                    if(solve.for != undefined && solve.for != ''){
                                                        $(+solve.for).focus();
                                                        $(solve.for).attr("style","border-bottom:2px solid red; color:red;");
                                                        $(solve.for).change(function(){
                                                            $(this).removeAttr("style");
                                                        });
                                                    }
                                                    if(solve.message != undefined && solve.message != '') {
                                                        swal('<?php echo __("website/account_info/modal-error-title"); ?>',solve.message,'error');
                                                    }
                                                }else if(solve.status == "sent"){
                                                    $("#verify-code-send-email").fadeOut(150,function(){
                                                        $("#verifyEmailSendBlockedButton").fadeIn(150);
                                                    });
                                                    swal('<?php echo __("website/account_info/modal-success-title"); ?>',solve.message,'success');
                                                }else if(solve.status == "successful")
                                                    window.location.href = window.location.href;
                                            }else
                                                console.log(result);
                                        }

                                    }
                                </script>
                            </div>
                        </div>
                        <?php
                    }

                    if(isset($remainingVerifications["verified-gsm"]))
                    {
                        ?>
                        <div class="hesapbilgisi">
                            <div class="yuzde50">
                                <div class="hesapbilgititle"><?php echo __("website/account_info/please-verify-your-gsm"); ?></div>
                            </div>
                            <div class="yuzde50">
                                <?php
                                    $sendBlocked = User::CheckBlocked("verify-code-send-gsm",$udata["id"],[
                                        'ip' => UserManager::GetIP(),
                                        'phone' => $udata["gsm_cc"].$udata["gsm"],
                                    ]);
                                ?>
                                <form action="<?php echo $operation_link; ?>" method="post" id="verifyGSM" onsubmit="return false;">
                                    <?php echo Validation::get_csrf_token('account'); ?>

                                    <input type="hidden" name="operation" value="verifyGSM">
                                    <input type="hidden" name="send" value="0">

                                    <input name="code" id="gsm_verify_code" type="text" class="yuzde30" placeholder="<?php echo __("website/account_info/verify-code-input-label"); ?>" style="border-bottom:2px solid red; color:red;">
                                    <a style="margin-right:5px;" href="javascript:verifyCodeCheckGSM();void 0;" id="verify-code-check-gsm" class="lbtn"><i class="fa fa-check" aria-hidden="true"></i> <?php echo __("website/account_info/verify-button"); ?></a>
                                    <a<?php echo $sendBlocked ? ' style="display:none;"' : ''; ?> href="javascript:verifyCodeSendGSM();void 0;" id="verify-code-send-gsm" class="lbtn"><i class="fa fa-refresh" aria-hidden="true"></i> <?php echo __("website/account_info/verify-code-send"); ?></a>
                                    <a style="opacity: 0.3;background: none;color: #777;<?php echo !$sendBlocked ? 'display:none;' : ''; ?>" id="verifyGSMSendBlockedButton" class="lbtn"><i class="fa fa-refresh" aria-hidden="true"></i> <?php echo __("website/account_info/verify-code-send"); ?></a>

                                    <div class="clear"></div>
                                    <div id="verify_gsm_result" class="error" style="display: none;     margin-top: 5px; margin-bottom:5px; font-size: 14px;font-weight:normal;"></div>

                                </form>
                                <script type="text/javascript">
                                    function verifyCodeCheckGSM(){
                                        $("#verifyGSM input[name='send']").val("0");
                                        MioAjaxElement($("#verify-code-check-gsm"),{
                                            form:$("#verifyGSM"),
                                            waiting_text:"<?php echo addslashes(__("website/others/button1-pending")); ?>",
                                            result:"verifyGSM_handler",
                                        });
                                    }

                                    function verifyCodeSendGSM(){
                                        $("#verifyGSM input[name='send']").val("1");
                                        MioAjaxElement($("#verify-code-send-gsm"),{
                                            form:$("#verifyGSM"),
                                            waiting_text:"<?php echo addslashes(__("website/others/button1-pending")); ?>",
                                            result:"verifyGSM_handler",
                                        });
                                    }

                                    function verifyGSM_handler(result) {
                                        if(result != ''){
                                            var solve = getJson(result);
                                            if(solve !== false){
                                                if(solve.status == "error"){
                                                    if(solve.for != undefined && solve.for != ''){
                                                        $(+solve.for).focus();
                                                        $(solve.for).attr("style","border-bottom:2px solid red; color:red;");
                                                        $(solve.for).change(function(){
                                                            $(this).removeAttr("style");
                                                        });
                                                    }
                                                    if(solve.message != undefined && solve.message != '') {
                                                        swal('<?php echo __("website/account_info/modal-error-title"); ?>',solve.message,'error');
                                                    }
                                                }else if(solve.status == "sent"){
                                                    $("#verify-code-send-gsm").fadeOut(150,function(){
                                                        $("#verifyGSMSendBlockedButton").fadeIn(150);
                                                    });
                                                    swal('<?php echo __("website/account_info/modal-success-title"); ?>',solve.message,'success');
                                                }else if(solve.status == "successful")
                                                    window.location.href = window.location.href;
                                            }else
                                                console.log(result);
                                        }

                                    }
                                </script>
                            </div>
                        </div>
                        <?php
                    }
                ?>

                <div class="clear"></div>

                <?php
                    if(isset($remainingVerifications["document_filters"])){
                        if($remainingVerifications["document_filters"]){
                            $show_submit = false;

                            ?>
                            <form action="<?php echo $operation_link; ?>" method="post" id="SubmitDocumentVerification" enctype="multipart/form-data">
                                <?php echo Validation::get_csrf_token('account'); ?>
                                <input type="hidden" name="operation" value="SubmitDocumentVerification">

                                <?php
                                    foreach($remainingVerifications["document_filters"] AS $f_id=>$f){
                                        if(!isset($f["fields"][$u_lang])) continue;
                                        $fields = $f["fields"][$u_lang];
                                        foreach($fields AS $f_k=>$field){
                                            $record = isset($field["record"]) ? $field["record"] : [];
                                            ?>
                                            <div class="hesapbilgisi">
                                                <div class="yuzde50">
                                                    <div class="hesapbilgititle"><?php echo $field["name"]; ?></div>
                                                </div>
                                                <div class="yuzde50">
                                                    <?php

                                                        if(!$record || $record["status"] == 'unverified'){
                                                            $r_value     = $record ? $record["field_value"] : '';
                                                            $show_submit = true;
                                                            if($record && $record["status"] == 'unverified'){
                                                                ?>
                                                                <strong><span style="color: #F44336;"><i class="fa fa-times-circle-o" aria-hidden="true"></i> <?php echo __("website/account_info/verification-text5"); ?></span></strong>
                                                                <?php
                                                                if($record["status_msg"]){
                                                                    ?><br><span>(<?php echo $record["status_msg"]; ?>)</span><?php
                                                                }
                                                                ?>
                                                                <div class="clear" style="margin-bottom: 5px;"></div>
                                                                <?php
                                                            }
                                                            if($field["type"] == "input"){
                                                                ?>
                                                                <input type="text" name="documents[<?php echo $f_id; ?>][fields][<?php echo $f_k; ?>]" value="<?php echo $r_value; ?>">
                                                                <?php
                                                            }

                                                            if($field["type"] == "textarea"){
                                                                ?>
                                                                <textarea name="documents[<?php echo $f_id; ?>][fields][<?php echo $f_k; ?>]"><?php echo $r_value; ?></textarea>
                                                                <?php
                                                            }

                                                            if($field["type"] == "select"){
                                                                ?>
                                                                <select name="documents[<?php echo $f_id; ?>][fields][<?php echo $f_k; ?>]">
                                                                    <?php
                                                                        foreach($field["options"] AS $opt){
                                                                            ?>
                                                                            <option<?php echo $opt == $r_value ? ' selected' : ''; ?> value="<?php echo $opt; ?>"><?php echo $opt; ?></option>
                                                                            <?php
                                                                        }
                                                                    ?>
                                                                </select>
                                                                <?php
                                                            }

                                                            if($field["type"] == "radio"){
                                                                foreach($field["options"] AS $k=>$opt){
                                                                    ?>
                                                                    <input<?php echo $opt == $r_value ? ' checked' : ''; ?> name="documents[<?php echo $f_id; ?>][fields][<?php echo $f_k; ?>]" type="radio" class="radio-custom" id="<?php echo "f_".$f_id."_".$f_k."_".$k; ?>" value="<?php echo $opt; ?>">
                                                                    <label class="radio-custom-label" for="<?php echo "f_".$f_id."_".$f_k."_".$k; ?>" style="margin-right: 10px;"><?php echo $opt; ?></label>
                                                                    <?php
                                                                }
                                                            }

                                                            if($field["type"] == "checkbox"){
                                                                $d_value = Utility::jdecode($r_value,true);
                                                                if(!$d_value) $d_value = [];
                                                                foreach($field["options"] AS $k=>$opt){
                                                                    ?>
                                                                    <input<?php echo in_array($opt,$d_value) ? ' checked' : ''; ?> name="documents[<?php echo $f_id; ?>][fields][<?php echo $f_k; ?>][]" type="checkbox" class="checkbox-custom" id="<?php echo "f_".$f_id."_".$f_k."_".$k; ?>" value="<?php echo $opt; ?>">
                                                                    <label class="checkbox-custom-label" for="<?php echo "f_".$f_id."_".$f_k."_".$k; ?>" style="margin-right: 10px;"><?php echo $opt; ?></label>
                                                                    <?php
                                                                }
                                                                ?>
                                                                <?php
                                                            }

                                                            if($field["type"] == "file"){
                                                                ?>
                                                                <input type="file" name="documents-<?php echo $f_id; ?>-fields-<?php echo $f_k; ?>">
                                                                <div class="clear"></div>
                                                                <span style="font-size: 9pt; color: #9d0006;">* <?php echo $field["allowed_ext"]; ?></span>
                                                                <?php
                                                            }
                                                        }
                                                        elseif($record["status"] == 'awaiting'){
                                                            ?>
                                                            <strong><span><i class="fa fa-clock-o" aria-hidden="true"></i> <?php echo __("website/account_info/verification-text6"); ?></span></strong>
                                                            <br><span>(<?php echo __("website/account_info/verification-text7"); ?>)</span>
                                                            <?php
                                                        }
                                                        elseif($record["status"] == 'verified'){
                                                            ?>
                                                            <strong> <span style="color:#4caf50;"><i class="fa fa-check-circle-o" aria-hidden="true"></i> <?php echo __("website/account_info/verification-text8"); ?></span></strong>
                                                            <?php
                                                        }
                                                    ?>
                                                </div>
                                            </div>
                                            <?php
                                        }
                                    }

                                    if($show_submit){
                                        ?>
                                        <div class="clear"></div>
                                        <a href="javascript:void(0);" class="yesilbtn gonderbtn mio-ajax-submit" mio-ajax-options='{"waiting_text":"<?php echo addslashes(__("website/others/button1-pending")); ?>","result":"SubmitDocumentVerification_handler"}'><?php echo ___("needs/button-submit"); ?></a>
                                        <div class="clear"></div>
                                        <?php
                                    }
                                ?>
                            </form>
                            <script type="text/javascript">
                                function SubmitDocumentVerification_handler(result) {
                                    if(result !== ''){
                                        var solve = getJson(result);
                                        if(solve !== false){
                                            if(solve.status === "error"){
                                                if(solve.for != undefined && solve.for != ''){
                                                    $("#SubmitDocumentVerification "+solve.for).focus();
                                                    $("#SubmitDocumentVerification "+solve.for).attr("style","border-bottom:2px solid red; color:red;");
                                                    $("#SubmitDocumentVerification "+solve.for).change(function(){
                                                        $(this).removeAttr("style");
                                                    });
                                                }

                                                if(solve.message != undefined && solve.message !== '')
                                                    alert_error(solve.message,{timer:5000});
                                            }else if(solve.status === "successful"){
                                                alert_success(solve.message,{timer:3000});
                                                setTimeout(function(){
                                                    window.location.href = window.location.href;
                                                },3000);
                                            }
                                        }else
                                            console.log(result);
                                    }
                                }
                            </script>
                            <?php


                        }
                    }
                ?>
            </div>


        </div>
    </div>

    <?php
        if($c_s_m && $c_s_m != "none")
        {
            ?>
            <div id="csm" class="tabcontent">
                <div class="tabcontentcon">


                    <?php
                        if($requiredFields["csm"] ?? [])
                        {
                            ?>
                            <div class="red-info">
                                <div class="padding15">
                                    <i class="fa fa-info-circle" aria-hidden="true"></i>
                                    <h5><strong><?php echo __("website/account_info/required-field-title"); ?></strong></h5>
                                    <p style="margin:10px 0px;"><?php echo __("website/account_info/required-field"); ?></p>
                                    <?php
                                        foreach($requiredFields["csm"] AS $key => $fv)
                                        {
                                            $message        = $fv["message"];
                                            $element_id     = $fv["element_id"];

                                            ?>
                                            <p><strong>- <?php echo $message; ?></strong></p>

                                        <?php if($element_id): ?>
                                            <script type="text/javascript">
                                                $(document).ready(function(){
                                                    $("<?php echo $element_id; ?>").attr("style","border-bottom:2px solid red; color:red;");
                                                    $("<?php echo $element_id; ?>").closest("tr").attr("style","color:red;");
                                                    $("<?php echo $element_id; ?>").change(function(){
                                                        $(this).removeAttr("style");
                                                        $(this).closest("tr").removeAttr("style");
                                                    });
                                                });
                                            </script>
                                        <?php endif; ?>
                                            <?php
                                        }
                                    ?>
                                    <div class="clear"></div>
                                </div>

                            </div>
                            <?php
                        }
                    ?>


                    <script type="text/javascript">
                        let waiting_text = '<i class="fa fa-spinner" style="-webkit-animation:fa-spin 2s infinite linear;animation: fa-spin 2s infinite linear;" aria-hidden="true"></i>'
                        $(document).ready(function(){
                            $(".card_as_default_btn").click(function(){
                                let
                                    btn = $(this),
                                    request = MioAjax({
                                        button_element  : btn,
                                        waiting_text    : waiting_text,
                                        action          : "<?php echo $operation_link; ?>",
                                        method          : "POST",
                                        data            : {
                                            token       : "<?php echo Validation::get_csrf_token('account',false); ?>",
                                            operation:"stored_card_as_default",
                                            id:btn.data("id")
                                        }
                                    },true,true);
                                request.done(function(result){
                                    if(result != ''){
                                        let solve = getJson(result);
                                        if(solve !== false){
                                            if(solve.status == "error")
                                                alert_error(solve.message,{timer:5000});
                                            else if(solve.status === "successful")
                                            {
                                                $(".creditcardbox").removeAttr("id");
                                                $(".card_as_default_btn").css("display","block");
                                                $(".creditcardbox .defaultcardtitle").css("display","none");
                                                $(".creditcardbox[data-id="+btn.data("id")+"]").attr("id","default");
                                                $(".creditcardbox[data-id="+btn.data("id")+"] .defaultcardtitle").css("display","block");
                                                $(".creditcardbox[data-id="+btn.data("id")+"] .card_as_default_btn").css("display","none");
                                            }
                                        }else
                                            console.log(result);
                                    }
                                });
                            });

                            $(".card_remove_btn").click(function(){

                                if(!confirm("<?php echo ___("needs/delete-are-you-sure"); ?>"))
                                    return false;

                                let
                                    btn = $(this),
                                    request = MioAjax({
                                        button_element  : btn,
                                        waiting_text    : waiting_text,
                                        action          : "<?php echo $operation_link; ?>",
                                        method          : "POST",
                                        data            : {
                                            token       : "<?php echo Validation::get_csrf_token('account',false); ?>",
                                            operation:"stored_card_remove",
                                            id:btn.data("id")
                                        }
                                    },true,true);
                                request.done(function(result){
                                    if(result != ''){
                                        let solve = getJson(result);
                                        if(solve !== false){
                                            if(solve.status == "error")
                                                alert_error(solve.message,{timer:5000});
                                            else if(solve.status === "successful")
                                                window.location.href = '<?php echo $operation_link; ?>?tab=csm';
                                        }else
                                            console.log(result);
                                    }
                                });
                            });

                            $("#auto_payment_cx").change(function(){
                                let
                                    cx = $(this),
                                    request = MioAjax({
                                        action          : "<?php echo $operation_link; ?>",
                                        method          : "POST",
                                        data            : {
                                            token       : "<?php echo Validation::get_csrf_token('account',false); ?>",
                                            operation:"stored_card_auto_payment",
                                            status      : cx.prop('checked') ? 1 : 0
                                        }
                                    },true,true);
                                request.done(function(result){
                                    if(result != ''){
                                        let solve = getJson(result);
                                        if(solve !== false){
                                            if(solve.status == "error")
                                            {
                                                alert_error(solve.message,{timer:5000});
                                                if(cx.prop('checked')) cx.prop('checked',false);
                                            }
                                        }else
                                            console.log(result);
                                    }
                                });
                            });

                        });
                    </script>


                    <div id="accordion2" style="margin-top:25px;" class="ui-accordion ui-widget ui-helper-reset" role="tablist">

                        <h3 class="ui-accordion-header ui-corner-top ui-state-default ui-accordion-icons" role="tab" id="ui-id-1" aria-controls="ui-id-2" aria-selected="true" aria-expanded="true" tabindex="0"><span class="ui-accordion-header-icon ui-icon ui-icon-triangle-1-s"></span><strong><?php echo __("website/account_info/stored-cards-1"); ?></strong></h3>
                        <div class="ui-accordion-content ui-corner-bottom ui-helper-reset ui-widget-content ui-accordion-content-active" id="ui-id-2" aria-labelledby="ui-id-1" role="tabpanel" aria-hidden="false" style="display: block;">


                            <?php
                                if(isset($stored_cards) && $stored_cards)
                                {
                                    ?>
                                    <div class="creditcardbox-list">

                                        <?php
                                            foreach($stored_cards AS $sc)
                                            {
                                                $as_default     = $sc["as_default"] == 1;
                                                $bank_logo      = PaymentGatewayModule::find_bank_logo($sc["bank_name"],$sc["card_brand"],$sc["card_type"]);

                                                ?>
                                                <div data-id="<?php echo $sc["id"]; ?>" class="creditcardbox"<?php if($as_default){ ?> id="default" title="<?php echo __("website/account_info/stored-cards-4"); ?>"<?php } ?>>
                                                    <div style="<?php echo !$as_default ? 'display:none;' : ''; ?>" class="defaultcardtitle"><?php echo __("website/account_info/stored-cards-5"); ?></div>
                                                    <a class="defaultcard card_as_default_btn" style="<?php echo $as_default ? 'display:none;' : ''; ?>" href="javascript:void 0;" title="<?php echo __("website/account_info/stored-cards-6"); ?>" data-id="<?php echo $sc["id"]; ?>" alt="<?php echo __("website/account_info/stored-cards-6"); ?>"><i class="fa fa-check" aria-hidden="true"></i></a>
                                                    <a class="deletecard tooltip-right card_remove_btn" href="javascript:void 0;" data-id="<?php echo $sc["id"]; ?>" title="<?php echo __("website/account_info/stored-cards-7"); ?>" alt="<?php echo __("website/account_info/stored-cards-7"); ?>"><i class="fa fa-trash" aria-hidden="true"></i></a>

                                                    <div class="creditcardbox-con">
                                                        <?php
                                                            if($bank_logo)
                                                            {
                                                                ?>
                                                                <img class="banklogo" src="<?php echo $bank_logo; ?>" alt="">
                                                                <?php
                                                            }
                                                            else
                                                            {
                                                                ?>
                                                                <div class="banknologo"><?php echo $sc["bank_name"]; ?></div>
                                                                <?php
                                                            }
                                                        ?>
                                                        <img class="visamaster" src="<?php echo APP_URI; ?>/resources/assets/images/creditcardlogos/<?php echo strtolower($sc["card_schema"]); ?>.png" alt="<?php echo $sc["card_schema"]; ?>">
                                                        <img class="cardchip" src="<?php echo APP_URI; ?>/resources/assets/images/creditcardlogos/chipicon.png" alt="">
                                                        <div class="creditcardbox-numbers"><h5>****</h5><h5>****</h5><h5>****</h5><h5><?php echo $sc["ln4"]; ?></h5></div>
                                                        <div class="creditcardbox-validdate"><?php echo $sc["expiry_month"]; ?>/<?php echo $sc["expiry_year"]; ?></div>
                                                        <div class="creditcardbox-fullname"><?php echo $sc["name"]; ?></div>
                                                        <div class="clear"></div>
                                                    </div>
                                                </div>
                                                <?php
                                            }
                                        ?>
                                        <div class="line"></div>

                                        <div class="clear"></div>

                                        <div class="all-payment-auto-approval">
                                            <input<?php echo isset($udata["auto_payment"]) && $udata["auto_payment"] ? ' checked' : ''; ?> id="auto_payment_cx" class="checkbox-custom" name="auto_payment" value="1" type="checkbox">
                                            <label for="auto_payment_cx" class="checkbox-custom-label">
                                                <span class="checktext"><?php echo __("website/account_info/stored-cards-8"); ?></span><br>
                                                <span class="kinfo"><?php echo __("website/account_info/stored-cards-9"); ?></span>
                                            </label>
                                        </div>
                                    </div>
                                    <?php
                                }
                                else
                                {
                                    ?>
                                    <div class="creditcardbox-list">
                                        <h4><?php echo __("website/account_info/stored-cards-3"); ?></h4>
                                    </div>
                                    <?php
                                }
                            ?>


                        </div>

                        <?php
                            if(isset($refund_support) && $refund_support)
                            {
                                ?>
                                <h3 class="ui-accordion-header ui-corner-top ui-state-default ui-accordion-icons" role="tab" id="ui-id-1" aria-controls="ui-id-2" aria-selected="true" aria-expanded="true" tabindex="0"><span class="ui-accordion-header-icon ui-icon ui-icon-triangle-1-s"></span><strong><?php echo __("website/account_info/stored-cards-2"); ?></strong></h3>
                                <div class="ui-accordion-content ui-corner-bottom ui-helper-reset ui-widget-content ui-accordion-content-active" id="ui-id-2" aria-labelledby="ui-id-1" role="tabpanel" aria-hidden="false" style="display: block;">
                                    <div class="creditcardpaypage">

                                        <div class="creditcardinfo">

                                            <?php
                                                echo isset($c_s_m_content) && $c_s_m_content ? $c_s_m_content : '';
                                            ?>

                                        </div>
                                    </div>


                                </div>
                                <?php
                            }
                        ?>

                    </div>





                </div>
            </div>
            <?php
        }
    ?>

    <?php
        if(Config::get("options/gdpr-status"))
        {
            ?>
            <div id="gdpr" class="tabcontent">

                <script type="text/javascript">
                    $(document).ready(function(){
                        $("#contract2").change(function(){
                            var request = MioAjax({
                                action: "<?php echo $operation_link; ?>",
                                method: "POST",
                                data:{"operation" : "update_gdpr_status",status: $("#contract2").prop('checked') ? 1 : 0}
                            },true,true);
                            request.done(function(result){
                                if(result != ''){
                                    var solve = getJson(result);
                                    if(solve !== false){
                                        if(solve.status === "error")
                                            alert_error(solve.message,{timer:3000});
                                        else if(solve.status === "successful")
                                            window.location.href = '<?php echo $operation_link; ?>?tab=gdpr';
                                    }
                                }
                            });
                        });
                    });
                    function gdpr_request(btn,action)
                    {
                        var request = MioAjax({
                            button_element: btn,
                            waiting_text:"<?php echo addslashes(__("website/others/button1-pending")); ?>",
                            action: "<?php echo $operation_link; ?>",
                            method: "POST",
                            data:{
                                operation: "gdpr_request",
                                token: "<?php echo Validation::get_csrf_token('account',false); ?>",
                                action: action
                            }
                        },true,true);
                        request.done(function(result){
                            if(result !== ''){
                                var solve = getJson(result);
                                if(solve !== false){
                                    if(solve.status === "error")
                                        alert_error(solve.message,{timer:4000});
                                    else if(solve.status === "successful")
                                        window.location.href = '<?php echo $operation_link; ?>?tab=gdpr';
                                }
                            }
                        });
                    }
                </script>

                <div class="tabcontentcon">

                    <div class="green-info" style="margin-bottom:25px;">
                        <div class="padding20">
                            <i class="fas fa-user-shield"></i>
                            <p><strong><?php echo __("website/account_info/gdpr-tx1"); ?></strong><br>
                                <?php echo __("website/account_info/gdpr-tx2"); ?></p>
                        </div>
                    </div>

                    <?php
                        if($requiredFields["gdpr"] ?? [])
                        {
                            ?>
                            <div class="red-info">
                                <div class="padding15">
                                    <i class="fa fa-info-circle" aria-hidden="true"></i>
                                    <h5><strong><?php echo __("website/account_info/required-field-title"); ?></strong></h5>
                                    <p style="margin:10px 0px;"><?php echo __("website/account_info/required-field"); ?></p>
                                    <?php
                                        foreach($requiredFields["gdpr"] AS $key => $fv)
                                        {
                                            $message        = $fv["message"];
                                            $element_id     = $fv["element_id"];

                                            ?>
                                            <p><strong>- <?php echo $message; ?></strong></p>

                                        <?php if($element_id): ?>
                                            <script type="text/javascript">
                                                $(document).ready(function(){
                                                    $("<?php echo $element_id; ?>").attr("style","border-bottom:2px solid red; color:red;");
                                                    $("<?php echo $element_id; ?>").closest("tr").attr("style","color:red;");
                                                    $("<?php echo $element_id; ?>").change(function(){
                                                        $(this).removeAttr("style");
                                                        $(this).closest("tr").removeAttr("style");
                                                    });
                                                });
                                            </script>
                                        <?php endif; ?>
                                            <?php
                                        }
                                    ?>
                                    <div class="clear"></div>
                                </div>

                            </div>
                            <?php
                        }
                    ?>


                    <div class="formcon">
                        <div class="yuzde30"><?php echo __("website/account_info/gdpr-tx3"); ?></div>
                        <div class="yuzde70">
                            <div class="yuzde70">
                                <input<?php echo ($udata["contract2"] ?? 0)  > 0 ? ' checked' : '' ; ?> type="checkbox" class="sitemio-checkbox" name="contract2" value="1" id="contract2">
                                <label class="sitemio-checkbox-label" for="contract2"></label>
                                <span class="kinfo"><?php echo __("website/account_info/gdpr-tx4",['{link}' => Controllers::$init->CRLink("contract2")]); ?></span>


                                <?php
                                    if($udata["contract2"])
                                    {
                                        ?>
                                        <div class="clear"></div>
                                        <div class="line"></div>
                                        <a style="float:left;" data-tooltip="<?php echo __("admin/users/gdpr-tx36"); ?>"> <img src="<?php echo $sadress; ?>assets/images/gdpr_compliant-ok.svg" style="    height: 40px;    float: left;    margin-right: 15px;"></a>
                                        <span class="kinfo" style="color: #4dbc70;margin-top: 9px;float: left;"><?php echo __("website/account_info/gdpr-tx5",['{date}' => UserManager::formatTimeZone($udata["contract2_updated_at"] ?? '',$timezone,Config::get("options/date-format")." - H:i")]); ?></span>
                                        <?php
                                    }

                                    if(!$udata["contract2"] && Config::get("options/gdpr-required"))
                                    {
                                        ?>
                                        <div class="clear"></div>
                                        <div class="line"></div>
                                        <span class="kinfo" style="color: #f44336;font-weight:600;"><i class="fas fa-exclamation-circle"></i> <?php echo __("website/account_info/gdpr-tx6"); ?></span>
                                        <?php
                                    }
                                ?>
                            </div>
                        </div>
                    </div>

                    <?php
                        if(!(Config::get("options/gdpr-required") && !$udata["contract2"]))
                        {
                            ?>
                            <div class="formcon">
                                <div class="yuzde30"><?php echo __("website/account_info/gdpr-tx7"); ?></div>
                                <div class="yuzde70">
                                    <div class="yuzde70">
                                        <a style="    margin-bottom: 10px;" class="lbtn" target="_blank" href="<?php echo $operation_link; ?>?operation=download_personal_data_gdpr&token=<?php echo Validation::get_csrf_token('account',false); ?>&format=xml"><i class="fa fa-cloud-download" aria-hidden="true"></i> <strong><?php echo __("website/account_info/gdpr-tx8"); ?></strong></a><div class="clear"></div>
                                        <span class="kinfo"><?php echo __("website/account_info/gdpr-tx9"); ?><BR>(<?php echo __("website/account_info/gdpr-tx10"); ?>) </span>

                                        <?php
                                            if($udata["gdpr_downloaded_at"])
                                            {
                                                ?>
                                                <div class="clear"></div>
                                                <div class="line"></div>
                                                <span class="kinfo" style="color: #4dbc70;"><i class="fas fa-info-circle"></i> <?php echo __("website/account_info/gdpr-tx11",['{date}' => UserManager::formatTimeZone($udata["gdpr_downloaded_at"] ?? '',$timezone,Config::get("options/date-format")." - H:i") ]); ?></span>
                                                <?php
                                            }
                                        ?>
                                    </div>
                                </div>
                            </div>

                            <div class="formcon">
                                <div class="yuzde30"><?php echo __("website/account_info/gdpr-tx12"); ?></div>
                                <div class="yuzde70">
                                    <div class="yuzde70">
                                        <span class="kinfo"><?php echo __("website/account_info/gdpr-tx13"); ?></span>
                                        <div style="margin-bottom:10px;" class="clear"></div>
                                        <?php
                                            if(isset($gdpr_request) && $gdpr_request && $gdpr_request["status"] != "cancelled")
                                            {
                                                ?>
                                                <div class="clear"></div>
                                                <div class="line"></div>
                                                <span class="kinfo" style="color: #f44336;    margin-bottom: 10px;    float: left;"><i class="fas fa-exclamation-triangle"></i> <?php echo __("website/account_info/gdpr-tx16",['{date}' => UserManager::formatTimeZone($gdpr_request["created_at"] ?? '',$timezone,Config::get("options/date-format")." - H:i"),'{type}' => $gdpr_request["type"] == "remove" ? __("website/account_info/gdpr-tx14") : __("website/account_info/gdpr-tx15")]); ?></span><div class="clear"></div>
                                                <a href="javascript:void 0;" onclick="gdpr_request(this,'cancel');" class="lbtn"><strong><?php echo __("website/account_info/gdpr-tx23"); ?></strong></a>
                                                <?php
                                            }
                                            else
                                            {
                                                ?><div class="clear"></div>
                                                <a class="lbtn red" href="javascript:open_modal('gdpr_remove_modal',{headerColor:'red'}); void 0;"><i class="fa fa-trash" aria-hidden="true"></i> <strong><?php echo __("website/account_info/gdpr-tx14"); ?></strong></a>
                                                <a class="lbtn" href="javascript:open_modal('gdpr_anonymize_modal'); void 0;"><i class="fa fa-ban" aria-hidden="true"></i> <strong><?php echo __("website/account_info/gdpr-tx15"); ?></strong></a>

                                                <?php
                                                if(isset($gdpr_request) && $gdpr_request && Utility::strlen($gdpr_request["status_note"]) > 1)
                                                {
                                                    ?>
                                                    <div class="clear"></div>
                                                    <div class="line"></div>
                                                    <span class="kinfo"><strong style="color: #f44336;"><i class="fa fa-exclamation-circle"></i> <?php echo __("website/account_info/gdpr-tx25"); ?></strong> <br>
                                                       <strong><?php echo __("website/account_info/gdpr-tx26"); ?>:</strong> <?php echo $gdpr_request["status_note"]; ?></span>
                                                    <?php
                                                }
                                            }
                                        ?>
                                    </div>
                                </div>
                            </div>
                            <?php
                        }
                    ?>
                </div>
            </div>
            <?php
        }
    ?>

    <div class="clear"></div>
</div>

<?php
    if(Config::get("options/gdpr-status"))
    {
        ?>
        <div id="gdpr_remove_modal" style="display:none;"  data-iziModal-title="<?php echo __("website/account_info/gdpr-tx17"); ?>">
            <div class="padding30" style="text-align:center;">
                <p><?php echo __("website/account_info/gdpr-tx19"); ?></p>
                <p><?php echo __("website/account_info/gdpr-tx20"); ?></p>
            </div>
            <div class="modal-foot-btn">
                <a href="javascript:void 0;" onclick="gdpr_request(this,'remove');" class="red lbtn"><?php echo __("website/account_info/gdpr-tx21"); ?></a>
            </div>
        </div>
        <div id="gdpr_anonymize_modal" style="display:none;"  data-iziModal-title="<?php echo __("website/account_info/gdpr-tx18"); ?>">
            <div class="padding30" style="text-align:center;">
                <p><?php echo __("website/account_info/gdpr-tx22"); ?></p>
                <p><?php echo __("website/account_info/gdpr-tx20"); ?></p>
            </div>
            <div class="modal-foot-btn">
                <a href="javascript:void 0;" onclick="gdpr_request(this,'anonymize');" class="lbtn"><?php echo __("website/account_info/gdpr-tx21"); ?></a>
            </div>
        </div>

        <?php
    }
?>

<style>
    #gdpr .kinfo {font-size:14px;}
</style>