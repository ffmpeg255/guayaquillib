<?php

echo '<h1>'.CommonExtender::LocalizeString('SearchByVIN').'</h1>';

include('guayaquillib'.DIRECTORY_SEPARATOR.'render'.DIRECTORY_SEPARATOR.'catalog'.DIRECTORY_SEPARATOR.'vinsearchform.php');

class VinSearchExtender extends CommonExtender
{
    function FormatLink($type, $dataItem, $catalog, $renderer)
    {
        return 'vehicles.php?ft=findByVIN&c='.$catalog.'&vin=$vin$&ssd=';
    }   
}

$renderer = new GuayaquilVinSearchForm(new VinSearchExtender());
echo $renderer->Draw(array_key_exists('c', $_GET) ? $_GET['c'] : '', $cataloginfo, @$formvin);

echo '<br><br>';

?>


