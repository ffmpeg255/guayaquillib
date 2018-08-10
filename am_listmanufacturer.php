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

$request = new GuayaquilRequestAM('en_US');
if (Config::$useLoginAuthorizationMethod) {
    $request->setUserAuthorizationMethod(Config::$userLogin, Config::$userKey);
}
$request->appendListManufacturer();
$data = $request->query();
if ($request->error != '')
{
    echo $request->error;
}
else
{
    $data = simplexml_load_string($data);
    $rows = $data[0]->ListManufacturer->row;

    echo '<table>';
    foreach ($rows as $row)
    {
        echo '<tr><td>'.$row['name'].'</td><td>'.$row['alias'].'</td><td>'.$row['searchurl'].'</td></tr>';
    }
    echo '</table>';
}
?>
</body>
</html>