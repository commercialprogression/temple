<?php if (!empty($content)): ?>
<div class="<?php print $classes ?>" <?php print ($attributes) ?>>
  <?php if (!empty($title_prefix)) print render($title_prefix); ?>
  <?php if (!empty($title_suffix)) print render($title_suffix); ?>

  <div class="content">
    <?php print render($content) ?>
  </div>
  
</div>
<?php endif; ?>
