<!DOCTYPE html>
<html class="rd-navbar-sidebar-active" lang="en">
<head>
<title>Company Profile</title>
<base href="/" />
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, height=device-height, initial-scale=1.0">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta property="og:title" content="United Agent Services Portal">
<meta property="og:description" content="Customer portal for United Agent Services">
<link rel="icon" href="images/favicon.ico" type="image/x-icon">
<link rel="stylesheet" href="components/base/base.css">
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/v/dt/dt-1.10.20/datatables.min.css"/>
<link rel="stylesheet" href="components/base/custom.css">
<link rel="stylesheet" href="components/dropdown/dropdown.css">
<script src="components/base/script.js"></script>
<script src="components/base/jquery-3.4.1.min.js"></script>
<script src="components/base/moment.min.js"></script>
<<<<<<< HEAD
<script src="components/datepicker/bootstrap-datepicker.js"></script>
=======
>>>>>>> 40dc85a65bed48a728895c7c6526ddf2ef25a7e5
<script type="text/javascript" src="https://cdn.datatables.net/v/dt/dt-1.10.20/datatables.min.js"></script>
</head>
	
<body>
<div class="preloader" id="contactLoader"><img src="images/loader.svg" alt=""></div>
<div class="page">
	
<header class="section page-header">
<!--RD Navbar--><div class="rd-navbar-wrap">
<nav class="rd-navbar">
<div class="rd-navbar-panel">
  <div class="rd-navbar-panel-cell">
    <button class="btn btn-navbar-panel rd-navbar-sidebar-toggle" data-navigation-switch="data-navigation-switch"><span class="fa-bars"></span></button>
  </div>
  <div class="rd-navbar-panel-cell rd-navbar-panel-cell-fullscreen">
    <button class="btn btn-navbar-panel" data-fullscreen="html"><span class="fa-arrows-alt"></span></button>
  </div>
  <div class="rd-navbar-panel-cell rd-navbar-panel-cell-search">
    <div class="rd-navbar-sidebar-search">
      <input class="form-control" type="text" placeholder="Search"/>
    </div>
  </div>
  <div class="rd-navbar-panel-center"></div>
  <div class="rd-navbar-panel-cell">
   <!-- <button class="btn btn-navbar-panel" data-multi-switch='{"targets":"#subpanel-chat","scope":"#subpanel-chat","isolate":"[data-multi-switch]"}'><span class="fa-comments"></span><span class="badge badge-danger">...</span></button>-->   <div class="rd-navbar-subpanel" id="subpanel-chat">
      <div class="panel">
        <div class="panel-header">
          <div class="panel-title"><span class="panel-icon fa-pencil"></span> <span>Chat widget</span> </div>
        </div>
        <div class="panel-body scroller scroller-vertical">
          <div class="media media-cloud group-10">
            <div class="media-item"><img src="/images/users/user-02-50x50.jpg" width="50" height="50" alt=""/></div>
            <div class="media-body">
              <h4 class="media-heading offline">Sara Marshall <small>- 12:30am</small> </h4>
              <div class="media-text">Cras sit amet nibh libero, in gravida nulla. Nulla vel metus scelerisque ante sollicitudin.</div>
            </div>
          </div>
          <div class="media media-cloud flex-row-reverse group-10">
            <div class="media-item"><img src="/images/users/user-03-50x50.jpg" width="50" height="50" alt=""/></div>
            <div class="media-body media-body-right-caret">
              <h4 class="media-heading online">Tom Jorgensen <small>- 12:30am</small> </h4>
              <div class="media-text">Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt.</div>
            </div>
          </div>
          <div class="media media-cloud group-10">
            <div class="media-item"><img src="/images/users/user-02-50x50.jpg" width="50" height="50" alt=""/></div>
            <div class="media-body">
              <h4 class="media-heading offline">Sara Marshall <small>- 12:30am</small> </h4>
              <div class="media-text">Cras sit amet nibh libero, in gravida nulla. Nulla vel metus scelerisque ante sollicitudin.</div>
            </div>
          </div>
          <div class="media media-cloud flex-row-reverse group-10">
            <div class="media-item"><img src="/images/users/user-03-50x50.jpg" width="50" height="50" alt=""/></div>
            <div class="media-body media-body-right-caret">
              <h4 class="media-heading online">Tom Jorgensen <small>- 12:30am</small> </h4>
              <div class="media-text">Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt.</div>
            </div>
          </div>
        </div>
        <div class="panel-footer">
          <div class="input-group form-group">
            <input class="form-control" type="text" placeholder="Enter your message here"/>
            <div class="input-group-append">
              <button class="btn btn-primary">Send </button>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

<!-- PANEL FOR NOTIFICATION -->
<!--<div class="rd-navbar-panel-cell">
<button class="btn btn-navbar-panel" data-multi-switch='{"targets":"#subpanel-notifications","scope":"#subpanel-notifications","isolate":"[data-multi-switch]"}'><span class="fa-bell"></span><span class="badge badge-warning">2</span></button>
<div class="rd-navbar-subpanel" id="subpanel-notifications">
      <div class="panel">
      <div class="panel-header">
      <div class="panel-title"><span class="panel-icon fa-bell"></span><span>Notifications</span></div>
      </div>
      <div class="panel-body p-2 scroller scroller-vertical">
      <div class="group-5">
      <div class="alert alert-dismissible alert-primary alert-sm" role="alert"><span class="alert-icon fa-telegram"></span><span>Message</span>
      <button class="close" type="button" data-dismiss="alert" aria-label="Close"><span class="fa-close" aria-hidden="true"></span></button>
      </div>
      <div class="alert alert-dismissible alert-warning alert-sm" role="alert"><span class="alert-icon fa-warning"></span><span>Warning</span>
      <button class="close" type="button" data-dismiss="alert" aria-label="Close"><span class="fa-close" aria-hidden="true"></span></button>
      </div>
      <div class="alert alert-dismissible alert-danger alert-sm" role="alert"><span class="alert-icon fa-remove"></span><span>Error</span>
      <button class="close" type="button" data-dismiss="alert" aria-label="Close"><span class="fa-close" aria-hidden="true"></span></button>
      </div>
      <div class="alert alert-dismissible alert-success alert-sm" role="alert"><span class="alert-icon fa-thumbs-up"></span><span>Success</span>
      <button class="close" type="button" data-dismiss="alert" aria-label="Close"><span class="fa-close" aria-hidden="true"></span></button>
      </div>
      </div>
      </div>
      <div class="panel-footer p-2"><a class="btn btn-primary btn-sm" href="alerts.html">See more</a></div>
      </div>
    </div>
  </div> -->

<!-- PANEL FOR COUNTRY FLAG -->
  <!-- <div class="rd-navbar-panel-cell">
    <button class="btn btn-navbar-panel dropdown-toggle" data-toggle="dropdown"><span class="fa-flag"></span></button>
    <div class="dropdown-menu">
      <a class="dropdown-item d-flex align-items-center" href="#"><img class="pr-1" src="/images/image-07-14x11.jpg" width="14" height="11" alt=""/><span>France</span></a>
      <a class="dropdown-item d-flex align-items-center" href="#"><img class="pr-1" src="images/image-08-14x11.jpg" width="14" height="11" alt=""/><span>Germany</span></a>
      <a class="dropdown-item d-flex align-items-center" href="#"><img class="pr-1" src="images/image-10-14x11.jpg" width="14" height="11" alt=""/><span>Turkey</span></a>
      <a class="dropdown-item d-flex align-items-center" href="#"><img class="pr-1" src="images/image-11-14x11.jpg" width="14" height="11" alt=""/><span>USA</span></a>
    </div>
  </div> -->

<!-- PANEL FOR PROFILING DROPDOWN -->  
  <div class="rd-navbar-panel-cell rd-navbar-panel-cell-user">
    <div class="btn btn-navbar-panel" data-multi-switch='{"targets":"#subpanel-user","scope":"#subpanel-user","isolate":"[data-multi-switch]"}'>
      <div class="media align-items-center"><!--<img class="rounded-circle" src="/images/users/jv_50x50.jpg" width="30" height="30" alt=""/>-->
        <div class="media-body ml-2">
          <p><?=$this->session->user["firstName"]." ".$this->session->user["lastName"];?></p>
        </div>
      </div>
    </div>
    <div class="rd-navbar-subpanel" id="subpanel-user">
      <div class="panel">
        <div class="panel-header">
          <div class="group-5 d-flex flex-wrap align-items-center"><img class="rounded mr-2" src="<?php echo $grav_url; ?>" width="50" height="50" alt=""/>
            <div class="panel-title"><?=$this->session->user["firstName"]." ".$this->session->user["lastName"];?></div>
          </div>
        </div>
        <div class="panel-body p-0 scroller scroller-vertical">
          <div class="list-group">

            <a class="list-group-item rounded-0" href="/entity/<?=$this->session->user['zohoId'];?>">
            <div class="media align-items-center">
              <div class="pr-2"><span class="fa-user"></span></div>
              <div class="media-body">
                <h5>My Profile</h5>
              </div>
            </div>
            </a>
            <!-- <a class="list-group-item rounded-0" href="#">
            <div class="media align-items-center">
              <div class="pr-2"><span class="fa-envelope-o"></span></div>
              <div class="media-body">
                <h5>My Messages</h5>
              </div>
              <div class="badge badge-warning">12</div>
            </div>
            </a>
            <a class="list-group-item rounded-0" href="#">
            <div class="media align-items-center">
              <div class="pr-2"><span class="fa-rocket"></span></div>
              <div class="media-body">
                <h5>My Activities</h5>
              </div>
            </div>
            </a>
            <a class="list-group-item rounded-0" href="#">
            <div class="media align-items-center">
              <div class="pr-2"><span class="fa-desktop"></span></div>
              <div class="media-body">
                <h5>My Tasks</h5>
              </div>
              <div class="badge badge-success">05</div>
            </div>
            </a>
            <a class="list-group-item rounded-0" href="#">
            <div class="media align-items-center">
              <div class="pr-2"><span class="fa-line-chart"></span></div>
              <div class="media-body">
                <h5>Billing</h5>
              </div>
            </div>
            </a> -->
          </div>
        </div>
        <div class="panel-footer p-2">
          <div class="d-flex align-items-center justify-content-between">
            <a class="btn btn-danger btn-sm" href="index.php/logout">Sign Out</a>
            <!-- <a class="btn btn-sm btn-light" href="#">Upgrade Plan</a> -->
          </div>
        </div>
      </div>
    </div>
  </div>
  <div class="rd-navbar-panel-cell">
    <button class="btn btn-navbar-panel" data-multi-switch="{"targets":".sidebar","scope","isolate":"[data-multi-switch]"}">
    <span class="fa-sliders"></span>
    </button>
  </div>
</div>
<div class="rd-navbar-sidebar scroller scroller-vertical">
<div class="rd-navbar-sidebar-panel">
  <div>
    <div class="group-15">
      <div class="rd-navbar-brand"><a href="portal"><img src="/images/AgentAdmin_Logo.png" alt=""/></a></div>
      <div class="rd-navbar-sidebar-search">
        <input class="form-control" type="text" placeholder="Search"/>
      </div>
    </div>
  </div>
  <button class="btn btn-navbar-panel rd-navbar-sidebar-toggle" data-navigation-switch="data-navigation-switch"><span class="fa-bars"></span></button>
</div>
<?php $this->load->view("sidebar"); ?>

<!-- COMMENTS THIS SECTION -->
<!-- <li class="rd-navbar-nav-item"><a class="rd-navbar-link" href="calendar.html"><span class="rd-navbar-icon fa-calendar"></span><span class="rd-navbar-link-text">Calendar</span></a> </li>
<li class="rd-navbar-nav-item"><a class="rd-navbar-link" href="documents.html"><span class="rd-navbar-icon fa-envelope-o"></span><span class="rd-navbar-link-text">Documents</span></a> </li>
<li class="rd-navbar-nav-item"><a class="rd-navbar-link" href="#"><span class="rd-navbar-icon fa-address-book-o"></span><span class="rd-navbar-link-text">Directory</span></a>
  <ul class="rd-navbar-dropdown">
    <li class="rd-navbar-dropdown-item"><a class="rd-navbar-link" href="director.html"><span class="rd-navbar-icon fa-user"></span>Directors</a> </li>
    <li class="rd-navbar-dropdown-item"><a class="rd-navbar-link" href="officer.html"><span class="rd-navbar-icon fa-user-o
						"></span>Officers</a> </li>
    <li class="rd-navbar-dropdown-item"><a class="rd-navbar-link" href="shareholder.html"><span class="rd-navbar-icon fa-group"></span>Shareholders</a> </li>
  </ul>
</li>
<li class="rd-navbar-nav-item"><a class="rd-navbar-link" href="#"><span class="rd-navbar-icon fa-file-o"></span><span class="rd-navbar-link-text">Corporate Minutes</span></a> </li> -->
</header>