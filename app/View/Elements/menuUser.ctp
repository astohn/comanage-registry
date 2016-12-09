<?php
/*
 * COmanage Registry Secondary Menu Bar
 * Displayed above all pages when logged in
 *
 * Version: $Revision$
 * Date: $Date$
 *
 * Copyright (C) 2012-15 University Corporation for Advanced Internet Development, Inc.
 *
 * Licensed under the Apache License, Version 2.0 (the "License"); you may not use this file except in compliance with
 * the License. You may obtain a copy of the License at
 *
 * http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software distributed under
 * the License is distributed on an "AS IS" BASIS, WITHOUT WARRANTIES OR CONDITIONS OF ANY
 * KIND, either express or implied. See the License for the specific language governing
 * permissions and limitations under the License.
 *
 */
?>

<?php if(isset($vv_my_notifications)): ?>
  <div id="notifications">
    <a href="#" class="topMenu" id="user-notifications">
      <span id="user-notification-count">
         <?php print count($vv_my_notifications); ?>
      </span>
      <?php if(count($vv_my_notifications) > 0): ?>
        <i class="material-icons icon-adjust">notifications_active</i>
      <?php else: ?>
        <i class="material-icons icon-adjust">notifications</i>
      <?php endif?>
      <i class="material-icons">arrow_drop_down</i>
    </a>
    <ul id="notifications-menu" for="user-notifications" class="mdl-menu mdl-js-menu mdl-js-ripple-effect mdl-menu--bottom-right">

      <?php $notificationCount = 0; ?>
      <?php foreach($vv_my_notifications as $n): ?>
        <li class="notification">
          <?php
            $args = array(
              'controller' => 'co_notifications',
              'action'     => 'view',
              $n['CoNotification']['id']
            );

            print '<span class="notification-comment">';
            print $this->Html->link($n['CoNotification']['comment'],$args);
            print '</span>';
            print '<span class="notification-created">';
            print $this->Html->link($this->Time->timeAgoInWords($n['CoNotification']['created']),$args);
            print '</span>';

            $notificationCount++;
            if ($notificationCount > 4) {
              break;
            }
          ?>
        </li>
      <?php endforeach; ?>
      <li id="see-all" class="co-menu-button">
        <a href="/registry/co_notifications/index/recipientcopersonid:<?php print $vv_co_person_id_notifications; ?>/sort:created/direction:desc"
           class="mdl-button mdl-js-button mdl-js-ripple-effect"><?php print _txt('op.see.notifications')?></a>
      </li>
    </ul>
  </div>
<?php endif; ?>

<?php if($this->Session->check('Auth.User.name')): ?>
  <div id="user">
    <a href="#" class="topMenu" id="user-links">
      <span id="user-common-name">
        <?php
          // Print the user's name
          $userCN = generateCn($this->Session->read('Auth.User.name'));
          print $userCN;
        ?>
      </span>
      <i class="material-icons icon-adjust">person</i>
      <i class="material-icons drop-arrow">arrow_drop_down</i>
    </a>
    <ul id="user-links-menu" class="mdl-menu mdl-menu--bottom-right mdl-js-menu mdl-js-ripple-effect" for="user-links">
      <li id="user-links-cn"><?php print $userCN; ?></li>
      <li id="user-links-id"><?php print $this->Session->read('Auth.User.username'); ?></li>
      <!-- Account Dropdown -->
      <?php
        // Profiles
        if(isset($permissions['menu']['coprofile']) && $permissions['menu']['coprofile']) {
          // Link to identity for self service.

          foreach($menuContent['cos'] as $co) {
            // The person must have an Active/GracePeriod status and at least
            // one defined role.

            if(isset($co['co_person']['status'])
               && ($co['co_person']['status'] == StatusEnum::Active
                   || $co['co_person']['status'] == StatusEnum::GracePeriod)
               && !empty($co['co_person']['CoPersonRole'])) {
              print '<li class="mdl-menu__item">';
              $args = array(
                'plugin' => '',
                'controller' => 'co_people',
                'action' => 'canvas',
                $co['co_person_id']
              );
              print $this->Html->link(_txt('me.identity.for', array($co['co_name'])), $args);
              print "</li>";
            }
          }
        }

        // Plugin submenus
        // This rendering is a bit different from how render_plugin_menus() does it...
        foreach(array_keys($menuContent['plugins']) as $plugin) {
          if(isset($menuContent['plugins'][$plugin]['coperson'])) {
            foreach(array_keys($menuContent['plugins'][$plugin]['coperson']) as $label) {
              print '<li class="mdl-menu__item"> 
                       <a href="#">'.$label.'</a>
                       <span class="sf-sub-indicator"> »</span>
                       <ul>';

              foreach($menuContent['cos'] as $co) {
                if(empty($co['co_person_id']))
                  continue;

                $args = $menuContent['plugins'][$plugin]['coperson'][$label];

                $args[] = $co['co_person_id'];
                $args['plugin'] = Inflector::underscore($plugin);

                print "<li>" . $this->Html->link($co['co_name'], $args) . "</li>\n";
              }

              print "</ul></li>";
            }
          }
        }
      ?>
      <li id="logout-in-menu" class="co-menu-button">
        <?php
          $args = array('controller' => 'auth',
            'action'     => 'logout',
            'plugin'     => false);
          print $this->Html->link(_txt('op.logout') . ' <span class="fa fa-sign-out"></span>',
            $args, array('escape'=>false, 'class' => 'mdl-button mdl-js-button mdl-js-ripple-effect'));
        ?>
      </li>
    </ul>
  </div>
<?php endif ?>

<?php if(!isset($noLoginLogout) || !$noLoginLogout) : ?>
  <?php // Print the login/logout buttons
    $buttonClasses = "mdl-button mdl-js-button mdl-button--raised mdl-js-ripple-effect mdl-button--accent";
    if($this->Session->check('Auth.User') != NULL) {
      $args = array('controller' => 'auth',
                    'action'     => 'logout',
                    'plugin'     => false);
      print $this->Html->link(_txt('op.logout') . ' <span class="fa fa-sign-out"></span>',
            $args, array('escape'=>false, 'id' => 'logout', 'class' => $buttonClasses));
    } else {
      $args = array('controller' => 'auth',
                    'action'     => 'login',
                    'plugin'     => false
                   );
      print $this->Html->link(_txt('op.login') . ' <span class="fa fa-sign-in"></span>',
            $args, array('escape'=>false, 'id' => 'login', 'class' => $buttonClasses));
    }
  ?>
<?php endif; ?>

