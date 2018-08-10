<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en">
<head>
    <meta http-equiv="content-type" content="text/html; charset=utf-8"/>
</head>
<body>
<?php
// Include soap request class
include('guayaquillib/data/requestOem.php');
// Include view class
include('guayaquillib/render/vehicles/vehicletable.php');

include('extender.php');

class VehiclesExtender extends CommonExtender
{
    function FormatLink($type, $dataItem, $catalog, $renderer)
    {
        if (!$catalog)
            $catalog = $dataItem['catalog'];
        $link = ($renderer->qg == 1 ? 'qgroups' : 'vehicle') . '.php?c=' . $catalog . '&vid=' . $dataItem['vehicleid'] . '&ssd=' . $dataItem['ssd'] . ($renderer->qg == -1 ? '&checkQG': ''). '&path_data=' . urlencode(base64_encode(substr($dataItem['vehicle_info'], 0, 300)));

        return $link;
        //return 'vehicle.php?c='.$catalog.'&vid='.$dataItem['vehicleid'].'&ssd='.$dataItem['ssd'];
    }
}

// Create request object
$catalogCode = array_key_exists('c', $_GET) ? $_GET['c'] : false;
$request = new GuayaquilRequestOEM($catalogCode, array_key_exists('ssd', $_GET) ? $_GET['ssd'] : '', Config::$catalog_data);
if (Config::$useLoginAuthorizationMethod) {
    $request->setUserAuthorizationMethod(Config::$userLogin, Config::$userKey);
}

// Append commands to request
$findType = $_GET['ft'];
if ($findType == 'findByVIN')
    $request->appendFindVehicleByVIN($_GET['vin']);
else if ($findType == 'findByFrame')
    $request->appendFindVehicleByFrame($_GET['frame'], $_GET['frameNo']);
else if ($findType == 'execCustomOperation')
    $request->appendExecCustomOperation($_GET['operation'], $_GET['data']);
else if ($findType == 'findByWizard2')
    $request->appendFindVehicleByWizard2($_GET['ssd']);

if ($catalogCode) {
    $request->appendGetCatalogInfo();
}
// Execute request
$data = $request->query();

// Check errors
if ($request->error != '') {
    echo $request->error;
} else {
    $vehicles = $data[0];
    $cataloginfo = $catalogCode ? $data[1]->row : false;

    if (is_object($vehicles) == false || $vehicles->row->getName() == '') {
        if ($_GET['ft'] == 'findByVIN')
            echo CommonExtender::FormatLocalizedString('FINDFAILED', $_GET['vin']);
        else
            echo CommonExtender::FormatLocalizedString('FINDFAILED', $_GET['frame'] . '-' . $_GET['frameNo']);
    } else {
        echo '<h1>' . CommonExtender::LocalizeString('Cars') . '</h1><br>';

        // Create data renderer
        $renderer = new GuayaquilVehiclesList(new VehiclesExtender());
        $renderer->columns = array('name', 'date', 'datefrom', 'dateto', 'model', 'framecolor', 'trimcolor', 'modification', 'grade', 'frame', 'engine', 'engineno', 'transmission', 'doors', 'manufactured', 'options', 'creationregion', 'destinationregion', 'description', 'remarks');

        $renderer->qg = !$cataloginfo ? -1 : (CommonExtender::isFeatureSupported($cataloginfo, 'quickgroups') ? 1 : 0);

        // Draw data
        echo $renderer->Draw($catalogCode, $vehicles);
    }

    if (($cataloginfo && CommonExtender::isFeatureSupported($cataloginfo, 'vinsearch')) || ($findType == 'findByVIN')) {
        $formvin = array_key_exists('vin', $_GET) ? $_GET['vin'] : '';
        include('forms/vinsearch.php');
    }

    if (($cataloginfo && CommonExtender::isFeatureSupported($cataloginfo, 'framesearch')) || $findType == 'findByFrame') {
        $formframe = array_key_exists('frame', $_GET) ? $_GET['frame'] : '';
        $formframeno = array_key_exists('frameNo', $_GET) ? $_GET['frameNo'] : '';
        include('forms/framesearch.php');
    }
}

?>
</body>
</html>
