<?php
# +--------------------------------------------------------------------+
# | phpEasyVCS                                                         |
# | The file-based version control system                              |
# +--------------------------------------------------------------------+
# | Copyright (c) 2011 Martin Vlcek                                    |
# | License: GPLv3 (http://www.gnu.org/licenses/gpl-3.0.html)          |
# +--------------------------------------------------------------------+

  require_once('inc/basic.php');
  require_once('inc/template.php');
  
  $msg = $err = null;
  $dir = sanitizeDir($_REQUEST['dir']);
  $name = sanitizeName($_REQUEST['name']);
  $vcs = new FileVCS(DATAPATH, null, getUserName(), isReadOnly());
  $entry = $vcs->getEntry($dir, $name, null, true);
  if ($entry->isDirectory) {
    $result = $vcs->undeleteDirectory($dir, $name, @$_REQUEST['comment']);
    if ($result == VCS_SUCCESS || $result > 0) {
      $msg = 'Directory '.$name.' was successfully undeleted. ';
    } else if ($result == VCS_NOACTION || $result == VCS_EXISTS) {
      $msg = 'Directory '.$name.' already exists. ';
    } else {
      $err = 'Error undeleting directory '.$name.'. ';
    }
  } else {
    $result = $vcs->revertFile($dir, $name, $entry->version - 1);
    if ($result == VCS_SUCCESS || $result > 0) {
      $msg = 'File '.$name.' was successfully undeleted. ';
    } else if ($result == VCS_NOACTION || $result == VCS_EXISTS) {
      $msg = 'File '.$name.' already exists. ';
    } else {
      $err = 'Error undeleting file '.$name.'. ';
    }
  }
  $url = url('browse.php',array('dir'=>$dir,'all'=>1,'msg'=>$msg,'error'=>$err));
  header('Location: '.$url);
  