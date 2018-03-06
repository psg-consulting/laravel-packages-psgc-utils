<?php
namespace PsgcLaravelPackages\Utils;

class FormHelpers
{

    // Label for forms
    public static function renderFieldLabel($fieldKey, $display=null, $isUpdate=false, $attrs=[])
    {
        $html = '';
        $html .= \Form::label($fieldKey,$display,$attrs); // %TODO: default or render function for display text
        if ( self::isFieldRequired($fieldKey,$isUpdate) ) {
            $html .= '<span class="tag-required">*</span>';
        }
        return $html;
    } // renderFieldLabel()

    public static function isFieldRequired($fieldKey,$isUpdate=false)
    {
        $isRequired = false; // default

        $vstruct = self::getValidationRules($isUpdate);
        $rules = $vstruct['rules'];
        if ( array_key_exists($fieldKey, $rules) ) {
            $isRequired = ( false !== strpos($rules[$fieldKey],'required') );
        }
        return $isRequired;
    }

}
