function prepareImage()
{
	var img = jQuery('img.dragger');
	
	var width = img.innerWidth();
	var height = img.innerHeight();

	img.attr('owidth', width);
	img.attr('oheight', height);

	jQuery('div.dragger').each(function(idx){
		var el = jQuery(this);
		el.attr('owidth', parseInt(el.css('width')));
		el.attr('oheight', parseInt(el.css('height')));
		el.attr('oleft', parseInt(el.css('margin-left')));
		el.attr('otop', parseInt(el.css('margin-top')));
	});
}

function rescaleImage(delta) {
	var img = jQuery('img.dragger');
		
	var original_width = img.attr('owidth');
	var original_height = img.attr('oheight');

	if (!original_width)
	{
		prepareImage();

		original_width = img.attr('owidth');
		original_height = img.attr('oheight');
	}

	var current_width = img.innerWidth();
	var current_height = img.innerHeight();

	var scale = current_width / original_width;

	var cont = jQuery('#viewport');
		
	var view_width = parseInt(cont.css('width'));
	var view_height = parseInt(cont.css('height'));
		
	var minScale = Math.min(view_width / original_width, view_height / original_height);

	var newscale = scale + (delta / 10);
	if (newscale < minScale)
		newscale = minScale;

	if (newscale > 1)
		newscale = 1;

	var correctX = Math.max(0, (view_width - original_width*newscale) / 2);
	var correctY = Math.max(0, (view_height - original_height*newscale) / 2);

	img.attr('width', original_width*newscale);
	img.attr('height', original_height*newscale);
	img.css('margin-left', correctX + 'px');
	img.css('margin-top', correctY + 'px');

	jQuery('div.dragger').each(function(idx){
		var el = jQuery(this);
		el.css('margin-left', (el.attr('oleft')*newscale + correctX) + 'px');
		el.css('margin-top', (el.attr('otop')*newscale + correctY) + 'px');
		el.css('width', el.attr('owidth')*newscale + 'px');
		el.css('height', el.attr('oheight')*newscale + 'px');
	});
}

function fitToWindow() {
	var t = jQuery('#g_container');
	var width = t.innerWidth() - (parseInt(t.css('padding-right')) || 0) - (parseInt(t.css('padding-left')) || 0);
	jQuery('#viewport, #viewtable').css('width', Math.ceil(width*0.48));
}

var el_name;

function SubscribeDblClick(selector)
{
    jQuery(selector).dblclick(function() {
        var el = jQuery(this);
        var elName = el.attr('name');

        var items = jQuery('tr[name="'+elName+'"]');

        if (items.length == 0)
            return false;

        if (items.length == 1)
        {
            var id = jQuery(items[0]).attr('id');
            items = jQuery('#' + id + ' a.follow');

            if (items.length == 0) {
                return false;
            }

            var url = jQuery(items[0]).attr('href');
            url += (url.indexOf('?') >= 0 ? '&' : '?') + 'format=raw';
            jQuery.colorbox({
                'href': url,
                'opacity': 0.3,
                'innerWidth' : '1000px',
                'maxHeight' : '98%'
            })
        } else {
            jQuery.colorbox({
                'html': function() {
                    var items = jQuery('tr[name="'+elName+'"] td[name=c_name]');
                    var name = jQuery(items[0]).text();

                    var html = '<h2><span>' + name + '</span></h2>' + '<table>';

                    var oems = jQuery('tr[name="'+elName+'"] td[name=c_oem]');
                    var notes = jQuery('tr[name="'+elName+'"] td[name=c_note]');
                    var urls = jQuery('tr[name="'+elName+'"] td[name=c_oem] a.follow');

                    var count = oems.length;
                    if (count == 0) {
                        count = notes.length;
                    }

                    for (var idx = 0; idx < count; idx++) {
                        var url = jQuery(urls[idx]).attr('href');
                        url += (url.indexOf('?') >= 0 ? '&' : '?') + 'format=raw';
                        html += '<tr><td><a href="#" onclick="jQuery.colorbox({\'href\': \'' + url +'\',\'opacity\': 0.3, \'innerWidth\' : \'1000px\',\'maxHeight\' : \'98%\'}); return false;">' + jQuery(oems[idx]).text() + '</a></td><td>' + jQuery(notes[idx]).text() + '</td></tr>';
                    }

                    html += '</table>';

                    return html;
                },
                'opacity': 0.3,
                'maxHeight' : '98%'
            })
        }
    })
}

jQuery(document).ready(function($){

	jQuery('.dragger, #viewport').bind('mousewheel', function(event, delta) {
		rescaleImage(delta);
		return false;
	});



	jQuery('#viewport div').tooltip({ 
	    track: true, 
	    delay: 0, 
	    showURL: false, 
	    fade: 250,
	    bodyHandler: function() {
			var name = jQuery(this).attr('name');

			var items = jQuery('tr[name="'+name+'"] td[name=c_name]');
			
			if (items.length == 0)
				return false;

			return jQuery(items[0]).text();
		}
	});

    jQuery('tr.g_highlight').click(function() {
        var name = jQuery(this).attr('name');
        jQuery('.g_highlight[name="'+name+'"]').toggleClass('g_highlight_lock');
        jQuery('.g_highlight_over[name="'+name+'"]').toggleClass('g_highlight_lock');
    });

    jQuery('#viewport div').click(function() {
        var name = jQuery(this).attr('name');
        jQuery('.g_highlight[name="'+name+'"]').toggleClass('g_highlight_lock');
        jQuery('.g_highlight_over[name="'+name+'"]').toggleClass('g_highlight_lock');

        var tr = jQuery('tr.g_highlight_lock[name="'+name+'"]');
        if (tr.length == 0)
            return;

        /*var scrolled = false;
        tr.each(function(){
            if (!scrolled)
                jQuery.scrollTo(this);
            //new Fx.Scroll(jQuery('#viewtable')).toElement(this);
            scrolled = true;
        });*/
    });

	jQuery('#viewport div, #viewport div img').hover(
		function () {
			hl(this, 'in');
		}, 
		function () {
			hl(this, 'out');
		}
	);

	jQuery(window).bind("resize", function() {
		fitToWindow();
	});

	fitToWindow();

	if ((document.all)?false:true)
		jQuery('#g_container div table').attr('width', '100%');

    jQuery('.guayaquil_zoom').colorbox({
        'href': function () {
            var url = jQuery(this).attr('full');
            return url;
        },
        'photo':true,
        'opacity': 0.3,
        'title' : function () {
            var title = jQuery(this).attr('title');
            return title;
        },
        'maxWidth' : '98%',
        'maxHeight' : '98%',
        'onComplete' : function () {
            var img1 = jQuery('#viewport img.dragger');
            var img2 = jQuery('#cboxLoadedContent img.cboxPhoto');
            var k = img2.innerWidth() / img1.attr('owidth');

            jQuery('#viewport div.dragger').each(function() {
                var el = jQuery(this);
                var blank = jQuery('#viewport div.g_highlight img').attr('src');
                var hl = el.hasClass('g_highlight_lock');
                var nel = '<div class="g_highlight' + (hl ? ' g_highlight_lock' : '') + '" name="' + el.attr('name') + '" style="position: absolute; width: ' + (el.attr('owidth') * k) + 'px; height: ' + (el.attr('oheight') * k) + 'px; margin-top: ' + (el.attr('otop') * k) + 'px; margin-left: ' + (el.attr('oleft') * k) + 'px; overflow: hidden;"><img width="200" height="200" src="' + blank + '"></div>';

                img2.before(nel);
            });

            jQuery('#cboxLoadedContent div').click(function() {
                var el = jQuery(this);
                var name = el.attr('name');
                jQuery('.g_highlight[name="'+name+'"]').toggleClass('g_highlight_lock');
                jQuery('.g_highlight_over[name="'+name+'"]').toggleClass('g_highlight_lock');
            });

            SubscribeDblClick('#cboxLoadedContent div');

            jQuery('#cboxLoadedContent div').tooltip({
                track: true,
                delay: 0,
                showURL: false,
                fade: 250,
                bodyHandler: function() {
                    var name = jQuery(this).attr('name');

                    var items = jQuery('tr[name="'+name+'"] td[name=c_name]');

                    if (items.length == 0)
                        return false;

                    return jQuery(items[0]).text();
                }
            });
        }
    });

    SubscribeDblClick('#viewport div');
});
