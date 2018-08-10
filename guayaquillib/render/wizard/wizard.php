<?php

require_once dirname(__FILE__).DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'template.php';

class GuayaquilWizard extends GuayaquilTemplate
{
	var $wizard = NULL;
	var $catalog = NULL;

	function __construct(IGuayaquilExtender $extender)
	{
		parent::__construct($extender);
	}

	function Draw($catalog, $wizard)
	{
		$this->wizard = $wizard;
		$this->catalog = $catalog;

		$html = '<div class="DrawHeader">';
		$html .= '<script type="text/javascript">';
		$html .= 'function openWizard(valueid) {';
		$html .= 'window.location = \''.$this->FormatLink('wizard', null, $catalog).'\'.replace(\'\\$valueid\\$\', valueid);';
		$html .= '}';
		$html .= '</script>';
		$html .= '<form name="findByParameterIdentifocation" id="findByParameterIdentifocation">';
		$html .= '<table border="0" width="100%">';
		
		$html .= $this->DrawHeader();

		foreach($wizard->row as $condition)
			$html .= $this->DrawConditionRow($catalog, $condition); 		
		
		$html .= '</table>';
		$html .= '</form>';

		if ($wizard->row['allowlistvehicles'] == 'true')
			$html .= $this->DrawVehiclesListLink($catalog, $wizard);

		$html .= '</div>';

		return $html;
	}

	function DrawHeader()
	{
		return '';
	}

	function DrawConditionRow($catalog, $condition)
	{
		$html = '<tr width="60%"'.($condition['automatic'] == 'false' ? ' class="guayaquil_SelectedRow"' : '').'>';
		$html .= '<td>'.$this->DrawConditionName($catalog, $condition).'</td>';

		$html .= '<td>';
		if ($condition['determined'] == 'false')
			$html .= $this->DrawSelector($catalog, $condition);
		else
			$html .= $this->DrawDisabledSelector($catalog, $condition);
		
		$html .= '</td><td>';
		$html .= $this->DrawConditionDescription($catalog, $condition);
		$html .= '</td></tr>';

		return $html;
	}

	function DrawConditionName($catalog, $condition)
	{
		return $condition['name'];
	}

	function DrawConditionDescription($catalog, $condition)
	{
		return $condition['description'];
	}

	function DrawSelector($catalog, $condition)
	{
		$html = '<select style="width:250px" name="Select'.$condition['type'].'" onChange="openWizard(this.options[this.selectedIndex].value); return false;">';

		$html .= '<option value="null">&nbsp;</option>';

		foreach($condition->options->row as $row) {
			$html .= $this->DrawSelectorOption($row);
		}

		$html .= '</select>';

		return $html;
	}

	function DrawDisabledSelector($catalog, $condition)
	{
		$html = '<select disabled style="width:250px" name="Select'.$condition['type'].'">';
		$html .= $this->DrawDisabledSelectorOption($condition);
		$html .= '</select>';

		return $html;
	}

	function DrawSelectorOption($row)
	{
		return '<option value="'.$row['key'].'">'.$row['value'].'</option>';
	}

	function DrawDisabledSelectorOption($condition)
	{
		return '<option disabled selected value="null">'.$condition['value'].'</option>';
	}

	function DrawVehiclesListLink($catalog, $wizard)
	{
		$html = '<br><a class="gWizardVehicleLink" href="'.$this->FormatLink('vehicles', $wizard, $catalog).'">';
		$html .= $this->GetLocalizedString('List vehicles');
		$html .= '</a>';

		return $html;
	}
}

?>