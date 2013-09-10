wooShortcodeMeta={
	attributes:[
			{
				label:"List Style",
				id:"style",
				help:'Various list numbering styles.<br /><br />When clicking "Insert", a list will be made available for entering list items. Press "ENTER" to create a new item.', 
				controlType:"select-control", 
				selectValues:['armenian', 'decimal', 'decimal-leading-zero', 'georgian', 'lower-alpha', 'lower-greek', 'lower-latin', 'lower-roman', 'upper-alpha', 'upper-latin', 'upper-roman'],
				defaultValue: 'decimal', 
				defaultText: 'decimal (Default)'
			}
			],
	defaultContent:"",
	customMakeShortcode: function(b){
		var a=b.data;
		
		if(!b)return"";
		
		// var c=b.content;
		
		// c=c.split( "|" );
		
		var g = '[ordered_list style="' + b.style + '"]<ol>';
			
		g += "<li>" + "Place your list items here" + "</li>"
				
		g += '</ol>[/ordered_list] ';
		
		return g
	
	}
};