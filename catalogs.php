<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en">
<head>
    <meta http-equiv="content-type" content="text/html; charset=utf-8"/>
</head>
<body>
<?php

// Include soap request class
include('guayaquillib/data/requestOem.php');
// Include catalog list view
include('guayaquillib/render/catalogs/3coltable.php');

include('extender.php');

class CatalogExtender extends CommonExtender
{
    function FormatLink($type, $dataItem, $catalog, $renderer)
    {
        $link = 'catalog.php?&c=' . $dataItem['code'] . '&ssd=' . $dataItem['ssd'];

        if (CommonExtender::isFeatureSupported($dataItem, 'wizardsearch2'))
            $link .= '&spi2=t';

        return $link;
    }
}

// Create request object
$request = new GuayaquilRequestOEM('', '', Config::$catalog_data);
if (Config::$useLoginAuthorizationMethod) {
    $request->setUserAuthorizationMethod(Config::$userLogin, Config::$userKey);
}

// Append commands to request
$request->appendListCatalogs();

// Execute request
$data = $request->query();

// Check errors
if ($request->error != '') {
    echo $request->error;
} else {
    ?>
    <table border="0" width="100%">
        <tr>
            <td>
                <?php
                $formvin = '';
                $cataloginfo = false;
                include('forms/vinsearch.php');
                ?>
            </td>
            <td>
                <?php
                $formframe = '';
                $formframeno = '';
                $cataloginfo = false;
                include('forms/framesearch.php');
                ?>
            </td>
        </tr>
    </table>
    <?php

    // Create GuayaquilCatalogsList object. This class implements default catalogs list view
    $renderer = new GuayaquilCatalogsList(new CatalogExtender());

    // Configure columns
    $renderer->columns = array('icon', 'name', 'version');

    // Draw catalogs list
    echo $renderer->Draw($data[0]);
}
?>
</body>
</html>