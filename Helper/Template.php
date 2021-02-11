<?php 

namespace Helper;

class Template {
    private $index = '';
    private $tpl = '';
    private $vars = [];
    
    public static function read( $file ) {
        return file_get_contents( $file );      
    }
    
    function setVar( $variable, $content ) {
        $this->vars[$variable] = $content;
    }
    
    function setVars( array $vars ) {
        $this->vars = [];

        foreach($vars as $k => $v) {
            $this->vars[$k] = $v;
        }
    }
    
    function set( String $file, bool $isIndex = true ) {
        $str = file_get_contents( $file );

        if($isIndex) {
            $this->index = $str;
        } else {
            $this->tpl = $str;
        }
    }
    
    function setFromString( String $str, bool $isIndex = true ) {
        if($isIndex) {
            $this->index = $str;
        } else {
            $this->tpl = $str;
        }
    }
    
    function parse( bool $isIndex = true ) {
        $str = $this->tpl;
        if( $isIndex ) {
            $str = $this->index;
        }

        foreach( $this->vars as $k => $v ) {
            $str = str_replace( "{" . strtoupper( $k ) . "}", $v, $str );
        }

        return $str;
    }
}

?>