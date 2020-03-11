<?php
    defined('CORE_FOLDER') OR exit('You can not get in here!');
    $hoptions = ["datatables"];
?>
<script type="text/javascript">
        $(document).ready(function(){

            if(gGET("from") != null && gGET("status") != null){
                var url = sGET("status",'');
                history.pushState(false, "", url);
            }


            $('#invoices').DataTable({
                "columnDefs": [
                    {
                        "targets": [5],
                        "orderable": false,
                        "searchable": false
                    }

                ],
                "aaSorting" : [[4, 'asc']],
                "lengthMenu": [
                    [10, 25, 50, -1], [10, 25, 50, "<?php echo __("website/others/datatable-all"); ?>"]
                ],
                "bProcessing": true,
                "bServerSide": true,
                "sAjaxSource": "<?php echo $links["ajax"]; ?>",
                responsive: true,
                "language":{"url":"<?php echo APP_URI; ?>/<?php echo ___("package/code"); ?>/datatable/lang.json"}
            });
        });
    </script>

<?php if(Filter::GET("from") == "bulk_payment" && Filter::GET("status") == "success"): ?>
    <script>
        swal(
            '<?php echo __("website/account_invoices/successx"); ?>',
            '<?php echo __("website/account_invoices/success"); ?>',
            'success'
        )
    </script>

<?php elseif(Filter::GET("from") == "bulk_payment" && Filter::GET("status") == "fail"): ?>
    <script>
        swal(
            '<?php echo __("website/account_invoices/errorx"); ?>',
            '<?php echo __("website/account_invoices/error2"); ?>',
            'error'
        )
    </script>
<?php endif; ?>

<div class="mpanelrightcon">

    <div class="mpaneltitle">
        <div class="sayfayolu"><?php include __DIR__.DS."inc/panel-breadcrumb.php"; ?></div>
        <h4><strong><i class="fa fa-file-text-o"></i> <?php echo __("website/account_invoices/page-title"); ?></strong></h4>

        <?php
            if(isset($statistic3) && $statistic3>0){
                ?>
                <a href="<?php echo Controllers::$init->CRLink("ac-ps-invoices-p",["bulk-payment"]); ?>" class="incelebtn" style="margin-left:20px;"><i class="fa fa-check"></i><?php echo __("website/account/remind-invoice-text3"); ?></a>
                <?php
            }
        ?>
    </div>

    <div class="faturalarim">
        <table id="invoices" class="table table-striped table-borderedx table-condensed nowrap">
            <thead style="background:#ebebeb;">
            <tr>
                <th align="center"><?php echo __("website/account_invoices/invoice-num"); ?></th>
                <th align="center"><?php echo __("website/account_invoices/creation-date"); ?></th>
                <th align="center"><?php echo __("website/account_invoices/due-date"); ?></th>
                <th align="center"><?php echo __("website/account_invoices/invoice-amount"); ?></th>
                <th align="center"><?php echo __("website/account_invoices/invoice-status"); ?></th>
                <th width="10%" align="center"><?php echo __("website/account_invoices/invoice-operation"); ?></th>
            </tr>
            </thead>
            <tbody align="center" style="border-top:none;"></tbody>
        </table>
    </div>

    <div class="clear"></div>
</div>
