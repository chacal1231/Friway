<?php 
if ( !empty($site_hooks[$position]) )
    foreach ( $site_hooks[$position] as $h )
        if ( $this->elementExists('hooks/' . $h['Hook']['key']) )
            echo $this->element('hooks/' . $h['Hook']['key']);
?>