// bigTarget.js - A jQuery Plugin
// Version 1.0.1
// Written by Leevi Graham - Technical Director - Newism Web Design & Development
// http://newism.com.au
// Notes: Tooltip code from fitted.js - http://www.trovster.com/lab/plugins/fitted/

// create closure
(function($) {
	// plugin definition
	$.fn.bigTarget = function(options) {
		debug(this);
		// build main options before element iteration
		var opts = $.extend({}, $.fn.bigTarget.defaults, options);
		// iterate and reformat each matched element
		return this.each(function() {
			// set the anchor attributes
			var $a = $(this);
			var href = $a.attr('href');
			var title = $a.attr('title');
			// build element specific options
			var o = $.meta ? $.extend({}, opts, $a.data()) : opts;
			// update element styles
			$a.parents(o.clickZone)
				.hover(function() {
					$h = $(this);
					$h.addClass(o.hoverClass);
					if(typeof o.title != 'undefined' && o.title === true && title != '') {
						$h.attr('title',title);
					}
				}, function() {
					
					$h.removeClass(o.hoverClass);
					if(typeof o.title != 'undefined' && o.title === true && title != '') {
						$h.removeAttr('title');
					}
				})
				// click
				.click(function() {
					if(getSelectedText() == "")
					{
						if($a.is('[rel*=external]')){
							window.open(href);
							return false;
						}
						else {
							//$a.click(); $a.trigger('click');
							window.location = href;
						}
					}
				});
		});
	};
	// private function for debugging
	function debug($obj) {
		if (window.console && window.console.log)
		window.console.log('bigTarget selection count: ' + $obj.size());
	};
	// get selected text
	function getSelectedText(){
		if(window.getSelection){
			return window.getSelection().toString();
		}
		else if(document.getSelection){
			return document.getSelection();
		}
		else if(document.selection){
			return document.selection.createRange().text;
		}
	};
	// plugin defaults
	$.fn.bigTarget.defaults = {
		hoverClass	: 'hover',
		clickZone	: 'li:eq(0)',
		title		: true
	};
// end of closure
})(jQuery);