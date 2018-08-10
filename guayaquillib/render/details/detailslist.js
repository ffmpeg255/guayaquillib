function hl(el, type){
	var name = jQuery(el).attr('name');

	if (type == 'in')
		jQuery('.g_highlight[name="'+name+'"]').addClass('g_highlight_over').removeClass('g_highlight');
	else
		jQuery('.g_highlight_over[name="'+name+'"]').removeClass('g_highlight_over').addClass('g_highlight');
}

function g_toggle(el, opennedimage, clossedimage){
	var name = jQuery(el).attr('id');

	var e = jQuery('tr#'+name);
	if (e.hasClass('g_collapsed'))
	{
		jQuery('tr.g_replacementRow[name='+name+']').show();
		jQuery(el).attr('src', opennedimage);
		e.removeClass('g_collapsed');
	}
	else
	{
		jQuery('tr.g_replacementRow[name='+name+']').hide();
		jQuery(el).attr('src', clossedimage);
		e.addClass('g_collapsed');
	}
}

function g_toggleAdditional(id, opennedimage, clossedimage){

	var e = jQuery('#' + id + ' .g_additional_toggler');
	if (e.hasClass('g_addcollapsed'))
	{
		jQuery('#' + id + ' tr.g_addgr').removeClass('g_addgr_collapsed');
		jQuery(e).attr('src', opennedimage);
		e.removeClass('g_addcollapsed');
	}
	else
	{
		jQuery('#' + id + ' tr.g_addgr').addClass('g_addgr_collapsed');
		jQuery(e).attr('src', clossedimage);
		e.addClass('g_addcollapsed');
	}
}

function g_getHint() {

	var str='<table border=0>';
	var items = jQuery(this).parent().find('td.g_ttd');

	for (var i = 0; i<items.length-1; i++) {
        var txt = jQuery(items[i]).html();
        if (txt.length <= 0)
            continue;
        
		str = str+'<tr><th align=right>' + jQuery('#'+jQuery(items[i]).attr('name')).text() + ':&nbsp;</th><td>' + txt + '</td></tr>';
	}
    var note_items = jQuery (items[i]).find ('span.item');
    for (var k = 0; k<note_items.length; k++)
    {
        var txt = jQuery(note_items[k]).find('span.value').text();
        if (txt.length <= 0)
            continue;
        str = str+'<tr><th align=right>' + jQuery(note_items[k]).find('span.name').text() + ':&nbsp;</th><td>' + txt+ '</td></tr>';
    }
	str = str + '</table>';

	return str;
}

jQuery(document).ready(function($){

    jQuery('tr.g_highlight a.follow').colorbox({
        'href': function () {
            var url = (jQuery(this).attr('href')).replace(/[ ]/g, '');
            url += (url.indexOf('?') >= 0 ? '&' : '?') + 'format=raw';
            return url;
        },
        'opacity': 0.3,
        'innerWidth' : '1000px',
        'maxHeight' : '98%'
    })
});