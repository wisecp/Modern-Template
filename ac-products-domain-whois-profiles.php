<?php defined('CORE_FOLDER') OR exit('You can not get in here!');
    include $tpath."common-needs.php";
    $hoptions = ["datatables"];
?>
<script type="text/javascript">
    var table1 = null;
    $(document).ready(function(){

        table1 =  $('#table1').DataTable({
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

        $(".delete").click(function(){
            var btn         = $(this);
            var btn_h       = btn.html();
            var id          = btn.data("id");

            if(!confirm("<?php echo ___("needs/delete-are-you-sure"); ?>")) return false;

            btn.html('<i class="fa fa-spinner" style="-webkit-animation:fa-spin 2s infinite linear;animation: fa-spin 2s infinite linear;" aria-hidden="true"></i>');

            var request = MioAjax({
                action:"<?php echo $links["controller"]; ?>",
                method:"POST",
                data:{operation:"delete_whois_profile",id:id}
            },true,true);

            request.done(function(result){
                btn.html(btn_h);

                if(result !== ''){
                    var solve = getJson(result);
                    if(solve !== false){
                        if(solve.status === "error")
                            alert_error(solve.message,{timer:3000});
                        else if(solve.status === "successful")
                        {
                            alert_success(solve.message,{timer:2000});

                            var row = table1.row( btn.parents('tr'));
                            var rowNode = row.node();
                            row.remove();
                            table1.draw();
                        }
                    }else
                        console.log(result);
                }

            });


        });

    });
</script>



<div class="mpanelrightcon">

    <div class="mpaneltitle">
        <div class="sayfayolu"><?php include $tpath."inc".DS."panel-breadcrumb.php"; ?></div>
        <h4><strong><i class="far fa-id-card" aria-hidden="true"></i> <?php echo $page_title; ?></strong></h4>
    </div>

    <a style="float:left;" href="<?php echo $links["create"]; ?>" class="lbtn"><i style="margin-right: 5px;" class="fas fa-plus"></i> <?php echo ___("needs/button-create"); ?></a>


    <table width="100%" class="table table-striped table-borderedx table-condensed nowrap" id="table1">
        <thead style="background:#ebebeb;">
        <tr>
            <th align="left">#</th>
            <th align="left"><?php echo __("website/account_products/domain-whois-tx7"); ?></th>
            <th align="left"><?php echo __("website/account_products/domain-whois-tx8"); ?></th>
            <th align="left"><?php echo __("website/account_products/domain-whois-tx9"); ?></th>
            <th align="left"><?php echo __("website/account_products/domain-whois-tx10"); ?></th>
            <th align="center"></th>
        </tr>
        </thead>
        <tbody align="center" style="border-top:none;">
        <?php
            if(isset($list) && $list)
            {
                foreach($list AS $i => $row)
                {
                    ?>
                    <tr id="row_<?php echo $row["id"]; ?>">
                        <td><?php echo $i; ?></td>
                        <td align="left"><?php echo $row["name"].($row["detouse"] ? ' <strong>('.___("needs/default").')</strong>' : ''); ?></td>
                        <td align="left"><?php echo $row["person_name"]; ?></td>
                        <td align="left"><?php echo $row["person_email"]; ?></td>
                        <td align="left"><?php echo $row["person_phone"]; ?></td>
                        <td align="center">
                            <a data-tooltip="<?php echo ___("needs/button-edit"); ?>" href="<?php echo $links["controller"]; ?>?page=edit_whois_profile&id=<?php echo $row["id"]; ?>" class="sbtn" style="margin-right: 5px;"><i class="fa fa-pencil"></i></a>
                            <a data-tooltip="<?php echo ___("needs/button-delete"); ?>" href="javascript:void 0;" class="sbtn red delete" data-id="<?php echo $row["id"]; ?>" title="<?php echo ___("needs/button-delete"); ?>"><i class="fa fa-trash"></i></a>
                        </td>
                    </tr>
                    <?php
                }
            }
        ?>
        </tbody>
    </table>


    <div class="clear"></div>
</div>