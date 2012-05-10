(function($){
$(document).ready(function(){
	
var description = $($('.description')),
	block = $($('.category-block'));

	block.hover(function(){
		
		$(this).find('.description').animate({
			top: 0
		});
		
	}, function(){
		$(this).find(description).animate({
			top: '100%'
		});

	});
});
})(jQuery);