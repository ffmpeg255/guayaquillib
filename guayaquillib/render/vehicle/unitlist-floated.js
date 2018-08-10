var glow_name = '';

function glow(name){
	glow_name = name.toUpperCase();

	jQuery('.guayaquil_floatunitlist_box').removeClass('g_highlight_glow');

	jQuery('div[name="' + glow_name + '"]').parent().addClass('g_highlight_glow');
	
	window.location = '#_' + name;
}

function hl(el, type){
	var name = el.name;
	if (name == null)
		name = el.getProperty('name');

	if (glow_name == name)
	{
		if (type == 'in')
			jQuery('.g_highlight_glow[name="'+name+'"]').attr('class','g_highlight_over');
		else
			jQuery('.g_highlight_over[name="'+name+'"]').attr('class','g_highlight_glow');
	} 
	else
	{
		if (type == 'in')
			jQuery('.g_highlight[name="'+name+'"]').attr('class','g_highlight_over');
		else
			jQuery('.g_highlight_over[name="'+name+'"]').attr('class','g_highlight');
	}
}

jQuery(document).ready(function($){
	jQuery('.guayaquil_floatunitlist_box div').hover(
		function () {
			jQuery('div[name="' + jQuery(this).attr('name') + '"]').parent().addClass('guayaquil_floatunitlist_box_hover');
		},
		function () {
			jQuery('div[name="' + jQuery(this).attr('name') + '"]').parent().removeClass('guayaquil_floatunitlist_box_hover');
		}
	);

    jQuery(document).ready(function(){
        jQuery('.guayaquil_zoom').colorbox({
                href: function () {
                    var url = jQuery(this).attr('full');
                    return url;
                },
                photo:true,
                rel: "img_group",
                opacity: 0.3,
                title : function () {
                    var title = jQuery(this).attr('title');
                    var url = jQuery(this).attr('link');
                    return '<a href="' + url + '">' + title + '</a>';
                },
                current: 'Рис. {current} из {total}',
                maxWidth : '98%',
                maxHeight : '98%'
            }
        )
    });

	jQuery('.guayaquil_floatunitlist_box').tooltip({
	    track: true,
	    delay: 0,
	    showURL: false,
	    fade: 250,
	    bodyHandler: function() {
			var id = jQuery(this).attr('note');

            var items = jQuery('#unm'+id);
            var tooltip = jQuery(items[0]).text();

			items = jQuery('#utt'+id);
			if (items.length > 0)
				tooltip = tooltip + '<br>' + jQuery(items[0]).text();

			return tooltip;
		}
	});

});