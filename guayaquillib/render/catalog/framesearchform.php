<?php

require_once dirname(__FILE__) . '/../template.php';

class GuayaquilFrameSearchForm extends GuayaquilTemplate
{
    var $catalog = NULL;
    var $cataloginfo = NULL;

    function __construct(IGuayaquilExtender $extender)
    {
        parent::__construct($extender);
    }

    function Draw($catalog, $cataloginfo = false, $prevframe = '', $prevframeno = '')
    {
        $this->cataloginfo = $cataloginfo;
        $this->catalog = $catalog;

        $html = $this->DrawFrameCheckScript();
        $html .= $this->DrawFrameExample($cataloginfo);
        $html .= $this->DrawFrameForm($catalog, $prevframe, $prevframeno);

        return $html;
    }

    function DrawFrameCheckScript()
    {
        $html = '<script type="text/javascript">
		function checkFrameValue(frame, frameno, submit_btn) {
            var frexpr = new RegExp(\'' . $this->GetFrameRegularExpression() . '\', \'i\');
            var frnexpr = new RegExp(\'' . $this->GetFrameNoRegularExpression() . '\', \'i\');
            var result = true;

            if (frexpr.test(frame))
                jQuery(\'#FrameInput\').attr(\'class\',\'g_input\');
            else {
                jQuery(\'#FrameInput\').attr(\'class\',\'g_input_error\');
                result = false;
            }

            if (frnexpr.test(frameno))
                jQuery(\'#FrameNoInput\').attr(\'class\',\'g_input\');
            else {
                jQuery(\'#FrameNoInput\').attr(\'class\',\'g_input_error\');
                result = false;
            }

            if (result) {
                jQuery(submit_btn).attr(\'disabled\', \'1\'); window.location = \'' . $this->FormatLink('vehicles', NULL, $this->catalog) . '\'.replace(\'\\$frame\\$\', frame).replace(\'\\$frameno\\$\', frameno);
            }

            return false;  // return result;
		} 
		</script>';

        return $html;
    }

    function GetFrameRegularExpression()
    {
        return '\\^[A-z0-9]{3,7}\$';
    }

    function GetFrameNoRegularExpression()
    {
        return '\\^[0-9]{3,7}\$';
    }

    function GetFrameExample($cataloginfo)
    {
        if ($cataloginfo) {
            foreach ($cataloginfo->features->feature as $feature) {
                if ((string)$feature['name'] == 'framesearch') {
                    return $feature['example'];
                }
            }
        }

        return 'XZU423-0001026';
    }

    function DrawFrameExample($cataloginfo)
    {
        return $this->GetLocalizedString('InputFrame', array($this->GetFrameExample($cataloginfo))) . '<br>';
    }

    function DrawFrameForm($catalog, $prevframe, $prevframeno)
    {
        $html = '<form name="findByFrame" onSubmit="return checkFrameValue(this.frame.value, this.frameNo.value, this.vinSubmit);" id="findByFrame" >';
        $html .= '<div id="FrameInput" class="g_input"><input name="frame" type="text" id="frame" size="17" width="90" value="' . $prevframe . '"/></div>';
        $html .= '-';
        $html .= '<div id="FrameNoInput" class="g_input"><input name="frameNo" type="text" id="frameNo" size="17" width="120" value="' . $prevframeno . '"/></div>';
        $html .= '<input type="submit" name="vinSubmit" value="' . $this->GetLocalizedString('Search') . '" id="vinSubmit" />';
        $html .= '</form>';

        return $html;
    }
}

?>
