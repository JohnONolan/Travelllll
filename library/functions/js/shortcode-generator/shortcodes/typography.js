wooShortcodeMeta={
	attributes:[
		{
			label:"Content",
			id:"content",
			help: 'The text to be styled. Use a &lt;br /&gt; to start a new line.',
			isRequired:true
		},
		{
			label:"Font",
			id:"font",
			help:"The font to be used.", 
			controlType:"font-control", 
			defaultValue: 'Cantarell', 
			defaultText: 'Cantarell (Default)'
		},
		{
			label:"Size",
			id:"size",
			help:"The text size.", 
			controlType:"range-control", 
			defaultValue: 24,
			rangeValues:[9, 70]
		},
		{
			label:"Size Format",
			id:"size_format",
			help:"The format of the size (px or em).", 
			controlType:"select-control", 
			selectValues:['px', 'em'],
			defaultValue: 'px', 
			defaultText: 'px (Default)'
		},
		{
			label:"Color",
			id:"color",
			help:"Values: &lt;empty&gt; for default or a color (e.g. red or #000000).", 
			controlType:"colourpicker-control"
		}
		],
		defaultContent:"",
		shortcode:"typography"
};
