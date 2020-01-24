<!-- SIDE BAR MENU -->

<ul class="rd-navbar-nav">
<li class="rd-navbar-nav-item">
  <div class="rd-navbar-title"><span class="rd-navbar-title-icon"><span class="fa-ellipsis-h"></span></span><span class="rd-navbar-title-text">Menu</span></div>
</li>
<li class="rd-navbar-nav-item"><a class="rd-navbar-link" href="<?php
if($this->session->user["child"] or getenv("SUPER_USER") == intval($this->session->user['zohoId'])):
   echo "portal";
else:
  echo "entity/".$this->session->user["zohoId"];
endif;?>"><span class="rd-navbar-icon fa-home"></span><span class="rd-navbar-link-text">Dashboard</span></a> </li>
<li class="rd-navbar-nav-item">
  <div class="rd-navbar-title"><span class="rd-navbar-title-icon"><span class="fa-ellipsis-h"></span></span><span class="rd-navbar-title-text">company profile</span></div>
</li>
<?php if($this->session->user["child"]):?>
<li class="rd-navbar-dropdown-item"><a class="rd-navbar-link" href="entity/form"><span class="rd-navbar-icon fa-user"></span>Add Entity</a> </li>
<?php endif; ?>
<!--<li class="rd-navbar-nav-item"><a class="rd-navbar-link" href="#"><span class="rd-navbar-icon fa-calendar"></span><span class="rd-navbar-link-text">Entity</span></a> 
  <ul class="rd-navbar-dropdown">
    <li class="rd-navbar-dropdown-item"><a class="rd-navbar-link" href="entity/form"><span class="rd-navbar-icon fa-user"></span>Add Entity</a> </li>
  </ul>
</li>-->
<li class="rd-navbar-nav-item"><a class="rd-navbar-link" href="/contacts"><span class="rd-navbar-icon fa-list-alt"></span><span class="rd-navbar-link-text">Contacts List</span></a> </li>
<li class="rd-navbar-nav-item"><a class="rd-navbar-link" href="/attachments"><span class="rd-navbar-icon fa-file-text"></span><span class="rd-navbar-link-text">Attachments List</span></a> </li>
<li class="rd-navbar-nav-item"><a class="rd-navbar-link" href="/agents"><span class="rd-navbar-icon fa-map-marker"></span><span class="rd-navbar-link-text">Registered Addresses</span></a> </li>
<?php if($this->session->user["isAdmin"]): ?>
  <li class="rd-navbar-nav-item"><a class="rd-navbar-link" href="<?= base_url('admin') ?>"><span class="rd-navbar-icon fa-map-marker"></span><span class="rd-navbar-link-text">Administrator</span></a> </li>
<?php endif; ?>