<?php
if ($is_front) {
    include 'page-front.tpl.php';
    return; 
}
		
//if none of the above applies, load the page-default.tpl.php
include 'page-default.tpl.php';
return;