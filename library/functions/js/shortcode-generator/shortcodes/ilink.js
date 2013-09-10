wooShortcodeMeta={
	attributes:[
		{
			label:"Title",
			id:"content",
			isRequired:true,
			help:"The link text."
		},
		{
			label:"Link",
			id:"url",
			help:"The Url for your link.",
			validateLink:true
		},
		{
			label:"Style",
			id:"style",
			help:"Values: download, note, tick, info.",
			controlType:"select-control", 
			selectValues:['', 'download', 'note', 'tick'],
			defaultValue: '', 
			defaultText: 'info (Default)'
		},
		{
			label:"Icon",
			id:"icon",
			help:"Optional. Url to a custom icon."
		}
		],
		defaultContent:"Download",
		shortcode:"ilink"
};