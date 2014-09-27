<?php
/*
* Plugin Name: tinychat-roomspy
* Plugin URI: https://wordpress.org/plugins/tinychat-roomspy/
* Author: Ruddernation Designs
* Author URI: http://profiles.wordpress.org/ruddernation
* Description: Allows you to check who is in a TinyChat room and who is on Video/Audio.
* Requires at least: WordPress 3.6.0, BuddyPress 1.8.1
* Tested up to: WordPress 4.0 / BuddyPress 2.0.1
* Version: 1.0.4
* License: GPLv3
* License URI: http://www.gnu.org/licenses/gpl-3.0.html
* Date: 27th September 2014
*/
define('COMPARE_VERSION', '1.0.4');

register_activation_hook(__FILE__, 'room_spy_install');

function room_spy_install() {

	global $wpdb, $wp_version;
	$post_date = date("Y-m-d H:i:s");
	$post_date_gmt = gmdate("Y-m-d H:i:s");
	$sql = "SELECT * FROM ".$wpdb->posts." WHERE post_content LIKE '%[room_spy_page]%' AND `post_type` NOT IN('revision') LIMIT 1";
	$page = $wpdb->get_row($sql, ARRAY_A);
	if($page == NULL) {
		$sql ="INSERT INTO ".$wpdb->posts."(

			post_author, post_date, post_date_gmt, post_content, post_content_filtered, post_title, post_excerpt,  post_status, comment_status, ping_status, post_password, post_name, to_ping, pinged, post_modified, post_modified_gmt, post_parent, menu_order, post_type)

			VALUES

			('1', '$post_date', '$post_date_gmt', '[room_spy_page]', '', 'roomspy', '', 'publish', 'closed', 'closed', '', 'roomspy', '', '', '$post_date', '$post_date_gmt', '0', '0', 'page')";

		$wpdb->query($sql);
		$post_id = $wpdb->insert_id;
		$wpdb->query("UPDATE $wpdb->posts SET guid = '" . get_permalink($post_id) . "' WHERE ID = '$post_id'");
	} else {
		$post_id = $page['ID'];
	}
	update_option('room_spy_url', get_permalink($post_id));
}
add_filter('the_content', 'wp_show_room_spy_page', 52);

function wp_show_room_spy_page($content = '') {

	if(preg_match("/\[room_spy_page\]/",$content)) {

		wp_show_room_spy();

		return "";
	}
	return $content;
}
function wp_show_room_spy() {

	if(!get_option('room_spy_enabled', 0)) {
	}
	$room = $_POST ['room']; 
$username = $_POST ['username']; 
	?>
    <style>#chat,.chatimages{margin:2px 6px 15px;width:220px;height:190px;-webkit-transition:all .3s ease;-moz-transition:all .3s ease;-o-transition:all .3s ease;-ms-transition:all .3s ease;transition:all .3s ease;display:inline-block;text-decoration:none;font-size:17px;-webkit-border-radius:14px;-moz-border-radius:14px;border-radius:6px}input[type=text]{width:25%;}.entry-content img,img[class*=wp-image-]{height:190px;}</style>
<?php
if(($room=='Room name')|($room=='')){}elseif(preg_match("/^[a-z0-9]{3,}/",$_POST['room'])){$room=preg_replace('/[^a-z0-9]/i','',$room);$room=preg_replace('/\s+/','',$room);$data=file_get_contents('http://tinychat.apigee.com/'.strtolower($room).'.xml');$rooms=new SimpleXMLElement($data,libxml_use_internal_errors(true));$array=json_decode(json_encode((array)simplexml_load_string($xml)),1);}if(($username=='Name')||($username=='')){}else{$namecheck=file_get_contents('https://tinychat.com/api/usernameCheck.php?username='.$username.'');}?>
<div><?php echo ' 
<form method="post">Room Name: <input type="text" name="room" title="Just enter the name of the tinychat room and press spy." placeholder="Lowercase Letters!"/><input type="submit" value="Spy"/>&nbsp;&bull;&nbsp;Admins: '.$rooms['mod_count'].'&nbsp;&bull;&nbsp;Chatters: '.$rooms['total_count'].'&nbsp;&bull;&nbsp;On Cam: '.$rooms['broadcaster_count'].'<a class="password" title="Password is required to enter the room"/>'.$rooms['error'].'</a>';if($room!=='Room name'){echo '</form>';}?> </div><br><?php
if((preg_match("/^[a-z0-9]{3,}/",$_POST['room'])=='1')){$room=preg_replace('/[^a-z0-9]/i','',$room);$room=preg_replace('/\s+/','',$room);$room=htmlspecialchars($room,ENT_QUOTES, 'UTF-8');if($room!=='Room name'){echo '<br><br>';{echo ' <br> '.$room.'<br>';$pic='http://upload.tinychat.com/pic/'.$room.'';$picture='<a href="http://www.ruddernation.net/'.urlencode($room).'" title="Click to go to chat, Do not click if you need to add Pro or Autoop info!" target="_blank"><img src="'.$pic.'"class="chatimages"><br></a>';echo $picture;}echo '<br><br>';foreach($rooms->names as $username){echo '<div id="chat">'.$username.'<br>';$pic='http://upload.tinychat.com/i/'.$room.'-'.$username.'.jpg';$picture='<a href="http://www.ruddernation.net/'.$room.'" title="Hey! I&#39;m '.$username.', Click to come and chat with me." target="_blank"><img src="'.$pic.'"class="chatimages"></a><br></div>';echo $picture;}}}}?>