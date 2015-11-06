/**
*	桌面端脚本
*	since: 2015/4/14
*	version: 1.0.0
**/
$.noConflict();
jQuery(function($){
	// data-tab plugins. use data-tab & data-tab-index attribute
	$("[data-tab]").each(function () {
		var me = $(this),
			index = me.attr("data-tab"),
			group = index.split("-")[0];

		me.click(function () {
			me.addClass("active")
				.siblings().removeClass("active");
			$("[data-tab-index^=" + group + "]").hide();
			$("[data-tab-index=" + index + "]").show();
			return false;
		});
	});

	// 首页导航条高亮判断 group news pic video live 
	var navRet = /(?:football|basket|group|live|jifen|activity|fans|tag|standings|scorer)/.exec(location.href),
		navRetMap = {
			football: 1,
			basket: 2,
			group: 3,
			live: 6,
			standings: 6,
			scorer: 6,
			jifen: 7,
			activity: 5,
			fans: 3,
			tag: 4
		};
	if (navRet == null) {
		if (location.href.indexOf("misc.php?") > 0) {
			$(".chnav.cl").find("li:eq(4)").addClass("a");
		}
		
		if(location.href.indexOf("thread") > 0) // 如果是帖子详细页面，去高亮 by 野百合
		{}
		else
		{
			$(".chnav.cl").find("li:eq(0)").addClass("a");
		}
	}else if (navRet) {
		$(".chnav.cl").find("li").eq(navRetMap[navRet[0]]).addClass("a");
	}
	// 帮助菜单
	if ('http://www.5usport.com/group/128' === location.href) {
		$(".chnav.cl").find("li:eq(3)").removeClass("a");
	}

	// 下拉弹出菜单 @tvrcgo
	$.fn.dropdown = function(options){

		options = options || {};

		var closemenu = true;

		$(this).hover(function(){
			$(options.target).show().hover(function(){
				closemenu = false;
			}, function(){
				closemenu = true;
				$(options.target).hide();
			});
		}, function(evt){
			setTimeout(function(){
				if (closemenu) {
					$(options.target).hide();
				}
			}, options.delay || 200);
		});
	};

	$('.btn-usermanage').dropdown({ target:"#usermanage_menu" });
	$('.btn-contentmanage').dropdown({ target:"#contentmanage_menu" });

	// @young
	function heightAnim(node, option){
		var that = {};

		option = option ? option : {};

		that.padding = option.padding ? option.padding : node.css("padding");
		that.border = option.border ? option.border : node.css("border");
		that.heightCompensation = option.heightCompensation ? option.heightCompensation : 0;
		that.transition = option.transition ? option.transition : "all .3s ease-in-out";
		that.getChildrenHeight = option.getChildrenHeight ? option.getChildrenHeight : function () {
			return $(this).outerHeight() + that.heightCompensation;
		};

		that.hidden = function () {
			node[0].style.height = node.height() + "px";
			node.css({
				height: 0,
				padding: 0,
				border: 0
			});
		};

		that.show = function () {
			var height = 0;
			node.children().each(function () {
				height += that.getChildrenHeight.call(this);
			});
			node.css({
				height: height,
				padding: that.padding,
				border: that.border
			});
		};

		// init
		node.css({
			height: 0,
			padding: 0,
			border: 0,
			display: "block",
			overflow: "hidden",
			transition: that.transition
		});

		return that;
	}


	// 球迷会广场首页：左则一级分类 @young
	$(".squareSide .navTitle>p").each(function (L1_index) {
		var me  = $(this);
		var anim = heightAnim(me.next(), {heightCompensation: 5});

		me.click(function () {
			var parent = me.parent();
			if (parent.hasClass("navTitleOpen")) {
				parent.removeClass("navTitleOpen");
				me.next().css("height", me.next().height());
				anim.hidden();
			} else {
				parent.addClass("navTitleOpen");
				anim.show();
			}
		});


		// 球迷会广场首页：左则二级分类
		me.next().find(".titleOperate").each(function (l2_index) {
			var me = $(this);
			var anim = heightAnim(me.next(), {
				getChildrenHeight: function () {
					return this.children[0].clientHeight;
				}
			});

			me.click(function () {
				me.parent().parent().css("height", "auto");
				if (me.hasClass("jian")) {
					me.removeClass("jian");
					anim.hidden();
				} else {
					me.addClass("jian");
					anim.show();
				}
			});

			me.prev().click(function () {
				//this.hash = L1_index + "-" + l2_index;
			});

			me.next().find("a").each(function (l3_index) {
				$(this).click(function () {
					//this.hash = L1_index + "-" + l2_index + "-" + l3_index;
				});
			});
		});
	});


	//if (/id=fansclub:fansclub/.test(location.href)) {
        /*
	(function squareMainMenuInit() {
		var hash = location.hash.replace("#", "").split("-"), temp;
		if (hash[0]) {
			temp = $(".squareSide .navTitle>p").eq(hash[0]).click();
		}
		if (hash[1]) {
			temp.next().find(".titleOperate").eq(hash[1]).click();
		}
	}());
    */
	//}

	// @young
	function loading(rootNode) {
		var that = {},
			state = 1; // 1:隐藏，2:显示,3：没有更多
			templ = $("<p style='display: none;text-align: center;padding: 10px'>加载中...</p>");

		rootNode.append(templ);

		that.show = function () {
			templ.show();
			state = 2;
			return that;
		};

		that.hide = function () {
			templ.hide();
			state = 1;
			return that;
		};

		that.noMore = function () {
			templ.show();
			state = 3;
			templ[0].innerHTML = "没有更多了";
			return that;
		};

		that.getState = function () {
			return state;
		};

		return that;
	}

	// @young
	var boxMain = $(".boxMain"),
		clubMain = $(".clubMain"),
		$window = $(window),
		load = loading(boxMain);

	var clubListTempl = [
			'<div class="clubList">',
				//'<span class="tag"></span>', // 精字
				'<a href="#" target="_blank" title="" class="clubImg"><img src="{cover}" title="" alt="" width="235px" height="100" /></a>',
				'<a href="#" target="_blank" title="" class="clubDes">{subject}</a>',
				'<div class="clubTxt"><span class="viewIco">{views}</span><span class="plIco">{replies}</span></div>',
				'<div class="clubInfo">',
					'<a href="#" target="_blank" title="" class="clubPhoto"><img src="{avatar}"  width="40" height="40"/></a>',
					'<div class="clubName"><a href="#" target="_blank">{author}</a><p>{deteline}</p></div>',
				'</div>',
			'</div>'
		];

	var indexPicListTempl = [
		'<div class="clubList">',
			//'<span class="tag"></span>', // 精字
			'<a href="{url}" target="_blank" title="" class="clubImg"><img src="{thumb}" title="" alt="" width="235px" /></a>',
			'<a href="{url2}" target="_blank" title="" class="clubDes">{subject}</a>',
			'<div class="clubInfo">',
				'<a href="{memberurl}" target="_blank" title="" class="clubPhoto"><img src="{avatar}"  width="40" height="40"/></a>',
				'<div class="clubName"><a href="{memberurl2}" target="_blank">{author}</a><p>{datetime}</p></div>',
				'<div class="clubTxt"><div class="viewIco">{views}</div><div class="plIco">{replies}</div></div>',
			'</div>',
		'</div>'
	];

	// @young
	function createTempl(templ, data) {
		var key,ret;

		if ($.isArray(templ)) {
			templ = templ.join("");
		}
		for (key in data) {
			templ = templ.replace("{" + key + "}", data[key]);
		}

		if (data.cover) {
			templ = templ.replace("{url}", data.cover);
		}

		ret = $(templ);

		if (!data.hot) {
			ret.find(".tag").remove();
		}

		return ret[0];
	}

	// @young
	function getClubListFactory(container, msnry, templ, imagesLoad) {
		var i, ret = [], continer = container, msnry = msnry, page = 2, pageSize = 4, url = $(container).attr("data-url");

		return function () {
			if (page !==  -1) {
				if (/page=/.test(url)) {
					url = url.replace(/&page=\d+/, "&page=" + page);
				} else {
					url = url + "&page=" + page;
				}
				if(typeof($(container).attr("data-url"))!= 'undefined')
				{
					load.show();
				}

				$.getJSON(url, function (result) {
					if (result == null || result.flag == -1) {
						load.noMore();
						page = -1;
					} else {
						load.hide();
						page++;

						for (i = 0; i < result.length; i++) {
							var t = createTempl(templ, {
								hot: false,    // TODO 暂无hot数据
								cover: result[i].cover,
								subject: result[i].subject,
								views: result[i].views,
								replies: result[i].replies,
								avatar: result[i].avatar,
								author: result[i].author,
								deteline: result[i].deteline,
								datetime: result[i].datetime,
								url: result[i].url,
								url2: result[i].url,
								memberurl: result[i].memberurl,
								memberurl2 : result[i].memberurl,
								thumb: result[i].thumb
							});

							continer.appendChild(t);
							msnry.appended(t);

							var imgl = imagesLoad(t);
							imgl.on('always', function () {
								msnry.layout();
							});
						}
					}
				});
			}
		};
	}

	// @young
	if (boxMain.length > 0) {
		seajs.use(["masnory", "imagesloaded"], function (Masnory, imagesLoad) {
			var msnry = new Masnory(clubMain[0], {
				itemSelector: '.clubList',
				stamp: '.stamp',
				gutter: 15
			});

			// first page layout trigger once.
			var imgLoad = imagesLoad(clubMain[0]);
			imgLoad.on('always', function (instance) {
				msnry.layout();
			});

			var getClubList = getClubListFactory(clubMain[0], msnry, indexPicListTempl, imagesLoad);

			$window.scroll(function () {
				if ($window.scrollTop() + window.innerHeight > boxMain[0].offsetTop + boxMain.height() && load.getState() == 1) {
					getClubList();
				}
			});
		});
	}

	// 栏目页瀑布流@young
	var waterfall = $(".waterfall"),
		$wp = $("#wp"),
		lanmuIsLoading = loading(waterfall);
	if (waterfall.length > 0) {

		waterfall.masonry({
			itemSelector: '.item',
			gutter: 15
		}).imagesLoaded( function() {
			waterfall.masonry();
		});

		var getNextPage = (function () {
			var currentPage = 1,
				// url = '/misc.php?mod=tag&name=%E6%A2%85%E8%A5%BF&type=thread&op=waterwall&page=1&pagesize=12';
				url = '/misc.php?mod=tag&id=18803&type=thread&op=waterwall&page=1&pagesize=12';
			return function () {
				lanmuIsLoading.show();
				currentPage++;
				url = url.replace(/&page=\w+/, '&page=' + currentPage);
				// url = url.replace(/&name=(.+?)&/, '&name=' + /&name=(.+?&)/.exec(location.href)[1]);
				url = url.replace(/&id=(.+?)&/, '&id=' + /tag\/(.+?)\//.exec(location.href)[1] + '&');
				$.get(url, function (data) {
					if (data !== '{"status":"0"}') {
						var items = $(data).find(".waterfall .item");
						waterfall.append(items)
							.imagesLoaded( function() {
								waterfall.masonry('appended', items);
							});
					} else {
						lanmuIsLoading.noMore();
					}
					lanmuIsLoading.hide();
				});
			};
		})();

		$window.scroll(function () {
			if (lanmuIsLoading.getState() === 1 && $window.scrollTop() + window.innerHeight > $wp.height() + 153) {
				getNextPage();
			}
		});
	}

	// @young
	//if (/tpl=(?:channel_picture|channel_video)/.test(location.href)) {
	//	seajs.use(["masnory", "imagesloaded"], function (Masnory, imagesLoad) {
	//		var msnry = new Masnory(clubMain[0], {
	//			gutter: 16
	//		});
    //
	//		// first page layout trigger once.
	//		var imgLoad = imagesLoad(clubMain[0]);
	//		imgLoad.on('always', function (instance) {
	//			msnry.layout();
	//		});
    //
	//		var getClubList = getClubListFactory(clubMain[0], msnry, indexPicListTempl, imagesLoad,
	//			"plugin.php?id=fansclub:index&ac=" +
	//			/channel_(\S+)/.exec(location.href)[1] === 'picture' ? 'pic' : 'video' +
	//			"&sort=hot&return=ajax");
    //
	//		$window.scroll(function () {
	//			if ($window.scrollTop() + window.innerHeight > boxMain[0].offsetTop + boxMain.height() && load.getState() == 1) {
	//				getClubList();
	//			}
	//		});
	//	});
	//}
    //
	//// @young
	//if (/id=fansclub:fansclub&ac=lists/.test(location.href)) {
	//	seajs.use(["masnory", "imagesloaded"], function (Masnory, imagesLoad) {
	//		var msnry = new Masnory(clubMain[0], {
	//			itemSelector: '.clubList',
	//			stamp: '.stamp',
	//			gutter: 16
	//		});
    //
	//		// first page layout trigger once.
	//		var imgLoad = imagesLoad(clubMain[0]);
	//		imgLoad.on('always', function (instance) {
	//			msnry.layout();
	//		});
    //
	//		var getClubList = getClubListFactory(clubMain[0], msnry, indexPicListTempl, imagesLoad,
	//			"plugin.php?id=fansclub:index&ac=" +
	//			/type=(\S+)/.exec(location.href)[1] +
	//			"&sort=hot&return=ajax");
    //
	//		$window.scroll(function () {
	//			if ($window.scrollTop() + window.innerHeight > boxMain[0].offsetTop + boxMain.height() && load.getState() == 1) {
	//				getClubList();
	//			}
	//		});
	//	});
	//}

	// @young
	//if (/mod=forumdisplay/.test(location.href)) {
	var players = $(".playersMain");
	if (players.length > 0) {
		var containerWidth = players.width(),
			ul = players.find("ul"),
			ulWidth = ul.width(),
			pre = players.prev(".preIco"),
			next = players.next(".nextIco"),
			left;

		pre.click(function () {
			left = parseInt(ul.css("left").replace("px", "")) - 90;
			if (left >= -(ulWidth - containerWidth)) {
				ul.css("left", left);
			}
			return false;
		});

		next.click(function () {
			left = parseInt(ul.css("left").replace("px", "")) + 90;
			if (left <= 0) {
				ul.css("left", left);
			}

			return false;
		});
	}
	//}

	function getFid() {
		return /fid=(\d+)/.exec(location.href)[1];
	}

	// @young
	//if (/mod=group/.test(location.href)) {
	var newMore = $(".new_more");
	if (newMore.length != 0) {
		var i,
			tbody = $(".flick_list tbody"),
			templ =
				'<tr>' +
					'<td class="avatar_c">' +
						'<a href="home.php?mod=space&amp;uid=43" target="_blank"><img src="http://www.usport.com.cn/uc_server/avatar.php?uid=43&amp;size=middle"></a>' +
				'<p><a href="" target="_blank">{username}</a></p>' +
				'</td>' +
				'<td class="new_infor">' +
				'<div class="new_name">{title}</div>' +
				'<p class="new_title">{content}</p>' +
				'<div class="new_meta">'+
				'<span class="sta_time">{dataline}</span>' + // 2015-5-12 21:59
				'<span class="y sta_post">{replies}</span>' +
				'<span class="y sta_view">{views}</span>' +
				'</div>' +
				'<span class="experie"><i>&nbsp;</i>+{credits}经验值</span>' +
				'</td>' +
				'</tr>';

		function createTempl(templ, data) {
			var key,ret;

			if ($.isArray(templ)) {
				templ = templ.join("");
			}
			for (key in data) {
				templ = templ.replace("{" + key + "}", data[key]);
			}

			ret = $(templ);

			return ret[0];
		}

		newMore.click(function () {
			var currentPage = 2,pageSize = 20;

			return function () {
				var me = $(this);

				if (parseInt(currentPage) !== -1) {
					var url = "plugin.php?id=fansclub:fansclub&ac=home&fid=" + getFid() + "&page=" + currentPage + "&pagesiz=" + pageSize + "&do=all&ajax=1";
					$.getJSON(url, function (result) {
						if (result == -1 || result.length % pageSize ) {
							currentPage = -1;
							me.hide();
						} else {
							currentPage++;
						}

						for (i = 0; i < result.length; i++) {
							if (result[i].icon === "thread") {
								var t = createTempl(templ, {
									username: result[i].username,
									title: result[i].title_template,
									dataline: result[i].dateline,
									replies: result[i].data.replies,
									views: result[i].data.views,
									credits: result[i].credits,
									content: function () {
										var ret = "";

										// video
										if (result[i].video) {
											ret = ret + result[i].video + "<br>";
										}

										// albume
										if (result[i].attachment) {
											for (var j = 0; j < result[i].attachment.length; j++) {
												ret = ret + '<a href="' + result[i].id + '"><img src="' + result[i].attachment[j].attachment + '"></a>';
											}
											ret = ret + "<br>";
										}

										// text
										ret += result[i].message;
										return ret;
									}
								});
								tbody.append(t);
							}
						}
					});
				}

				return false;
			}
		}());
	}
	//}

	function getPage(url, sourceSelecter, targetSelecter) {
		$.get(url, function (html) {
			$(targetSelecter).append($(sourceSelecter, $(html)));
		});
	}

	//权益 @ch
	$(".rights_vip li").hover(function(){
		$(this).find(".vip_show").show(80);
		},function(){
		$(this).find(".vip_show").hide(20);
	});
	
	// 登录按钮 callback 地址 
	if ($('.ch_simp').length) {	
		var signin_callback = location.href.substr(7);
		$('.ch_QQ').attr('href', 'http://www.5usport.com/member/qqlogin/?referer='+signin_callback);
		$('.ch_webo').attr('href', 'http://www.5usport.com/member/sinalogin/?referer='+signin_callback);
	}
	
	//首页赛事告示  @ch
	(function($){
	   var scrollDiv = parseInt($('#schedules .scheCon').width());
		$('#schedules .scheCon ul').each(function() {
			$(this).width(parseInt($(this).find('li').length * 190));
			$(this).css('left', 0);
		});
		var scrollUlLeft=0;
		$('#schedules').on('click','div.sche_page a',function(){
		   var scrollUl=$('#schedules .scheCon ul');
		   var ulWidth=scrollUl.width();
		   var scrollUlLeft=scrollUl.position().left;
		   if ($(this).hasClass('prev_ch')) {
				var scrollWidth = scrollUlLeft;
				if ((scrollUlLeft + scrollDiv) <= 0) {
					scrollWidth = scrollUlLeft = scrollUlLeft + scrollDiv;
				} else if (scrollUlLeft <= 0) {
					scrollWidth = 0;
				} else {
					return;
				}
				if (!scrollUl.is(':animated')) {
					scrollUl.animate({'left': scrollWidth + "px"}, 200);
				}
			}else if ($(this).hasClass('next_ch')) {
				var scrollWidth = scrollUlLeft;
				if (Math.abs(scrollUlLeft - scrollDiv * 2) <= ulWidth) {
					scrollWidth = scrollUlLeft = scrollUlLeft - scrollDiv;
				} else if (Math.abs(scrollUlLeft - scrollDiv) <= ulWidth) {
					scrollWidth = scrollUlLeft = scrollUlLeft - (ulWidth - Math.abs(scrollUlLeft - scrollDiv));
				} else {
					return;
				}
				if (!scrollUl.is(':animated')) {
					scrollUl.animate({'left': scrollWidth + "px"}, 200);
				}
			}
		});
	})($);

    //脚部微信、新浪、手机二维码 @ch
		if ($('div.footer_beta').length) {
			$('div.footer_beta').children('a').on('mouseover', function() {
				var $box = $('div.' + $(this).attr('class') + '-box');
				var $self = $('this');
				if (!$box.length || $box.is('visible'))
					return false;
				seajs.use('showMenue', function(s) {
					s.showMenue($self, $box);
				});
			});
		}
		//合作伙伴切换 @ch
		$(".parter_Tag span").each(function(index, elen) {
			var chilidList = $(".parter_link").children();
			$(elen).mouseover(function() {
				chilidList.hide();
				$(chilidList[index]).show();
				$(".parter_Tag span").removeClass("active");
				$(this).addClass("active");
			});
		});
		$(".coor_nav span").each(function(index, elen) {
			var chilidList = $(".coor_ul").children();
			$(elen).mouseover(function() {
				chilidList.hide();
				$(chilidList[index]).show();
				$(".coor_nav span").removeClass("active");
				$(this).addClass("active");
			});
		});
		//二维码 @ch
		$(".close_c").click(function(){
			 $(".scroll_code").remove();
	    });


	var jWin = jQuery(window),
		jScroll = jQuery("#leftSideScrollCode"),
		jScrollTopBtn =jQuery("#rightSideScrollTopBtn"),
		wp = jQuery("#wp"),
		temp;
	var top = (function(container) {
		var result = container.offsetTop, c = container;
		while(c = c.offsetParent) {
			result += c.offsetTop;
		}
		return result;
	}(wp[0])) + 20;
	var jScrollBottom = top - jScroll.height();
	var jScrollTopBtnBottom = window.innerHeight;
	jScroll.css({top: top});
	jScrollTopBtn.css({bottom: 30});

	function scrollFun() {
		if ((temp = jWin.scrollTop()) < top) {
			jScroll.css({top: top - temp});
		} else if (temp >= top && temp < jScrollBottom + wp.height()){
			jScroll.css({top: 15});
		} else {
			jScroll.css({top: jScrollBottom + wp.height() - temp});
		}

		if (temp > top + wp.height() - jScrollTopBtnBottom) {
			jScrollTopBtn.css({bottom: (temp + jScrollTopBtnBottom) - (top + wp.height()) + 45});
		}
	}
	scrollFun();
	jWin.scroll(scrollFun);

	$(".channel_header .channel_icons").each(function () {
		var me = $(this);
		me.click(function () {
			if (me.hasClass("hide")) {
				me.removeClass("hide");
				me.parent().next(".channel_cont").show();
			} else {
				me.addClass("hide");
				me.parent().next(".channel_cont").hide();
			}
		});
	});
});
function getPage(url, sourceSelecter, targetSelecter) {
	$.get(url, function (html) {
		$(targetSelecter).append($(sourceSelecter, $(html)));
	});
}

function changeGodReply(targe) {
	var $this = jQuery(targe);
	jQuery.get($this.attr("href"), function (data) {
		jQuery("#replyByGod").html(jQuery(data).find("#replyByGod li"));
	});

	event.preventDefault();
	return false;
}

function changeBody(targe) {
	var $this = jQuery(targe);
	jQuery.get($this.attr("href"), function (data) {
		jQuery("#avtiveFans").html(jQuery(data).find(".active_Con li"));
	});

	event.preventDefault();
	return false;
}

function tabChange(hideSelect, showSelect) {
	jQuery(hideSelect).css("display", "none");
	jQuery(showSelect).css("display", "block");
}