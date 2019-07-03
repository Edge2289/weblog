$("#sendMessage").click(function(){
		var message = $("#message").val();
		if (message == '' || message == null) {
			alert("请输入评论内容");
			return false;
		}
		// 先判断用户是否登录
		// 登录后显示问题
		// 回复评论时判断用户是否登录
		var data = localStorage.getItem("user_nick");
		if (data == null) {
			$("#yincanmoyitai").trigger('click');
			return false;
		}
		// 根据前台的数据去判断用户登录是否过期
		var static = 0;
		$.ajaxSettings.async = false;
		$.get(sUrl+'api/index/isLogin',{'user_qq' : localStorage.getItem("user_qq"),// http://bogo.uikiss.cn/
				'access_token' : localStorage.getItem("access_token"),},function(data){
				var data = JSON.parse(data);
				if (data.code == -1) {
					alert("登录已过期");
					localStorage.clear();
					$(".login").show();
					$(".logutout").hide();
					static = 1;
					return false;
				}
		})		
		if (static) {
			return false;
		}	
        //获取输入内容
		// 所需的参数
        var sNick = localStorage.getItem("user_nick");
        var sImg = localStorage.getItem("user_img");
        var sqq = localStorage.getItem("user_qq");
        var oSize = $("#message").val();
		// 往后台push数据
		$.ajax({
			'type' : 'post',
			'url' : sUrl+'api/index/commentIns',
			'data' : {'sqq':sqq,'oSize':oSize,'aid':$("#article_id").val()},
			'dataType' : 'json',
			success:function(data){
				var data = JSON.param(data);
				if (data.code == 0) {
					static = 1;
				}
			},
			error:function(){
				alert('请重新提交');
					static = 1;
			}
		})		
		if (static) {
			return false;
		}	

		// ajax 成功则添加节点
		// 已经登录  js添加节点到html
        var myDate = new Date();
        //获取当前年
        var year=myDate.getFullYear();
        //获取当前月
        var month=myDate.getMonth()+1;
        //获取当前日
        var date=myDate.getDate();
        var h=myDate.getHours();       //获取当前小时数(0-23)
        var m=myDate.getMinutes();     //获取当前分钟数(0-59)
        if(m<10) m = '0' + m;
        var s=myDate.getSeconds();
        if(s<10) s = '0' + s;
        var now=year+'-'+month+"-"+date+" "+h+':'+m+":"+s;

        //动态创建评论模块
        oHtml = '<div class="comment-show-con clearfix"><div class="comment-show-con-img pull-left"><img src="'+sImg+'" width="38px" style="border-radius: 50%;" alt=""></div> <div class="comment-show-con-list pull-left clearfix"><div class="pl-text clearfix"> <a href="#" class="comment-size-name"><span class="userNick" data-id="'+sqq+'">'+sNick+'</span>: </a> <span class="my-pl-con">&nbsp;'+ oSize +'</span> </div> <div class="date-dz"> <span class="date-dz-left pull-left comment-time">'+now+'</span> <div class="date-dz-right pull-right comment-pl-block"><a href="javascript:;" class="removeBlock">删除</a> <a href="javascript:;" class="date-dz-pl pl-hf hf-con-block pull-left">回复</a> <span class="pull-left date-dz-line">|</span> <a href="javascript:;" class="date-dz-z pull-left"><i class="date-dz-z-click-red"></i>赞 (<i class="z-num">666</i>)</a> </div> </div><div class="hf-list-con"></div></div> </div>';
        if(oSize.replace(/(^\s*)|(\s*$)/g, "") != ''){
            $('.comment-show').prepend(oHtml);
            $(this).siblings('.flex-text-wrap').find('.comment-input').prop('value','').siblings('pre').find('span').text('');
        }
    });
<!--点击回复动态创建回复块-->
    $('.comment-show').on('click','.pl-hf',function(){
        //获取回复人的名字
        var fhName = $(this).parents('.date-dz-right').parents('.date-dz').siblings('.pl-text').find('.comment-size-name').children('.userNick').html();
        //回复@
        var fhN = '回复@'+fhName;
        //var oInput = $(this).parents('.date-dz-right').parents('.date-dz').siblings('.hf-con');
        var fhHtml = '<div class="hf-con pull-left"> <textarea class="content comment-input hf-input" placeholder="" onkeyup="keyUP(this)"></textarea> <a href="javascript:;" class="hf-pl">评论</a></div>';
        //显示回复
        if($(this).is('.hf-con-block')){
            $(this).parents('.date-dz-right').parents('.date-dz').append(fhHtml);
            $(this).removeClass('hf-con-block');
            $('.content').flexText();
            $(this).parents('.date-dz-right').siblings('.hf-con').find('.pre').css('padding','6px 15px');
            //console.log($(this).parents('.date-dz-right').siblings('.hf-con').find('.pre'))
            //input框自动聚焦
            $(this).parents('.date-dz-right').siblings('.hf-con').find('.hf-input').val('').focus().attr('vval',fhN);
            $(this).parents('.date-dz-right').siblings('.hf-con').find('.hf-input').val('').focus().attr('placeholder',fhN);
        }else {
            $(this).addClass('hf-con-block');
            $(this).parents('.date-dz-right').siblings('.hf-con').remove();
        }
    });
<!--评论回复块创建-->
    $('.comment-show').on('click','.hf-pl',function(){
    	// 先判断用户是否登录
		// 登录后显示问题
		// 回复评论时判断用户是否登录
		var data = localStorage.getItem("user_nick");
		if (data == null) {
			$("#yincanmoyitai").trigger('click');
			return false;
		}
		// 根据前台的数据去判断用户登录是否过期
		var static = 0;
		$.ajaxSettings.async = false;
		$.get(sUrl+'api/index/isLogin',{'user_qq' : localStorage.getItem("user_qq"),// http://bogo.uikiss.cn/
				'access_token' : localStorage.getItem("access_token"),},function(data){
				var data = JSON.parse(data);
				if (data.code == -1) {
					alert("登录已过期");
					localStorage.clear();
					$(".login").show();
					$(".logutout").hide();
					static = 1;
					return false;
				}
		})		
		if (static) {
			return false;
		}	
    	
        var oThis = $(this);
        var myDate = new Date();
        //获取当前年
        var year=myDate.getFullYear();
        //获取当前月
        var month=myDate.getMonth()+1;
        //获取当前日
        var date=myDate.getDate();
        var h=myDate.getHours();       //获取当前小时数(0-23)
        var m=myDate.getMinutes();     //获取当前分钟数(0-59)
        if(m<10) m = '0' + m;
        var s=myDate.getSeconds();
        if(s<10) s = '0' + s;
        var now=year+'-'+month+"-"+date+" "+h+':'+m+":"+s;
        //获取输入内容 以及一下Ajax参数
        var sNick = localStorage.getItem("user_nick");
        var sId = localStorage.getItem("user_qq");
        var target_id = $(this).parents('.hf-con').parents('.date-dz').siblings('.pl-text').find('.comment-size-name').children('.userNick').attr('data-id');
        // 值
        var oHfVal = $(this).siblings('.flex-text-wrap').find('.hf-input').val();
        var oHfVval = $(this).siblings('.flex-text-wrap').find('.hf-input').attr('vval');

        var oHfName = $(this).parents('.hf-con').parents('.date-dz').siblings('.pl-text').find('.comment-size-name').children('.userNick').html();
        var oAllVal = '回复@'+oHfName;
        var sqq = localStorage.getItem("user_qq");
        var oHfVvalData = oHfVval.split("@");
        var static = 0;
        // 往后台push数据
		$.ajax({
			'type' : 'post',
			'url' : sUrl+'api/index/commentIns',
			'data' : {'sqq':sqq,'oSize':oHfVal,'tid':target_id,'aid':$("#article_id").val()},
			'dataType' : 'json',
			success:function(data){
				var data = JSON.param(data);
				if (data.code == 0) {
					static = 1;
				}
			},
			error:function(){
				alert('请重新提交');
					static = 1;
			}
		})		
		if (static) {
			return false;
		}

        if(oHfVal.replace(/^ +| +$/g,'') == '' || oHfVal == oAllVal){
        	alert('请输入需要回复的内容');
        }else {
		var oHtml = '<div class="all-pl-con"><div class="pl-text hfpl-text clearfix"><a href="javascript:void(0)" class="comment-size-name"><span class="userNick" data-id="1">'+sNick+'</span> </a><span class="my-pl-con comment-size-name">'+oHfVvalData[0]+'@<span class="comment-size-name">'+oHfVvalData[1]+'：</span>'+oHfVal+'</span></div><div class="date-dz"> <span class="date-dz-left pull-left comment-time">2019-10-10</span> <div class="date-dz-right pull-right comment-pl-block"> <a href="javascript:;" class="removeBlock">删除</a> <a href="javascript:;" class="date-dz-pl pl-hf hf-con-block pull-left">回复</a> <span class="pull-left date-dz-line">|</span> <a href="javascript:;" class="date-dz-z pull-left"><i class="date-dz-z-click-red"></i>赞 (<i class="z-num">666</i>)</a> </div> </div></div>';
        oThis.parents('.hf-con').parents('.comment-show-con-list').find('.hf-list-con').css('display','block').append(oHtml) && oThis.parents('.hf-con').siblings('.date-dz-right').find('.pl-hf').addClass('hf-con-block') && oThis.parents('.hf-con').remove();
        }
    });
<!--删除评论块-->
    $('.commentAll').on('click','.removeBlock',function(){
        var oT = $(this).parents('.date-dz-right').parents('.date-dz').parents('.all-pl-con');
        if(oT.siblings('.all-pl-con').length >= 1){
            oT.remove();
        }else {
            $(this).parents('.date-dz-right').parents('.date-dz').parents('.all-pl-con').parents('.hf-list-con').css('display','none')
            oT.remove();
        }
        $(this).parents('.date-dz-right').parents('.date-dz').parents('.comment-show-con-list').parents('.comment-show-con').remove();

    })
<!--点赞-->
    $('.comment-show').on('click','.date-dz-z',function(){
        var zNum = $(this).find('.z-num').html();
        if($(this).is('.date-dz-z-click')){
            zNum--;
            $(this).removeClass('date-dz-z-click red');
            $(this).find('.z-num').html(zNum);
            $(this).find('.date-dz-z-click-red').removeClass('red');
        }else {
            zNum++;
            $(this).addClass('date-dz-z-click');
            $(this).find('.z-num').html(zNum);
            $(this).find('.date-dz-z-click-red').addClass('red');
        }
    })
			$(function() {
				editormd.markdownToHTML("article_texta", {
					htmlDecode      : "style,script,iframe",  
					emoji           : true,
					taskList        : true,
					tex             : true,  // 默认不解析
					flowChart       : true,  // 默认不解析
					sequenceDiagram : true  // 默认不解析
				});
				
			$('img').error(function(){
						var _this = this;
						var src = sUrl+$(_this).attr("src");
						let index = $(_this).attr("src").split('=')[1];
						setTimeout(function () {
						       if(index == undefined){
						            $(_this).attr('src',src + '?timestemp=10');//请求时加上时间戳，防止缓存在
						       } else if(parseInt(index)>0){
						            _this.url = _this.url.split('?')[0]+'?timestemp='+(parseInt(index)-1);//重复请求10次
						       }else{
						            _this.url = $(_this).attr('src',src + '?timestemp=10')//默认图片
						       }
						   }.bind(this), 1000);

			});

            });