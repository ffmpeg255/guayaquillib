<?php
echo '<h1>'.CommonExtender::LocalizeString('SearchByFrame').'</h1>';

include('guayaquillib'.DIRECTORY_SEPARATOR.'render'.DIRECTORY_SEPARATOR.'catalog'.DIRECTORY_SEPARATOR.'framesearchform.php');

class FrameSearchExtender extends CommonExtender
{
    function FormatLink($type, $dataItem, $catalog, $renderer)
    {
        return 'vehicles.php?ft=findByFrame&c='.$catalog.'&frame=$frame$&frameNo=$frameno$';
    }   
}
$renderer = new GuayaquilFrameSearchForm(new FrameSearchExtender());
echo $renderer->Draw(array_key_exists('c', $_GET) ? $_GET['c'] : '', $cataloginfo, @$formframe, @$formframeno);

echo '<br><br>';

?>

