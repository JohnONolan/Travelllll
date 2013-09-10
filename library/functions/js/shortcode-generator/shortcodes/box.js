wooShortcodeMeta={
	attributes:[
		{
			label:"Content",
			id:"content",
			help: 'The content of your info box. Use a &lt;br /&gt; to start a new line.',
			isRequired:true
		},
		{
			label:"Type",
			id:"type",
			help:"Values: &lt;empty&gt;, info, alert, tick, download, note.", 
			controlType:"select-control", 
			selectValues:['', 'info', 'alert', 'tick', 'download', 'note'],
			defaultValue: '', 
			defaultText: 'none (Default)'
		},
		{
			label:"Size",
			id:"size",
			help:"Values: &lt;empty&gt;, medium, large.", 
			controlType:"select-control", 
			selectValues:['', 'large'],
			defaultValue: '', 
			defaultText: 'medium (Default)'
		},
		{
			label:"Style",
			id:"style",
			help:"Values: &lt;empty&gt; or rounded.", 
			controlType:"select-control", 
			selectValues:['', 'rounded'],
			defaultValue: '', 
			defaultText: 'none (Default)'
		},
		{
			label:"Border",
			id:"border",
			help:"Values: &lt;empty&gt;, none, full.", 
			controlType:"select-control", 
			selectValues:['', 'full'],
			defaultValue: '', 
			defaultText: 'top and bottom (Default)'
		},
		{
			label:"Icon",
			id:"icon",
			help:"Values: &lt;empty&gt;, none (for no icon), or full URL to a custom icon."
		}
		],
		defaultContent:"Don't box me in.",
		shortcode:"box"
};
