
	// 设置api 地址
const sUrl = 'http://blogapi.uikiss.cn/';
const APP_ID = 'UGX6JHKBhWTPuzZjnzCIB7s8raaY6Kfm';
const APP_SECRET = 'X2fCKYhc4gNH435yTMgyMQVihaexTgevGCPByeNW6vUkHXstAIKwD9Z4XbYByev9';
const os = 'bk';



document.write("<script type='text/javascript' src='js/encryption/utils.js'></script>"); 
document.write("<script type='text/javascript' src='js/encryption/md5.js'></script>"); 
$(function(){


	function reConfig(data){
		/**********    加密    ***********/

			//sign 进行md5加密 后端那边需要将小写的字段名转为大写
			var config = {
					nonce: getCurrentTime(),
			        app_id: APP_ID,
			        format: 'json',
			        os: os,
			        sign_method: 'md5',
			};
			if(!(data == '' || data == null)){
				$.each(data, function(k, v){
					config[k] = v;
				})
			}
			config.sign = sign(config);
			return config;
	}




	var domain = GetQueryString("bolg");
	var arti = GetQueryString("arti");
	var time = GetQueryString("time");
	if (domain != '' && domain != null) {
		$("title").html("小小的成 - "+domain);
	}
	var outTime = (new Date()).valueOf();
	var startTime = localStorage.getItem('user_time');

	if ((outTime - startTime) > 1800000) {
		// 先取消登录不操作过时
		localStorage.clear();
	}else{
		localStorage.setItem('user_time',(new Date()).valueOf());
	}
	// 实现登录操作
	if (time) {

		// 定义一个json 接收数据
		var sendData = []

		// 创建Base64对象
		var Base64={_keyStr:"ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789+/=",encode:function(e){var t="";var n,r,i,s,o,u,a;var f=0;e=Base64._utf8_encode(e);while(f<e.length){n=e.charCodeAt(f++);r=e.charCodeAt(f++);i=e.charCodeAt(f++);s=n>>2;o=(n&3)<<4|r>>4;u=(r&15)<<2|i>>6;a=i&63;if(isNaN(r)){u=a=64}else if(isNaN(i)){a=64}t=t+this._keyStr.charAt(s)+this._keyStr.charAt(o)+this._keyStr.charAt(u)+this._keyStr.charAt(a)}return t},decode:function(e){var t="";var n,r,i;var s,o,u,a;var f=0;e=e.replace(/[^A-Za-z0-9+/=]/g,"");while(f<e.length){s=this._keyStr.indexOf(e.charAt(f++));o=this._keyStr.indexOf(e.charAt(f++));u=this._keyStr.indexOf(e.charAt(f++));a=this._keyStr.indexOf(e.charAt(f++));n=s<<2|o>>4;r=(o&15)<<4|u>>2;i=(u&3)<<6|a;t=t+String.fromCharCode(n);if(u!=64){t=t+String.fromCharCode(r)}if(a!=64){t=t+String.fromCharCode(i)}}t=Base64._utf8_decode(t);return t},_utf8_encode:function(e){e=e.replace(/rn/g,"n");var t="";for(var n=0;n<e.length;n++){var r=e.charCodeAt(n);if(r<128){t+=String.fromCharCode(r)}else if(r>127&&r<2048){t+=String.fromCharCode(r>>6|192);t+=String.fromCharCode(r&63|128)}else{t+=String.fromCharCode(r>>12|224);t+=String.fromCharCode(r>>6&63|128);t+=String.fromCharCode(r&63|128)}}return t},_utf8_decode:function(e){var t="";var n=0;var r=c1=c2=0;while(n<e.length){r=e.charCodeAt(n);if(r<128){t+=String.fromCharCode(r);n++}else if(r>191&&r<224){c2=e.charCodeAt(n+1);t+=String.fromCharCode((r&31)<<6|c2&63);n+=2}else{c2=e.charCodeAt(n+1);c3=e.charCodeAt(n+2);t+=String.fromCharCode((r&15)<<12|(c2&63)<<6|c3&63);n+=3}}return t}}
 		var kv = {};
		var result =  Base64.decode(time); 
		var urlData = result.split('&');
		for (var i = 0; i < urlData.length; i++) {
			a = urlData[i].split('=');
			sendData[a[0]] = a[1];
			localStorage.setItem(a[0],a[1]);
		}

		localStorage.setItem('user_time',(new Date()).valueOf());
		$.get(sUrl+'api/index/userlogin',reConfig({'user_qq':sendData['user_qq']}),function(){
			var href = window.location.href;
			h = href.split('?');
			// window.location.href = h[0];
		})
	}

	// 获取首页分类标签
	$.post(sUrl+'api/index/cateData',reConfig(),function(data){
		var data = JSON.parse(data);
		var html = '';
			var href = window.location.href;
			h = href.split('/');

		if(!domain && h[3] != 'portfolio.html'){
			html += '<li class="fh5co-active"><a href="index.html">首页</a></li>';
		}else{
			html += '<li><a href="index.html">首页</a></li>';  //  class="fh5co-active"
		}
		$.each(data.data,function(key,val){
			var burl = val['cate_name'];
			
			if( val['cate_name'] == domain){
				html += '<li class="fh5co-active"><a href="blog.html?bolg='+burl+'" data-cate_id="'+val['cate_id']+'">'+val['cate_name']+'</a></li>';
			}else{
				html += '<li><a href="blog.html?bolg='+burl+'" data-cate_id="'+val['cate_id']+'">'+val['cate_name']+'</a></li>';
			}
		}); 
		if (h[3] != 'portfolio.html') {
			html += '<li><a href="portfolio.html">聊天系统（未完善）</a></li>';
		}else{

			html += '<li class="fh5co-active"><a href="portfolio.html">聊天系统（未完善）</a></li>';
		}
		$("#fh5co-main-menu").children("ul").append(html);
	})
	
	
	// 获取域名参数
	function GetQueryString(name)
	{
		 var reg = new RegExp("(^|&)"+ name +"=([^&]*)(&|$)");
		 var r = window.location.search.substr(1).match(reg);
		 if(r!=null)
			return  unescape(decodeURI(r[2]));
		  return null;
	}

	if(domain){
		$("#cateName").html(domain);
		blog(domain);
	}

	// 根据分类获取信息
	function blog(type){
		
		$.post(sUrl+'api/index/articleData',reConfig({'type':type}),function(data){
			var data = JSON.parse(data);
			var html = '';
			$.each(data.data,function(key, val){
			html += '<div class="col-md-3 col-sm-6 col-padding animate-box fadeInLeft animated" data-animate-effect="fadeInLeft">';
			html += '<div class="blog-entry">';
			html += '<a href="details.html?arti='+val["article_id"]+'" class="blog-img"><img src="'+sUrl+val["article_img"]+'" class="img-responsive" alt="Free HTML5 Bootstrap Template by FreeHTML5.co"></a>';
			html += '	<div class="desc">';
			html += '	<h3><a href="details.html?arti='+val["article_id"]+'">'+val["article_title"]+'</a></h3>';
			html += '		<span><small>'+val["article_nick"]+'</small> / <small> '+val["cate_name"]+' </small> / <small> <i class="icon-comment"></i> 2019-4-19</small></span>';
			var hh = val["introduction"] == '' || val["introduction"] == null ? '暂无简介<br><br>' : val["introduction"];//.substring(1,10);
			html += '		<p>'+hh+'</p>';
			html += '		<a href="details.html?arti='+val["article_id"]+'" class="lead">阅读文章 <i class="icon-arrow-right3"></i></a>';
			html += '	</div>';
			html += '</div>';
			html += '</div>';	
			})
		$("#list").append(html);
		})
	}
	


function getLocalTime(nS) {     
   return new Date(parseInt(nS) * 1000).toLocaleString().replace(/:\d{1,2}$/,' ');     
}


	/**
	* 文章的显示
	* 评论的加载
	*/
	if(arti){
		// 	请求属性
			$.ajaxSettings.async = false;
		$.post(sUrl+'api/index/articleHtml',reConfig({'arti':arti}),function(data){
			var data = JSON.parse(data);
			// 文章内容显示
			 $("title").html("小小的成 - "+data.data.html.article_title);
			 $("#article_title").html(data.data.html.article_title);
			 $("#article_markdown").html(data.data.html.article_markdown);
			 $("#article_id").val(data.data.html.article_id);
			 if(data.data.html.is_comment == 1){
				$("#is_comment").show();
			 }
			 var commentHtml = '';
			 // 评论的展示
			 data.data.comment.forEach(function(e){

			 	commentHtml += '<div class="comment-show-con clearfix">';
				commentHtml += '			<div class="comment-show-con-img pull-left" style="width: 38px;height: 38px;">';
				commentHtml += '				<img src="'+e.user_img+'" width="38px" alt="">';
				commentHtml += '			</div>';
				commentHtml += '			<div class="comment-show-con-list pull-left clearfix">';
				commentHtml += '				<div class="pl-text clearfix">';
				commentHtml += '					<a href="javascript:void(0)" class="comment-size-name"><span class="userNick" data-id="'+e.comment_id+'">'+e.user_nick+'</span> : </a>';
				commentHtml += '					<span class="my-pl-con">'+e.comment_val+'</span>';
				commentHtml += '				</div>';
				commentHtml += '				<div class="date-dz">';
				commentHtml += '					<span class="date-dz-left pull-left comment-time">'+getLocalTime(e.comment_time)+'</span>';
				commentHtml += '					<div class="date-dz-right pull-right comment-pl-block">';
				commentHtml += '						<a href="javascript:;" class="removeBlock">删除</a>';
				commentHtml += '						<a href="javascript:;" class="date-dz-pl pl-hf hf-con-block pull-left">回复</a>';
				commentHtml += '						<span class="pull-left date-dz-line">|</span>';
				commentHtml += '						<a href="javascript:;" class="date-dz-z pull-left"><i class="date-dz-z-click-red"></i>赞 (<i class="z-num">'+e.click_num+'</i>)</a>';
				commentHtml += '					</div>';
				commentHtml += '				</div>';
				commentHtml += '<div class="hf-list-con">';
						if (e.children) {
			 						e.children.forEach(function(f){
										commentHtml += '	<div class="all-pl-con">';
										commentHtml += '		<div class="pl-text hfpl-text clearfix">';
										commentHtml += '			<a href="#" class="comment-size-name"><span class="userNick" data-id="'+f.comment_id+'">'+f.user_nick+'</span><span style="color: #8b8b8b;"> 回复@</span>'+f.target_nick+':</a>';
										commentHtml += '			<span class="my-pl-con">'+f.comment_val+'</span>';
										commentHtml += '		</div>';
										commentHtml += '		<div class="date-dz"> ';
										commentHtml += '			<span class="date-dz-left pull-left comment-time">'+getLocalTime(e.comment_time)+'</span> ';
										commentHtml += '			<div class="date-dz-right pull-right comment-pl-block"> ';
										commentHtml += '				<a href="javascript:;" class="removeBlock">删除</a> ';
										commentHtml += '				<a href="javascript:;" class="date-dz-pl pl-hf hf-con-block pull-left">回复</a> ';
										commentHtml += '				<span class="pull-left date-dz-line">|</span> ';
										commentHtml += '				<a href="javascript:;" class="date-dz-z pull-left">';
										commentHtml += '				<i class="date-dz-z-click-red"></i>赞 (<i class="z-num">'+e.click_num+'</i>)</a> ';
										commentHtml += '			</div> ';
										commentHtml += '		</div>';
										commentHtml += '		</div>';
									 })
							}
				commentHtml += '</div>';
				commentHtml += '			</div>';
				commentHtml += '		</div> ';
			 })
			 $('.comment-show').html(commentHtml);

			 $('.upper').html("上一章："+data.data.upper.article_title);
			 $('.down').html("下一章："+data.data.down.article_title);
			 var href = window.location.href;
				h = href.split('?');
			 if (data.data.upper.article_id != '') {
			 	$('.upper').attr('href',h[0]+'?arti='+data.data.upper.article_id);
			 }if (data.data.down.article_id != '') {
			 	$('.down').attr('href',h[0]+'?arti='+data.data.down.article_id);
			 }
		})

	}


	/**
	* 前台获取热门  以及最新的博客  banner
	*/
	var url = document.location.toString();
	var arrObj = url.split("/");
	var obj = arrObj[arrObj.length-1];
	if(obj == '' || obj == 'index.html'){
		    $.ajaxSettings.async = false;
		$.post(sUrl+'api/index/artindex',reConfig(),function(data){
			var data = JSON.parse(data);
			var hotHtml = '';
			var newHtml = '';
			// 热门
			$.each(data.data.hot,function(key, val){
				hotHtml += '<div class="col-md-3 col-sm-6 col-padding animate-box fadeInLeft animated" data-animate-effect="fadeInLeft">';
				hotHtml += '<div class="blog-entry">';
				hotHtml += '<a href="details.html?arti='+val["article_id"]+'" class="blog-img"><img src="'+sUrl+val["article_img"]+'" class="img-responsive" alt="Free HTML5 Bootstrap Template by FreeHTML5.co"></a>';
				hotHtml += '	<div class="desc">';
				hotHtml += '	<h3><a href="details.html?arti='+val["article_id"]+'">'+val["article_title"]+'</a></h3>';
				hotHtml += '		<span><small>'+val["article_nick"]+'</small> / <small> '+val["cate_data"]["cate_name"]+' </small> / <small> <i class="icon-comment"></i> 2019-4-19</small></span>';
				var hh = val["introduction"] == '' || val["introduction"] == null ? '暂无简介<br><br>' : val["introduction"];//.substring(1,10);
				hotHtml += '		<p>'+hh+'</p>';
				hotHtml += '		<a href="details.html?arti='+val["article_id"]+'" class="lead">阅读文章 <i class="icon-arrow-right3"></i></a>';
				hotHtml += '	</div>';
				hotHtml += '</div>';
				hotHtml += '</div>';	
			})
			
			// 最新
			$.each(data.data.newd,function(key, val){
				newHtml += '<div class="col-md-3 col-sm-6 col-padding animate-box fadeInLeft animated" data-animate-effect="fadeInLeft">';
				newHtml += '<div class="blog-entry">';
				newHtml += '<a href="details.html?arti='+val["article_id"]+'" class="blog-img"><img src="'+sUrl+val["article_img"]+'" class="img-responsive" alt="Free HTML5 Bootstrap Template by FreeHTML5.co"></a>';
				newHtml += '	<div class="desc">';
				newHtml += '	<h3><a href="details.html?arti='+val["article_id"]+'">'+val["article_title"]+'</a></h3>';
				newHtml += '		<span><small>'+val["article_nick"]+'</small> / <small> '+val["cate_data"]["cate_name"]+' </small> / <small> <i class="icon-comment"></i> 2019-4-19</small></span>';
				var hh = val["introduction"] == '' || val["introduction"] == null ? '暂无简介<br><br>' : val["introduction"];//.substring(1,10);
				newHtml += '		<p>'+hh+'</p>';
				newHtml += '		<a href="details.html?arti='+val["article_id"]+'" class="lead">阅读文章 <i class="icon-arrow-right3"></i></a>';
				newHtml += '	</div>';
				newHtml += '</div>';
				newHtml += '</div>';	
			})
		/**
		 *  获取banner 图
		 */
		var bannerHtml = '';
			data.data.banner.forEach(function(e){
				bannerHtml +='	   	<li style="background-image: url(http://bogo.uikiss.cn'+e.banner_img+');">';
				bannerHtml +='	   		<div class="overlay"></div>';
				bannerHtml +='	   		<div class="container-fluid">';
				bannerHtml +='	   			<div class="row">';
				bannerHtml +='		   			<div class="col-md-8 col-md-offset-2 text-center js-fullheight slider-text">';
				bannerHtml +='		   				<div class="slider-text-inner">';
				bannerHtml +='		   					<h1>'+e.banner_title+' <strong></strong> </h1>';
				bannerHtml +='								<p><a class="btn btn-primary btn-demo popup-vimeo" href="javascript:void(0)"> <i class="icon-monitor"></i> 给我点赞 </a> <a class="btn btn-primary btn-learn" href="'+e.banner_url+'"> 前往围观 <i class="icon-arrow-right3"></i></a></p>';
				bannerHtml +='		   				</div>';
				bannerHtml +='		   			</div>';
				bannerHtml +='		   		</div>';
				bannerHtml +='	   		</div>';
				bannerHtml +='	   	</li>';
			})
			$('.slides').html(bannerHtml);
			$("#newBlog").append(newHtml);
			$("#hotBlog").append(hotHtml);
		})

	}
		
	
		$.get(sUrl+'api/index/syslist',function(data){
			var data = JSON.parse(data);
			data.data.forEach(function(e){
				var obj = '.'+e.name;
				$(obj).html(e.value);
			})
		})

	// 登录后显示问题
	var loginData = localStorage.getItem('user_nick');
	if (loginData) {
		$(".nameimg").attr('src',localStorage.getItem('user_img'));
		$(".namenick").html(loginData);

		$(".login").hide();
		$(".logutout").show();
	}else{
		$(".login").show();
		$(".logutout").hide();
	}
	
	// 取消登录
	$(".logut").click(function(){
		localStorage.clear();
		$(".login").show();
		$(".logutout").hide();
	})

	/**
	 * [referer 访问记录操作]
	 * @type {[type]}
	 */
	var referer = document.referrer, // 来源信息
	localhref = window.location.href; // 链接
	$.get(sUrl+'api/index/sourceget',{
		"referer" : referer,
		"localhref" : localhref,
	},function(){
		// 没有操作
	})


	// 封装cookie 使用方法 
	var cookie = {
	    set:function(key,val,time){//设置cookie方法
	        var date=new Date(); //获取当前时间
	        var expiresDays=time;  //将date设置为n天以后的时间
	        date.setTime(date.getTime()+expiresDays*24*3600*1000); //格式化为cookie识别的时间
	        document.cookie=key + "=" + val +";expires="+date.toGMTString();  //设置cookie
	    },
	    get:function(key){//获取cookie方法
	        /*获取cookie参数*/
	        var getCookie = document.cookie.replace(/[ ]/g,"");  //获取cookie，并且将获得的cookie格式化，去掉空格字符
	        var arrCookie = getCookie.split(";")  //将获得的cookie以"分号"为标识 将cookie保存到arrCookie的数组中
	        var tips;  //声明变量tips
	        for(var i=0;i<arrCookie.length;i++){   //使用for循环查找cookie中的tips变量
	            var arr=arrCookie[i].split("=");   //将单条cookie用"等号"为标识，将单条cookie保存为arr数组
	            if(key==arr[0]){  //匹配变量名称，其中arr[0]是指的cookie名称，如果该条变量为tips则执行判断语句中的赋值操作
	                tips=arr[1];   //将cookie的值赋给变量tips
	                break;   //终止for循环遍历
	            }
	    	}
	        return tips;
	    },
	    delete:function(key){ //删除cookie方法
	         var date = new Date(); //获取当前时间
	         date.setTime(date.getTime()-10000); //将date设置为过去的时间
	         document.cookie = key + "=v; expires =" +date.toGMTString();//设置cookie
	    }
	}

	// 判断用户登录是否正常
	var cookieItem = cookie.get('user_cookie');
	if (!cookieItem) {
		cookie.set('user_cookie',1131191695+"_"+(new Date()).valueOf());
		$.post(sUrl+'api/index/uvcollect',reConfig({
			'cookie' : cookie.get('user_cookie'),
		}),function(){
			'你的快递已经到达';
		})
	}

})