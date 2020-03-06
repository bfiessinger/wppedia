<form action="" method="GET" class="--wp-pedia-admin-filter">
  <input type="hidden" name="page" value="wiki">

  <fieldset class="--wp-pedia-letter-group">
  <?php foreach( $this->get_wiki_initial_letters() as $initial_letter ): ?>
    <input type="radio" name="wiki-initial-letter" id="wiki-initial-letter-<?php echo $initial_letter; ?>" class="screen-reader-text" value="<?php echo $initial_letter; ?>">
    <label for="wiki-initial-letter-<?php echo $initial_letter; ?>"><?php echo $initial_letter; ?></label>
  <?php endforeach; ?>
  </fieldset>

  <input type="submit" value="<?php echo __('Filter'); ?>" class="button button-primary --wp-pedia-filter-submit" />

</form>
