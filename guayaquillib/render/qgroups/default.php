<?php

require_once dirname(__FILE__).DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'template.php';

class GuayaquilQuickGroupsList extends GuayaquilTemplate
{
	var $groups = NULL;
	var $vehicleid = NULL;
	var $ssd = NULL;
    var $collapsed_level = 1;
    var $catalog;

    var $drawtoolbar = true;

	function Draw($groups, $catalog, $vehicle_id, $ssd)
	{
        $this->catalog = $catalog;
        $this->vehicleid = $vehicle_id;
        $this->ssd = $ssd;

        $this->groups = $groups->row;

		GuayaquilToolbar::AddButton($this->GetLocalizedString('VehicleLink'), $this->FormatLink('vehicle', null, $this->catalog));

        $html = $this->drawtoolbar ? GuayaquilToolbar::Draw() : '';

        $html .= $this->DrawSearchPanel();

        $html .= '<div id="qgTree" onclick="tree_toggle(arguments[0])"><ul class="qgContainer">';

        if (count($this->groups) == 1)
        {
            $index = 1;
            $amount = count($groups->row);
            foreach ($this->groups->row as $subgroup)
                $html .= $this->DrawTreeNode($subgroup, 1, $index++ == $amount);
        }
        else
            foreach ($this->groups as $group)
                $html .= $this->DrawTreeNode($group, 1);

        $html .= '</ul></div>';


		return $html;
	}

	function DrawTreeNode($group, $level, $last = false)
	{
        $childrens = count($group->children());
        $html = '<li class="qgNode '.($childrens == 0 ? 'qgExpandLeaf' : 'qgExpandClosed').($last ? ' qgIsLast' : '').'"><div class="qgExpand"></div>';

        $html .= $this->DrawItem($group, $level);

        $subhtml = '';
        $index = 1;
		foreach ($group->row as $subgroup)
			$subhtml .= $this->DrawTreeNode($subgroup, $level + 1, $index++ == $childrens);

        if ($subhtml)
            $html .= '<ul class="qgContainer">'.$subhtml.'</ul>';

		$html .= '</li>';

		return $html;
	}
	
	function DrawItem($group, $level)
	{
        $has_link = ((string)$group['link']) == 'true';
        if ($has_link)
        {
            $link = $this->FormatLink('quickgroup', $group, $this->catalog);
            return '<div class="qgContent"><a href="'.$link.'">'.$group['name'].'</a></div>';
        }

        return '<div class="qgContent">'.$group['name'].'</div>';
	}

	function DrawSearchPanel()
	{
        return '<input type="text" maxlength="20" size="50" id="qgsearchinput" value="'.$this->GetLocalizedString('ENTER_GROUP_NAME').'" title="'.$this->GetLocalizedString('ENTER_GROUP_NAME').'" onfocus=";if(this.value==\''.$this->GetLocalizedString('ENTER_GROUP_NAME').'\')this.value=\'\';" onblur="if(this.value.replace(\' \', \'\')==\'\')this.value=\''.$this->GetLocalizedString('ENTER_GROUP_NAME').'\';" onkeyup="QuickGroups.Search(this.value);">
            <input type="button" value="'.$this->GetLocalizedString('RESET_GROUP_NAME').'" onclick="jQuery(\'#qgsearchinput\').attr(\'value\', \''.$this->GetLocalizedString('ENTER_GROUP_NAME').'\'); QuickGroups.Search(\'\');">

        <div id="qgFilteredGroups"></div>';
	}

}
