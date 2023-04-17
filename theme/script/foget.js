$(function(){
var $tip=$('#tip-warning'),$tipText=$('#tip-text');
var isTouch=('ontouchstart' in window),click=isTouch?'touchstart':'click';
$("#foget-bth").on(click,function(){
		$('#login .button').hide();
		$('#login .waiting').show();
		var formuser= $.trim($("#foget-user").val()),
		    formEmail= $.trim($("#foget-email").val()),
		regUrl= Path+"?app=user&action=fogeter",
		data={
		user:formuser,
		email:formEmail
		};
		if(formEmail==''||formuser==''){
			$tip.show();
			$tipText.html("邮箱/账户不能为空");
			$('#login .button').show();
		    $('#login .waiting').hide();
			return false;
		}
		$.post(regUrl,data,function(exit){
			if(exit=='ERROR:NAME'){
			$('#login .button').show();
		    $('#login .waiting').hide();
			$tip.show();
			$tipText.html("昵称无效");
			$("#foget-user").focus();
				return false;
			}
			if(exit=='ERROR:NAMEFORMAT'){
			$('#login .button').show();
		    $('#login .waiting').hide();
			$tip.show();
			$tipText.html("昵称不能包含特殊字符");
			$("#foget-user").focus();
				return false;
			}
			if(exit=='ERROR:FEMAIL'){
			$('#login .button').show();
		    $('#login .waiting').hide();
				$tip.show();
				$tipText.html("邮箱格式错误");
				$("#foget-email").focus();
				return false;
			}
		   if(exit=='ERROR:No'){
		   	$('#login .button').show();
		    $('#login .waiting').hide();
				$tip.show();
				$tipText.html("用户名或者邮箱错误");
				$("#foget-email").focus();
				return false;
			} 
			if(exit=='ERROR:scus'){ 
			$('#login .waiting').hide();
			$tip.show();
			$tipText.html("邮件已发送您的邮箱请注意查收");
				return false;
			}if(exit=='ERROR:ERROR'){
			$('#login .button').show();
		    $('#login .waiting').hide();
				$tip.show();
				$tipText.html("未知错误");
				return false;
			}
			
		});
	});
	$("#find-bth").on(click,function(){
		var formpass= $.trim($("#find-pass").val()),
		    passagan= $.trim($("#find-passagan").val()),
		    pkey= $.trim($("#find-key").val()),
		regUrl= Path+"?app=user&action=find",
		data={
		pkey:pkey,
		newpass:formpass,
		newpassagan:passagan
		};
		if(formpass==''||passagan==''){
			$tip.show();
			$tipText.html("请输入新密码");
			return false;
		}
		$.post(regUrl,data,function(exit){
			if(exit=='ERROR:PASS'){
			$tip.show();
			$tipText.html("密码长度不能小于6位");
			$("#foget-user").focus();
				return false;
			}
			if(exit=='ERROR:UNPASS'){
				$tip.show();
				$tipText.html("两次输入的密码不同");
				$("#find-email").focus();
				return false;
			}
			if(exit=='ERROR:scus'){
				$tip.show();
				$tipText.html("重置成功！");
				location.href = 'index.php';
				return false;
			}
			if(exit=='ERROR:ERROR'){
				alert('非法重置');
				return false;
			}
			
		});
	});
});