<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en">
<head>
    <meta http-equiv="content-type" content="text/html; charset=utf-8" />
</head>

<body>
<?php
// Include soap request class
include('guayaquillib'.DIRECTORY_SEPARATOR.'data'.DIRECTORY_SEPARATOR.'requestOem.php');
// Include view class
include('guayaquillib'.DIRECTORY_SEPARATOR.'render'.DIRECTORY_SEPARATOR.'qdetails'.DIRECTORY_SEPARATOR.'default.php');
// Include view class
include('extender.php');

class QuickDetailsExtender extends CommonExtender
{
    function FormatLink($type, $dataItem, $catalog, $renderer)
    {
        if ($type == 'vehicle')
            $link = 'vehicle.php?c='.$catalog.'&vid='.$renderer->vehicleid. '&ssd=' . $renderer->ssd;
        elseif ($type == 'category')
            $link = 'vehicle.php?c=' . $catalog . '&vid=' . $renderer->vehicleid . '&cid=' . $dataItem['categoryid'] . '&ssd=' . $dataItem['ssd'];
        elseif ($type == 'unit')
        {
            $coi = array();
            foreach ($dataItem->Detail as $detail)
            {
                if ((string)$detail['match']) {
                    $i = (string)$detail['codeonimage'];
                    $coi[$i] = $i;
                }
            }

            $link = 'unit.php?c=' . $catalog . '&vid=' . $renderer->vehicleid . '&uid=' . $dataItem['unitid'] .  '&cid=' . $renderer->currentunit['categoryid'] . '&ssd=' . $dataItem['ssd'] . '&coi=' . implode(',', $coi);
        }
        elseif ($type == 'detail') {
            $link = Config::$redirectUrl;
            $link = str_replace('$oem$', urlencode($dataItem['oem']), $link);
        }

        return $link;
    }
}

// Create request object
$request = new GuayaquilRequestOEM($_GET['c'], $_GET['ssd'], Config::$catalog_data);
if (Config::$useLoginAuthorizationMethod) {
    $request->setUserAuthorizationMethod(Config::$userLogin, Config::$userKey);
}

// Append commands to request
$request->appendGetVehicleInfo($_GET['vid']);
$request->appendListCategories($_GET['vid'], isset($_GET['cid']) ? $_GET['cid'] : -1);
$request->appendListQuickDetail($_GET['vid'], $_GET['gid'], 1);

// Execute request
$data = $request->query();

// Check errors
if ($request->error != '')
{
    echo $request->error;
}
else
{
    $vehicle = $data[0]->row;
    $categories = $data[1];
    $details= $data[2];
    echo '<h1>'.CommonExtender::FormatLocalizedString('GroupDetails', $vehicle['name']).'</h1>';

    echo '<div id="pagecontent">';

    $renderer = new GuayaquilQuickDetailsList(new QuickDetailsExtender());
    $renderer->detaillistrenderer = new GuayaquilDetailsList($renderer->extender);
    $renderer->detaillistrenderer->group_by_filter = 1;
    echo $renderer->Draw($details, $_GET['c'], $_GET['vid'], $_GET['ssd']);


    echo '</div>';
}
?>
</body>
</html>
