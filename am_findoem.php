<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en">
<head>
    <meta http-equiv="content-type" content="text/html; charset=utf-8" />
</head>
<body>
<?php

include('am_searchpanel.php');
// Include soap request class
include('guayaquillib'.DIRECTORY_SEPARATOR.'data'.DIRECTORY_SEPARATOR.'requestAm.php');

$brand = @$_GET['brand'] ? $_GET['brand'] : null;
$oem = @$_GET['oem'];
$options = @$_GET['options'];

if ($options) {
    $options = implode($options, ',');
}

$replacementtypes = @$_GET['replacementtypes'];
$replacementtypes = implode($replacementtypes, ',');

$request = new GuayaquilRequestAM('ru_RU');
if (Config::$useLoginAuthorizationMethod) {
    $request->setUserAuthorizationMethod(Config::$userLogin, Config::$userKey);
}
$request->appendFindOEM($oem, $options, $brand, $replacementtypes);
$data = $request->query();

if ($request->error != '')
{
    echo $request->error;
}
else
{
    $data = simplexml_load_string($data);
    $data = $data[0]->FindOEM->detail;
    if (!$data || (!(string)$data['manufacturerid'])) {
        $request = new GuayaquilRequestAM('ru_RU');
        if (Config::$useLoginAuthorizationMethod) {
            $request->setUserAuthorizationMethod(Config::$userLogin, Config::$userKey);
        }
        $request->appendFindOEMCorrection($oem);
        $data = $request->query();
        $data = simplexml_load_string($data);
        $data = $data[0]->FindOEMCorrection->detail;
        if (!$data || (!(string)$data['manufacturerid'])) {
            echo "<p>Article $oem not found.</p>";
        } else {
            echo "<p>Article $oem not found. Please select it from list</p>";
        }
    }

    if ($data) {
        foreach ($data as $detail)
        {
            echo '<a href="am_manufacturerinfo.php?manufacturerid='.$detail['manufacturerid'].'">'.$detail['manufacturer'].'</a> <a href="am_finddetail.php?detail_id='.$detail['detailid'].'&options='.$options.'">'.$detail['formattedoem'].'</a> '.$detail['name'];
            echo '</div>';

            $weight = (float)$detail['weight'];
            if ($weight)
                echo '<div>Weight '.$weight.'</div>';

            $volume = (float)$detail['volume'];
            if ($volume)
                echo '<div>Volume '.$volume.'</div>';

            $dimensions = (float)$detail['dimensions'];
            if ($dimensions)
                echo '<div>Weight '.$dimensions.'</div>';

            foreach ($detail->properties->property as $property) {
                echo '<div>'.$property['property'].' '.$property['locale'].' '.$property['value'].'</div>';
            }
            foreach ($detail->images->image as $image) {
                echo '<div>'.$image['filename'].'</div>';
            }

            foreach ($detail->replacements->replacement as $replacement) {
                echo '<div>'.$replacement['type'].' '.$replacement['way'].' ';

                echo '<a href="am_manufacturerinfo.php?manufacturerid='.$replacement->detail['manufacturerid'].'">'.$replacement->detail['manufacturer'].'</a> <a href="am_finddetail.php?detail_id='.$replacement->detail['detailid'].'&options='.$options.'">'.$replacement->detail['formattedoem'].'</a> '.$replacement->detail['name'];

                $weight = (float)$replacement->detail['weight'];
                if ($weight)
                    echo 'Weight '.$weight;

                $volume = (float)$replacement->detail['volume'];
                if ($volume)
                    echo 'Volume '.$volume;

                $dimensions = (float)$replacement->detail['dimensions'];
                if ($dimensions)
                    echo 'Weight '.$dimensions;

                echo '</div>';
            }
        }
    }
}

?>
</body>
</html>