<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en">
<head>
    <meta http-equiv="content-type" content="text/html; charset=utf-8" />
</head>
<body>
<?php
// Include soap request class
include('guayaquillib'.DIRECTORY_SEPARATOR.'data'.DIRECTORY_SEPARATOR.'requestOem.php');
// Include view class
include('guayaquillib'.DIRECTORY_SEPARATOR.'render'.DIRECTORY_SEPARATOR.'wizard2'.DIRECTORY_SEPARATOR.'wizard.php');
include('extender.php');

class WizardExtender extends CommonExtender { 
	function FormatLink($type, $dataItem, $catalog, $renderer)
	{
		if ($type == 'vehicles') {
			return 'vehicles.php?ft=findByWizard2&c='.$catalog.'&ssd='.$_GET['ssd'];
        }
		else
			return 'wizard2.php?c='.$catalog.'&ssd=$ssd$';
	}	
}

// Create request object
$request = new GuayaquilRequestOEM($_GET['c'], $_GET['ssd'], Config::$catalog_data);
if (Config::$useLoginAuthorizationMethod) {
    $request->setUserAuthorizationMethod(Config::$userLogin, Config::$userKey);
}

// Append commands to request
$request->appendGetCatalogInfo();
$request->appendGetWizard2($_GET['ssd']);

// Execute request
$data = $request->query();

// Check errors
if ($request->error != '')
{
    echo $request->error;
}
else
{
  $wizard = $data[1];
  $cataloginfo = $data[0]->row;

	echo '<h1>'.CommonExtender::LocalizeString('Search by wizard').' - '.$cataloginfo['name'].'</h1>';

	$renderer = new GuayaquilWizard(new WizardExtender());
	echo $renderer->Draw($_GET['c'], $wizard);
}
?>
</body>
</html>
