<?php

/**
 * Debug your variables in the coolest way possible
 *
 * @param $var
 * @param string|null $name
 * @param bool $attributesOnly
 */
if (!function_exists('debug')) {
    function debug($var, $name = null, $attributesOnly = true)
    {
        $bt = debug_backtrace();
        $file = str_ireplace(dirname(dirname(__FILE__)), '', $bt[0]['file']);
        if (!class_exists('CActiveRecord', false))
            $attributesOnly = false;
        $name = $name ? '<b><span style="font-size:18px;">' . $name . ($attributesOnly ? ' [attributes]' : '') . '</span></b>:<br/>' : '';
        echo '<div style="background: #FFFBD6">';
        echo '<span style="font-size:12px;">' . $name . ' ' . $file . ' on line ' . $bt[0]['line'] . '</span>';
        echo '<div style="border:1px solid #000;">';
        echo '<pre>';
        if (is_scalar($var))
            var_dump($var);
        elseif ($attributesOnly && $var instanceof CActiveRecord)
            echo '<b>' . get_class($var) . '</b>' . substr(print_r($var->attributes, true), 6);
        elseif ($attributesOnly && is_array($var) && current($var) instanceof CActiveRecord)
            foreach ($var as $k => $_var)
                echo '<b>' . get_class($_var) . '[' . $k . ']</b>' . substr(print_r($_var->attributes, true), 6);
        else
            print_r($var);
        echo '</pre></div></div>';
    }
}