var listIndex={
	seven_Nav:jq('#seven_Nav'),
	seven_table:jq('#seven_table'),
	req_lock:1,
	init: function() {
        var _self = this;
        _self.seven_Nav.find('li').on('click', 
        function() {
            _self.goList(jq(this));
        });
    },
    goList: function(J_list)
    {
        if (J_list.hasClass('hover')) {
        	return false;
        }
        var cur_list = listIndex.seven_Nav.parent().find('.hover');
        var cur_id = cur_list.attr('data-sevenid');
        var next_id = J_list.addClass('hover').attr('data-sevenid');
        if ((jq('#seven_Nav' + next_id).length == 0) && (listIndex.req_lock == 1))
        {
            var req_url = SITEURL + 'forum.php?mod=forumdisplay&fid=1390&ac=forumdisplay_schedule&league_id=1&num_round_id=' + next_id;
            listIndex.req_lock = 0;
            jq.ajax({
                type: 'GET',
                url: req_url,
                dataType: 'json',
                success: function(data) {
                    listIndex.req_lock = 1;
                    if(data.code == 0)
                    {
                    	var aryic=data.match;
                        listIndex.createList(aryic, next_id);
                        jq('#seven_table' + cur_id).hide();
                        jq('#seven_table' + next_id).show();
                        cur_list.removeClass('hover');
                        J_list.addClass('hover');
                    }
                },
                error: function(xhr, type, err) {}
            });
        }
        else
        {
            jq('#seven_table' + cur_id).hide();
            jq('#seven_table' + next_id).show();
            cur_list.removeClass('hover');
            J_list.addClass('hover');
        }
    },
    createList: function(ary, id) {
        var _len = ary.length;
        var _html = '<table border="0" cellpadding="0" cellspacing="0" width="100%" id="seven_table' + id + '" class="com_shooter c_match none">';
            _html+='<colgroup>\
            <col width="190">\
            <col width="510">\
            <col width="auto">\
            </colgroup>';
        for (var i = 0; i <= _len - 1; i++)
        {
            var oo = ary[i];
            _html += '<tr>';
            _html +='<td>\
                     <span class="mat_time">'+oo.date+'</span>\
                     <span class="mat_span">'+oo.time+'</span>\
                     </td>';
            _html +='<td class="mat_ma cl">';
            _html +='<div class="mat_left">\
                     <img src="'+oo.a_logo+'">\
                     <span>'+oo.a_name+'</span>\
                     </div>';
            if(oo.a_score == ''){
            	_html +='<div class="mat_cent">VS</div>';
            }else{
            	_html +='<div class="mat_cent">'+oo.a_score+'-'+oo.b_score+'</div>';
            }
            _html +='<div class="mat_left">\
                     <img src="'+oo.b_logo+'">\
                     <span>'+oo.b_name+'</span>\
                    </div>';
            _html +='</td>';
            _html +='<td>\u573a\u5730\uff1a'+oo.address+'</td>';
            _html +='</tr>';
    
        };
        _html += '</table>';
        listIndex.seven_table.append(jq(_html));
    }
};
var listfiveIndex={
	seven_Nav:jq('#five_Nav'),
	seven_table:jq('#five_table'),
	req_lock:1,
	init: function() {
        var _self = this;
        _self.seven_Nav.find('li').on('click', 
        function() {
            _self.goList(jq(this));
        });
    },
    goList: function(J_list)
    {
        if (J_list.hasClass('hover')) {
        	return false;
        }
        var cur_list = listfiveIndex.seven_Nav.parent().find('.hover');
        var cur_id = cur_list.attr('data-fiveid');
        var next_id = J_list.addClass('hover').attr('data-fiveid');
        if ((jq('#five_Nav' + next_id).length == 0) && (listfiveIndex.req_lock == 1))
        {
            var req_url = SITEURL + 'forum.php?mod=forumdisplay&fid=1390&ac=forumdisplay_schedule&league_id=2&num_round_id=' + next_id;
            listfiveIndex.req_lock = 0;
            jq.ajax({
                type: 'GET',
                url: req_url,
                dataType: 'json',
                success: function(data) {
                    listfiveIndex.req_lock = 1;
                    if(data.code == 0)
                    {
                    	var aryic=data.match;
                        listfiveIndex.createList(aryic, next_id);
                        jq('#five_table' + cur_id).hide();
                        jq('#five_table' + next_id).show();
                        cur_list.removeClass('hover');
                        J_list.addClass('hover');
                    }
                },
                error: function(xhr, type, err) {}
            });
        }
        else
        {
            jq('#five_table' + cur_id).hide();
            jq('#five_table' + next_id).show();
            cur_list.removeClass('hover');
            J_list.addClass('hover');
        }
    },
    createList: function(ary, id) {
        var _len = ary.length;
        var _html = '<table border="0" cellpadding="0" cellspacing="0" width="100%" id="five_table' + id + '" class="com_shooter c_match none">';
            _html+='<colgroup>\
            <col width="190">\
            <col width="510">\
            <col width="auto">\
            </colgroup>';
        for (var i = 0; i <= _len - 1; i++)
        {
            var oo = ary[i];
            _html += '<tr>';
            _html +='<td>\
                     <span class="mat_time">'+oo.date+'</span>\
                     <span class="mat_span">'+oo.time+'</span>\
                     </td>';
            _html +='<td class="mat_ma cl">';
            _html +='<div class="mat_left">\
                     <img src="'+oo.a_logo+'">\
                     <span>'+oo.a_name+'</span>\
                     </div>';
            if(oo.a_score == ''){
            	_html +='<div class="mat_cent">VS</div>';
            }else{
            	_html +='<div class="mat_cent">'+oo.a_score+'-'+oo.b_score+'</div>';
            }
            _html +='<div class="mat_left">\
                     <img src="'+oo.b_logo+'">\
                     <span>'+oo.b_name+'</span>\
                    </div>';
            _html +='</td>';
            _html +='<td>\u573a\u5730\uff1a'+oo.address+'</td>';
            _html +='</tr>';
    
        };
        _html += '</table>';
        listfiveIndex.seven_table.append(jq(_html));
    }
};
jQuery.noConflict();
jQuery(function($){
	listIndex.init();
	listfiveIndex.init();

});
function cg(id,m){
             var n=3;
             for(var i=1;i<=3;i++){
             jq("#"+id+"_lb_"+i).removeClass(" active");
             jq("#"+id+"_div_"+i).css("display","none");
             if(m==i){
             jq("#"+id+"_lb_"+i).addClass(" active");
             jq("#"+id+"_div_"+i).css("display","");      
           }
         }
}
