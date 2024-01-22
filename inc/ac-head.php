<?php
    defined('CORE_FOLDER') OR exit('You can not get in here!');
    $suffix         = __("website/index/meta/title-suffix");
    $home_title     = __("website/index/meta/title");

    if(strlen($suffix) > 1)
        $meta["title"] = str_replace(['{home_title}','{page_title}'],[$home_title,$meta["title"]],$suffix);
?>
<!-- Meta Tags -->
<?php View::main_meta(); ?>
<title><?php echo $meta["title"]; ?></title>
<meta http-equiv="content-language" content="<?php echo ___("package/code");?>">
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no" />
<meta name="robots" content="NOINDEX,NOFOLLOW" />
<link rel="canonical" href="<?php echo $canonical_link;?>" />
<link rel="icon" type="image/x-icon" href="<?php echo $favicon_link;?>" />
<meta name="theme-color" content="<?php echo $meta_color; ?>">
<?php if(isset($page) && isset($page["mockup"]) && $page["mockup"] != ''): ?>
    <meta property="og:image" content="<?php echo $page["mockup"]; ?>">
<?php endif; ?>
<!-- Meta Tags -->

<!-- Css -->
<?php
    View::main_style();
?>
<link rel="stylesheet" href="<?php echo $_theme->get_css_url(); ?>"/>
<link rel="stylesheet" href="<?php echo $tadress;?>css/font-awesome.min.css">
<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.15.4/css/all.css">
<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.15.4/css/v4-shims.css">
<link rel="stylesheet" href="<?php echo $tadress;?>css/ionicons.min.css"/>
<link rel="stylesheet" href="<?php echo $tadress;?>css/animate.css" media="none" onload="if(media!='all')media='all'">
<link rel="stylesheet" href="<?php echo $tadress;?>css/aos.css" />
<?php if(isset($hoptions) && in_array("jquery-ui",$hoptions)): ?>
<link rel="stylesheet" href="<?php echo $sadress;?>assets/plugins/css/jquery-ui.css">
<?php endif; ?>
<?php if(isset($hoptions) && in_array("datatables",$hoptions)): ?><link rel="stylesheet" href="<?php echo $sadress;?>assets/plugins/dataTables/css/jquery.dataTables.min.css" />
<link rel="stylesheet" href="<?php echo $sadress;?>assets/plugins/dataTables/css/dataTables.responsive.min.css" />
<?php endif; ?>
<?php if(isset($hoptions) && in_array("iziModal",$hoptions)): ?><link rel="stylesheet" type="text/css" href="<?php echo $sadress; ?>assets/plugins/iziModal/css/iziModal.min.css?v=<?php echo License::get_version(); ?>">
<?php endif; ?>
<?php if(isset($hoptions) && in_array("select2",$hoptions)): ?><link rel="stylesheet" href="<?php echo $sadress; ?>assets/plugins/select2/css/select2.min.css">
<?php endif; ?>
<?php if(isset($hoptions) && in_array("ion.rangeSlider",$hoptions)): ?><link rel="stylesheet" href="<?php echo $sadress; ?>assets/plugins/ion.rangeSlider/css/ion.rangeSlider.min.css">
<?php endif; ?>
<?php if(___("package/rtl")): ?><link rel="stylesheet" href="<?php echo $sadress."assets/style/theme-rtl.css?v=".License::get_version();?>&lang=<?php echo Bootstrap::$lang->clang; ?>">
<?php endif; ?>
<link rel="stylesheet" href="<?php echo $sadress; ?>assets/style/theme-default.css?v=<?php echo License::get_version(); ?>"  type="text/css">
<!-- Css -->

<!-- Js -->
<script>
    var template_address = "<?php echo $tadress;?>";
</script>
<script src="<?php echo $tadress;?>js/jquery-2.2.4.min.js"></script>
<script src="<?php echo $tadress;?>js/jquery-stick<?php echo ($header_type==2) ? '-header2' : ''; ?>.js"></script>

<?php if(isset($hoptions) && in_array("jsPDF",$hoptions)): ?>
    <script type="text/javascript" src="<?php echo $sadress;?>assets/plugins/jsPDF/jspdf.debug.js"></script>
    <script type="text/javascript" src="<?php echo $sadress;?>assets/plugins/jsPDF/jspdf.min.js"></script>
<?php endif; ?>
<?php if(isset($hoptions) && in_array("jquery-ui",$hoptions)): ?>
    <script src="<?php echo $sadress;?>assets/plugins/js/jquery-ui.js"></script>
    <script type="text/javascript" src="<?php echo $sadress;?>assets/plugins/js/i18n/datepicker-<?php echo ___("package/code"); ?>.js"></script>
<?php endif; ?>
<?php if(isset($hoptions) && in_array("jquery-mask",$hoptions)): ?>
    <script src="<?php echo $sadress;?>assets/plugins/js/jquery.mask.min.js"></script>
<?php endif; ?>
<?php if(isset($hoptions) && in_array("datatables",$hoptions)): ?>
<script src="<?php echo $sadress;?>assets/plugins/dataTables/js/jquery.dataTables.min.js" type="text/javascript"></script>
<script src="<?php echo $sadress;?>assets/plugins/dataTables/js/dataTables.responsive.min.js" type="text/javascript"></script>
<script type="text/javascript">
$(document).ready(function() {
    $('.datatable').DataTable({
        responsive: true,
        "language":{
            "url":"<?php echo APP_URI."/".___("package/code")."/datatable/lang.json";?>"
        }
    });

    $('.datatable2').DataTable({
        responsive: true,
        paging: false,
        info:     false,
        searching: false,
        "language":{
            "url":"<?php echo APP_URI."/".___("package/code")."/datatable/lang.json";?>"
        }
    });

    $('.datatable3').DataTable({
        responsive: true,
        paging: false,
        info:     false,
        searching: true,
        "language":{
            "url":"<?php echo APP_URI."/".___("package/code")."/datatable/lang.json";?>"
        }
    });

});
</script>
<?php endif; ?>


<?php if(isset($hoptions) && in_array("counterup",$hoptions)): ?>
<script src="<?php echo $tadress;?>js/jquery.counterup.min.js"></script>
<script src="<?php echo $tadress;?>js/waypoints.min.js"></script>
<?php endif; ?>

<?php if(isset($hoptions) && in_array("iziModal",$hoptions)): ?>
    <script src="<?php echo $sadress; ?>assets/plugins/iziModal/js/iziModal.min.js?v=<?php echo License::get_version(); ?>" type="text/javascript"></script>
<?php endif; ?>
<?php if(isset($hoptions) && in_array("select2",$hoptions)): ?>
    <script src="<?php echo $sadress; ?>assets/plugins/select2/js/select2.min.js"></script>
<?php endif; ?>
<?php if(isset($hoptions) && in_array("ion.rangeSlider",$hoptions)): ?>
    <script src="<?php echo $sadress; ?>assets/plugins/ion.rangeSlider/js/ion.rangeSlider.min.js"></script>
<?php endif; ?>

<?php if(isset($hoptions) && in_array("voucher_codes",$hoptions)): ?>
    <script src="<?php echo $sadress; ?>assets/plugins/js/voucher_codes.js"></script>
<?php endif; ?>

<?php
    View::main_script();
?>

<!-- Js -->