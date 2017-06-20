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
  require_once('inc/settings.class.php');
  require_once('inc/users.class.php');

  $repname = $_GET[repository];
  if (changeUserRepository($repname)) {
    $url = url('browse.php',array('msg'=>"Repository successfully switched to $repname!"));
  } else {
    $url = url('browse.php',array('error'=>"Error switching repositories!"));
  }
  header('Location: '.$url);
  