$(function(){
	const sUrl = 'http://www.uikiss.com/';
	var domain = GetQueryString("bolg");
	var arti = GetQueryString("arti");
	$.get(sUrl+'api/index/cateData',function(data){
		var data = JSON.parse(data);
		var html = '';
		
		if(!domain){
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
			html += '<li><a href="contact.html">留言</a></li>';
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
		
		$.get(sUrl+'api/index/articleData',{'type':type},function(data){
			var data = JSON.parse(data);
			var html = '';
			$.each(data.data,function(key, val){
			html += '<div class="col-md-3 col-sm-6 col-padding animate-box fadeInLeft animated" data-animate-effect="fadeInLeft">';
			html += '<div class="blog-entry">';
			html += '<a href="details.html?arti='+val["article_id"]+'" class="blog-img"><img src="'+sUrl+val["article_img"]+'" class="img-responsive" alt="Free HTML5 Bootstrap Template by FreeHTML5.co"></a>';
			html += '	<div class="desc">';
			html += '	<h3><a href="details.html?arti='+val["article_id"]+'">'+val["article_title"]+'</a></h3>';
			html += '		<span><small>'+val["article_nick"]+'</small> / <small> '+val["cate_name"]+' </small> / <small> <i class="icon-comment"></i> 2019-4-19</small></span>';
			html += '		<p>Design must be functional and functionality must be translated into visual aesthetics</p>';
			html += '		<a href="details.html?arti='+val["article_id"]+'" class="lead">阅读文章 <i class="icon-arrow-right3"></i></a>';
			html += '	</div>';
			html += '</div>';
			html += '</div>';	
			})
		$("#list").append(html);
		})
	}
	
	/**
	* 文章的显示
	*/
	if(arti){
		$.get(sUrl+'api/index/articleHtml',{'arti':arti},function(data){
				var data = JSON.parse(data);
			 $("#article_title").html(data.data.article_title);
			 $("#article_text").html(data.data.article_text);
			 if(data.data.is_comment == 1){
				$("#is_comment").show();
			 }
		})
	}
	/**
	* 前台获取热门  以及最新的博客
	*/
	var url = document.location.toString();
	var arrObj = url.split("/");
	var obj = arrObj[arrObj.length-1];
	if(obj == '' || obj == 'index.html'){
		$.get(sUrl+'api/index/artHotNew',function(data){
			var data = JSON.parse(data);
			var hotHtml = '';
			var newHtml = '';
			console.log(data);
			// 热门
			$.each(data.data.hot,function(key, val){
			hotHtml += '<div class="col-md-3 col-sm-6 col-padding animate-box fadeInLeft animated" data-animate-effect="fadeInLeft">';
			hotHtml += '<div class="blog-entry">';
			hotHtml += '<a href="details.html?arti='+val["article_id"]+'" class="blog-img"><img src="'+sUrl+val["article_img"]+'" class="img-responsive" alt="Free HTML5 Bootstrap Template by FreeHTML5.co"></a>';
			hotHtml += '	<div class="desc">';
			hotHtml += '	<h3><a href="details.html?arti='+val["article_id"]+'">'+val["article_title"]+'</a></h3>';
			hotHtml += '		<span><small>'+val["article_nick"]+'</small> / <small> '+val["cate_name"]+' </small> / <small> <i class="icon-comment"></i> 2019-4-19</small></span>';
			hotHtml += '		<p>Design must be functional and functionality must be translated into visual aesthetics</p>';
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
			newHtml += '		<span><small>'+val["article_nick"]+'</small> / <small> '+val["cate_name"]+' </small> / <small> <i class="icon-comment"></i> 2019-4-19</small></span>';
			newHtml += '		<p>Design must be functional and functionality must be translated into visual aesthetics</p>';
			newHtml += '		<a href="details.html?arti='+val["article_id"]+'" class="lead">阅读文章 <i class="icon-arrow-right3"></i></a>';
			newHtml += '	</div>';
			newHtml += '</div>';
			newHtml += '</div>';	
			})
			$("#newBlog").append(newHtml);
			$("#hotBlog").append(hotHtml);
		})
	}
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
})