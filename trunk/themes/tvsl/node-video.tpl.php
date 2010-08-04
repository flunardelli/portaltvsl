<?php
if ($teaser) {
?>
  <div id="node-<?php print $node->nid; ?>" class="<?php print $classes; ?> clearfix">
	<?php if (!$page): ?>
  	  <h2 class="title"><a href="<?php print $node_url; ?>"><?php print $title; ?></a></h2>
	<?php endif; ?>
	<div class="content">  
	  <?php
	  print $node->content['video_image_thumbnail']['#value'];
	  print '<div id="video-content" >' . $node->content['body']['#value'] . '</div>';
	  ?>
	</div>
  </div>
<?php
} else {
  $width = '480';
  $node->videox = ($node->videox) ? $node->videox : $width;
  $height = '360';
  $node->videoy = ($node->videoy) ? $node->videoy : $height;

  ?>
  <div id="node-<?php print $node->nid; ?>" class="<?php print $classes; ?> clearfix">
  <?php print $user_picture; ?>
  <?php if (!$page): ?>
    <h2 class="title"><a href="<?php print $node_url; ?>"><?php print $title; ?></a></h2>
  <?php endif; ?>

  <?php if ($unpublished): ?>
    <div class="unpublished"><?php print t('Unpublished'); ?></div>
  <?php endif; ?>

  <?php if ($display_submitted || $terms): ?>
    <div class="meta">
      <?php if ($display_submitted): ?>
        <span class="submitted">
          <?php
            print t('Submitted by !username on !datetime',
              array('!username' => $name, '!datetime' => $date));
          ?>
        </span>
      <?php endif; ?>

    </div>
  <?php endif; ?>

  <div class="content">
  <div id="video-media" class="video-media-body">
  <?php		  
      if ($node->iid) {
	global $base_url; 
	$imagenode = node_load($node->iid);
	$snapshot = $base_url . '/' . $imagenode->images['poster'];
    } else {
        $snapshot = '';
    }
  $element = new stdClass();
  $element->width = $width;
  $element->height = $height;
  $element->poster = $snapshot;
  $element->mediafile =  url($node->current_video_upload_file->filepath);
  $element->duration = $node->playtime_seconds;
  $element->showcontrols = true;
  $element->autoplay = false;
  $element->statusheight = 30;
  $element->autobuffer = true;
  
  print theme('portaltvsl_player',$element);
  print theme('portaltvsl_player_embed_toolbar',$element,$node);    
  ?>
  </div>

  <?php
  $view = views_get_current_view();
  if ($view->name != 'vod_images') {
	print '<div id="video-content">' . $node->content['body']['#value'] . '</div>';
	?>
	<div class="video-label">
    <?php if ($terms): ?>
      <div class="terms terms-inline"><?php print $terms; ?></div>
    <?php endif; ?>
	<ul>
	<li><?php print t('Submitted by !username on !datetime',array('!username' => $name, '!datetime' => $date)) ?></li>
	<li><b>Arquivo: </b>
	<span class="filefield-file"><span class="filefield-icon field-icon-<?php print $node->current_video_upload_file->filemime?>" style="float: none"><img class="field-icon-<?php print $node->current_video_upload_file->filemime?>" alt="<?php print $node->current_video_upload_file->filemime?> icon" src="<?php print $base_url.'/modules/filefield/icons/protocons/16x16/mimetypes/video-x-generic.png' ?>" /></span><a href="<?php print $node->vidfile?>" type="<?php print $node->current_video_upload_file->filemime?>; length=<?php print $node->size?>"><?php print substr(strrchr($node->current_video_upload_file->filename, "/"),1)?></a></span>

	<?php 
	//print substr(strrchr($node->current_video_upload_file->filename, "/"),1) 
	?>
	
	</li>
	<li><b>Tamanho: </b><?php print format_size($node->size)?></li>
	<li><b>Duração: </b><?php print format_interval($node->playtime_seconds) ?></li>
	<li><b>Dimensões: </b><?php print $node->videox  .'x' . $node->videoy?></li>
	</ul>
	</div>

	<div id="video-fivestar">
	<?php print $node->content['fivestar_widget']['#value'] ?>
	</div>
	<?php
  }			     
  ?>
  </div>
    <?php print $links; ?>
  </div> <!-- /.node -->
<?php
}
?>