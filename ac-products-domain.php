<?php defined('CORE_FOLDER') OR exit('You can not get in here!');
    include $tpath."common-needs.php";
    $hoptions = ["datatables"];
?>
<script type="text/javascript">
    $(document).ready(function(){
        $('#products').DataTable({
            "columnDefs": [
                {
                    "targets": [ 0 ],
                    "visible": false,
                    "searchable": false
                },
            ],
            "lengthMenu": [
                [10, 25, 50, -1], [10, 25, 50, "<?php echo __("website/others/datatable-all"); ?>"]
            ],
            responsive: true,
            "oLanguage":<?php include __DIR__.DS."datatable-lang.php"; ?>
        });

        $("#default-nameserver").on("click","#ModifyDefaultNameserver_submit",function(){
            MioAjaxElement($(this),{
                waiting_text: '<?php echo __("website/others/button1-pending"); ?>',
                result:"ModifyDefaultNameserver_handler",
            });
        });

    });

    function ModifyDefaultNameserver_handler(result){
        if(result != ''){
            var solve = getJson(result);
            if(solve !== false){
                if(solve.status === "error")
                    alert_error(solve.message,{timer:5000});
                else if(solve.status === "successful")
                {
                    alert_success(solve.message,{timer:2000});
                    setTimeout(function(){
                        window.location.href = '<?php echo $links["controller"]; ?>';
                    },2000);
                }
            }else
                console.log(result);
        }
    }
</script>

<style type="text/css">
    #products tbody tr td:nth-child(2) {
        text-align: left;
    }
</style>

<div id="default-nameserver" data-izimodal-title="<?php echo __("website/account_products/domain-nameservers-tx1"); ?>" style="display: none">
    <form action="<?php echo $links["controller"]; ?>" method="post" id="ModifyDefaultNameserver">
        <input type="hidden" name="operation" value="modify_default_nameserver">

        <div class="padding20">

            <div class="blue-info">
                <div class="padding15">
                    <i class="fas fa-info-circle"></i>
                    <p><?php echo __("website/account_products/domain-nameservers-tx2"); ?></p>
                </div>
            </div>

            <div class="red-info">
                <div class="padding15">
                    <i class="fas fa-exclamation-circle"></i>
                    <p><?php echo __("website/account_products/domain-nameservers-tx3"); ?></p>
                </div>
            </div>

            <div style="width:100%;text-align:center;">
                <div class="clear"></div>
                <?php
                    for($i=0;$i<=3; $i++)
                    {
                        $ix = $i+1;
                        ?>
                        <input name="values[]" value="<?php echo $default_nameserver[$i] ?? ''; ?>" type="text" class="" placeholder="ns<?php echo $ix; ?>.example.com">
                        <div class="clear"></div>
                        <?php
                    }
                ?>
            </div>
        </div>
        <div class="modal-foot-btn">
            <a href="javascript:void(0);" class="green lbtn" id="ModifyDefaultNameserver_submit"><?php echo ___("needs/button-update"); ?></a>
        </div>
    </form>
</div>


<div class="mpanelrightcon">

    <div class="mpaneltitle">
        <div class="sayfayolu"><?php include $tpath."inc".DS."panel-breadcrumb.php"; ?></div>
        <h4><strong><i class="fas fa-globe"></i> <?php echo $page_title; ?></strong></h4>
    </div>

    <a href="<?php echo $links["whois-profiles"]; ?>" class="green lbtn"><i style="margin-right: 5px;" class="far fa-id-card"></i> <?php echo __("website/account_products/domain-whois-tx6"); ?></a>
    <a href="javascript:open_modal('default-nameserver');" class="blue lbtn"><i style="margin-right: 5px;" class="fas fa-server"></i> <?php echo __("website/account_products/domain-nameservers-tx1"); ?></a>

    <div class="clear"></div>


    <div class="ac-domainlist-status">
        <a<?php echo !$filter_status ? ' class="active"' : ''; ?> href="<?php echo $links["controller"].(stristr($links["controller"],"?") ? "&" : "?"); ?>filter_status="><?php echo __("website/account_products/list-status-all"); ?> (<strong><?php echo $filter_counts["all"] ?? 0; ?></strong>)</a>
        <a<?php echo $filter_status == "waiting" ? ' class="active"' : ''; ?> href="<?php echo $links["controller"].(stristr($links["controller"],"?") ? "&" : "?"); ?>filter_status=waiting"><?php echo __("website/account_products/list-status-waiting"); ?> (<strong><?php echo $filter_counts["waiting"] ?? 0; ?></strong>)</a>
        <a<?php echo $filter_status == "active" ? ' class="active"' : ''; ?> href="<?php echo $links["controller"].(stristr($links["controller"],"?") ? "&" : "?"); ?>filter_status=active"><?php echo __("website/account_products/list-status-active"); ?> (<strong><?php echo $filter_counts["active"] ?? 0; ?></strong>)</a>
        <a<?php echo $filter_status == "upcoming30" ? ' class="active"' : ''; ?> href="<?php echo $links["controller"].(stristr($links["controller"],"?") ? "&" : "?"); ?>filter_status=upcoming30"><?php echo __("website/account_products/list-status-upcoming30"); ?> (<strong><?php echo $filter_counts["upcoming30"] ?? 0; ?></strong>)</a>
        <a<?php echo $filter_status == "inprocess" ? ' class="active"' : ''; ?> href="<?php echo $links["controller"].(stristr($links["controller"],"?") ? "&" : "?"); ?>filter_status=inprocess"><?php echo __("website/account_products/list-status-inprocess"); ?> (<strong><?php echo $filter_counts["inprocess"] ?? 0; ?></strong>)</a>
        <a<?php echo $filter_status == "suspended" ? ' class="active"' : ''; ?> href="<?php echo $links["controller"].(stristr($links["controller"],"?") ? "&" : "?"); ?>filter_status=suspended"><?php echo __("website/account_products/list-status-suspended"); ?> (<strong><?php echo $filter_counts["suspended"] ?? 0; ?></strong>)</a>
        <a<?php echo $filter_status == "cancelled" ? ' class="active"' : ''; ?> href="<?php echo $links["controller"].(stristr($links["controller"],"?") ? "&" : "?"); ?>filter_status=cancelled"><?php echo __("website/account_products/list-status-cancelled"); ?> (<strong><?php echo $filter_counts["cancelled"] ?? 0; ?></strong>)</a>
    </div>

    <?php if($list): ?>
        <table width="100%" class="table table-striped table-borderedx table-condensed nowrap" id="products" style="<?php echo $list ? '' : 'display:none;'; ?>">
            <thead style="background:#ebebeb;">
            <tr>
                <th align="left">#</th>
                <th align="center"><?php echo __("website/account_products/table-ordernum"); ?></th>
                <th align="left"><?php echo __("website/account_products/table-field1"); ?></th>
                <th align="center"><?php echo __("website/account_products/table-field2"); ?></th>
                <th align="center"><?php echo __("website/account_products/table-field3"); ?></th>
                <th align="center"><?php echo __("website/account_products/table-field4"); ?></th>
                <th align="center"><?php echo __("website/account_products/table-field5"); ?></th>
            </tr>
            </thead>
            <tbody align="center" style="border-top:none;">
            <?php
                if($list){
                    foreach($list AS $r=>$row){
                        $amount = Money::formatter_symbol($row["amount"],$row["amount_cid"]);
                        $period = View::period($row["period_time"],$row["period"]);
                        $duedate = $row["duedate"];
                        if(in_array(substr($duedate,0,4),['1881','1970'])) $duedate = '1970';
                        $duedate_format = $duedate == "1970" ? ' - ' : DateManager::format(Config::get("options/date-format"),$duedate);
                        ?>
                        <tr>
                            <td align="left"><?php echo $r; ?></td>
                            <td align="center"><?php echo $row["id"]; ?></td>
                            <td align="left">
                                <strong><?php echo $row["name"]; ?></strong>
                            </td>
                            <td align="center">
                                <?php echo $amount; ?> <?php echo $period; ?>
                            </td>
                            <td align="center">
                                <span style="display: none;"><?php echo DateManager::format("Y-m-d",$duedate); ?></span>
                                <?php echo $duedate_format; ?>
                            </td>
                            <td align="center"><?php echo $situations[$row["status"]]; ?></td>
                            <td align="center">
                                <?php
                                    if($row["status"] == "waiting" || $row["status"] == "cancelled" || $row["status"] == "inactive")
                                    {
                                        ?>
                                        <a title="<?php echo __("website/account_products/manage"); ?>" style="-webkit-filter:grayscale(100%);filter: grayscale(100%);color: #777;opacity: 0.5;filter: alpha(opacity=50);" class="incelebtn"><i class="fa fa-cog" aria-hidden="true"></i> <?php echo __("website/account_products/manage"); ?></a>
                                        <?php
                                    }else{
                                        ?>
                                        <a href="<?php echo $row["detail_link"]; ?>" title="<?php echo __("website/account_products/manage"); ?>" class="incelebtn"><i class="fa fa-cog" aria-hidden="true"></i> <?php echo __("website/account_products/manage"); ?></a>
                                        <?php
                                    }
                                ?>
                            </td>
                        </tr>
                        <?php
                    }
                }
            ?>
            </tbody>
        </table>
    <?php else: ?>
        <?php if($filter_status): ?>
            <div id="orders-none">
                <i style="font-size:70px;" class="fa fa-info-circle"></i><br><br>
                <h5> <?php echo __("website/account_products/domain-filter-no-results"); ?><h5>
            </div>
        <?php else: ?>
            <div id="orders-none">
                <i style="font-size:70px;" class="fa fa-info-circle"></i><br><br>
                <h5> <?php echo __("website/account_products/empty-list-domain"); ?><h5>
            </div>
        <?php endif; ?>
    <?php endif; ?>

    <div class="clear"></div>
</div>