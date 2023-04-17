$().ready(function(){

          
		var _defautlTop = $("#navigator2").offset().top;
		var _defautlLeft = $("#navigator2").offset().left;
		var _position = $("#navigator2").css('position');
		var _top = $("#navigator2").css('top');
		var _left = $("#navigator2").css('left');

		var _zIndex = $("#navigator2").css('z-index');
		var _filter = $("#navigator2").css('filter');
		var _mozopacity = $("#navigator2").css('-moz-opacity');
		var _opacity = $("#navigator2").css('opacity');

		$(window).scroll(function(){
			if($(this).scrollTop() > _defautlTop){
		$("#navigator2").css({'position':'fixed','top':60,'left':_defautlLeft,'margin-left':'auto','z-index':2,'filter':'alpha(opacity=90)','-moz-opacity':0.9,'opacity':0.9});
			}else{
				$("#navigator2").css({'position':_position,'top':_top,'left':_left,'z-index':_zIndex,'filter':_filter,'-moz-opacity':_mozopacity,'opacity':_opacity});
				
			}
		});
	});