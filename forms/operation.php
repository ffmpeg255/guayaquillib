<?php

echo '<h1>' . CommonExtender::LocalizeString('SearchByCustom').' ' . $operation['description'] . '</h1>';

include_once('guayaquillib/render/catalog/operationform.php');

if (!class_exists('OperationSearchExtender')) {
    class OperationSearchExtender extends CommonExtender
    {
        function FormatLink($type, $dataItem, $catalog, $renderer)
        {
            return 'vehicles.php?ft=execCustomOperation&c=' . $catalog;
        }
    }
}

$renderer = new GuayaquilOperationSearchForm(new OperationSearchExtender());
echo $renderer->Draw(array_key_exists('c', $_GET) ? $_GET['c'] : '', $operation, @$_GET['data']);

echo '<br><br>';

?>


