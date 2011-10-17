<?php if ($show_slideshow == FALSE): ?>
  <?php $tag = $label_hidden ? 'div' : 'section'; ?>
  <<?php print $tag; ?> class="<?php print $classes; ?>"<?php print $attributes; ?>>
    <?php if (!$label_hidden) : ?>
      <h2 class="field-label"<?php print $title_attributes; ?>><?php print $label ?>:&nbsp;</h2>
    <?php endif; ?>
    <div class="field-items"<?php print $content_attributes; ?>>
      <?php foreach ($items as $delta => $item): ?>
        <figure class="field-item"<?php print $item_attributes[$delta]; ?>>
          <?php print render($item); ?>
          <?php if (isset($item['#item']['title'])): ?>
            <?php if ($field_view_mode == 'full'): ?> 
              <?php if (theme_get_setting('image_caption_full') == 1): ?>   
                <figcaption class="caption full-caption"><?php print $item['#item']['title']; ?></figcaption>
              <?php endif; ?>
            <?php endif; ?>
            <?php if ($field_view_mode == 'teaser'): ?> 
              <?php if (theme_get_setting('image_caption_teaser') == 1): ?>
                <figcaption class="caption teaser-caption"><?php print $item['#item']['title']; ?></figcaption>
              <?php endif; ?>
            <?php endif; ?>
          <?php endif; ?>
        </figure>
      <?php endforeach; ?>
    </div>
  </<?php print $tag; ?>>
<?php elseif ($show_slideshow == TRUE): ?>
		<div class="flex-container">
			<div class="flexslider">
		    <ul class="slides">
          <?php foreach ($items as $delta => $item): ?>
            <li>
              <?php print render($item); ?>
              <?php if ($show_slideshow_caption == TRUE): ?>
                <div class="slideshow-caption"><p><?php print $item['#item']['title']; ?></p></div>
              <?php endif; ?>
            </li>
          <?php endforeach; ?>
		    </ul>
		  </div>
	 	</div>
<?php endif; ?>
