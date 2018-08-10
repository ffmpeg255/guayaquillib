<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en">
<head>
    <meta http-equiv="content-type" content="text/html; charset=utf-8" />
</head>
<body>
<?php
include('guayaquillib'.DIRECTORY_SEPARATOR.'data'.DIRECTORY_SEPARATOR.'request_prag.php');
ini_set('display_errors', 1);
error_reporting(E_ALL);

$soapWrapper = new GuayaquilSoapWrapper();
$soapClient = $soapWrapper->getSoapClient('prag', false);
$result = $soapClient->FindOffers('VIC', 'C110', 1, 1, 1);

print_r($soapClient);
print_r($result);
die;

if ($request->error != '')
{
    echo $request->error;
}
else
{
    $data = simplexml_load_string($data);
    $data = $data['FindOffers'];
}
?>
</body>
</html>