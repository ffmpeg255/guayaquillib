<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en">
<head>
    <meta http-equiv="content-type" content="text/html; charset=utf-8" />
</head>
<body>
<?php
require_once('extender.php');

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
echo $renderer->Draw($_GET['c'], $cataloginfo, @$formframe, @$formframeno);

echo '<br><br>';

?>
</body>
</html>

