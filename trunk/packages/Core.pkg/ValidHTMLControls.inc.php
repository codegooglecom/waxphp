<?
    class ValidHTMLControls {
        const NO_AUTOCLOSE = 1;
        const ALLOW_AUTOCLOSE = 2;
        private static $VALID = array(
            'a'         => self::NO_AUTOCLOSE,'abbr'        => self::NO_AUTOCLOSE,'acronym' => self::NO_AUTOCLOSE,'address' => self::NO_AUTOCLOSE,
            'area'      => self::NO_AUTOCLOSE,'b'           => self::NO_AUTOCLOSE,'base '   => self::NO_AUTOCLOSE,'bdo'     => self::NO_AUTOCLOSE,'big' => self::NO_AUTOCLOSE,
            'blockquote'=> self::NO_AUTOCLOSE,'body'        => self::NO_AUTOCLOSE,'br'      => self::ALLOW_AUTOCLOSE,'button'  => self::NO_AUTOCLOSE,
            'caption'   => self::NO_AUTOCLOSE,'cite'        => self::NO_AUTOCLOSE,'code'    => self::NO_AUTOCLOSE,'col'     => self::NO_AUTOCLOSE,
            'colgroup'  => self::NO_AUTOCLOSE,'dd'          => self::NO_AUTOCLOSE,'del'     => self::NO_AUTOCLOSE,'dfn'     => self::NO_AUTOCLOSE,'div' => self::NO_AUTOCLOSE,
            'dl'        => self::NO_AUTOCLOSE,'doctype'     => self::NO_AUTOCLOSE,'dt'      => self::NO_AUTOCLOSE,'em'      => self::NO_AUTOCLOSE,'fieldset' => self::NO_AUTOCLOSE,
            'form'      => self::NO_AUTOCLOSE,'h1'          => self::NO_AUTOCLOSE,'h2'      => self::NO_AUTOCLOSE,'h3'      => self::NO_AUTOCLOSE,'h4' => self::NO_AUTOCLOSE,
            'h5'        => self::NO_AUTOCLOSE,'h6'          => self::NO_AUTOCLOSE,'head'    => self::NO_AUTOCLOSE,'html'    => self::NO_AUTOCLOSE,'hr'=> self::ALLOW_AUTOCLOSE,
            'input'     => self::ALLOW_AUTOCLOSE,'ins'      => self::NO_AUTOCLOSE,'kbd'     => self::NO_AUTOCLOSE,'label'   => self::NO_AUTOCLOSE,'legend' => self::NO_AUTOCLOSE,
            'li'        => self::NO_AUTOCLOSE,'link'        => self::ALLOW_AUTOCLOSE,'map'     => self::NO_AUTOCLOSE,'meta'    => self::NO_AUTOCLOSE,'noscript' => self::NO_AUTOCLOSE,
            'object'    => self::NO_AUTOCLOSE,'ol'          => self::NO_AUTOCLOSE,'optgroup'=> self::NO_AUTOCLOSE,'option'  => self::NO_AUTOCLOSE,'p' => self::NO_AUTOCLOSE,
            'param'     => self::NO_AUTOCLOSE,'pre'			=> self::NO_AUTOCLOSE,'q'     	=> self::NO_AUTOCLOSE,'samp'    => self::NO_AUTOCLOSE,'script'  => self::NO_AUTOCLOSE,
            'select'    => self::NO_AUTOCLOSE,'small'       => self::NO_AUTOCLOSE,'span'    => self::NO_AUTOCLOSE,'strong'  => self::NO_AUTOCLOSE,
            'style'     => self::NO_AUTOCLOSE,'sub'         => self::NO_AUTOCLOSE,'sup'     => self::NO_AUTOCLOSE,'table'   => self::NO_AUTOCLOSE,'tbody' => self::NO_AUTOCLOSE,
            'td'        => self::NO_AUTOCLOSE,'textarea'    => self::NO_AUTOCLOSE,'tfoot'   => self::NO_AUTOCLOSE,'th'      => self::NO_AUTOCLOSE,'thead' => self::NO_AUTOCLOSE,
            'title'     => self::NO_AUTOCLOSE,'tr'          => self::NO_AUTOCLOSE,'tt'      => self::NO_AUTOCLOSE,'ul'      => self::NO_AUTOCLOSE,'var' => self::NO_AUTOCLOSE,
            'i'         => self::NO_AUTOCLOSE,'img'         => self::ALLOW_AUTOCLOSE
        );
        
        static function Contains($tag) { 
        	$tag = strtolower($tag);
            return (isset(self::$VALID[$tag]) ? self::$VALID[$tag] : false); 
        }
        
        static function ForceRenderValue($tag) {
        	return (self::$VALID[strtolower($tag)] == self::NO_AUTOCLOSE);
        }
    }
?>