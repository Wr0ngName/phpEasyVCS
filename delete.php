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
  $result = $vcs->delete($dir, $name, @$_REQUEST['comment']);
  if ($result >= 0) {
    $msg = 'File/directory '.$name.' was successfully deleted. ';
  } else if ($result == VCS_NOACTION || $result == VCS_NOTFOUND) {
    $err = 'File/directory '.$name.' not found. ';
  } else {
    $err = 'Error deleting file/directory '.$name.'. ';
  }
  $url = url('browse.php',array('dir'=>$dir,'all'=>@$_REQUEST['all'],
                                  'msg'=>$msg,'error'=>$err));
  header('Location: '.$url);
