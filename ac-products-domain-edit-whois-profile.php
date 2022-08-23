<?php defined('CORE_FOLDER') OR exit('You can not get in here!');
    include $tpath."common-needs.php";
    $hoptions = ["datatables"];
?>
<script type="text/javascript">
    $(document).ready(function(){

    });

    function editForm_handler(result)
    {
        if(result != ''){
            var solve = getJson(result);
            if(solve !== false){
                if(solve.status == "error"){
                    alert_error(solve.message,{timer:3000});
                }
                else if(solve.status === "successful")
                {
                    alert_success(solve.message,{timer:2000});
                    setTimeout(function(){
                        window.location.href = "<?php echo $links["controller"]."?page=whois_profiles"; ?>";
                    },2000);
                }
            }else
                console.log(result);
        }
    }
</script>

<div class="mpanelrightcon">

    <div class="mpaneltitle">
        <div class="sayfayolu"><?php include $tpath."inc".DS."panel-breadcrumb.php"; ?></div>
        <h4><strong><i class="far fa-id-card" aria-hidden="true"></i> <?php echo $page_title; ?></strong></h4>
    </div>

    <div class="tabcontentcon ModifyWhois">
    <form action="<?php echo $links["controller"]; ?>" method="post" id="editForm">
        <input type="hidden" name="operation" value="edit_whois_profile">
        <input type="hidden" name="id" value="<?php echo $profile["id"]; ?>">

        <div class="formcon">
            <div class="yuzde30"><?php echo __("website/account_products/domain-whois-tx7"); ?></div>
            <div class="yuzde70">
                <input type="text" name="profile_name" placeholder="<?php echo __("website/account_products/domain-whois-tx7"); ?>" value="<?php echo htmlentities($profile["name"]); ?>">
            </div>
        </div>


        <input name="Name" value="<?php echo $profile["information"]["Name"] ?? ''; ?>" type="text" class="yuzde33" placeholder="<?php echo __("website/account_products/whois-full_name"); ?>">
        <input name="Company" value="<?php echo $profile["information"]["Company"] ?? ''; ?>" type="text" class="yuzde33" placeholder="<?php echo __("website/account_products/whois-company_name"); ?>">
        <input name="EMail" value="<?php echo $profile["information"]["EMail"] ?? ''; ?>" type="text" class="yuzde33" placeholder="<?php echo __("website/account_products/whois-email"); ?>">
        <input name="PhoneCountryCode" value="<?php echo $profile["information"]["PhoneCountryCode"] ?? ''; ?>" type="text" class="yuzde33" placeholder="<?php echo __("website/account_products/whois-phoneCountryCode"); ?>">
        <input name="Phone" type="text" value="<?php echo $profile["information"]["Phone"] ?? ''; ?>" class="yuzde33" placeholder="<?php echo __("website/account_products/whois-phone"); ?>">
        <input name="FaxCountryCode" type="text" value="<?php echo $profile["information"]["FaxCountryCode"] ?? ''; ?>" class="yuzde33" placeholder="<?php echo __("website/account_products/whois-faxCountryCode"); ?>">
        <input name="Fax" type="text" value="<?php echo $profile["information"]["Fax"] ?? ''; ?>" class="yuzde33" placeholder="<?php echo __("website/account_products/whois-fax"); ?>">
        <input name="City" type="text" value="<?php echo $profile["information"]["City"] ?? ''; ?>" class="yuzde33" placeholder="<?php echo __("website/account_products/whois-city"); ?>">
        <input name="State" type="text" value="<?php echo $profile["information"]["State"] ?? ''; ?>" class="yuzde33" placeholder="<?php echo __("website/account_products/whois-state"); ?>">
        <input name="Address" type="text" value="<?php echo $profile["information"]["Address"] ?? ''; ?>" class="yuzde33" placeholder="<?php echo __("website/account_products/whois-address"); ?>">
        <input name="Country" type="text" value="<?php echo $profile["information"]["Country"] ?? ''; ?>" class="yuzde33" placeholder="<?php echo __("website/account_products/whois-CountryCode"); ?>">
        <input name="ZipCode" type="text" value="<?php echo $profile["information"]["ZipCode"] ?? ''; ?>" class="yuzde33" placeholder="<?php echo __("website/account_products/whois-zipcode"); ?>">

        <div class="line"></div>
        <div style="margin-top: 5px;display: inline-block;">
            <input<?php echo $profile["detouse"] ? ' checked' : ''; ?> type="checkbox" name="detouse" value="1" class="checkbox-custom" id="detouse">
            <label class="checkbox-custom-label" for="detouse"><?php echo __("website/account_info/stored-cards-6"); ?></label>
        </div>




        <div class="line"></div>

        <div style="float: right;" class="guncellebtn yuzde30">
            <a href="javascript:void(0);" class="yesilbtn gonderbtn mio-ajax-submit" mio-ajax-options='{"result":"editForm_handler","waiting_text":"<?php echo addslashes(__("website/others/button1-pending")); ?>"}'><?php echo ___("needs/button-save"); ?></a>
        </div>

    </form>
    </div>

    <div class="clear"></div>
</div>