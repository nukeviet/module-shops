<!-- BEGIN: main -->
<!-- BEGIN: loop -->
<div class="panel panel-default panel-shops-cat">
    <div class="panel-heading clearfix">
        <a class="pull-left" href="{LINK_CATALOG}" title="{TITLE_CATALOG}" rel="dofollow">
            <!-- BEGIN: icon_image -->
            <img src="{icon_image}" alt="{TITLE_CATALOG}" height="100"/>
            <!-- END: icon_image -->
            {TITLE_CATALOG} ({NUM_PRO} {LANG.title_products})
        </a>
        <span class="pull-right more-cats">
            <!-- BEGIN: subcatloop --><a href="{SUBCAT.link}" title="{SUBCAT.title}" rel="dofollow">{SUBCAT.title}</a><!-- END: subcatloop -->
        </span>
    </div>
    <div class="panel-body">
        {CONTENT}
    </div>
</div>
<!-- END: loop -->
<!-- END: main -->
