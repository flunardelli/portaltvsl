#!/usr/bin/php -q
<?php
    $drupal_root = '/var/www/stream';
    $_SERVER['HTTP_HOST'] = 'stream.softwarelivre.org';
    include_once $drupal_root . '/includes/bootstrap.inc';
    drupal_bootstrap(DRUPAL_BOOTSTRAP_FULL);

    global $user;
    $uid = 12;
    $user = user_load($uid);

    $basedir = '/home/fuxu/videos_editados';
    
    $f = file_scan_directory($basedir, '\.og[gv]$', array('.', '..', 'CVS'), 0, FALSE);
    $n = 0;
    foreach($f as $file) {
	$n++;
	//$filepath = "/var/www/asltube/videos_import/introducao-ao-apache-lucine_inserindo-busca-eficiente-ao-seu-projeto.ogg";
	$filepath = $file->filename;

	$filename = basename($filepath);
	$details = stat($filepath);
	$filesize = $details['size'];
	$mtime = $details['mtime'];
	$date_value = date('Y-m-d\T00:00:00',$mtime);

	$infodata = array();
	
	
	$p = $filepath;
	$filepath = new stdClass();
	$filepath->filepath = $p;
	$filepath->filename = basename($p);
        $filepath->filesize = filesize($p);
	$dest = file_directory_path() . '/videos/'.$filename;
	
	if (is_file($dest)) {
	    print "$n - $filename (imported)\n";
	    continue;
	}
	exec('./ogginfoparser ' . $p,$infodata);
	
	//var_dump($infodata);

	$info = explode(';',$infodata[1]);

	$vid = 3;
	if ($info[0] && $info[1] && $info[2]) {
	    $tagstring = trim($info[0]).','.str_replace(' - ',',',$info[1]).','.str_replace(' ',',',$info[2]);
        } else { 
	    $tagstring = '';
	}
	//continue;
	/*
	[0]=>
	string(15) "Trilha: Desktop"
	[1]=>
	string(17) "CC - BR - BY - SA"
	[2]=>
	string(12) "2009 ASL.org"
	[3]=>
	string(58) "Thomaz Philippe C. Silva, Mauricio Lin, Roberto dos Santos"
	[4]=>
	string(71) "Introdução ao Apache Lucene: Inserindo busca eficiente ao seu projeto"
	[5]=>
	string(20) "Porto Alegre, Brasil"
	[6]=>
	string(4) "2009"
	[7]=>
	string(7) "ASL.org"	      
	*/
	$videotitle = ($info[0]) ? $info[4] : $filename;
	$videobody = implode("\n",$info);
	$videoauthor = ($info[3]) ? $info[3] : 'ASL.org';
        
	$node = new stdClass();
	if ($tagstring)
	    $node->taxonomy['tags'] = array($vid => $tagstring);
	$node->type = 'video';
	$node->status = 1;
	$node->uid = $uid;
	$node->title = $videotitle;
	$node->body = $videobody;
	$node->created = time();
	$node->changed = $node->created;
	$node->promote = 0;
	$node->sticky = 0;
	$node->format = 1;
	$node->translate = 0;
	$node->comment = 2; 
	$node->language = 'pt-br';
	$node->vtype = 'upload';
	$node->form_id ="video_node_form";
	$node->field_autor = array(array("value"=>$videoauthor,"_error_element"=>"field_autor][0][value"));					    
	$node->path = "";
	$node->pathauto_perform_alias = 1;
	$node->new_video_upload_file_fid = 0;
	$node->hvresolution = 'x';
	$node->op = 'Salvar';
	$node->attach = 'Anexar';
    
	print "$n - $videotitle\n";
	
	file_copy($filepath,$dest);
	$filepath = $dest;

    			
	$file_obj = new stdClass();
	$file_obj->filename = $filename;
	$file_obj->filepath = $filepath;
	$file_obj->filemime =  file_get_mimetype($filename);
	$file_obj->filesize = $filesize;
	$file_obj->filesource = $filename;
	$file_obj->uid = $uid;
	$file_obj->status = FILE_STATUS_TEMPORARY;
	$file_obj->timestamp = time();
	$file_obj->list = 1;
	$file_obj->new = true;
	// save file to database
	drupal_write_record('files', $file_obj);
	// change file status to permanent (default is temporary)
	file_set_status($file_obj,1);

	$node->new_video_upload_file = $file_obj;
	$node->new_video_upload_file_fid = $file_obj->fid;
																						    
	$_POST['op'] = 'Submit';
	node_invoke_nodeapi($node, 'prepare');
	$node->new_video_upload_file_fid = $node->new_video_upload_file->fid;
	$node = node_submit($node);
	node_save($node);
}
?>
