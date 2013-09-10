wooShortcodeMeta={
	attributes:[
		{
			label:"Title",
			id:"title",
			help:"Optional title text (if not entered, text will be automatically generated for you)."
		},
		{
			label:"Social Profile Link",
			id:"url",
			help:"The URL to your social profile.",
			validateLink:true
		},
		{
			label:"Float",
			id:"float",
			help:"Optionally float your icon to the left or right.",
			controlType:"select-control", 
			selectValues:['', 'left', 'right'],
			defaultValue: '', 
			defaultText: 'none (Default)'
		},
		{
			label:"Profile Type",
			id:"profile_type",
			help:"Specify what type of social profile this is (if not specified, the shortcode will attempt to identify it).",
			controlType:"select-control", 
			selectValues:['rss', 'facebook', 'twitter', 'youtube', 'delicious', 'flickr', 'linkedin'],
			defaultValue: 'rss', 
			defaultText: 'rss (Default)'
		},
		{
			label:"Custom Icon URL",
			id:"icon_url",
			help:"An optional custom icon.",
			validateLink:true
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
		,
		{
			label:"Set the link Relationship",
			id:"rel",
			help:"Optionally set the link relationship.", 
			controlType:"select-control", 
			selectValues:['', 'nofollow'],
			defaultValue: '', 
			defaultText: 'none (Default)'
		}
		],
		defaultContent:"",
		shortcode:"social_icon"
};