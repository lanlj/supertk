(function(c){var b="Fileupload";var d={url:document.URL,method:"POST",extraData:{},maxFileSize:0,allowedTypes:"*",extFilter:null,dataType:null,fileName:"file",onInit:function(){},onFallbackMode:function(){message},onNewFile:function(g,f){},onBeforeUpload:function(f){},onComplete:function(){},onUploadProgress:function(g,f){},onUploadSuccess:function(g,f){},onUploadError:function(g,f){},onFileTypeError:function(f){},onFileSizeError:function(f){},onFileExtError:function(f){}};var a=function(g,f){this.element=c(g);this.settings=c.extend({},d,f);if(!this.checkBrowser()){return false}this.init();return true};a.prototype.checkBrowser=function(){if(window.FormData===undefined){this.settings.onFallbackMode.call(this.element,"Browser doesn't support From API");return false}if(this.element.find("input[type=file]").length>0){return true}if(!this.checkEvent("drop",this.element)||!this.checkEvent("dragstart",this.element)){this.settings.onFallbackMode.call(this.element,"Browser doesn't support Ajax Drag and Drop");return false}return true};a.prototype.checkEvent=function(f,h){var h=h||document.createElement("div");var f="on"+f;var g=f in h;if(!g){if(!h.setAttribute){h=document.createElement("div")}if(h.setAttribute&&h.removeAttribute){h.setAttribute(f,"");g=typeof h[f]=="function";if(typeof h[f]!="undefined"){h[f]=undefined}h.removeAttribute(f)}}h=null;return g};a.prototype.init=function(){var f=this;f.queue=new Array();f.queuePos=-1;f.queueRunning=false;f.element.on("drop",function(g){g.preventDefault();var h=g.originalEvent.dataTransfer.files;f.queueFiles(h)});f.element.find("input[type=file]").on("change",function(g){var h=g.target.files;f.queueFiles(h);c(this).val("")});this.settings.onInit.call(this.element)};a.prototype.queueFiles=function(m){var g=this.queue.length;for(var k=0;k<m.length;k++){var h=m[k];if((this.settings.maxFileSize>0)&&(h.size>this.settings.maxFileSize)){this.settings.onFileSizeError.call(this.element,h);continue}if((this.settings.allowedTypes!="*")&&!h.type.match(this.settings.allowedTypes)){this.settings.onFileTypeError.call(this.element,h);continue}if(this.settings.extFilter!=null){var n=this.settings.extFilter.toLowerCase().split(";");var l=h.name.toLowerCase().split(".").pop();if(c.inArray(l,n)<0){this.settings.onFileExtError.call(this.element,h);continue}}this.queue.push(h);var f=this.queue.length-1;this.settings.onNewFile.call(this.element,f,h)}if(this.queueRunning){return false}if(this.queue.length==g){return false}this.processQueue();return true};a.prototype.processQueue=function(){var h=this;h.queuePos++;if(h.queuePos>=h.queue.length){h.settings.onComplete.call(h.element);h.queuePos=(h.queue.length-1);h.queueRunning=false;return}var g=h.queue[h.queuePos];var f=new FormData();f.append(h.settings.fileName,g);c.each(h.settings.extraData,function(i,j){f.append(i,j)});h.settings.onBeforeUpload.call(h.element,h.queuePos);h.queueRunning=true;c.ajax({url:h.settings.url,type:h.settings.method,dataType:h.settings.dataType,data:f,cache:false,contentType:false,processData:false,forceSync:false,xhr:function(){var i=c.ajaxSettings.xhr();if(i.upload){i.upload.addEventListener("progress",function(m){var l=0;var j=m.loaded||m.position;var k=m.total||e.totalSize;if(m.lengthComputable){l=Math.ceil(j/k*100)}h.settings.onUploadProgress.call(h.element,h.queuePos,l)},false)}return i},success:function(j,i,k){h.settings.onUploadSuccess.call(h.element,h.queuePos,j)},error:function(k,i,j){h.settings.onUploadError.call(h.element,h.queuePos,j)},complete:function(i,j){h.processQueue()}})};c.fn.Fileupload=function(f){return this.each(function(){if(!c.data(this,b)){c.data(this,b,new a(this,f))}})};c(document).on("dragenter",function(f){f.stopPropagation();f.preventDefault()});c(document).on("dragover",function(f){f.stopPropagation();f.preventDefault()});c(document).on("drop",function(f){f.stopPropagation();f.preventDefault()})})(jQuery);
$(function(){
	function add_file(id, file){
		var template = '' +
		  '<div class="file" style="margin-top:30px;width:100%;position:relative;margin-bottom:20px;" id="uploadFile' + id + '">' +
		    '<div class="bar" style="height:5px;background-color:#f8f8f8;position: absolute;left:0;top: 0;right: 0;">' +
		      '<div class="progress" style="height:5px;background-color:#37a;" style="width:0%"></div>' +
		    '</div>' +
		  '</div>';
	  
	 	$('#upload-speed').prepend(template);
	}
	function update_file_progress(id, percent){
		$('#uploadFile' + id).find('div.progress').width(percent);
	}
	$('#upload-avatar').Fileupload({
		url: Path+'?app=user&action=avatar',
		maxFileSize: 1024000,
		dataType: 'json',
		allowedTypes: 'image/*',
		extFilter: 'jpg',
		onNewFile: function(id, file){ 
		  add_file(id, file);
		},
		onUploadProgress: function(id, percent){
		  var percentStr = percent + '%';
		  update_file_progress(id, percentStr);
		},
		onUploadSuccess: function(id, data){
			update_file_progress(id, '100%');
			$('#upload-speed').fadeOut(1000);
			alert("上传成功！");
			$('#images-avatar').find('img').attr("src",data.message);
		},
		onFileTypeError: function(file){
		  alert("图片格式不支持");
		},
		onFileSizeError: function(file){
		  alert('文件不能超过1M');
		},
		onFallbackMode: function(message){
		  alert('您的浏览器版本过旧，建议使用谷歌浏览器');
		}
	});
	$('#upload-bg').Fileupload({
		url: Path+'?app=user&action=setbg',
		maxFileSize: 2126000,
		dataType: 'json',
		allowedTypes: 'image/*',
		extFilter: 'jpg',
		onNewFile: function(id, file){ 
		  add_file(id, file);
		},
		onUploadProgress: function(id, percent){
		  var percentStr = percent + '%';
		  update_file_progress(id, percentStr);
		},
		onUploadSuccess: function(id, data){
			update_file_progress(id, '100%');
			$('#upload-speed').fadeOut(1000);
			alert("主页封面上传成功！");
		},
		onFileTypeError: function(file){
		  alert("图片格式不支持");
		},
		onFileSizeError: function(file){
		  alert('文件不能超过1M');
		},
		onFallbackMode: function(message){
		  alert('您的浏览器版本过旧，建议使用谷歌浏览器');
		}
	});
});