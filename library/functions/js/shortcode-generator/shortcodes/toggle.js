wooShortcodeMeta={
	attributes:[
			{
				label:"Default 'Open' Title Text",
				id:"title_open",
				help:"The text of the title when the toggle is set to 'open'.",
				defaultValue:"Close Me"
			}, 
			{
				label:"Default 'Closed' Title Text",
				id:"title_closed",
				help:"The text of the title when the toggle is set to 'closed'.", 
				defaultValue:"Open Me"
			}, 
			{
				label:"Content",
				id:"content",
				help:"The content to be toggled.", 
				controlType:"textarea-control", 
				isRequired:true
			}, 
			{
				label:"Hide On Start",
				id:"hide",
				help:"Optionally hide the content on start.", 
				controlType:"select-control", 
				selectValues:['yes', 'no'],
				defaultValue: 'yes', 
				defaultText: 'yes (Default)'
			}, 
			{
				label:"Show Border",
				id:"border",
				help:"Optionally show a border around the toggle.", 
				controlType:"select-control", 
				selectValues:['yes', 'no'],
				defaultValue: 'yes', 
				defaultText: 'yes (Default)'
			},
			{
				label:"Toggle Style",
				id:"style",
				help:"Set an optional alternate style for the toggle.", 
				controlType:"select-control", 
				selectValues:['default', 'white'],
				defaultValue: 'default', 
				defaultText: 'default'
			}, 
			{
				label:"Excerpt Length",
				id:"excerpt_length",
				help:"Set an optional length for an excerpt, with a link to show/hide the remaining content. Please use a number.<br /><strong>Set to 0 to disable this feature.</strong>", 
				defaultValue:"0"
			}, 
			{
				label:"Default 'Read More' Text",
				id:"read_more_text",
				help:"The text of the 'more' link with the remaining text is hidden.", 
				defaultValue:"Read More"
			}, 
			{
				label:"Default 'Read Less' Text",
				id:"read_less_text",
				help:"The text of the 'more' link with the remaining text is visible.", 
				defaultValue:"Read Less"
			}, 
			{
				label:"Include XHTML in excerpt text.",
				id:"include_excerpt_html",
				help:"Optionally include XHTML tags in the excerpt text<br />(<strong>NOTE: the excerpt length must be long enough to include these tags</strong>).", 
				controlType:"select-control", 
				selectValues:['no', 'yes'],
				defaultValue: 'no', 
				defaultText: 'no (Default)'
			}
			],
	defaultContent:"Content to be toggled.",
	shortcode:"toggle"
};
