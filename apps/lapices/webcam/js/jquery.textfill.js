(function($) {
	/**
    * Resizes an inner element's font so that the inner element completely fills the outer element.
    * @author Russ Painter WebDesign@GeekyMonkey.com
    * @author Blake Robertson 
    * @version 0.2 -- Modified it so a min font parameter can be specified.
    *    
    * @param {Object} Options which are maxFontPixels (default=40), innerTag (default='span')
    * @return All outer elements processed
    * @example <div class='mybigdiv filltext'><span>My Text To Resize</span></div>
    */
	$.fn.textfill = function(maxFontSize) {
		maxFontSize = parseInt(maxFontSize, 10);
		return this.each(function(){
			var ourText = $("span", this);
			function resizefont(){
				var parent = ourText.parent(),
				maxHeight = parent.height(),
				maxWidth = parent.width(),
				fontSize = parseInt(ourText.css("fontSize"), 10),
				multiplier = maxWidth/ourText.width(),
				newSize = (fontSize*(multiplier));
				ourText.css("fontSize", maxFontSize > 0 && newSize > maxFontSize ? maxFontSize : newSize );
			}
			$(window).resize(function(){
				resizefont();
			});
			resizefont();
		});
	};
})(jQuery);