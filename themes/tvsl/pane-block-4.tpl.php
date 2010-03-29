<?php
// $Id: block.tpl.php,v 1.6 2009/11/02 17:42:27 johnalbin Exp $

/**
 * @file
 * Theme implementation to display a block.
 *
 * Available variables:
 * - $title: Block title.
 * - $content: Block content.
 * - $block->module: Module that generated the block.
 * - $block->delta: An ID for the block, unique within each module.
 * - $block->region: The block region embedding the current block.
 * - $edit_links: A list of contextual links for the block. It can be
 *   manipulated through the variable $edit_links_array from preprocess
 *   functions.
 * - $classes: String of classes that can be used to style contextually through
 *   CSS. It can be manipulated through the variable $classes_array from
 *   preprocess functions. The default values can be one or more of the
 *   following:
 *   - block: The current template type, i.e., "theming hook".
 *   - block-[module]: The module generating the block. For example, the user
 *     module is responsible for handling the default user navigation block. In
 *     that case the class would be "block-user".
 *   - region-count-[x]: The position of the block in the list of blocks in the
 *     current region.
 *   - region-odd: An odd-numbered block of the list of blocks in the current
 *     region.
 *   - region-even: An even-numbered block of the list of blocks in the current
 *     region.
 *   - count-[x]: The position of the block in the list of blocks on the current
 *     page.
 *   - odd: An odd-numbered block of the list of blocks on the current page.
 *   - even: An even-numbered block of the list of blocks on the current page.
 *
 * Helper variables:
 * - $classes_array: Array of html class attribute values. It is flattened
 *   into a string within the variable $classes.
 * - $edit_links_array: An array of contextual links for the block.
 * - $block_zebra: Outputs 'odd' and 'even' dependent on each block region.
 * - $zebra: Same output as $block_zebra but independent of any block region.
 * - $block_id: Counter dependent on each block region.
 * - $id: Same output as $block_id but independent of any block region.
 * - $is_front: Flags true when presented in the front page.
 * - $logged_in: Flags true when the current user is a logged-in member.
 * - $is_admin: Flags true when the current user is an administrator.
 *
 * @see template_preprocess()
 * @see zen_preprocess()
 * @see template_preprocess_block()
 * @see zen_preprocess_block()
 * @see zen_process()
 */
?>
<div id="block-<?php print $block->module . '-' . $block->delta; ?>" class="<?php print $classes; ?>">
  <?php if ($title): ?>
    <h2 class="title"><?php print $title; ?></h2>
  <?php endif; ?>

  <div class="content">
	<?php
    global $base_path;
	$base_path = 'http://stream.softwarelivre.org/';
	drupal_add_js('radiosl/popup/libs/jquery.hoverIntent.minified.js');
	drupal_add_js('radiosl/popup/libs/bgiframe_2.1.1/jquery.bgiframe.min.js');
	drupal_add_js('radiosl/popup/libs/excanvas_r3/excanvas.js');
	drupal_add_js('radiosl/popup/jquery.bt.min.js');
	drupal_add_js('radiosl/popup/libs/jquery.easing.1.3.js');
	drupal_add_css('radiosl/radiosl.css');
	drupal_add_css('radiosl/anogg.css');
	$embed_str = '<iframe id="player-base" src="' . $base_path .'radiosl/embed.php" width="250" height="60" align="right" frameborder="0" scrolling="no"></iframe>';
	
	$b = '$(\'#player-embed\').bt(\'<textarea style="font-size:10px;height:60px;width:260px;border:0;"  onFocus="this.select()" >'.$embed_str.'</textarea>\', {width: 260, fill: \'white\',cornerRadius: 20,padding:10, strokeWidth: 1,trigger: \'click\', positions: \'bottom\'});';
	$r = '$(document).ready(function() { '.$b.' })';
	drupal_add_js($r,'inline');
	?>
	<?php print $embed_str;?>
	<div id="player-embed" style="width:30px ;color: white; font-size: 8px;color:#fff;float:right;font-size:8px;margin-left:-28px;width:30px;"><a href="javascript:void(0);" style="color: #fff;">(+)</a></div>
  </div>
  <?php print $edit_links; ?>
</div> <!-- /.block -->
