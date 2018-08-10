<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en">
<head>
    <meta http-equiv="content-type" content="text/html; charset=utf-8" />
</head>

<?php
// Include soap request class
include('guayaquillib'.DIRECTORY_SEPARATOR.'data'.DIRECTORY_SEPARATOR.'request.php');
// Include view class
include('guayaquillib'.DIRECTORY_SEPARATOR.'render'.DIRECTORY_SEPARATOR.'unit'.DIRECTORY_SEPARATOR.'default.php');
include('extender.php');

class GuayaquilExtender3 extends CommonExtender
{
	function FormatLink($type, $dataItem, $catalog, $renderer)
	{
        $link = 'unit.php?c=' . $catalog . '&vid=' . $renderer->vehicle_id . '&uid=' . $dataItem['unitid'] .  '&cid=' . $renderer->categoryid . '&ssd=' . $dataItem['ssd'] . '&path_id=' . $renderer->path_id;

        return $link;
	}
}

// Create request object
$request = new GuayaquilRequest($_GET['c'], $_GET['ssd'], Config::$catalog_data);

// Append commands to request
$request->appendGetUnitInfo($_GET['uid']);
$request->appendListDetailByUnit($_GET['uid']);
$request->appendListImageMapByUnit($_GET['uid']);

// Execute request
$data = $request->query();

// Check errors
if ($request->error != '')
{
    echo $request->error;
}
else
{
    $unit = $data[0]->row;
    $imagemap = $data[2];
    $details = $data[1];

		echo '<h1>'.CommonExtender::FormatLocalizedString('UnitName', (string)$unit['name']).'</h1>';

		$renderer = new GuayaquilUnit(new DetailExtender());
		$renderer->detaillistrenderer = new GuayaquilDetailsList($renderer->extender);
		$renderer->detaillistrenderer->columns = array('Toggle'=>1, 'PNC'=>3, 'OEM'=>2, 'Name'=>3, 'Cart'=>1, 'Price'=>3, 'Note'=>2, 'Tooltip'=>1);
		echo $renderer->Draw($_GET['c'], $unit, $imagemap, $details, NULL, NULL);

}
?>
</body>
</html>
