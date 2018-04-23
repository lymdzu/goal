<?php
function smarty_modifier_short_email($string){
    $s = explode('@',$string);
    return substr($s[0],0,2).'****'.substr($s[0],-1).'@'.$s[1];
}
