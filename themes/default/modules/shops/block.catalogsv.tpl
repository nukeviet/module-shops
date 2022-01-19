<!-- BEGIN: main -->
<link rel="stylesheet" type="text/css"	href="{NV_STATIC_URL}themes/{TEMPLATE}/css/jquery.metisMenu.css" />
<script type="text/javascript" src="{NV_STATIC_URL}{NV_ASSETS_DIR}/js/jquery/jquery.metisMenu.js"></script>

<div class="clearfix panel metismenu">
    <aside class="sidebar">
        <nav class="sidebar-nav">
            <ul id="menu_{ID}">
                {CONTENT}
            </ul>
        </nav>
    </aside>
</div>

<script type="text/javascript">
$(function () {
    $('#menu_{ID}').metisMenu({
        toggle: false
    });
});
</script>
<!-- END: main -->