<?php defined('CORE_FOLDER') OR exit('You can not get in here!'); ?>
<script type="text/javascript">
    var update_online_link  = "<?php echo Controllers::$init->CRLink("my-account"); ?>";
    var is_logged = <?php echo UserManager::LoginCheck("member") ? "true" : "false"; ?>;
    var warning_modal_title = "<?php echo ___("needs/warning-modal-title",['"' => '\"']); ?>";
    var success_modal_title = "<?php echo ___("needs/success-modal-title",['"' => '\"']); ?>";
</script>
<script src="<?php echo $baddress."assets/plugins/iziModal/js/iziModal.min.js?v=".License::get_version();?>"></script>
<script src="<?php echo $baddress."assets/plugins/sweetalert2/dist/promise.min.js";?>"></script>
<script src="<?php echo $baddress."assets/plugins/sweetalert2/dist/sweetalert2.min.js";?>"></script>
<script src="<?php echo $baddress."assets/javascript/jquery.form.min.js"; ?>"></script>
<script src="<?php echo $baddress."assets/javascript/webmio.js?v=".License::get_version(); ?>"></script>