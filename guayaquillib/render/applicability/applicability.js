/**
 * Created with JetBrains PhpStorm.
 * User: altunint
 * Date: 7/23/13
 * Time: 11:23 AM
 * To change this template use File | Settings | File Templates.
 */


jQuery(document).ready (function() {
    jQuery(".guayaquil_table tr.g_vehicle").tooltip({
        track: true,
        delay: 0,
        showURL: false,
        fade: 250,
        bodyHandler: function() {
            var items = jQuery (this).find ('td.ttp .item');
            var tooltip = '';
            jQuery.each (items, function () {
                tooltip+=jQuery (this).html()+'<br/>';
            });
            return tooltip;
        }
    });
    jQuery(".guayaquil_table tr.g_unit").tooltip({
        track: true,
        delay: 0,
        showURL: false,
        fade: 250,
        bodyHandler: function() {
            var items = jQuery (this).find ('.g_hint');
            var tooltip = '';
            jQuery.each (items, function () {

                if (tooltip != "")
                    tooltip += "<br>";

                tooltip+=jQuery (this).html();
            });
            if (tooltip == "")
                return null;

            return tooltip;
        }
    });
});