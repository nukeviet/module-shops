<!-- BEGIN: main -->
<h1>{TITLE}</h1>
<hr />
<!-- BEGIN: displays -->
<div class="form-group form-inline pull-right">
    <label class="control-label"><select name="sort" id="sort" class="form-control input-sm" onchange="nv_chang_price();">
            <!-- BEGIN: sorts -->
            <option value="{key}"{se}>{value}</option>
            <!-- END: sorts -->
    </select>&nbsp;&nbsp;</label>
    <!-- BEGIN: viewtype -->
    <div class="viewtype">
        <span class="pointer {VIEWTYPE.active}" onclick="nv_chang_viewtype('{VIEWTYPE.index}');" title="{VIEWTYPE.title}"><em class="fa fa-{VIEWTYPE.icon} fa-lg">&nbsp;</em></span>
    </div>
    <!-- END: viewtype -->
</div>
<div class="clearfix"></div>
<!-- END: displays -->
<div class="wishlist">{CONTENT}</div>
<script type="text/javascript" data-show="after">
    var lang_del_confirm = '{LANG.wishlist_del_item_confirm}';
</script>
<!-- END: main -->