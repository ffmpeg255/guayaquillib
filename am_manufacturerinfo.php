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

$manufacturerid = $_GET['manufacturerid'];

$request = new GuayaquilRequestAM('en_US');
if (Config::$useLoginAuthorizationMethod) {
    $request->setUserAuthorizationMethod(Config::$userLogin, Config::$userKey);
}
$request->appendManufacturerInfo($manufacturerid);
$data = $request->query();

if ($request->error != '')
{
    echo $request->error;
}
else
{
    $data = simplexml_load_string($data);
    $data = $data[0]->ManufacturerInfo->row;

    echo '<div> name: '.$data['name'].'</div>';
    echo '<div> alias: '.$data['alias'].'</div>';
    echo '<div> searchurl: '.$data['searchurl'].'</div>';
}
?>
</body>
</html>