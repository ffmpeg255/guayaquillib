<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en">
<head>
    <meta http-equiv="content-type" content="text/html; charset=utf-8" />
</head>
<body>
<?php
// Include soap request class
include('guayaquillib'.DIRECTORY_SEPARATOR.'data'.DIRECTORY_SEPARATOR.'request.php');
// Include catalog list view
include('guayaquillib'.DIRECTORY_SEPARATOR.'render'.DIRECTORY_SEPARATOR.'applicability'.DIRECTORY_SEPARATOR.'applicability.php');

include('extender.php');

class CatalogExtender extends CommonExtender
{
    function FormatLink($type, $dataItem, $catalog, $renderer)
    {
        if ($type == 'unit')
            return 'unit.php?&uid='.$dataItem['unitid'].'&c='.$catalog.'&ssd='.$dataItem['ssd'].'&oem='.$renderer->oem;

        return 'applicability.php?&oem='.$dataItem['oem'].'&brand='.$dataItem['brand'];
    }
}

$brand = $_GET['brand'];
$oem = $_GET['oem'];

// Create request object
$request = new GuayaquilRequest('', '', Config::$catalog_data);

// Append commands to request
$request->appendFindDetailApplicability($oem, $brand);

// Execute request
$data = $request->query();

//echo '<pre>'; print_r($data); echo '</pre>';
// Check errors
if ($request->error != '')
{
    echo $request->error;
}
else
{
    // Create GuayaquilCatalogsList object. This class implements default catalogs list view
    $renderer = new GuayaquilApplicability(new CatalogExtender(), $oem);
    $renderer->columns = array('name', 'date', 'datefrom', 'dateto', 'model', 'framecolor', 'trimcolor', 'modification', 'grade', 'frame', 'engine', 'engineno', 'transmission', 'doors', 'manufactured', 'options', 'creationregion', 'destinationregion', 'description', 'remarks');

    // Draw catalogs list
    echo $renderer->Draw($data[0]);
}
?>
</body>
</html>