wooShortcodeMeta={
	attributes:[
		{
			label:"Optional URL to Share",
			id:"url",
			help:"Optionally place the URL you want viewers to 'Share' here. Defaults to the page/post URL.<p>Don't forget the <code>http://</code>.</p>"
		}, 
		{
			label:"Type",
			id:"type",
			help:"Values: button, icon_link, icon (default: button).<p>Note: Depending on how fast the Facebook API is today, the preview could take a few moments to load.</p>",
			controlType:"select-control", 
			selectValues:['button', 'icon_link', 'icon'],
			defaultValue: 'button', 
			defaultText: 'button (Default)'
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
		shortcode:"fbshare"
};