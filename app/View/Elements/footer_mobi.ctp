<div class="visible-xs visible-sm">
    <div class="mobile-footer">
<?php if( !empty($uid) || (!$this->isEmpty('east') && $this->isActive('east')) || (!$this->isEmpty('west') && $this->isActive('west')) ):  ?>

        <?php if( !$this->isEmpty('west') && $this->isActive('west') ): ?>
        <a class="pull-left" href="#" data-toggle="modal" data-target="#leftnav"><i class="icon-leftpanel"></i></a>
        <?php endif; ?>
        <?php if( !$this->isEmpty('east') && $this->isActive('east') ): ?>
        <a href="#" data-toggle="modal" data-target="#right" class="pull-right"><i class="icon-rightpanel"></i></a>
        <?php endif; ?>
   
<?php endif; ?>
 </div>
</div>
