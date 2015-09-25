/**
 * Created by young on 15/9/2.
 */

$(function () {

    function toHtml(code) {
        return code.replace(/&amp;/g, '&');
    }

    function loadmore(buttinId, templ, container, url, templFn) {
        var doTempl = doT.template(toHtml($(templ).text())),
            nextPage = 2,
            isLoading = false,
            hasNexPage = true;

        $(buttinId).click(function () {
            if (hasNexPage && !isLoading) {
                isLoading = true;
                try    {
                    url = url.replace(/fid=\d+/, /fid=\d+/.exec(location.search)[0]);
                    url = url.replace(/page=\d+/, "page=" + nextPage);
                } catch (exception) {
                    console.log("not need fid and page");
                }
                if (url.indexOf("http") != 0) {
                    url = "http://" + location.host + "/" + url;
                }
                var list = $(container);

                $.getJSON(url, function (data) {
                    isLoading = false;
                    nextPage++;

                    if (data.nextpage == 1) {
                        hasNexPage = false;
                        $(buttinId).text("没有更多了");
                    }
                    list.append(templFn(doTempl, data));
                });
            }

            return false;
        });
    }

    var delMemStatus = false;
    // 删除会员
    $(".navbar .icon-garbage").click(function () {
        delMemStatus = true;
        $(".author-thumbs .icon-del").addClass("active");
        $(".navbar .right").find("a").hide().end()
            .find(".cancel").addClass("active");

        $(".author-thumbs .author-thumb").each(function () {
            var me = $(this);
            me.find("a:eq(0)").attr("href", me.find("a:eq(1)").attr("href"));
        });
    });

    $(".navbar .right .cancel").click(function () {
        delMemStatus = false;
        $(".author-thumbs .icon-del").removeClass("active");
        $(".navbar .right").find("a").show().end()
            .find(".cancel").removeClass("active");

        $(".author-thumbs .author-thumb").each(function () {
            var me = $(this);
            me.find("a:eq(0)").attr("href", me.find("a:eq(0)").attr("data-url"));
        });
    });

    loadmore("#load-more-group-index",
        "#load-more-group-index-templ",
        ".ylist",
        "forum.php?mod=group&fid=1367&page=2&ajax=1&mobile=2",
        function(doTempl, data) {
            console.log(data);
            return doTempl(data.threadlist);
    });

    loadmore("#load-more-group-memberlist",
        "#load-more-group-memberlist-templ",
        ".author-thumbs",
        "forum.php?mod=group&action=memberlist&fid=1367&page=2&ajax=1&mobile=2",
        function(doTempl, data) {
            var ret = doTempl(data.userlist);
            if (delMemStatus) {
                ret = ret.replace(/icon-del/g, "icon-del active");
            }
            return ret;
        });
    loadmore("#load-more-group-manage",
        "#load-more-group-manage-templ",
        ".author-thumbs",
        "forum.php?mod=group&action=manage&op=manageuser&fid=1367&page=2&ajax=1&mobile=2",
        function(doTempl, data) {
            return doTempl(data.userlist);
        });
    loadmore("#load-more-group-activity",
        "#load-more-group-activity-templ",
        "#ch_actbox",
        "forum.php?mod=group&action=activity&fid=1367&page=2&ajax=1&mobile=2",
        function(doTempl, data) {
            return doTempl(data.threadlist);
        });
    loadmore("#load-more-forum-discuz",
        "#load-more-forum-discuz-templ",
        "#forum-discuz",
        "forum.php?page=2",
        function(doTempl, data) {
            return doTempl(data.threadlist);
        });

    // 球迷会
    $('#league_1').addClass('cur');
    $('.hsub_team>li').each(function () {
        var me = $(this);
        me.click(function () {
            me.addClass('cur')
                .siblings().removeClass('cur');

            $('.hst_club').hide();
            $('#club_' + /league_(\d+)/.exec(me.attr('id'))[1]).show();
        });
    });
    // active first .hclub_li
    $('.hclub_li:eq(0)').addClass('active');

    // group-index-templ
    var hsub_content = $('.hsub_content ul');
    $('.hst_club a:not(".hst_r_icon")').each(function () {
        var me = $(this);
        me.click(function () {
            hsub_content.empty();
            $('.hst_club .hclub_li').removeClass('active');
            me.parent().addClass('active');

            $.get(me.attr('data-href'), function (data) {
                data = JSON.parse(data);
                if (!$.isEmptyObject(data)) {
                    var temFn = doT.template(toHtml($('#group-index-templ').text()));
                    hsub_content.append(temFn(data));
                } else {
                    hsub_content.append('<li><p style="text-align: center;color: #ccc;">暂无球迷会列表</p></li>');
                }
            });

            return false;
        });
    });

    var radiusThumbSelector = ['.author-thumb > img'];
    $(radiusThumbSelector.join(',')).each(function () {
        var me = $(this),
            containerHeight = me.parent().height(),
            containerWidth = me.parent().width();
        me.load(function () {
            var naturalHeight = this.naturalHeight,
                naturalWidth = this.naturalWidth;
            if (naturalWidth == naturalHeight) {
                return;
            } else if (naturalWidth > naturalHeight) {
                me.parent().css({
                    overflow: "hidden",
                });
                me.css({
                    maxWidth: "none",
                    width: "auto",
                    height: "100%",
                    borderRadius: "0",
                    marginLeft: (naturalHeight * containerWidth / naturalWidth - containerWidth) / 2
                });
            } else {
                me.parent().css({
                    overflow: "hidden",
                });
                me.css({
                    maxWidth: "none",
                    width: "100%",
                    height: "auto",
                    borderRadius: "0",
                    marginTop: (naturalWidth * containerWidth / naturalHeight - containerHeight) / 2
                });
            }
        });
    });
});