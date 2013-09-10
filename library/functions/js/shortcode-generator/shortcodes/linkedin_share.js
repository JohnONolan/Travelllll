wooShortcodeMeta={
	attributes:[
		{
			label:"Optional URL to Share",
			id:"url",
			help:"Optionally place the URL you want viewers to 'Share' here. Defaults to the page/post URL.<p>Don't forget the <code>http://</code>.</p>"
		}, 
		{
			label:"Counter Style",
			id:"style",
			help:"Values: top, right, nine (default: none).<p>Note: Depending on how fast the Facebook API is today, the preview could take a few moments to load.</p>",
			controlType:"select-control", 
			selectValues:['top', 'right', 'none'],
			defaultValue: 'none', 
			defaultText: 'no counter (Default)'
		}, 
		{
			label:"Float",
			id:"float",
			help:"Float left, right, or none.",
			controlType:"select-control", 
			selectValues:['', 'left', 'right'],
			defaultValue: '', 
			defaultText: 'none (Default)'
		}
		],
		defaultContent:"",
		shortcode:"linkedin_share"
};