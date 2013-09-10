wooShortcodeMeta={
	attributes:[
		{
			label:"Quote",
			id:"content",
			isRequired:true
		},
		{
			label:"Style",
			id:"style",
			help:"Values: &lt;empty&gt; or boxed.", 
			controlType:"select-control", 
			selectValues:['', 'boxed'],
			defaultValue: '', 
			defaultText: 'none (Default)'
		},
		{
			label:"Float",
			id:"float",
			help:"Values: &lt;empty&gt;, left, right.", 
			controlType:"select-control", 
			selectValues:['', 'left', 'right'],
			defaultValue: '', 
			defaultText: 'none (Default)'
		}
		],
		defaultContent:"Climbing is the only cure for gravity.",
		shortcode:"quote"
};