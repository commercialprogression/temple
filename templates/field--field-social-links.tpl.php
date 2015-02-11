<?php if (!$label_hidden): ?>
  <h3 class="field-label"<?php print $title_attributes; ?>><?php print $label ?></h3>
<?php endif; ?>
<div class="social">
<?php foreach ($items as $delta => $item): ?>
  <?php print render($item); ?>
<?php endforeach; ?>
</div>
