function tree_toggle(event) {
	event = event || window.event
	var clickedElem = event.target || event.srcElement

	if (!hasClass(clickedElem, 'qgExpand')) {
		return // клик не там
	}

	// Node, на который кликнули
	var node = clickedElem.parentNode
	if (hasClass(node, 'qgExpandLeaf')) {
		return // клик на листе
	}

	// определить новый класс для узла
	var newClass = hasClass(node, 'qgExpandOpen') ? 'qgExpandClosed' : 'qgExpandOpen'
	// заменить текущий класс на newClass
	// регексп находит отдельно стоящий open|close и меняет на newClass
	var re =  /(^|\s)(qgExpandOpen|qgExpandClosed)(\s|$)/
	node.className = node.className.replace(re, '$1'+newClass+'$3')
}


function hasClass(elem, className) {
	return new RegExp("(^|\\s)"+className+"(\\s|$)").test(elem.className)
}

var QuickGroups = {};
QuickGroups.Search = function(value)
{
    var filtered_groups = jQuery('#qgFilteredGroups');
    var tree = jQuery('#qgTree');

    if (value.length < 3)
    {
        filtered_groups.css("display", "none");
        tree.css("display", "block");
    }
    else
    {
        filtered_groups.css("display", "block");
        tree.css("display", "none");
        filtered_groups.html('');

        QuickGroups.InnerSearch(value, '', tree, filtered_groups)
    }
}

QuickGroups.InnerSearch = function(value, current_path, item, filtered_groups)
{
    var items = item.children();
    items.each(function()
    {
        var el = jQuery(this);
        if (el.hasClass('qgContent'))
        {
            var text = el.text();
            var text2 = text.replace(new RegExp('(' + value + ')', 'i'), '<span class="qgSelected">$1</span>');
            if (text != text2)
                jQuery('<div class="qgFilteredGroup"><div class="qgCurrentPath">'+ current_path + '</div><div class="qgFilteredName">'+ el.html().replace(text, text2)+'</div></div>').appendTo(filtered_groups);

            current_path = current_path + ' / ' + text;
        }
        QuickGroups.InnerSearch(value, current_path, el, filtered_groups)
    });
}