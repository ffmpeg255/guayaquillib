<?php

require_once dirname(__FILE__) . '/../template.php';

class GuayaquilCategoriesList extends GuayaquilTemplate
{
    var $categories = NULL;
    var $catalog = NULL;
    var $cataloginfo = NULL;
    var $ssd = NULL;
    var $vehicleid;
    var $selectedcategoryid;
    var $drawtoolbar = true;

    function MakeHierarchy($source_categories)
    {
        $categories = array();
        foreach ($source_categories as $row)
            if (($parent_id = (int)$row['parentcategoryid']) > 0) {
                $child            = new stdClass();
                $child->childrens = array();
                $child->data      = $row;

                if (!isset($categories[$parent_id])) {
                    $obj                    = new stdClass();
                    $obj->childrens         = array($child);
                    $categories[$parent_id] = $obj;
                } else
                    $categories[$parent_id]->childrens[] = $child;
            } else {
                if (!isset($categories[(int)$row['categoryid']])) {
                    $obj                                 = new stdClass();
                    $obj->childrens                      = array();
                    $obj->data                           = $row;
                    $categories[(int)$row['categoryid']] = $obj;
                } else
                    $categories[(int)$row['categoryid']]->data = $row;
            }
        return $categories;
    }

    function Draw($catalog, $categories, $vehicleid, $selectedcategoryid, $ssd, $cataloginfo = null)
    {
        $this->catalog            = $catalog;
        $this->categories         = $this->MakeHierarchy($categories->row);
        $this->vehicleid          = $vehicleid;
        $this->selectedcategoryid = $selectedcategoryid;
        $this->ssd                = $ssd;
        $this->cataloginfo        = $cataloginfo;

        if ((string)$this->cataloginfo['supportquickgroups'] == 'true')
            GuayaquilToolbar::AddButton($this->GetLocalizedString('QuickGroupsLink'), $this->FormatLink('quickgroup', null, $this->catalog));

        $html = $this->drawtoolbar ? GuayaquilToolbar::Draw() : '';
        $html .= $this->DrawBox($selectedcategoryid);

        return $html;
    }

    function DrawBox($selectedcategoryid)
    {
        $html = '<div class="guayaquil_categoryfloatbox">';
        $html .= '<b class="xtop"><b class="xb1"></b><b class="xb2"></b><b class="xb3"></b><b class="xb4"></b></b>';

        $html .= '<div class="xboxcontent"><p class="block_header">' . $this->GetLocalizedString('Categories') . '</p>';

        $html .= $this->DrawItems($this->categories, $selectedcategoryid);

        $html .= '</div>';
        $html .= '<b class="xbottom"><b class="xb4"></b><b class="xb3"></b><b class="xb2"></b><b class="xb1"></b></b>';
        $html .= '</div>';


        return $html;
    }

    function DrawItems($categories, $selectedcategoryid)
    {
        $row_num = 1;
        $html    = '';
        foreach ($categories as $row) {
            $link = $this->FormatLink('unit', $row->data, $this->catalog);
            $html .= $this->DrawItem($row, $row_num, $link, $selectedcategoryid, 0);
        }

        return $html;
    }

    function DrawItem($row, &$row_num, $link, $selectedcategoryid, $level)
    {
        if (($selectedcategoryid == (string)$row->data['categoryid']) || ($selectedcategoryid == '-1' && $row_num == 1)) {
            $html = '<div class="guayaquil_categoryitem_selected" style="margin-left:' . ($level * 20) . 'px" onclick="window.location=\'' . $link . '\'">';
            $html .= $this->DrawItemValue($row->data);
            $html .= '</div>';
        } else {
            $html = '<div class="guayaquil_categoryitem" style="margin-left:' . ($level * 20) . 'px" onmouseout="this.className=\'guayaquil_categoryitem\';" onmouseover="this.className=\'guayaquil_categoryitem_selected\';" onclick="window.location=\'' . $link . '\'">';
            $html .= '<a href="' . $link . '">';
            $html .= $this->DrawItemValue($row->data);
            $html .= '</a>';
            $html .= '</div>';
        }

        $row_num = $row_num + 1;

        if (isset($row->childrens) && is_array($row->childrens))
            foreach ($row->childrens as $sub_row) {
                $link = $this->FormatLink('unit', $sub_row->data, $this->catalog);
                $html .= $this->DrawItem($sub_row, $row_num, $link, $selectedcategoryid, $level + 1);
            }

        return $html;
    }

    function DrawItemValue($row)
    {
        return '<b>'.$row['code'] . '</b> ' . $row['name'];
    }

    function DrawSearchForm()
    {
        $html = '<center><form onSubmit="glow(this.code.value); return false;">';
        $html .= '<table border="0"><tr>';
        $html .= '<td valign="center" align="right">' . $this->GetLocalizedString("Search by code") . '</td>';
        $html .= '<td valign="center"><input name="code" size="7"></td>';
        $html .= '</tr></table>';
        $html .= '</form></center>';

        return $html;
    }
}
