<?php

/*
Plugin Name: LeetSubdomain
Plugin URI: https://gyanlog.in
Description: This plugin allow you create subdomain without using Wordpress Multisite ! Setup your main pages as subdomains in one click !
Author: Piyush Joshi
Version: 1.0
Author URI: http://gyanlog.in
*/

function pjchecked( $value, $current) {
	if ( ! is_array( $value ) ) return;
	if ( in_array( $current, $value ) ) {
		echo 'checked="checked"';
	}
}

class pjleetdotcom_options_leet_subdomain {

	function __construct() {
		add_action( 'admin_menu', array( $this, 'admin_menu' ) );
	}

	function admin_menu() {
		add_options_page(
			'Subdomains Setup',
			'Leet Subdomain',
			'manage_options',
			'setup-subdomain',
			array(
				$this,
				'pjleetdotcom_settings_page'
			)
		);
	}

	function  pjleetdotcom_settings_page() {
		?>
		<div class="wrap">
			<h1><span class="dashicons dashicons-admin-tools"></span> Setup subdomain for your website</h1>
			
			<?php 
				$pass = true;
				$url = home_url();
				$U = parse_url( $url );
				echo '<p><em>';
				if ( strpos( $U['host'], 'www' ) === false ) {
					$pass = false;
					echo '<span class="dashicons dashicons-no-alt"></span> You must set your homepage to www like <b>www.'. $U['host'] . '</b> befor turn on Super Subdomain in Setting Menu, "www." avoid error for plugin to work';
				} else {
					echo '<span class="dashicons dashicons-yes"></span> Your home page is <b>'. $U['host'] . '</b>';
				}
				echo '</em></p>';
				
				echo '<p>';
				
				
				if( isset($_POST['submit'])) {
					@update_option('subdomain_function', ($_POST['subdomain_function']) );
				}
				if ( ! $pass) {
					delete_option('subdomain_function');
				}
				
				$sub_func = get_option('subdomain_function');
			?>
			<form action="" method="post" name="leet_subdomain_setup">
			<table class="form-table">
				<tbody>
				<tr>
					<th scope="row"><label for="subdomain_function">Activate/Deactivate Subdomain for Page</label></th>
					<td>
						
						<label><input <?php pjchecked( $sub_func, 'page' ); ?> type="checkbox" name="subdomain_function[]" value="page"> Page <small style="color: #999"> - aboutus.domain.com</small></label><br>
					</td>
					
				</tr>
				</tbody>
			</table>
			
				
			<p class="submit"><input type="submit" value="<?php _e('Save') ?>" class="button button-primary" id="submit" name="submit"></p>
			</form>
			<p><center><b>Need any help Regarding this plugin Dm me on <a href="https://Instagram.com/piyush_133t">Instagram</a>
			</b></center></p></b>
		</div>
		<?php
	}
}

new pjleetdotcom_options_leet_subdomain;

Class Start_init_subdomain {
	
	var $subdomain; 
	var $slug; 
	var $type; 
	var $pagedata;
	
	
	var $root;
	var $url;
	
	var $subdomain_setting;
	
	function __construct() {
	
		
		if (function_exists('create_initial_taxonomies')) {
			create_initial_taxonomies();
		}
		$this->url = home_url();
			$U = parse_url($this->url);
			$V = $U['host'];
		$this->root = str_replace('www.', '', $V);
		
		$this->subdomain_setting = $sub_func = get_option('subdomain_function');
		if ( ! empty ( $sub_func ) ) {
			$this->Inital_subdomain();
			$this->addActions();
			$this->addFilters();
		}
	}
	
	
	function Inital_subdomain() {
		$sub = $_SERVER['HTTP_HOST'];
		$sl = $_SERVER['REQUEST_URI'];
		$findsub = explode('.', $sub);
		$this->subdomain = $subdomain = $findsub[0];
		$subdomain_setting = $this->subdomain_setting;
		$slug = max( explode('/', $sl));
		$continue = true;
		
		if ( $subdomain == 'www' || count($findsub)< 3 ) {
			$this->type = 0;
		} else {
			
			
			if ( in_array('page', $subdomain_setting ) && $continue == true ) {
				
				if ( get_page_by_path($subdomain) ) {
				
					$this->type = 4;
					$this->slug = $this->subdomain = $subdomain;
					$continue = false;
				}
			}
			
			if ( $continue == true ) {
				/* Opsss nothing found ! I create a 404 error link */
				$url = ( home_url('/opps_404_error') );
				// 301 Moved Permanently
				header("Location: $url",TRUE,301);
				die('x-1');
			}
		
		} 
	}


	
	function this_uri() {
		return 'http://'. $this->subdomain .'.'. $this->root . '/';
	}
	function leet_subdomain_getUrlPath($url) {
		$parsed_url = parse_url($url);
		
		if(isset($parsed_url['path'])) {
		$path = ( (substr($parsed_url['path'], 0, 1) == '/') ? substr($parsed_url['path'], 1) : $parsed_url['path'] );
		} else {
			$path = '';
		}
		$path .= ( isset($parsed_url['query']) ? '?'.$parsed_url['query'] : '' );
		$path .= ( isset($parsed_url['fragment']) ? '#'.$parsed_url['fragment'] : '' );

		return $path;	
	}
	function changeGeneralLink( $link ) {
		$path = $this->leet_subdomain_getUrlPath($link);
		$link = $this->this_uri() . $path;
		return $link;
	}
	
	// action //
	function addActions() {
		add_action( 'init', array($this, 'leet_subdomain_init'), 99, 1 );
		add_action( 'wp', array( $this, 'pjleet_redirect' ), 99, 1 );
		
	}
	
	
	function addFilters() {
		
		add_filter( 'rewrite_rules_array', array($this, 'pjleet_rewrite_rules' ));
		add_filter( 'root_rewrite_rules', array( $this, 'pjleet_root_rewrite_rules' ) );

		
		$subdomain_setting = $this->subdomain_setting;
		foreach ( $subdomain_setting as $v ) {
			
			
			if ( $v == 'author'):
				add_filter( 'author_rewrite_rules', array( $this, 'pjleet_author_rewrite_rules' ), 99, 1 );
				add_filter( 'author_link', array( $this, 'pjleet_author_link'), 99, 2 );
			endif;
			if ( $v == 'page'):
				add_filter( 'page_rewrite_rules', array( $this, 'pjleet_page_rewrite_rules' ), 99, 1 );
				add_filter( 'page_link', array($this, 'pjleet_page_link'), 99, 2 ); // page
			endif;
		}
		
		
		
		/* URL Filters */
		//add_filter( 'bloginfo_url', array( $this, 'pjleet_filter_bloginfo_url'), 10, 2 );
		//add_filter( 'bloginfo', array( $this, 'pjleet_filter_bloginfo'), 10, 2 );
		
		
		
		#add_filter( 'post_type_link', array($this, 'pjleet_custom_post_link'), 10, 2 ); // Custom Post 
		
		if ( $this->type > 0 )
			add_filter( 'get_pagenum_link', array( $this, 'changeGeneralLink' ) );
	}
	
	
	function leet_subdomain_init () {
		if ( ! is_admin() ) {
			if (function_exists('set_transient'))
				set_transient('rewrite_rules', "");
			update_option('rewrite_rules', "");
		}
	}
	

	function pjleet_rewrite_rules( $rules ) {
		//var_dump($rules);
		/*
		    foreach ($rules as $rule => $rewrite) {
				if ( preg_match('/(feed|attachment|comment-page|trackback|search)/',$rule) || preg_match('/(year|monthnum|attachment)/',$rewrite) ) {
					unset($rules[$rule]);
				}
			}
		*/
		//var_dump($rules);
		return $rules;
	}
	
	function pjleet_root_rewrite_rules( $rules ) {
		if ( $this->type == 0) {
			/* khoa tam thoi
			unset( $rules);
			$rules = array();
			$rules["([^/]+)/([^/]+)?$"] = "index.php?mode=\$matches[1]&key=\$matches[2]";
			$rules["([^/]+)/([^/]+)/([^/]+)/?$"] = "index.php?mode=\$matches[1]&key=\$matches[2]&function=\$matches[3]";
			$rules["([^/]+)/([^/]+)/page/?([0-9]{1,})/?$"] = "index.php?mode=\$matches[1]&key=\$matches[2]&paged=\$matches[3]";
			*/
		}
		return $rules;
	}
	
	function pjleet_post_link( $link, $post_object ) {
		return preg_replace('#www.'.$this->root .'/(.+?)/(.+?)#','$1.'.$this->root .'/$2', $link);
	}
		
	function pjleet_page_link( $link, $PID ) {
		$data = get_post($PID);
		//avoid using https //
		return '//'. $data->post_name . '.' . $this->root;
	}
	
	
	function pjleet_author_link($link, $id) {
		$newlink =  preg_replace('#www.'.$this->root .'/(.+)/(.+)#', '$2.'.$this->root .'/', $link);
		return $newlink;
	}
	/*
	#/////////////////////////////////// REWRITE ////////////////////////////#
	*/
	
	function getRewriteRules() {
		switch ( $this->type ) {
			case 1 :
				$field = 'pagename';
			break;
			default:
				$field = 'pagename';
			break;
		}
		unset( $rules);
		$rules = array();
		//$rules["feed/(feed|rdf|rss|rss2|atom)/?$"] = "index.php?" . $field . "=" . $this->slug . "&feed=\$matches[1]";
		//$rules["(feed|rdf|rss|rss2|atom)/?$"] = "index.php?" . $field . "=" . $this->slug . "&feed=\$matches[1]";
		$rules["page/?([0-9]{1,})/?$"] = "index.php?" . $field . "=" . $this->slug . "&paged=\$matches[1]";
		/* remember, if /$? will break something */
		$rules["$"] = "index.php?" . $field . "=" . $this->slug;
		
		return $rules;
	}
	
	
	
	
	/* post */
	
	function pjleet_page_rewrite_rules( $rules ) {
		if ( $this->type == 4 ) {
			$rules = $this->getRewriteRules( );
		}
		return $rules;
	}
	
	function pjleet_redirect ($redirect = '') {
		global $wp_query;
		$redirect = false;
		$subdomain_setting = $this->subdomain_setting;
		
		if ( $this->type == 0) {
				// Check if it's a page
			
			if ($wp_query->is_page  && in_array('page', $subdomain_setting) ) {
				$redirect = 'http://'. get_query_var('pagename') . '.' . $this->root;
			}
			
			
		/*} elseif ( $this->type == 3) {
			
			if ( strlen($_SERVER['REQUEST_URI']) < 2 )
				$redirect = home_url();
			// nếu hiển thị nhầm sang category ?
			if ( ! $wp_query->is_tag )
				$redirect = home_url();
		*/
		} elseif ( $wp_query->is_home && $this->type != 0 ) {
			$redirect = home_url();
		}
		// If a redirect is found then do it
		if ($redirect) {
			wp_redirect($redirect, 301);
			exit();
		}
	}

}

// Run the Plugin
new Start_init_subdomain;