wooShortcodeMeta={
	attributes:[
		{
			label:"Optional URL to Like",
			id:"url",
			help:"Optionally place the URL you want viewers to 'Like' here. Defaults to the page/post URL."
		}, 
		{
			label:"Style",
			id:"style",
			help:"Values: standard, button_count, box_count (default: standard).<p>Note: Depending on how fast the Facebook API is today, the preview could take a few moments to load.</p>",
			controlType:"select-control", 
			selectValues:['standard', 'button_count', 'box_count'],
			defaultValue: 'standard', 
			defaultText: 'standard (Default)'
		}, 
		{
			label:"Float",
			id:"float",
			help:"Float left, right, or none.",
			controlType:"select-control", 
			selectValues:['', 'left', 'right'],
			defaultValue: '', 
			defaultText: 'none (Default)'
		}, 
		{
			label:"Show Faces",
			id:"showfaces",
			help:"Show the faces of Facebook users who 'like' your URL.",
			controlType:"select-control", 
			selectValues:['false', 'true'],
			defaultValue: 'false', 
			defaultText: 'false (Default)'
		}, 
		{
			label:"Width",
			id:"width",
			help:"Set the width of this button.", 
			defaultValue: '450'
		}, 
		{
			label:"Verb to display",
			id:"verb",
			help:"The verb to display with this button.",
			controlType:"select-control", 
			selectValues:['like', 'recommend'],
			defaultValue: 'like', 
			defaultText: 'like (Default)'
		}, 
		{
			label:"Font",
			id:"font",
			help:"The font to use when displaying this button.",
			controlType:"select-control", 
			selectValues:['arial', 'lucida grande', 'segoe ui', 'tahoma', 'trebuchet ms', 'verdana'],
			defaultValue: 'arial', 
			defaultText: 'arial (Default)'
		}, 
		],
		defaultContent:"",
		shortcode:"fblike"
};