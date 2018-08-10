<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en">
<head>
    <meta http-equiv="content-type" content="text/html; charset=utf-8" />
</head>

<?php
// Include soap request class
include('guayaquillib'.DIRECTORY_SEPARATOR.'data'.DIRECTORY_SEPARATOR.'requestOem.php');
// Include view class
include('guayaquillib'.DIRECTORY_SEPARATOR.'render'.DIRECTORY_SEPARATOR.'filter'.DIRECTORY_SEPARATOR.'default.php');
include('extender.php');

class GuayaquilExtender3 extends CommonExtender
{
	function FormatLink($type, $dataItem, $catalog, $renderer)
	{
        $link = 'unit.php?c=' . $catalog . '&vid=' . $renderer->vehicle_id . '&uid=' . $dataItem['unitid'] .  '&cid=' . $renderer->categoryid . '&ssd=' . $_GET['ssd'];

        return $link;
	}
}

// Create request object
$request = new GuayaquilRequestOEM($_GET['c'], $_GET['ssd'], Config::$catalog_data);
if (Config::$useLoginAuthorizationMethod) {
    $request->setUserAuthorizationMethod(Config::$userLogin, Config::$userKey);
}

// Append commands to request
$request->appendGetFilterByUnit($_GET['f'], $_GET['vid'], $_GET['uid']);
$request->appendGetUnitInfo($_GET['uid']);

// Execute request
$data = $request->query();

// Check errors
if ($request->error != '')
{
    echo $request->error;
}
else
{
    $filter_data = $data[0];
    $unit = $data[1]->row;

		echo '<h1>'.CommonExtender::FormatLocalizedString('UnitName', (string)$unit['name']).'</h1>';

    $renderer = new GuayaquilFilter(new GuayaquilExtender3());
    $renderer->vehicle_id = $_GET['vid'];
    $renderer->categoryid = $_GET['cid'];
    $renderer->ssd = $_GET['ssd'];
    echo $renderer->Draw($_GET['c'], $filter_data, $_GET['ssd'], $unit);

}
?>

<script type="text/javascript">
    function ProcessFilters(skip)
    {
        var url = '<?php echo 'vehicle.php?&c='.$_GET['c'].'&vid='.$_GET['vid'].'&cid='.$_GET['cid'].'&ssd=$'?>';
        var ssd = '<?php echo $_GET['ssd']?>';
        var col = jQuery('#guayaquilFilterForm .g_filter');
        var hasErrors = false;
        col.each(function(){
            var name = this.nodeName;
            var ssdmod = null;
            if (name == 'SELECT')
                ssdmod = this.value;
            else if (name == 'INPUT' && jQuery(this).attr('type') == 'text' && this.value.length > 0)
            {
                var s = jQuery(this).attr('ssd');
                if (s != null && s.length > 0)
                {
                    var expr = new RegExp(jQuery(this).attr('regexp'), 'i');
                    if ((expr.test(value)))
                    {
                        ssdmod = s.replace('\$', this.value);
                        jQuery(this).removeClass('g_error');
                    }
                    else
                    {
                        jQuery(this).addClass('g_error');
                        hasErrors = true;
                    }
                }
            }
            else if (name == 'INPUT' && jQuery(this).attr('type') == 'radio' && this.checked)
                var ssdmod = jQuery(this).attr('ssd');

            if (ssdmod != null && ssdmod.length > 0)
                ssd += ssdmod;
        })

        if (!hasErrors)
            window.location = url.replace('\$', ssd);
    }
</script>
</body>
</html>

