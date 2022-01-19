<!-- BEGIN: main -->
<div class="panel panel-default">
	<div class="panel-body">
		<div class="row form-review">
			<div class="col-xs-24 col-sm-11 border border-right">
				<form id="review_form" <!-- BEGIN: recaptcha3 --> data-recaptcha3="1"<!-- END: recaptcha3 -->>
					<div class="form-group">
						<input type="text" class="form-control" name="sender" value="{SENDER}" placeholder="{LANG.profile_user_name}">
					</div>
					<div class="form-group">
						<div class="rate-ex2-cnt">
							<div id="1" class="rate-btn-1 rate-btn"></div>
							<div id="2" class="rate-btn-2 rate-btn"></div>
							<div id="3" class="rate-btn-3 rate-btn"></div>
							<div id="4" class="rate-btn-4 rate-btn"></div>
							<div id="5" class="rate-btn-5 rate-btn"></div>
						</div>
					</div>
					<div class="form-group">
						<textarea name="comment" class="form-control" placeholder="{LANG.rate_comment}"></textarea>
					</div>
					<!-- BEGIN: recaptcha -->
                    <div class="form-group">
                        <div class="nv-recaptcha-default"><div id="{RECAPTCHA_ELEMENT}" data-toggle="recaptcha" data-pnum="4" data-btnselector="[type=submit]"></div></div>
                    </div>
                    <!-- END: recaptcha -->
                    <!-- BEGIN: captcha -->
                    <div class="form-group">
                        <div class="middle text-center clearfix">
                            <img class="captchaImg display-inline-block" src="{SRC_CAPTCHA}" width="{GFX_WIDTH}" height="{GFX_HEIGHT}" alt="{N_CAPTCHA}" title="{N_CAPTCHA}" /><em class="fa fa-pointer fa-refresh margin-left margin-right" title="{CAPTCHA_REFRESH}" onclick="change_captcha('.bsec');"></em><input type="text" style="width:100px;" class="bsec required form-control display-inline-block" name="fcode" value="" maxlength="{GFX_MAXLENGTH}" placeholder="{GLANG.securitycode}" data-pattern="/^(.){{GFX_MAXLENGTH},{GFX_MAXLENGTH}}$/" onkeypress="validErrorHidden(this);" data-mess="{GLANG.securitycodeincorrect}" />
                        </div>
                    </div>
                    <!-- END: captcha -->
					<div class="form-group">
						<input type="submit" class="btn btn-primary" value="{LANG.rate}" />
					</div>
				</form>
			</div>
			<div class="col-xs-24 col-sm-13 border">
				<div id="rate_list">
					<p class="text-center">
						<em class="fa fa-spinner fa-spin fa-3x">&nbsp;</em>
					</p>
				</div>
			</div>
		</div>
	</div>
</div>
<script type="text/javascript" data-show="after">
	$("#rate_list").load('{LINK_REVIEW}&showdata=1');
	var rating = 0;
	$('.rate-btn').hover(function() {
		$('.rate-btn').removeClass('rate-btn-hover');
		rating = $(this).attr('id');
		for (var i = rating; i >= 0; i--) {
			$('.rate-btn-' + i).addClass('rate-btn-hover');
		};
	});

	$('#review_form').submit(function() {
		var sender = $(this).find('input[name="sender"]').val();
		var comment = $(this).find('textarea[name="comment"]').val();
		var type = '';
		if ($('input[name="fcode"]').length) {
            var fcaptcha = $('input[name="fcode"]').val();
            if(rating !=0) {
                if(sender == '') {
                    $('input[name="sender"]').focus();
                }
                else if(fcaptcha == '') {
                    $('input[name="fcode"]').focus();
                }
            }
            var datapost = 'sender=' + sender + '&rating=' + rating + '&comment=' + comment + '&fcaptcha=' + fcaptcha;
			type = 'fcode';
        } else {
            var fcaptcha = $('.g-recaptcha-response').val();
            var datapost = 'sender=' + sender + '&rating=' + rating + '&comment=' + comment + '&fcaptcha=' + fcaptcha;
			type = 'fcaptcha';
        }

		$.ajax({
			type : "POST",
			url : '{LINK_REVIEW}' + '&nocache=' + new Date().getTime(),
			data : datapost,
			success : function(data) {
				var s = data.split('_');
				alert(s[1]);
				if (s[0] == 'OK') {
					$('#review_form input[name="sender"], #review_form input[name="fcode"], #review_form textarea').val('');
					$('.rate-btn').removeClass('rate-btn-hover');
					$("#rate_list").load('{LINK_REVIEW}&showdata=1');
					window.location.href = 	$(location).attr('href')+'#detail';
				} else {				    
				   	if (type == 'fcode') {
						change_captcha();
						$("[name=fcode]",a).val('');
				   	}
				}				
			}
		});		
		return !1;
	});
</script>
<!-- END: main -->