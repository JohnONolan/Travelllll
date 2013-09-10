wooShortcodeMeta={
	attributes:[
		{
			label:"Optional URL to +1",
			id:"href",
			help:"Optionally place the URL you want viewers to '+1' here. Defaults to the page/post URL."
		}, 
		{
			label:"Size",
			id:"size",
			help:"Values: standard, small, medium, tall (default: standard).<p>Note: Depending on how fast the Google +1 API is today, the preview could take a few moments to load.</p>",
			controlType:"select-control", 
			selectValues:['standard', 'small', 'medium', 'tall'],
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
			label:"Include Counter",
			id:"count",
			help:"Show the counter of users who '+1' your URL.",
			controlType:"select-control", 
			selectValues:['false', ''],
			defaultValue: '', 
			defaultText: 'true (Default)'
		}, 
		{
			label:"JavaScript Callback Function",
			id:"callback",
			help:"Optionally include a JavaScript callback function to run when the +1 button is clicked. <strong>For Advanced Users Only</strong>."
		} 
		],
		defaultContent:"",
		shortcode:"google_plusone"
};