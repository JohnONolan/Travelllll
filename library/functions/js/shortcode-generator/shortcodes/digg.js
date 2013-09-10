wooShortcodeMeta={
	attributes:[
		{
			label:"Style",
			id:"style",
			help:"Values: medium, large, compact, icon (default: medium).", 
			controlType:"select-control", 
			selectValues:['', 'large', 'compact', 'icon'],
			defaultValue: '', 
			defaultText: 'medium (Default)'
		},
		{
			label:"Title",
			id:"title",
			help:"Optional. Specify title directly (must add link also)."
		},
		{
			label:"Link",
			id:"link",
			help:"Optional. Specify link directly."
		},
		{
			label:"Float",
			id:"float",
			help:"Values: none, left, right (default: left).", 
			controlType:"select-control", 
			selectValues:['', 'left', 'right'],
			defaultValue: 'left', 
			defaultText: 'left (Default)'
		}
		],
		defaultContent:"",
		shortcode:"digg"
};