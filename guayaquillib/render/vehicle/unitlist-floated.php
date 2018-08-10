<?php

require_once dirname(__FILE__) . '/../template.php';

class GuayaquilUnitsList extends GuayaquilTemplate
{
    var $units = NULL;
    var $catalog = NULL;
    var $zoom_image = NULL;
    var $filter_image = NULL;

    var $imagesize = 175;

    function __construct(IGuayaquilExtender $extender)
    {
        parent::__construct($extender);
    }

    function Draw($catalog, $units)
    {
        $this->catalog = $catalog;

        $this->AppendJavaScript(dirname(__FILE__) . '/unitlist-floated.js');
        $this->AppendJavaScript(dirname(__FILE__) . '/../jquery.colorbox.js');
        $this->AppendJavaScript(dirname(__FILE__) . '/../jquery.tooltip.js');

        $this->AppendCSS(dirname(__FILE__) . '/../colorbox.css');
        $this->zoom_image   = '/guayaquillib/render/images/zoom.png';
        $this->filter_image = '/guayaquillib/render/images/filter.png';

        $html = '';

        foreach ($units->row as $row) {
            $filter = (string)$row['filter'];
            $link   = $this->FormatLink(strlen($filter) > 0 ? 'filter' : 'unit', $row, $catalog);
            $html .= $this->DrawItem($row, $link, $filter);
        }

        return $html;
    }

    function DrawItem($row, $link, $filter)
    {
        static $noteid = 1;

        $row['note']='';
        foreach ($row->attribute as $attr)
            $row['note'].='<b>'.(string) ($attr->attributes()->name) .'</b>: ' .(string) $attr->attributes()->value.'<br/>';

        $note = $this->GetUnitNote($row['note']);//$row['note']);

        $html = '<a name="_' . $row['code'] . '"></a>';
        $html .= '<div class="guayaquil_floatunitlist_box guayaquil_floatunitlist_' . $this->imagesize . '" note="' . $noteid . '">';

        $html .= '<div class="guayaquil_unit_icons">';

        if (strlen($filter) > 0)
            $html .= '<div class="guayaquil_unit_filter"><img src="' . $this->filter_image . '"></div>';
        $html .= '<div class="guayaquil_zoom" link="'.$link.'" full="' . str_replace('%size%', 'source', $row['imageurl']) . '" title="' . $row['code'] . ': ' . $row['name'] . '"><img src="' . $this->zoom_image . '"></div>';

        $html .= '</div>';

        //$html .= ' <div name="'.$row['code'].'" class="g_highlight">';
        $html .= ' <div name="' . trim($row['code']) . '" class="g_highlight" onclick="window.location=\'' . $link . '\'">';
        $html .= '  <table class="guayaquil_floatunitlist" border="0">';
        $html .= '  <tr><td valign="center" class="guayaquil_floatunitlist_image_' . $this->imagesize . '">';
        $html .= $this->DrawImage($row, $link);
        $html .= '  </td></tr>';
        $html .= '  <tr><td class="guayaquil_floatunitlist_title" id="unm' . $noteid . '">';
        $html .= $this->DrawUnitName($row, $link, $filter);
        $html .= '  </td></tr>';
        $html .= '  </table>';
        $html .= '</div></div>';

        if (strlen($note))
            $html .= '<span id="utt' . $noteid . '" style="display:none">' . htmlspecialchars($note) . '</span>';

        $noteid++;

        return $html;
    }

    function GetUnitNote($note)
    {
        return str_replace("\n", '<br>', (string)$note);
    }

    function DrawImage($row, $link)
    {
        $img = $row['imageurl'];

        if (!strlen($img))
            $img = $this->Convert2uri(dirname(__FILE__) . '/../images/noimage.png');

        return '<center><img border="0" src="' . str_replace('%size%', $this->imagesize, $img) . '"></center>';
        //return '<center><a href="'.$link.'"><img border="0" src="'.str_replace('%size%', $this->imagesize, $row['imageurl']).'" full="'.str_replace('%size%', 'source', $row['imageurl']).'"></a></center>';
    }

    function DrawUnitName($row, $link, $filter)
    {
        return '<center><a href="' . $link . '"' . (strlen($filter) > 0 ? ' class="g_filter_unit"' : '') . '>' . $this->DrawUnitNameValue($row) . '</a></center>';
    }

    function DrawUnitNameValue($row)
    {
        return '<b>' . $row['code'] . ':</b> ' . $row['name'];
    }
}

?>