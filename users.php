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

  checkUserLevel(USERLEVEL_ADMIN);
  $errors = array();
  # get settings
  $settings = Settings::getSettings();
  # get repositories
  $repositories = array();
  $dh = opendir(ROOTPATH.'data');
  while (($filename = readdir($dh)) !== false) {
    if (substr($filename,0,1) != '.' && is_dir(ROOTPATH.'data/'.$filename)) {
      $repositories[] = $filename;
    }
  }
  sort($repositories);
  # process request
  $user = null;
  $users = Users::getUsers($settings->realm);
  if (@$_REQUEST['name']) {
    # get user from user database
    $user = $users->getUser($_REQUEST['name']);
    if (!$user) {
      header('Location: '.url('users.php', array('error'=>'User '.$_REQUEST['name'].' could not be found. ')));
      die;
    }
  }
  if (isset($_REQUEST['delete'])) {
    # delete user
    $users->deleteUser($_REQUEST['name']);    
    $success = $users->save() === TRUE;
    if ($success) {
      header('Location: '.url('users.php', array('msg'=>'User '.$_REQUEST['name'].' was successfully deleted. ')));
    } else {
      header('Location: '.url('users.php', array('error'=>'Error deleting user '.$_REQUEST['name'].'. ')));
    }
    die;
  } else if (isset($_POST['save'])) {
    # add or update user
    if (@$_REQUEST['name']) {
      $username = $_REQUEST['name'];
    } else if (!@$_POST['username']) {
      $errors[] = 'Missing user name!';
      $username = '';
    } else {
      $username = $_POST['username'];
      # check, if already existing
      if ($users->getUser($username)) $errors[] = 'Duplicate user name!'; 
    }
    if (!@$_REQUEST['name'] && !@$_POST['password']) {
      $errors[] = 'You need to specify a password';
    } else if (@$_POST['password'] && $_POST['password'] != @$_POST['password2']) {
      $errors[] = 'The passwords do not match!';
    } else if (@$_REQUEST['name'] && !@$_POST['password']) {
      # check, if user has been authorized for additional repositories
      $reps = array();
      if ($user->repository) foreach ($user->repository as $r) $reps[] = (string) $r->name;
      foreach ($repositories as $repository) {
        if (@$_POST['level_'.$repository] && !in_array($repository, $reps)) {
          $errors[] = 'You must (re)specify a password, if you add repositories.';
          break;
        }
      }
    }
    # check, if user is authorized for at least one repository
    $reps = array();
    foreach ($repositories as $repository) {
      if (@$_POST['level_'.$repository]) $reps[] = $repository;
    }
    if (count($reps) <= 0) {
      $errors[] = 'You must enable at least one repository. ';
    }
    $timezone = @$_POST['timezone'];
    $password = @$_POST['password'];
    $levels = array();
    foreach ($repositories as $repository) {
      if (@$_POST['level_'.$repository]) {
        $levels[$repository] = $_POST['level_'.$repository];
      }
    }
    if (count($errors) == 0) {
      $users->setUser($username, $timezone, $password, $levels);
      $success = $users->save();
      if ($success) {
        header('Location: '.url('users.php', array('name'=>$username,'msg'=>'User '.$username.' was successfully saved. ')));
        die;
      }
      $errors[] = 'Error saving user! ';
    }
  } else if (isset($_REQUEST['name'])) {
    $username = $_REQUEST['name'];
    $timezone = $user ? $user->timezone : TIMEZONE;
    $levels = array();
    if ($user && $user->repository) {
      foreach ($user->repository as $r) $levels[(string) $r->name] = (string) $r->level;
    }
  }
  template_header();
  if (isset($_REQUEST['name'])) {
    $timezones = timezone_identifiers_list();
?>
  <ul class="actions">
    <li><a href="users.php">Back to users</a></li>
  </ul>
  <?php if (count($errors) > 0) { ?>
    <?php foreach ($errors as $error) { ?><div class="error"><?php echo hsc($error); ?></div><?php } ?>
  <?php } ?>
  <h2><?php echo @$_REQUEST['name'] ? 'User '.hsc($username) : 'Add User'; ?></h2>
  <form method="POST" action="<?php echo href('users.php', array('name'=>@$_REQUEST['name'])); ?>">
    <table class="form">
      <?php if (!@$_REQUEST['name']) { ?>
        <tr>
          <td>User name</td>
          <td><input name="username" value="<?php echo hsc($username); ?>"></td>
          <td></td>
        </tr>
      <?php } ?>
      <tr>
        <td>User password</td>
        <td><input type="password" name="password" value=""/></td>
        <td></td>
      </tr>
      <tr>
        <td>User password (repeated)</td>
        <td><input type="password" name="password2" value=""/></td>
        <td></td>
      </tr>
      <tr>
        <td>Time zone</td>
        <td>
          <select name="timezone">
            <?php foreach ($timezones as $tz) echo '<option'.($timezone == $tz ? ' selected="selected"' : '').'>'.$tz."</option>\r\n"; ?>
          </select>
        </td>
        <td></td>
      <tr>
      <tr>
        <td>Repositories</td>
        <td>
          <table>
          <?php foreach ($repositories as $rep) { ?>
            <tr>
              <td><?php echo hsc($rep); ?></td>
              <td>
                <select name="level_<?php echo hsc($rep); ?>">
                  <option value="">(no access)</option>
                  <option value="<?php echo USERLEVEL_VIEW; ?>" <?php if (@$levels[$rep] == USERLEVEL_VIEW) echo 'selected="selected"'; ?>>read only</option>
                  <option value="<?php echo USERLEVEL_EDIT; ?>" <?php if (@$levels[$rep] == USERLEVEL_EDIT) echo 'selected="selected"'; ?>>full access</option>
                </select>
              </td>
            </tr>
          <?php } ?>
          </table>
        </td>
      </tr>
      <tr>
        <td>
          <input type="hidden" name="save" value="save"/>
          <?php if (@$name) { ?><input type="hidden" name="name" value="<?php echo hsc($name); ?>" /><?php } ?>
        </td>
        <td colspan="2"><input type="submit" value="Save"/> or <a href="users.php">Cancel</a></td>
      </tr>
    </table>
  </form>
<?php 
  } else { 
    $allusers = array();
    foreach ($users->getAllUsers() as $u) $allusers[(string) $u->name] = $u;
    ksort($allusers);
?>
  <ul class="actions">
    <li><a href="users.php?name=">Add user</a></li>
  </ul>
  <h2>Users</h2>
  <table class="list">
    <thead>
      <tr><th>User name</th><th>Time zone</th><th>Repositories</th><th></th></tr>
    </thead>
    <tbody>
      <?php if (count($allusers) <= 0) { ?>
        <tr><td colspan="4"><i>No users found</i></td></tr>
      <?php } else foreach ($allusers as $u) { ?>
        <tr>
          <td><a href="<?php echo href('users.php', array('name'=>$u->name)); ?>"><?php echo hsc((string) $u->name); ?></a></td>
          <td><?php echo hsc((string) $u->timezone); ?></td>
          <td>
            <?php
              $reps = array();
              if ($u->repository) {
                foreach ($u->repository as $r) $reps[(string) $r->name] = (string) $r->level;
              }
            ?>
            <?php foreach ($reps as $rname => $rlevel) { ?>
              <p><?php echo hsc((string) $rname); ?> (<?php echo $rlevel == (string) USERLEVEL_EDIT ? 'full access' : 'read only'; ?>)</p>
            <?php } ?>
          </td>
          <td class="link delete">
            <a href="<?php echo href('users.php', array('name'=>(string) $u->name, 'delete'=>'')); ?>" 
                title="Delete user: <?php echo hsc((string) $u->name); ?>">X</a>
          </td>
        </tr>
      <?php } ?>
    </tbody>
  </table>
<?php
  }
  template_footer();