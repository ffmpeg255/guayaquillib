<?php
require_once('extender.php');

echo '<h1>'.CommonExtender::LocalizeString('Search by wizard').'</h1>';

include('guayaquillib'.DIRECTORY_SEPARATOR.'render'.DIRECTORY_SEPARATOR.'wizard'.DIRECTORY_SEPARATOR.'wizard.php');

class WizardExtender extends CommonExtender
{
	function FormatLink($type, $dataItem, $catalog, $renderer)
	{
		if ($type == 'vehicles')
			return 'vehicles.php?ft=findByWizard&c='.$catalog.'&wid='.$renderer->wizard->row['wizardid'].'&ssd='.$renderer->wizard->row['ssd'];
		else
			return 'wizard.php?c='.$catalog.'&wizardid='.$renderer->wizard->row['wizardid'].'&valueid=$valueid$&ssd='.$renderer->wizard->row['ssd'];
	}	
}

class GuayaquilWizard2 extends GuayaquilWizard
{
	function DrawConditionDescription($catalog, $condition)
	{
		return '';
	}

	function DrawVehiclesListLink($catalog, $wizard)
	{
		return '';
	}
}

$renderer = new GuayaquilWizard2(new WizardExtender());
echo $renderer->Draw($_GET['c'], $wizard);

echo '<br><br>';

?>

