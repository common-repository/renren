<?php
/**
 * @package RenRen
 * @author ychen
 * @version 0.1
 */
/*
Plugin Name: RenRen
Plugin URI: http://chenyundong.com
Description: Pull status info from Renren.com and show them in sidebar.
Author: ychen
Version: 0.1
Author URI: http://chenyundong.com/
*/
define('LOGIN_URL', 'http://www.renren.com/PLogin.do');

define('STATUS_URL', 'http://status.renren.com/GetSomeomeDoingList.do');

define('MAX_ITEM_COUNT', 20); # max # of status to show

define('DEFAULT_RETRIEVAL_INTERVAL', 1800); # 30 mins

function widget_renren_register() {
	if ( function_exists('register_sidebar_widget') ) :
	function widget_renren($args) {
		extract($args);
		$options = get_option('widget_renren');
		if( time() - $options['update_time'] > $options['retrieval_interval'] ){
			require_once(ABSPATH. WPINC . '/class-snoopy.php');
			require_once(ABSPATH. WPINC . '/class-json.php');

			$login_data['domain'] = 'renren.com';
			$login_data['email'] = $options['account'];
			$login_data['password'] = $options['password'];

			$snoopy = new Snoopy;
			$snoopy->submit(LOGIN_URL, $login_data);
			$snoopy->submit(STATUS_URL);
			$json_str = $snoopy->results;
			$json = new Services_JSON(0x10);
			$status_array = $json->decode($json_str);
			if( isset($status_array['doingArray']) ) {
				foreach($status_array['doingArray'] as $item){
					$widget_renren_data[] = array('dtime' => $item['dtime'], 'content' => $item['content'], 'user_id' => $item['userId'], 'comment_count' => $item['comment_count']);
				}
			}
			else
				$widget_renren_data =  array('dtime' => date('Y-m-d H:i:s'), 'content' => 'Error when retrieving status. Please check whether account is active now.', 'comment_count' => '0');
			if( count($widget_renren_data) == 0) $widget_renren_data =  array('dtime' => date('Y-m-d H:i:s'), 'content' => 'No Status retrieved.', 'comment_count' => '0');
			delete_option('widget_renren_data');
			update_option('widget_renren_data', $widget_renren_data);
			$options['update_time'] = time();
			update_option('widget_renren', $options);
		}
		$items = get_option('widget_renren_data');
		?>
		<?php echo $before_widget; ?>
			<?php echo $before_title . $options['title'] . $after_title; $i = 0;?>
			<div id="renrenwrap">
			<?php foreach($items as $item):?>
				<li><?php printf(__('[%s] %s | <a href="http://renren.com/profile.do?id=%s" target="_blank">%s Reply</a>'), $item['dtime'], $item['content'], $item['user_id'], $item['comment_count']); $i++; if($i == $options['count']) break; ?></li>
			<?php endforeach; ?>
			</div>
		<?php echo $after_widget; ?>
	<?php
	}

	function widget_renren_style() {
		?>
		<style type="text/css">
		#renren,#renren:link,#renren:hover,#renren:visited,#renren:active{color:#fff;text-decoration:none}
		</style>
		<?php
	}

	function widget_renren_control() {
		$options = $newoptions = get_option('widget_renren');
		if ( $_POST["renren-submit"] ) {
			$newoptions['title'] = strip_tags(stripslashes($_POST['renren-title']));
			if ( empty($newoptions['title']) ) $newoptions['title'] = 'RenRen';
			$newoptions['account'] = strip_tags(stripslashes($_POST['renren-account']));
			$newoptions['password'] = strip_tags(stripslashes($_POST['renren-password']));
			$newoptions['count'] = strip_tags(stripslashes($_POST['renren-count']));
			if ( empty($newoptions['count']) ) $newoptions['count'] = MAX_ITEM_COUNT;
			if($newoptions['count'] > MAX_ITEM_COUNT || !is_numeric($newoptions['count']) ) $newoptions['count'] = MAX_ITEM_COUNT;
			$newoptions['retrieval_interval'] = strip_tags(stripslashes($_POST['renren-retrieval-interval']));
			if ( empty($newoptions['retrieval_interval']) ) $newoptions['retrieval_interval'] = DEFAULT_RETRIEVAL_INTERVAL;
			if( !is_numeric($newoptions['count']) ) $newoptions['renren-retrieval-interval'] = DEFAULT_RETRIEVAL_INTERVAL;
		}
		if ( $options != $newoptions ) {
			$options = $newoptions;
			update_option('widget_renren', $options);
		}
		$title = htmlspecialchars($options['title'], ENT_QUOTES);
		$account = htmlspecialchars($options['account'], ENT_QUOTES);
		$password = htmlspecialchars($options['password'], ENT_QUOTES);
		$count = htmlspecialchars($options['count'], ENT_QUOTES);
		$retrieval_interval = htmlspecialchars($options['retrieval_interval'], ENT_QUOTES);
	?>
	<p><label for="renren-title"><?php _e('Title:'); ?> <input style="width: 250px;" id="renren-title" name="renren-title" type="text" value="<?php echo $title; ?>" /></label></p>
	<p><label for="renren-account"><?php _e('Account:'); ?> <input style="width: 250px;" id="renren-account" name="renren-account" type="text" value="<?php echo $account ?>" /></label></p>
	<p><label for="renren-password"><?php _e('Password:'); ?> <input style="width: 250px;" id="renren-password" name="renren-password" type="password" value="<?php echo $password; ?>" /></label></p>
	<p><label for="renren-count"><?php _e('Number of status to show(<= ' . MAX_ITEM_COUNT . '):'); ?> <input style="width: 250px;" id="renren-count" name="renren-count" type="text" value="<?php echo $count; ?>" /></label></p>
	<p><label for="renren-retrieval-interval"><?php _e('Retrieval interval(Default ' . DEFAULT_RETRIEVAL_INTERVAL . ' seconds):'); ?> <input style="width: 250px;" id="renren-retrieval-interval" name="renren-retrieval-interval" type="text" value="<?php echo $retrieval_interval; ?>" /></label></p>
	<input type="hidden" id="renren-submit" name="renren-submit" value="1" />
	<?php
	}
	register_sidebar_widget('RenRen', 'widget_renren');
	register_widget_control('RenRen', 'widget_renren_control');
	if ( is_active_widget('widget_renren') )
		add_action('wp_head', 'widget_renren_style');
	endif;
}

add_action('init', 'widget_renren_register');
?>
