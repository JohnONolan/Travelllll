wooShortcodeMeta={
	attributes:[
			{
				label:"List Style",
				id:"style",
				help:'Various list bullet styles.<br /><br />When clicking "Insert", a list will be made available for entering list items. Press "ENTER" to create a new item.', 
				controlType:"select-control", 
				selectValues:['tick', 'red-x', 'bullet', 'green-dot', 'arrow', 'star'],
				defaultValue: 'tick', 
				defaultText: 'tick (Default)'
			}
			],
	defaultContent:"",
	customMakeShortcode: function(b){
		var a=b.data;
		
		if(!b)return"";
		
		// var c=b.content;
		
		// c=c.split( "|" );
		
		var g = '[unordered_list style="' + b.style + '"]<ul>';
		
		g += "<li>" + "Place your list items here" + "</li>"
				
		g += '</ul>[/unordered_list] ';
		
		return g
	
	}
};