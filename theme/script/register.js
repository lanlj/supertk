$(function(){
	
	var $tip=$('#tip-warning'),$tipText=$('#tip-text');
	var isTouch=('ontouchstart' in window),click=isTouch?'touchstart':'click';
	$('.arrow').addClass('arrowleft');

	$('#login-tab').on(click,function(){
		$tip.hide();
		$('#login').show();
		$('#reg').hide();
		$("#form-email").focus();
		$(".arrow").removeClass('arrowright').addClass('arrowleft');
	});

	$("#reg-tab").on(click,function(){
		$tip.hide();
		$('#reg').show();
		$('#login').hide();
		$("#form-email-reg").focus();
		$(".arrow").removeClass('arrowleft').addClass('arrowright');
	});
	$("#login-btn").on(click,function(){

		var formEmail=$.trim($("#form-email").val()),
		formPassword=$.trim($("#form-password").val()),
		url=Path+"?app=user&action=login",
		data={
			email:formEmail,
			pass:formPassword
		};
		if(formEmail==''||formPassword==''){
			$tip.show();
			$tipText.html("账号/密码不能为空");
			return false;			
		}
		$.post(url,data,function(exit){
			if(exit=='ERROR:EMAILERROR'){
				$tip.show();
				$tipText.html("帐号不存在或密码有误");
				$("#form-email").focus();
				return false;
			}else{
				if(document.referrer!=''){
					location.href = document.referrer;
				}else{
					location.href = '/';
				}
			}
		});
	});

	$("#reg-btn").on(click,function(){

		var formEmailReg=$.trim($("#form-email-reg").val()),
		formPasswordReg=$.trim($("#form-password-reg").val()),
		formPasswordConfirm=$.trim($("#form-password-com").val()),
		formNickname=$.trim($("#form-nickname").val()),
		regUrl=Path+"?app=user&action=register",
		data={
			email:formEmailReg,
			name:formNickname,
			pass:formPasswordReg,
			pass_confirm:formPasswordConfirm
		};
		$.post(regUrl,data,function(exit){
			if(exit=='ERROR:NAME'){
				$tip.show();
				$tipText.html("昵称不能为空");
				$("#form-nickname").focus();
				return false;
			}
			if(exit=='ERROR:NAMEFORMAT'){
				$tip.show();
				$tipText.html("昵称不能包含特殊字符");
				$("#form-nickname").focus();
				return false;
			}
			if(exit=='ERROR:NAMEED'){
				$tip.show();
				$tipText.html("昵称已存在哦，换一个试试~");
				$("#form-nickname").focus();
				return false;
			}
			if(exit=='ERROR:EMAIL'){
				$tip.show();
				$tipText.html("Email不能为空");
				$("#form-email-reg").focus();
				return false;
			}
			if(exit=='ERROR:EMAILX'){
				$tip.show();
				$tipText.html("Email格式不对");
				$("#form-email-reg").focus();
				return false;
			}
			if(exit=='ERROR:EMAILED'){
				$tip.show();
				$tipText.html("Email已注册，换一个试试~");
				$("#form-email-reg").focus();
				return false;
			}
			if(exit=='ERROR:PASS'){
				$tip.show();
				$tipText.html("密码长度不能小于6位");
				$("#form-password-reg").focus();
				return false;
			}
			if(exit=='ERROR:UNPASS'){
				$tip.show();
				$tipText.html("两次输入的密码不同");
				$("#form-password-com").focus();
				return false;
			}
			if(document.referrer!=''){
				location.href = document.referrer;
			}else{
				location.href = '/';
			}
			
		});
	});

	$("#login").find(".input").on("keydown",function(e){
 		if(e.keyCode==13){
 			$('#login-btn').click();	
 		}		
	});
	var hash=window.location.hash;
	if(hash.indexOf("#register")!=-1){
		$('#reg').show();
		$('#login').hide();
		$("#form-email-reg").focus();
		$(".arrow").removeClass('arrowleft').addClass('arrowright');
	}
});