(function(){
	tinymce.create('tinymce.plugins.print_grid', { 
		//ed is an instance of the editor and url is the url to the js code that wp supplies 
        init : function(ed, url) {  
            ed.addButton('print_grid', {  
                title : 'Drop a grid of category images in',  
                image : url+'/image.png',  
                onclick : function() {  
                     ed.selection.setContent('[print_grid]' + ed.selection.getContent());  
  
                }  
            });  
        },  
        createControl : function(n, cm) {  
            return null;  
        },  
    });  
    tinymce.PluginManager.add('print_grid', tinymce.plugins.print_grid);  
})();