<?php 
$arr_manu = [
	[
		"label"=> "Dashboard",
		"slug"=> "/",
		"icon" => "edit"
	],
	[
		"label"=> "Leads",
		"slug"=> "leads",
		"icon" => "list"
	]
]
?>

<!-- Main Sidebar -->
        <aside class="main-sidebar col-12 col-md-3 col-lg-2 px-0">
          <div class="main-navbar">
            <nav class="navbar align-items-stretch navbar-light bg-white flex-md-nowrap border-bottom p-0">
              <a class="navbar-brand w-100 mr-0" href="#" style="line-height: 25px;">
                <div class="d-table m-auto">
                  <img id="main-logo" class="d-inline-block align-top mr-1" style="max-width: 25px;" src="<?php echo ADMIN_IMAGE_URL; ?>shards-dashboards-logo.svg" alt="Legal Conflict Check">
                  <span class="d-none d-md-inline ml-1">UAS Portal</span>
                </div>
              </a>
              <a class="toggle-sidebar d-sm-inline d-md-none d-lg-none">
                <i class="material-icons">&#xE5C4;</i>
              </a>
            </nav>
          </div>
          <div class="nav-wrapper">
            <ul class="nav flex-column">
              <?php 
				foreach($arr_manu as $key => $manu){
			  ?>
				  <li class="nav-item">
					<a class="nav-link <?php echo $manu['slug'] == $selected_manu?'active':''  ?>" href="<?php echo base_url($manu['slug']); ?>">
					  <i class="material-icons"><?php echo $manu['icon']; ?></i>
					  <span><?php echo $manu['label']; ?></span>
					</a>
				  </li>
			  <?php } ?>
			  

            </ul>
          </div>
        </aside>
        <!-- End Main Sidebar -->