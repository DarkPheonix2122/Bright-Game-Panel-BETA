<?php

/**
 * LICENSE:
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 *
 * @categories	Games/Entertainment, Systems Administration
 * @package		Bright Game Panel V2
 * @author		warhawk3407 <warhawk3407@gmail.com> @NOSPAM
 * @copyleft	2014
 * @license		GNU General Public License version 3.0 (GPLv3)
 * @version		0.1
 * @link		http://www.bgpanel.net/
 */



class Core_GUI
{

	// This Module Settings
	private $module_title = '';
	private $module_icon = '';

	// Parent Module Settings
	private $parent_module_title = '';
	private $parent_module_href = '';

	// Module Options
	private $empty_navbar 	= FALSE;
	private $no_sidebar		= FALSE;


	/**
	 * Default Constructor
	 *
	 * @param Object $bgp_module
	 * @return void
	 * @access public
	 */
	function __construct( $bgp_module )
	{
		if ( !empty($bgp_module) && is_object($bgp_module) && is_subclass_of($bgp_module, 'BGP_Module') ) {
			$this->module_title = $bgp_module::getModuleSetting( 'title' );
			$this->module_icon = $bgp_module::getModuleSetting( 'icon' );

			// Get parent module properties if this module is a subpage of a module
			if ( is_subclass_of($bgp_module, $bgp_module::$module_definition['class_definition']['@attributes']['classname'] ) ) {

				// Hack
				$parentModule = new $bgp_module::$module_definition['class_definition']['@attributes']['classname'](); // Ugly, but it works ;-)

				$this->parent_module_title = $parentModule::getModuleSetting( 'title' );
				$this->parent_module_href = $parentModule::getModuleSetting( 'href' );

				unset($parentModule);
			}

			// Sets the module options
			if ( !empty($bgp_module::$module_definition['module_options']) ) {

				if ( !empty($bgp_module::getModuleOption( 'empty_navbar' )) ) {

					$this->empty_navbar = boolval( $bgp_module::getModuleOption( 'empty_navbar' ) );
				}

				if ( !empty($bgp_module::getModuleOption( 'no_sidebar' )) ) {

					$this->no_sidebar = boolval( $bgp_module::getModuleOption( 'no_sidebar' ) );
				}
			}
		}
		else {
			trigger_error("Core_GUI -> Missing module !", E_USER_ERROR);
		}
	}



	/**
	 * Get BGP Bootstrap 3 Template Filename
	 *
	 * @param none
	 * @return String
	 * @access public
	 */
	public static function getBS3Template()
	{
		if ( !empty($_SESSION['TEMPLATE']) ) {
			return $_SESSION['TEMPLATE'];
		}
		else {
			switch (Core_AuthService::getSessionPrivilege()) {
				case 'Admin':
					return BGP_ADMIN_TEMPLATE;

				case 'User':
					return BGP_USER_TEMPLATE;
				
				default:
					return 'bootstrap.min.css';
			}
		}
	}



	/**
	 * Display Module Header
	 *
	 * @param none
	 * @return String
	 * @access public
	 */
	public function getHeader()
	{
//------------------------------------------------------------------------------------------------------------+
?>
<!DOCTYPE html>
<html ng-app="bgpApp" lang="<?php

	// Language
	if ( isset($_SESSION['LANG']) ) {
		echo htmlspecialchars( substr($_SESSION['LANG'], 0, 2), ENT_QUOTES );
	} else {
		echo htmlspecialchars( substr(CONF_DEFAULT_LOCALE, 0, 2), ENT_QUOTES );
	}

			?>">
	<head>
		<meta charset="utf-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<meta name="viewport" content="width=device-width, initial-scale=1">

		<!-- Powered By Bright Game Panel -->

		<title><?php

		// Tab Title
		echo htmlspecialchars( $this->module_title . ' - ' . BGP_PANEL_NAME, ENT_QUOTES );

		?></title>

		<base href="<?php echo BASE_URL; ?>">

		<!-- Javascript -->
			<script src="./gui/angularjs/js/angular.min.js"></script>
			<script src="./gui/jquery/js/jquery-2.1.1.min.js"></script>
			<script src="./gui/bootstrap3/js/bootstrap.min.js"></script>
			<!-- Metis Menu Plugin -->
    		<script src="./gui/metisMenu/js/metisMenu.min.js"></script>
			<!-- SB Admin 2 -->
			<script src="./gui/bootstrap3/js/sb-admin-2.js"></script>
		<!-- Style -->
			<!-- Bootstrap 3 -->
			<link href="./gui/bootstrap3/css/<?php echo htmlspecialchars( Core_GUI::getBS3Template(), ENT_QUOTES ); ?>" rel="stylesheet">
			<!-- MetisMenu -->
			<link href="./gui/metisMenu/css/metisMenu.min.css" rel="stylesheet">
			<!-- SB Admin 2 -->
			<link href="./gui/bootstrap3/css/dashboard.css" rel="stylesheet">
			<!-- Font Awesome 4 -->
			<link href="./gui/font-awesome/css/font-awesome.min.css" rel="stylesheet">
		<!-- Favicon -->
			<link rel="icon" href="./gui/img/favicon.ico">
		<!-- HTML5 shim and Respond.js IE8 support of HTML5 elements and media queries -->
		<!--[if lt IE 9]>
			<script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
			<script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
		<![endif]-->
	</head>

	<body ng-controller="bgpController">
	<div id="wrapper">

		<!-- NAVIGATION -->
		<nav class="navbar navbar-default navbar-static-top" role="navigation">
<?php
//------------------------------------------------------------------------------------------------------------+

		// Display Navigation Bar
		echo $this->getNavBar();

//------------------------------------------------------------------------------------------------------------+
?>

<?php
//------------------------------------------------------------------------------------------------------------+

		// Display Sidebar
		if (!$this->no_sidebar) {
			echo $this->getSideBar();
		}

//------------------------------------------------------------------------------------------------------------+
?>
		</nav>
		<!-- END: NAVIGATION -->

		<!-- Page Content -->
		<div id="page-wrapper">
			<div class="row">
				<!-- MAIN -->
				<div class="col-lg-12">
<?php
//------------------------------------------------------------------------------------------------------------+

		// Page Header
		// Title

		if (!empty($this->parent_module_title)) {
//------------------------------------------------------------------------------------------------------------+
?>
					<h1 class="page-header">
						<i class="<?php echo htmlspecialchars( $this->module_icon, ENT_QUOTES ); ?>"></i>&nbsp;
						<?php echo htmlspecialchars( $this->parent_module_title, ENT_QUOTES ); ?>&nbsp;
						<i class="fa fa-angle-right"></i>&nbsp;
						<small><?php echo htmlspecialchars( $this->module_title, ENT_QUOTES ); ?></small>
					</h1>
<?php
//------------------------------------------------------------------------------------------------------------+
		}
		else {
//------------------------------------------------------------------------------------------------------------+
?>
					<h1 class="page-header"><i class="<?php echo htmlspecialchars( $this->module_icon, ENT_QUOTES ); ?>"></i>&nbsp;<?php echo htmlspecialchars( $this->module_title, ENT_QUOTES ); ?></h1>
<?php
//------------------------------------------------------------------------------------------------------------+
		}

//------------------------------------------------------------------------------------------------------------+
?>
					<!-- ALERTS -->
					<div id="message" class="alert alert-dismissible" role="alert" ng-show="msg" ng-class="'alert-' + msgType">
						<button type="button" class="close" data-dismiss="alert"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
						<strong ng-bind="msg"></strong>
					</div>
					<!-- END: ALERTS -->

<?php
//------------------------------------------------------------------------------------------------------------+
	}



	/**
	 * Display Module Navigation Bar
	 *
	 * @param none
	 * @return String
	 * @access public
	 */
	public function getNavBar()
	{
//------------------------------------------------------------------------------------------------------------+
?>
				<!-- TOP NAVBAR -->
				<div class="navbar-header">
					<button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target=".navbar-collapse">
						<span class="sr-only">Toggle navigation</span>
						<span class="icon-bar"></span>
						<span class="icon-bar"></span>
						<span class="icon-bar"></span>
					</button>
					<a class="navbar-brand" href="#">BrightGamePanel V2</a>
				</div>

				<!-- Breadcrumbs -->
				<div class="nav navbar-left">
<?php

		if (!$this->empty_navbar)
		{
//------------------------------------------------------------------------------------------------------------+
?>
					<ol class="navbar-breadcrumbs">
						<li class="active"><span class="glyphicon glyphicon-home"></span>&nbsp;Home</li>
<?php
//------------------------------------------------------------------------------------------------------------+

			if (!empty($this->parent_module_title)) {
//------------------------------------------------------------------------------------------------------------+
?>
						<li><a href="<?php echo $this->parent_module_href; ?>"><?php echo $this->parent_module_title; ?></a></li>
						<li class="active"><?php echo $this->module_title; ?></li>
<?php
//------------------------------------------------------------------------------------------------------------+
			}
			else {
//------------------------------------------------------------------------------------------------------------+
?>
						<li class="active"><?php echo $this->module_title; ?></li>
<?php
//------------------------------------------------------------------------------------------------------------+
			}

//------------------------------------------------------------------------------------------------------------+
?>
					</ol>
<?php
//------------------------------------------------------------------------------------------------------------+
		}

?>
				</div>
				<!-- END: Breadcrumbs -->

				<ul class="nav navbar-top-links navbar-right">
<?php

		if (!$this->empty_navbar)
		{
//------------------------------------------------------------------------------------------------------------+
?>
					<li class="dropdown">
						<a class="dropdown-toggle" data-toggle="dropdown" href="#">
							<i class="fa fa-bell fa-fw"></i>  <i class="fa fa-caret-down"></i>
						</a>
						<ul class="dropdown-menu dropdown-alerts" role="menu">
							<li>
								<a href="#">
									<div>
										<i class="fa fa-comment fa-fw"></i> New Comment
										<span class="pull-right text-muted small">4 minutes ago</span>
									</div>
								</a>
							</li>
							<li class="divider"></li>
							<li>
								<a href="#">
									<div>
										<i class="fa fa-twitter fa-fw"></i> 3 New Followers
										<span class="pull-right text-muted small">12 minutes ago</span>
									</div>
								</a>
							</li>
							<li class="divider"></li>
							<li>
								<a class="text-center" href="#">
									<strong>See All Alerts</strong>
									<i class="fa fa-angle-right"></i>
								</a>
							</li>
						</ul>
						<!-- /.dropdown-alerts -->
					</li>
					<li class="dropdown">
						<a class="dropdown-toggle" data-toggle="dropdown" href="#">
							<i class="fa fa-user fa-fw"></i>  <i class="fa fa-caret-down"></i>
						</a>
						<ul class="dropdown-menu dropdown-user" role="menu">
							<li role="presentation" class="dropdown-header"><?php
								echo htmlspecialchars(
									$_SESSION['INFORMATION']['firstname'] .
									' ' .
									$_SESSION['INFORMATION']['lastname'] .
									' @' .
									$_SESSION['USERNAME']
									, ENT_QUOTES );
							?></li>
							<li>
								<a href="./myaccount"><i class="fa fa-gear fa-fw"></i>&nbsp;<?php echo T_('Settings'); ?></a>
							</li>
						</ul>
						<!-- /.dropdown-user -->
					</li>
	                <!-- /.dropdown -->
	                <li>
	                	<a href="./logout"><i class="fa fa-sign-out fa-fw"></i></a>
	                </li>
<?php
//------------------------------------------------------------------------------------------------------------+
		}

?>
				</ul>
				<!-- END: TOP NAVBAR -->
<?php
//------------------------------------------------------------------------------------------------------------+
	}



	/**
	 * Display Module Sidebar
	 *
	 * @param none
	 * @return String
	 * @access public
	 */
	public function getSideBar()
	{
		$items = $this->getSideBarItems();

//------------------------------------------------------------------------------------------------------------+
?>
				<!-- SIDEBAR -->
				<div class="navbar-default sidebar" role="navigation">
					<div class="sidebar-nav navbar-collapse">
						<ul class="nav" id="side-menu">
<?php
//------------------------------------------------------------------------------------------------------------+

		$i = 0; // First index
		$last_index = 99; // Last key

		while ( $i <= $last_index )
		{
			if (!array_key_exists($i, $items)) {
				$i++;
				continue;
			}

			$txt 		= key($items[$i]);

			$href 		= $items[$i][$txt]['href'];
			$icon 		= $items[$i][$txt]['icon'];
			$sub_menu 	= $items[$i][$txt]['sub_menu'];

			// Translate
			$txt_t 		= T_( $txt );

			if (empty($sub_menu))
			{
//------------------------------------------------------------------------------------------------------------+
?>
							<li>
								<a <?php if ($this->module_title == $txt) echo 'class="active"'; ?> href="<?php echo $href; ?>"><i class="<?php echo $icon; ?>"></i>&nbsp;<?php echo $txt_t; ?></a>
<?php
//------------------------------------------------------------------------------------------------------------+
			}
			else
			{
//------------------------------------------------------------------------------------------------------------+
?>
							<li id="<?php echo $txt; ?>" class="">
								<a href="<?php echo $href; ?>"><i class="<?php echo $icon; ?>"></i>&nbsp;<?php echo $txt_t; ?><i class="fa arrow"></i></a>
								<ul class="nav nav-second-level">
<?php
//------------------------------------------------------------------------------------------------------------+

				$j = 0;
				foreach ($sub_menu as $menu_key => $menu)
				{

					// Add separator after the first iteration
					if ($j != 0)
					{
//------------------------------------------------------------------------------------------------------------+
?>
									<li class="divider"></li>
<?php
//------------------------------------------------------------------------------------------------------------+
					}

					// Format
					$menu_key = ucfirst($menu_key);

					// Translate
					$menu_key = T_( $menu_key );

//------------------------------------------------------------------------------------------------------------+
?>
									<li class="sidebar-header"><?php echo $menu_key; ?></li>
<?php
//------------------------------------------------------------------------------------------------------------+

					foreach ($menu as $menu_sub_key => $menu_sub_menu)
					{

						// Format
						$menu_sub_menu['txt'] = ucwords( str_replace( '-', ' ', $menu_sub_key ) );

						// Translate
						$menu_sub_menu['txt'] = T_( $menu_sub_menu['txt'] );

//------------------------------------------------------------------------------------------------------------+
?>
									<li><a <?php if ($this->module_title == $menu_sub_menu['txt']) echo 'class="active"'; ?> href="<?php echo $menu_sub_menu['href']; ?>"><i class="<?php echo $menu_sub_menu['icon']; ?>"></i>&nbsp;<?php echo $menu_sub_menu['txt']; ?></a></li>
<?php
//------------------------------------------------------------------------------------------------------------+

						// Toggle the parent menu item in JS
						if ($this->module_title == $menu_sub_menu['txt'])
						{
//------------------------------------------------------------------------------------------------------------+
?>
									<script>
										$('#<?php echo $txt; ?>').addClass('active');
									</script>
<?php
//------------------------------------------------------------------------------------------------------------+
						}
					}

					$j++;
				}

//------------------------------------------------------------------------------------------------------------+
?>
								</ul>
								<!-- /.nav-second-level -->
<?php
//------------------------------------------------------------------------------------------------------------+
			}

//------------------------------------------------------------------------------------------------------------+
?>
							</li>
<?php
//------------------------------------------------------------------------------------------------------------+

			$i++;
		}

//------------------------------------------------------------------------------------------------------------+
?>
						</ul>
					</div>
					<!-- /.sidebar-collapse -->
				</div>
				<!-- END: SIDEBAR -->

<?php
//------------------------------------------------------------------------------------------------------------+
	}



	/**
	 * Get Sidebar Items
	 *
	 * @param none
	 * @return array
	 * @access public
	 */
	private function getSideBarItems()
	{
		$privilege = Core_AuthService::getSessionPrivilege();
		$manifestFiles = array();

		// Read all "sidebar.gui.xml" files under the "app/modules" directory
		$handle = opendir( MODS_DIR );

		if ($handle) {

			// Foreach modules
			while (false !== ($entry = readdir($handle))) {

				// Dump specific directories
				if ($entry != "." && $entry != "..") {

					// Analyze module name
					$parts = explode('.', $entry);

					if (!empty( $parts[1] )) {
						$role = $parts[0];
						$module = $parts[1];
					}
					else {
						$role = NULL;
						$module = $parts[0];
					}

					// Case: "admin.module" OR "user.module"
					if (!empty($role) && $privilege == ucfirst($role)) {

						// Get the manifest
						$manifest = MODS_DIR . '/' . $role . '.' . $module . '/sidebar.gui.xml';

						if (is_file( $manifest )) {
							$manifestFiles[] = simplexml_load_file( $manifest ); // Store the object
						}
					}

					// Case: "module"
					else {

						// Get the manifest
						$manifest = MODS_DIR . '/' . $module . '/sidebar.gui.xml';

						if (is_file( $manifest )) {
							$manifestFiles[] = simplexml_load_file( $manifest );
						}
					}
				}
			}

			closedir($handle);
		}

		if (!empty($manifestFiles)) {

			$items = array();

			// XML Object to Array

			foreach( $manifestFiles as $manifest ) {

				$txt  = (string)$manifest->{'module_sidebar'}->txt;
				$rank = (int)$manifest->{'module_sidebar'}->rank;

				$item[$txt]['rank'] = $rank;
				$item[$txt]['href'] = (string)$manifest->{'module_sidebar'}->href;
				$item[$txt]['icon'] = (string)$manifest->{'module_sidebar'}->icon;

				// Processing sub-menu if any

				if ( !empty($manifest->{'module_sidebar'}->{'sub_menu'}) ) {
					
					$sub_menu = (array)$manifest->{'module_sidebar'}->{'sub_menu'};

					foreach ($sub_menu as $sub_menu_key => $sub_menu_item) {

						$sub_menu_item = (array)$sub_menu_item;

						foreach ($sub_menu_item as $sub_menu_item_href => $sub_menu_item_link) {

							$sub_menu_item_href = (string)$sub_menu_item_href;

							// Push to array

							$item[$txt]['sub_menu'][$sub_menu_key][$sub_menu_item_href]['href'] = (string)$sub_menu_item_link->{'href'};
							$item[$txt]['sub_menu'][$sub_menu_key][$sub_menu_item_href]['icon'] = (string)$sub_menu_item_link->{'icon'};
						}
					}
				}
				else {

					$item[$txt]['sub_menu'] = array();
				}

				$items = array_merge($items, $item); // Push
			}

			$sideBarItems = array();

			// Sort Array

			foreach ($items as $key => $item) {

				$rank = $item['rank'];
				unset($item['rank']);

				// Free key
				if (!isset($sideBarItems[$rank])) {

					$sideBarItems[$rank][$key] = $item; // Push
				}
				// Key not available
				else {

					$i = 1;
					while ( isset($sideBarItems[ $rank + $i ]) ) {
						$i++;
					}

					$sideBarItems[ $rank + $i ][$key] = $item; // Push
				}
			}

			// Return Array
			return $sideBarItems;
		}

		return array();
	}



	/**
	 * Display Module Footer
	 *
	 * @param none
	 * @return String
	 * @access public
	 */
	public function getFooter()
	{
//------------------------------------------------------------------------------------------------------------+
?>
					<hr>

					<!-- FOOTER -->
					<footer>
						<div class="pull-left">
							Copyleft - 2014. Released Under <a href="http://www.gnu.org/licenses/gpl.html" target="_blank">GPLv3</a>.<br />
							All images are copyrighted by their respective owners.
						</div>
						<div class="pull-right" style="text-align: right;">
							<a href="http://www.bgpanel.net/" target="_blank">Bright Game Panel</a> V2<br />
							Built with <a href="http://getbootstrap.com/" target="_blank">Bootstrap 3</a>.
						</div>
					</footer>
					<!-- END: FOOTER -->
				</div>
				<!-- END: MAIN -->
			</div>
			<!-- END: ROW -->
		</div>
		<!-- /#page-wrapper -->
	</div>
	<!-- /#wrapper -->

	<!-- Powered By Bright Game Panel -->

	</body>
</html>
<?php
//------------------------------------------------------------------------------------------------------------+
	}

}
