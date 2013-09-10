wooShortcodeMeta={
	attributes:[
		{
			label:"Columns",
			id:"content",
			controlType:"column-control"
		}
		],
		disablePreview:true,
		customMakeShortcode: function(b){
			var a=b.data;
			
			if(!a)return"";
			
			b=a.numColumns;
			
			var c=a.content;
			
			a=["0","one","two","three","four","five","six"];
			
			var f=a[b];
			
			f+="col_";
			
			c=c.split( "|" );
			
			var g="";
			
			for(var h in c){
				
				var d=jQuery.trim(c[h]);
				
				if(d.length>0){
					
					var e=f+a[d.length];
					
					if(b==4&&d.length==2) e="twocol_one";
					if(h==c.length-1)e+="_last";
					g+="["+e+"]Column "+d.length+"/"+b+"[/"+e+"] "
					
				}
				
			}
			
			return g
		
		}
};