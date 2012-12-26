<?php
	require('../../global.php');
	require('inc/setting.inc.php');
	
	//验证是否登入
	if(!checkLogin()){
		header('Location: ../error.php?code='.$errorcode['noLogin']);
	}
?>
<!DOCTYPE HTML>
<html>
<head>
<meta charset="utf-8">
<title>应用市场</title>
<?php include('sysapp/global_css.php'); ?>
<link rel="stylesheet" href="../../img/ui/sys.css">
</head>

<body>
<div class="sub-nav">
	<ul class="nav-sort">
		<li class="focus" value="0"><a href="javascript:;">全部</a></li>
		<?php
			$mytype = $db->select(0, 1, 'tb_member', 'type', 'and tbid='.$_SESSION['member']['id']);
			foreach($apptype as $at){
				if(($at['id'] == 1 && $mytype['type'] == 1) || $at['id'] != 1){
					echo '<li value="'.$at['id'].'"><a href="javascript:;">'.$at['name'].'</a></li>';
				}
			}
		?>
	</ul>
	<dl class="nav-personal">
		<dt></dt>
		<dd value="-1"><a href="javascript:;" class="per-app">我的应用</a></dd>
	</dl>
	<input type="hidden" name="search_1" id="search_1" value="">
</div>
<div class="wrap">
	<div class="col-sub">
		<div class="search-box">
			<div class="input-append">
				<input type="text" name="keyword" id="keyword" style="width:158px"><button id="search_3" class="btn"><i class="icon-search"></i></button>
			</div>
		</div>
		<div class="mbox commend-day">
			<h3>今日推荐</h3>
			<div class="commend-container">
				<a href="?ct=app&amp;ac=show&amp;app_id=192">
					<!--img src="http://open.115.com/static/uploads/ico/2012021316591511928.png" alt="图吧地图"-->			
				</a>
			</div>
			<div class="commend-text">
				<h4>
					<strong>图吧地图</strong>
					<span>33593人在用</span>				
				</h4>
				<div class="con">
					图吧地图是国内最大的在线电子地图及无线地图服务提供商，市场占有率超过80％。图吧地图（map）为互联网和手机用户提供地图搜索、位置查询和公交，驾车线路等交通规划服务，为行业客户提供GIS行业应用解决方案和地图API产品，同时为中小企业客户提供推广必备的地图标注产品。				
				</div>
				<a href="javascript:;" app_id="192" class="btn-add">添加应用</a>
			</div>
			<span class="star-box"><i style="width:68%;"></i></span>
		</div>
		<div class="mbox commend-day">
			<h3>我也要开发应用</h3>
		</div>
	</div>
	<div class="col-main">
		<div class="mbox app-list-box">
			<div class="title">
				<ul>
					<li class="focus" value="1"><a href="javascript:;">最新应用</a></li>
					<li value="2"><a href="javascript:;">最热门</a></li>
					<li value="3"><a href="javascript:;">最高评价</a></li>
					<input type="hidden" name="search_2" id="search_2" value="1">
				</ul>
			</div>
			<ul class="app-list"></ul>
			<div class="pagination pagination-centered" style="margin-top:6px"><ul id="pagination"></ul></div>
			<?php $appcount = $db->select(0, 2, 'tb_app', 'tbid'); ?>
			<input id="pagination_setting" type="hidden" maxrn="<?php echo $appcount; ?>" prn="5" pid="0" />
		</div>
	</div>
</div>
<?php if(isset($id)){ ?>
	<div id="detailIframe" style="background:#fff;position:fixed;z-index:1;top:0;left:60px;right:0;height:100%">
		<iframe frameborder="0" src="detail.php?id=<?php echo $id; ?>" style="width:100%;height:100%"></iframe>
	</div>
<?php }else{ ?>
	<div id="detailIframe" style="background:#fff;position:fixed;z-index:1;top:0;left:140px;right:0;height:100%;display:none">
		<iframe frameborder="0" style="width:100%;height:100%"></iframe>
	</div>
<?php } ?>
<?php include('sysapp/global_js.php'); ?>
<script>
$(function(){
	//detailIframe
	openDetailIframe2 = function(url){
		ZENG.msgbox.show('正在载入中，请稍后...', 6, 100000);
		$('#detailIframe iframe').attr('src', url).load(function(){
			ZENG.msgbox._hide();
			$('#detailIframe').animate({
				'left' : '60px',
				'opacity' : 'show'
			}, 500);
		});
	};
	closeDetailIframe2 = function(callback){
		$('#detailIframe').animate({
			'left' : 0,
			'opacity' : 'hide'
		}, 500, function(){
			$('#detailIframe').css('left', '140px');
			callback && callback();
		});
	};
	$('.nav-sort li, .nav-personal dd').click(function(){
		closeDetailIframe2();
		$('.nav-sort li, .nav-personal dd').removeClass('focus');
		$(this).addClass('focus');
		$('#search_1').val($(this).attr('value'));
		$('.app-list-box .title li').removeClass('focus').eq(0).addClass('focus');
		$('#search_2').val(1);
		pageselectCallback(-1);
	});
	$('.app-list-box .title li').click(function(){
		$('.app-list-box .title li').removeClass('focus');
		$(this).addClass('focus');
		$('#search_2').val($(this).attr('value'));
		pageselectCallback(-1);
	});
	//搜索按钮
	$('#search_3').click(function(){
		$('.app-list-box .title li').removeClass('focus').eq(0).addClass('focus');
		$('.nav-sort li').removeClass('focus').eq(0).addClass('focus');
		$('#search_1').val(0);
		$('#search_2').val(1);
		pageselectCallback(-1);
	});
	//添加应用
	$('.btn-add-s').live('click', function(){
		var appid = $(this).attr('app_id');
		$(this).removeClass().addClass('btn-loading-s');
		window.parent.HROS.app.add(appid, function(){
			pageselectCallback();
			window.parent.HROS.app.get();
		});
	});
	//删除应用
	$('.btn-remove-s').live('click', function(){
		window.parent.HROS.app.remove($(this).attr('app_id'), function(){
			pageselectCallback();
			window.parent.HROS.app.get();
		});
	});
	//打开应用
	$('.btn-run-s').live('click', function(){
		if($(this).attr('app_type') == 'app'){
			window.parent.HROS.window.create($(this).attr('app_id'));
		}else{
			window.parent.HROS.widget.create($(this).attr('app_id'));
		}
	});
	//加载列表
	pageselectCallback(0);
});
function initPagination(cpn){
	$('#pagination').pagination(parseInt($('#pagination_setting').attr('maxrn')), {
		current_page : cpn,
		items_per_page : parseInt($('#pagination_setting').attr('prn')),
		num_display_entries : 6,
		callback : pageselectCallback,
		prev_text : '上一页',
		next_text : '下一页',
		corner : '0'
	});
}
function pageselectCallback(page_id, reset){
	ZENG.msgbox.show('正在加载中，请稍后...', 6, 100000);
	page_id = (page_id == undefined || isNaN(page_id)) ? $('#pagination_setting').attr('pid') : page_id;
	if(page_id == -1){
		page_id = 0;
		reset = 1;
	}
	var from = page_id * parseInt($('#pagination_setting').attr('prn')), to = parseInt($('#pagination_setting').attr('prn')); 
	$.ajax({
		type : 'POST',
		url : 'index.ajax.php',
		data : 'ac=getList&reset=' + reset + '&from=' + from + '&to=' + to + '&search_1=' + $('#search_1').val() + '&search_2=' + $('#search_2').val() + '&search_3=' + $('#keyword').val(),
		success : function(msg){
			var arr = msg.split('<{|*|}>');
			if(parseInt(arr[0], 10) != -1){
				$('#pagination_setting').attr('maxrn', arr[0]);
				initPagination(page_id);
			}
			$('.app-list').html(arr[1]);
			ZENG.msgbox._hide();
		}
	}); 
}
</script>
</body>
</html>