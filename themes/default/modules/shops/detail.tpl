<!-- BEGIN: main -->
<div id="fb-root"></div>
<script type="text/javascript">
	( function(d, s, id) {
			var js, fjs = d.getElementsByTagName(s)[0];
			if (d.getElementById(id))
				return;
			js = d.createElement(s);
			js.id = id;
			js.src = "//connect.facebook.net/{FACEBOOK_LANG}/all.js#xfbml=1&appId={FACEBOOK_APPID}";
			fjs.parentNode.insertBefore(js, fjs);
		}(document, 'script', 'facebook-jssdk'));
</script>

<div itemscope itemtype="http://schema.org/Product" style="display: none">
	<span itemprop="name">{TITLE}</span>
	<img itemprop="image" src="{SRC_PRO_FULL}" alt="{TITLE}" />
	<span itemprop="description">{hometext}</span>
	<span itemprop="mpn">{PRODUCT_CODE}</span>
	<!-- BEGIN: allowed_rating_snippets -->
	<span itemprop="aggregateRating" itemscope itemtype="http://schema.org/AggregateRating"> <span itemprop="ratingValue">{RATE_VALUE}</span> {LANG.trong} <span itemprop="reviewCount">{RATE_TOTAL} </span> {LANG.dg} </span>
	<!-- END: allowed_rating_snippets -->
	<span itemprop="offers" itemscope itemtype="http://schema.org/Offer"> <span itemprop="category">{CAT_TITLE}</span> <!-- BEGIN: price1 --> <span itemprop="price">{PRICE.sale_format}</span> <span itemprop="priceCurrency">{PRICE.unit}</span> <!-- END: price1 --> <span itemprop="availability">{LANG.detail_pro_number}: {PRODUCT_NUMBER} {pro_unit}</span> </span>
</div>

<div id="detail">
	<div class="panel panel-default">
		<div class="panel-body">
			<div class="row">
				<div class="col-xs-24 col-sm-6 text-center">
					<img src="{SRC_PRO}" alt="" width="140px" class="img-thumbnail pointer" id="imageproduct" data-target="#imagemodal">
               		<div class="modal fade" id="imagemodal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
						<div class="modal-dialog">
							<div class="modal-content">
								<div class="modal-header">
									<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
									<h4 class="modal-title" id="myModalLabel">{TITLE}</h4>
								</div>
								<div class="modal-body">
									<img src="{SRC_PRO_LAGE}" id="imagepreview" class="img-thumbnail" >
								</div>
							</div>
						</div>
					</div>
					<br />
					<!-- BEGIN: adminlink -->
					<p class="tab-pane">
						{ADMINLINK}
					</p>
					<!-- END: adminlink -->
					<!-- BEGIN: social_icon -->
					<hr />
					<ul style="padding: 0; margin-top: 5px;">
						<li class="pull-left">
							<div class="fb-like" data-href="{SELFURL}" data-width="The pixel width of the plugin" data-height="The pixel height of the plugin" data-colorscheme="light" data-layout="button_count" data-action="like" data-show-faces="true" data-send="false" data-share="true">
								&nbsp;
							</div>
						</li>
						<li class="pull-left">
							<div class="g-plusone" data-size="medium"></div>
						</li>
					</ul>
					<script type="text/javascript">
						window.___gcfg = {
							lang : nv_sitelang
						};
						(function() {
							var po = document.createElement('script');
							po.type = 'text/javascript';
							po.async = true;
							po.src = 'https://apis.google.com/js/plusone.js';
							var s = document.getElementsByTagName('script')[0];
							s.parentNode.insertBefore(po, s);
						})();
					</script>
					<!-- END: social_icon -->
				</div>

				<div class="col-xs-24 col-sm-18">
					<ul class="product_info">
						<li>
							<h2>{TITLE}</h2>
						</li>
						<li class="text-muted">
							{DATE_UP} - {NUM_VIEW} {LANG.detail_num_view}
						</li>

						<!-- BEGIN: product_code -->
						<li>
							{LANG.product_code}: <strong>{PRODUCT_CODE}</strong>
						</li>
						<!-- END: product_code -->

						<!-- BEGIN: price -->
						<li>
							<p>
								{LANG.detail_pro_price}:
								<!-- BEGIN: discounts -->
								<span class="money">{PRICE.sale_format} {PRICE.unit}</span>
								<span class="discounts_money">{PRICE.price_format} {PRICE.unit}</span>
								<span class="money">{product_discounts} {money_unit}</span>
								<!-- END: discounts -->

								<!-- BEGIN: no_discounts -->
								<span class="money">{PRICE.price_format} {PRICE.unit}</span>
								<!-- END: no_discounts -->
							</p>
						</li>
						<!-- END: price -->

						<!-- BEGIN: contact -->
						<li>
							{LANG.detail_pro_price}: <span class="money">{LANG.price_contact}</span>
						</li>
						<!-- END: contact -->

						<!-- BEGIN: group_detail -->
						<li>
							<!-- BEGIN: loop -->
							<!-- BEGIN: maintitle -->
							<div class="pull-left">
								<strong>{MAINTITLE}:</strong>&nbsp;
							</div>
							<!-- END: maintitle -->

							<!-- BEGIN: subtitle -->
							<ul class="pull-left list-inline" style="padding: 0 10px 0">
								<!-- BEGIN: loop -->
								<li>
									{SUBTITLE.title}
								</li>
								<!-- END: loop -->
							</ul>
							<div class="clear"></div>
							<!-- END: subtitle -->
							<!-- END: loop -->
						</li>
						<!-- END: group_detail -->

						<!-- BEGIN: custom_data -->
						{CUSTOM_DATA}
						<!-- END: custom_data -->

						<!-- BEGIN: hometext -->
						<li>
							<p class="text-justify">
								{hometext}
							</p>
						</li>
						<!-- END: hometext -->

						<!-- BEGIN: promotional -->
						<li>
							<strong>{LANG.detail_promotional}:</strong> {promotional}
						</li>
						<!-- END: promotional -->

						<!-- BEGIN: warranty -->
						<li>
							<strong>{LANG.detail_warranty}:</strong> {warranty}
						</li>
						<!-- END: warranty -->
					</ul>
					<hr />
					<!-- BEGIN: gift -->
					<div class="alert alert-info">
						<div class="pull-left">
							<em class="fa fa-gift fa-3x">&nbsp;</em>
						</div>
						<div class="pull-left">
							<h4>{gift_content}</h4>
						</div>
						<div class="clearfix"></div>
					</div>
					<!-- END: gift -->
					<!-- BEGIN: group -->
					<div class="well">
						<div class="filter_product">
							<!-- BEGIN: items -->
							<div class="row">
								<!-- BEGIN: header -->
								<div class="col-xs-8 col-sm-5" style="margin-top: 4px">
									{HEADER}
								</div>
								<!-- END: header -->
								<div class="col-xs-16 col-sm-19" data-groupid="{GROUPID}" data-header="{HEADER}">
									<!-- BEGIN: loop -->
									<label class="label_group <!-- BEGIN: active -->active<!-- END: active -->"><input type="radio" class="groupid" name="groupid[{GROUPID}]" value="{GROUP.groupid}" <!-- BEGIN: checked -->checked="checked" <!-- END: checked -->>{GROUP.title}</label>
									<!-- END: loop -->
								</div>
							</div>
							<!-- END: items -->
						</div>
						<span id="group_error">&nbsp;</span>
					</div>
					<!-- END: group -->

					<!-- BEGIN: product_number -->
					<div class="well">
						<div class="row">
							<div class="col-xs-8 col-sm-5">
								{LANG.detail_pro_number}
							</div>
							<div class="col-xs-16 col-sm-19">
								<input type="number" name="num" value="1" min="1" max="{PRODUCT_NUMBER}" id="pnum" class="pull-left form-control" style="width: 100px; margin-right: 5px">
								<span class="help-block pull-left" id="product_number">{LANG.detail_pro_number}: <strong>{PRODUCT_NUMBER}</strong> {pro_unit}</span>
							</div>
						</div>
					</div>
					<!-- END: product_number -->

					<div class="clearfix"></div>

					<!-- BEGIN: typepeice -->
					<table class="table table-striped table-bordered table-hover">
						<thead>
							<tr>
								<th class="text-right">{LANG.detail_pro_number}</th>
								<th class="text-left">{LANG.cart_price} ({money_unit})</th>
							</tr>
						</thead>
						<tbody>
							<!-- BEGIN: items -->
							<tr>
								<td class="text-right">{ITEMS.number_from} -> {ITEMS.number_to}</td>
								<td class="text-left">{ITEMS.price}</td>
							</tr>
							<!-- END: items -->
						</tbody>
					</table>
					<!-- END: typepeice -->

					<!-- BEGIN: order -->
					<button class="btn btn-danger btn-order" data-id="{proid}" onclick="cartorder_detail(this, '{POPUP}', 0); return false;">
						<em class="fa fa-shopping-cart fa-lg">&nbsp;</em>
						{LANG.add_cart}
					</button>
					<button class="btn btn-success btn-order" data-id="{proid}" onclick="cartorder_detail(this, '{POPUP}', 1); return false;">
						<em class="fa fa-paper-plane-o fa-lg">&nbsp;</em>
						{LANG.buy_now}
					</button>
					<!-- END: order -->
					<!-- BEGIN: product_empty -->
					<button class="btn btn-danger disabled">{LANG.product_empty}</button>
					<!-- END: product_empty -->
				</div>
			</div>
		</div>
	</div>

	<!-- BEGIN: product_detail -->
		<!-- BEGIN: tabs -->
		<div role="tabpanel" class="tabs">
			<ul class="nav nav-tabs" role="tablist">
				<!-- BEGIN: tabs_title -->
				<li role="presentation" <!-- BEGIN: active -->class="active"<!-- END: active -->>
					<a href="#{TABS_KEY}-{TABS_ID}" aria-controls="{TABS_KEY}-{TABS_ID}" role="tab" data-toggle="tab">
						<!-- BEGIN: icon -->
						<img src="{TABS_ICON}" />
						<!-- END: icon -->
						<!-- BEGIN: icon_default -->
						<em class="fa fa-bars">&nbsp;</em>
						<!-- END: icon_default -->
						<span>{TABS_TITLE}</span>
					</a>
				</li>
				<!-- END: tabs_title -->
			</ul>

			<div class="tab-content">
				<!-- BEGIN: tabs_content -->
				<div role="tabpanel" class="tab-pane fade <!-- BEGIN: active -->active in<!-- END: active -->" id="{TABS_KEY}-{TABS_ID}">
					{TABS_CONTENT}
				</div>
				<!-- END: tabs_content -->
			</div>
		</div>
		<!-- END: tabs -->

		<!-- BEGIN: keywords -->
	    <div class="panel panel-default">
		    <div class="panel-body">
				<div class="keywords">
					<em class="fa fa-tags">&nbsp;</em><strong>{LANG.keywords}: </strong>
					<!-- BEGIN: loop -->
						<a title="{KEYWORD}" href="{LINK_KEYWORDS}"><em>{KEYWORD}</em></a>{SLASH}
					<!-- END: loop -->
				</div>
			</div>
		</div>
		<!-- END: keywords -->

		<!-- BEGIN: other -->
		<div class="panel panel-default">
			<div class="panel-heading">
				{LANG.detail_others}
			</div>
			<div class="panel-body">
				{OTHER}
			</div>
		</div>
		<!-- END: other -->

		<!-- BEGIN: other_view -->
		<div class="panel panel-default">
			<div class="panel-heading">
				{LANG.detail_others_view}
			</div>
			<div class="panel-body">
				{OTHER_VIEW}
			</div>
		</div>
		<!-- END: other_view -->
	<!-- END: product_detail -->
</div>

<div class="msgshow" id="msgshow"></div>

<!-- BEGIN: allowed_print_js -->
<script type="text/javascript">
	$(function() {
		$('#click_print').click(function(event) {
			var href = $(this).attr("href");
			event.preventDefault();
			nv_open_browse(href, '', 640, 500, 'resizable=no,scrollbars=yes,toolbar=no,location=no,status=no');
			return false;
		});
	});
</script>
<!-- END: allowed_print_js -->

<!-- BEGIN: imagemodal -->
<script type="text/javascript">
    $("#imageproduct").on("click", function() {
    	$('#imagemodal').modal('show');
    });
</script>
<!-- END: imagemodal -->

<script type="text/javascript">
	var detail_error_group = '{LANG.detail_error_group}';

	$('.groupid').click(function() {
		var _this = $('input[name="'+$(this).attr('name')+'"]');
		$('input[name="'+$(this).attr('name')+'"]').parent().css('border-color', '#ccc');
		if( $(this).is(':checked') )
		{
		    $(this).parent().css('border-color', 'blue');
		}
		$('#group_error').css( 'display', 'none' );
		check_price( '{proid}', '{pro_unit}' );
	});

	$('#pnum').change(function(){
		if( intval($(this).val()) > intval($(this).attr('max')) ){
			alert('{LANG.detail_error_number} ' + $(this).attr('max') );
			$(this).val( $(this).attr('max') );
		}
	});
</script>

<!-- END: main -->