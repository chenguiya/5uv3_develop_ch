/* 
 *下拉菜单函数
 */
define(function(require, exports, module) {
	var $ = require('$');
	exports.showMenue = function(ctrlobj, menueobj, delay, layer) {
		var settime = '';
		delay = delay ? delay : 500;
		layer = layer ? layer : 1;
		if (menueobj.is(':hidden')) {
			menueobj.stop().fadeIn(50, function() {
				$(this).attr('data-showMenue',layer);
				$('[data-showMenue="'+layer+'"]').not(this).fadeOut(50);
			});
		}
		
		ctrlobj.on('mouseout',function(){
			clearTimeout(settime);
			settime = setTimeout(function(){menueobj.stop().fadeOut(delay,function(){menueobj.removeAttr('data-showMenue');});},delay);
		});
		
		menueobj.on('mouseover',function(){
			clearTimeout(settime);
			$(this).stop().fadeIn(50,function(){
				$(this).attr('data-showMenue',layer);
			});
		}).on('mouseout',function(){
			$(this).stop().fadeOut(50,function(){
				$(this).removeAttr('data-showMenue');
			});
		});
	};
	
});

