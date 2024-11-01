try {
	jQuery.noConflict();
	(function($){ 
		$(document).ready(function() {
			var insertedElementCount = 0;
			$(".elementHeadLabel").each(function() {
				$(this).unbind("click").click(function() {
					if($(this).find('span').text() == '+') {
						$(this).parent().parent().children('.elementBody').css({"display":"block"});
						$(this).find('span').text('-');
					} else {
						$(this).parent().parent().children('.elementBody').css({"display":"none"});
						$(this).find('span').text('+');
					}
				});
			});
	
		 $("button").unbind("click").click(function(){
				 window.send_to_editor("[MTM element=\""+$(this).attr('id')+"\"]");
			 });	
		});
	})(jQuery);
} catch (e) {
	console.log ("Off jquery not found: "+e.message);    //this executes if jQuery isn't loaded
}
