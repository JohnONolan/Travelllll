wooShortcodeMeta={
	attributes:[
			{
				label:"Title",
				id:"content",
				help:"The button title.",
				isRequired:true
			},
			{
				label:"Link",
				id:"link",
				help:"Optional link (e.g. http://google.com).",
				validateLink:true
			},
			{
				label:"Size",
				id:"size",
				help:"Values: &lt;empty&gt; for normal size, small, large, xl.", 
				controlType:"select-control", 
				selectValues:['small', '', 'large', 'xl'],
				defaultValue: '', 
				defaultText: 'medium (Default)'
			},
			{
				label:"Style",
				id:"style",
				help:"Values: &lt;empty&gt;, info, alert, tick, download, note.",
				controlType:"select-control", 
				selectValues:['', 'info', 'alert', 'tick', 'download', 'note'],
				defaultValue: '', 
				defaultText: 'none (Default)'
			},
			{
				label:"Predefined Style",
				id:"color",
				help:'Optionally use one of our predefined styles (this overrides the custom colour settings).', 
				controlType:"select-control", 
				selectValues:['', 'red', 'orange', 'green', 'aqua', 'teal', 'purple', 'pink', 'silver'],
				defaultValue: '', 
				defaultText: 'none (Default)'
			},
			{
				label:"Background Color",
				id:"bg_color",
				controlType:"colourpicker-control",
				help:"Values: &lt;empty&gt; for default or a color (e.g. red or #000000)."
			},
			{
				label:"Border",
				id:"border",
				controlType:"colourpicker-control",
				help:"&lt;empty&gt; for default or the border color (e.g. red or #000000)."
			},
			{
				label:"Dark Text?",
				id:"text",
				help:'Leave empty for light text color or use "dark" (for light background color buttons).', 
				controlType:"select-control", 
				selectValues:['', 'dark'],
				defaultValue: '', 
				defaultText: 'light (Default)'
			},
			{
				label:"CSS Class",
				id:"class",
				help:"Optional CSS class."
			},
			{
				label:"Open in a new window",
				id:"window",
				help:"Optionally open this link in a new window.", 
				controlType:"select-control", 
				selectValues:['', 'yes'],
				defaultValue: '', 
				defaultText: 'no (Default)'
			}
			],
	defaultContent:"Button",
	shortcode:"button"
};
