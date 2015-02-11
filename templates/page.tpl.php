<?php if ($page['header']) print render($page['header']) ?>
<?php if ($page['pre_content']) print render($page['pre_content']) ?>
  <div class ="content-and-sidebar">
    <div class="content-regions" id="content-regions">
      <?php if ($page['content_top']) print render($page['content_top']) ?>
      <?php if ($page['content']) print render($page['content']) ?>
      <?php if ($page['content_bottom']) print render($page['content_bottom']) ?>
    </div>
    <?php if ($page['sidebar_first']) print render($page['sidebar_first']) ?>
  </div>
<?php if ($page['post_content']) print render($page['post_content']) ?>
<?php if ($page['pre_footer']) print render($page['pre_footer']) ?>
<footer class="page-footer">
  <?php if ($page['footer']) print render($page['footer']) ?>
  <?php if ($page['copyright']) print render($page['copyright']) ?>
</footer>
