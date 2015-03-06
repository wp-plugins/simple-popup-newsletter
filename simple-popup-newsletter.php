<?php
/*
Plugin Name: Simple Popup Newsletter
Plugin URI: http://www.keszites.com/wordpress/simple-popup-newsletter-wordpress-plugin-in-english/
Description: A popup window for email subscriptions, email export functions and ability to include your code of the submit forms.
Author: Varga Zsolt
Author URI: http://www.keszites.com
Version: 1.1.1
*/


require_once('simple-popup-newsletter-langs.php');
require_once('simple-popup-newsletter-options.php');

add_action('init','init_session', 1);
add_action('init','reg_scripts');
add_action('wp_enqueue_scripts','enque_scripts');

add_action('admin_init','reg_scripts');
add_action('admin_enqueue_scripts', 'enque_scripts' );

add_action('admin_menu','newsletter_menu_setup');
add_filter('widget_text', 'do_shortcode');

function init_session() { if(!session_id()) session_start(); }


register_activation_hook(__FILE__, 'newsletter_install');

function newsletter_install() {
  global $wpdb;

  $sql = "CREATE TABLE newsletter_popup_subscribers (".
  "subscriber_id mediumint(8) NOT NULL auto_increment,".
  "subscriber_name varchar(50) DEFAULT '' NOT NULL,".
  "subscriber_email varchar(50) DEFAULT '' NOT NULL,".
  "subscriber_time int(11) DEFAULT '0' NOT NULL,".
  "PRIMARY KEY(subscriber_id));";

  $wpdb->query($sql);

  $sql = "CREATE TABLE newsletter_popup_options (".
  "option_id mediumint(8) NOT NULL auto_increment,".
  "option_label1 varchar(100) DEFAULT '' NOT NULL,".
  "option_label2 varchar(100) DEFAULT '' NOT NULL,".
  "option_name varchar(100) DEFAULT '' NOT NULL,".
  "option_email varchar(100) DEFAULT '' NOT NULL,".
  "option_button1 varchar(100) DEFAULT '' NOT NULL,".
  "option_button2 varchar(100) DEFAULT '' NOT NULL,".
  "option_exit varchar(100) DEFAULT '' NOT NULL,".
  
  "option_active tinyint(1) DEFAULT '1' NOT NULL,".
  "option_delay int(5) DEFAULT '0' NOT NULL,".
  "option_html varchar(50) DEFAULT '' NOT NULL,".
  "option_submit tinyint(1) DEFAULT '0' NOT NULL,".
  "option_code varchar(10) DEFAULT '' NOT NULL,".
  
  "PRIMARY KEY(option_id));";

  $wpdb->query($sql);

  $v1 = md5(microtime()); $v2 = substr($v1, 22);

  $sql = "INSERT INTO newsletter_popup_options (".
  "option_id, ".
  "option_label1, option_label2, option_name, ".
  "option_email, option_button1, option_button2, option_exit, option_code) VALUES (".
  "1, '', '', '', '', '', '', '', '".$v2."' );";

  $wpdb->query($sql);

}

function reg_scripts() {
  $styleUrl  = plugin_dir_url (__FILE__).'css/style.css';
	$styleFile = plugin_dir_path(__FILE__).'css/style.css';
	if (file_exists($styleFile)) {
	    wp_register_style('newsletter_style', $styleUrl);
	}
  $jsUrl  = plugin_dir_url (__FILE__).'js/newsletter.js';
	$jsFile = plugin_dir_path(__FILE__).'js/newsletter.js';
	if (file_exists($jsFile)) {
	    wp_register_script('newsletter_javascript', $jsUrl, array('jquery'), '3.45.0-2013.10.17', true);
	}
}

function enque_scripts() {
	    wp_enqueue_style ('newsletter_style');
	    wp_enqueue_script ('newsletter_javascript');
}


function newsletter_menu_setup() {
	add_menu_page('Popup Newsletter', 'Popup Newsletter', 'manage_options', 'simple-popup-newsletter', 'newsletter_page');
  add_submenu_page('simple-popup-newsletter', 'Options', 'Options', 'manage_options', 'simple-popup-newsletter-options', 'simple_popup_newsletter_options'); 
  add_submenu_page('simple-popup-newsletter', 'Language', 'Language', 'manage_options', 'simple-popup-newsletter-langs', 'simple_popup_newsletter_langs'); 
}

add_action('wp_footer', 'newsletter_footer');

function newsletter_footer() {
  global $wpdb, $box_lang, $box_options;
    
  get_langwords();

  if (!(isset($box_options->option_active) && $box_options->option_active == 1)) return;
  
  
  if(isset($_POST['subscribe_submit'])) {
  
    $email = isset($_POST['popup_box_email'])?strrep($_POST['popup_box_email']):"";
    $name = isset($_POST['popup_box_name'])?strrep($_POST['popup_box_name']):"";
    
    if(!empty($email) && !empty($name)) {

      $sql = "INSERT INTO newsletter_popup_subscribers (subscriber_name, subscriber_email, subscriber_time)".
      " VALUES ('".$name."',"."'".$email."','".time()."');";
      
      $wpdb->query($sql);
    }
  
  }
  if(isset($_POST['unsubscribe_submit'])) {

    $email = isset($_POST['popup_box_email_unsub'])?strrep($_POST['popup_box_email_unsub']):"";

    if(!empty($email)) {

      $sql = "DELETE FROM newsletter_popup_subscribers WHERE subscriber_email = '".$email."'";
      $wpdb->query($sql);
    }
  
  }

  $jav_src = "";
  
  $uri = $_SERVER['REQUEST_URI'];
  if (!empty($box_options->option_html) && (strpos($uri, $box_options->option_html) > -1)) {
    if(!(isset($_SESSION['box_showed_unsub']) && $_SESSION['box_showed_unsub'])) {
      $jav_src = '<script type="text/javascript">setTimeout("show_popup_unsub();",'.$box_options->option_delay.');</script>';  
      $_SESSION['box_showed_unsub'] = true;
    }
  } else {
    if(!(isset($_SESSION['box_showed']) && $_SESSION['box_showed'])) {
      $jav_src = '<script type="text/javascript">setTimeout("show_popup();",'.$box_options->option_delay.');</script>';  
      $_SESSION['box_showed'] = true;
    }
  }
  
  $label = array();
  $label = $box_lang;
  foreach($label as $k => $v) {
    if(isset($box_options->$k) && !empty($box_options->$k)) $label[$k] = $box_options->$k; 
  }


if (!(isset($box_options->option_submit) && $box_options->option_submit == 1)) {
?>
<div class="popup-box-wrapper-unsub">
	<div class="popup-box">
		<a href="#" class="popup-box-close txt-none">Exit</a>
    <p><?php echo "".$label['option_label2']; ?></p>
    <form id="popup_box_form_unsub" name="popup_box_form_unsub" method="post" action="#">
      <?php echo "".$label['option_email']; ?><br />
      <input id="popup_box_email_unsub" name="popup_box_email_unsub" type="text" maxlength="50" />
    <input type="hidden" id="unsubscribe_submit" name="unsubscribe_submit" value="1" />
    </form>
		<ul class="box-buttons">
		  <li><a href="#" id="popup_box_unsub"><?php echo "".$label['option_button2']; ?></a></li>
		  <li><a href="#" id="popup_box_exit_unsub"><?php echo "".$label['option_exit']; ?></a></li>
    </ul>
	</div>
</div>
<div class="popup-box-wrapper">
	<div class="popup-box">
		<a href="#" class="popup-box-close txt-none">Exit</a>
    <p><?php echo "".$label['option_label1']; ?></p>
    <form id="popup_box_form" name="popup_box_form" method="post" action="#">
      <?php echo "".$label['option_name']; ?><br />
      <input id="popup_box_name" name="popup_box_name" type="text" maxlength="50" /><br />
      <?php echo "".$label['option_email']; ?><br />
      <input id="popup_box_email" name="popup_box_email" type="text" maxlength="50" />
    <input type="hidden" id="subscribe_submit" name="subscribe_submit" value="1" />
    </form>
		<ul class="box-buttons">
		  <li><a href="#" id="popup_box_sub"><?php echo "".$label['option_button1']; ?></a></li>
		  <li><a href="#" id="popup_box_exit"><?php echo "".$label['option_exit']; ?></a></li>
    </ul>
	</div>
</div>
<?php
  echo $jav_src;
  }
} //********************** END OF newsletter_footer

add_shortcode('subscribtion', 'subscribtion_shortcode');
function subscribtion_shortcode($args, $content = null , $code = "") {

  $result = "";
  $caption = (isset($args['caption']))?$args['caption']:'Subscribtion';
  $result = '<input id="subscribtion_button" name="subscribtion_button" class="button_newsletter" type="button" value="'.$caption.'" />';
  return $result;
}

add_shortcode('unsubscribtion', 'unsubscribtion_shortcode');
function unsubscribtion_shortcode($args, $content = null , $code = "") {

  $result = "";
  $caption = (isset($args['caption']))?$args['caption']:'Unsubscribtion';
  $result = '<input id="unsubscribtion_button" name="unsubscribtion_button" class="button_newsletter" type="button" value="'.$caption.'" />';
  return $result;
}

/******************* MAIN FUNCTION ******************/
function newsletter_page() {
  global $wpdb, $box_options;

  get_langwords();

  $jav_src = "";
  $c1 = $box_options->option_code;

  if(isset($_POST['submit1'])) {
    
    $filename = plugin_dir_path(__FILE__).'emails/subscribtions_'.$c1.'.csv';
    $fileurl  = plugin_dir_url (__FILE__).'emails/subscribtions_'.$c1.'.csv';
  
    $result = "NAME;EMAIL;"."\r\n";
    $sql = "SELECT * FROM newsletter_popup_subscribers ORDER BY subscriber_id DESC";
    $datas = array();
    $datas = $wpdb->get_results($sql);
    foreach($datas as $data) {
      $result .= "".$data->subscriber_name.";".$data->subscriber_email.";"."\r\n";
    }
    writeFile1($filename, $result);
    $jav_src = '<script type="text/javascript">window.open("'.$fileurl.'", "_blank");</script>';
    
  }

  if(isset($_POST['submit2'])) {

    $filename = plugin_dir_path(__FILE__).'emails/subscribtions_'.$c1.'.txt';
    $fileurl  = plugin_dir_url (__FILE__).'emails/subscribtions_'.$c1.'.txt';
  
    $result = "";
    $sql = "SELECT * FROM newsletter_popup_subscribers ORDER BY subscriber_id DESC";
    $datas = array();
    $datas = $wpdb->get_results($sql);
    foreach($datas as $data) {
      $result .= "".$data->subscriber_email.";"."\r\n";
    }
    writeFile1($filename, $result);
    $jav_src = '<script type="text/javascript">window.open("'.$fileurl.'", "_blank");</script>';
    
  }

  if(isset($_POST['submit3'])) {
    $sql = "";
    if(isset($_POST['chk'])) {
		  foreach($_POST['chk'] as $chk1) {
			 $sql .= $chk1.",";
		  }
    }
    if(!empty($sql)) {
      $sql = substr($sql, 0, strlen($sql)-1);
      $sql = "DELETE FROM newsletter_popup_subscribers WHERE subscriber_id IN (".$sql.")";
      $wpdb->query($sql);
    }
  }


//** PAGING *************/

        $page = isset($_GET['p'])?$_GET['p']:0;
        $rowNumber = rowNumber("SELECT COUNT(*) AS cnt FROM newsletter_popup_subscribers ORDER BY subscriber_id DESC");
         
        $orderby = "subscriber_id DESC"; 
        $sel = isset($_GET['orderby'])?$_GET['orderby']:((isset($_POST['table_order']))?$_POST['table_order']:"0");
        if ($sel == "1") { $orderby = "subscriber_email ASC"; }
        if ($sel == "2") { $orderby = "subscriber_name ASC"; }

        $url = "".$_SERVER['PHP_SELF']."?page=simple-popup-newsletter&p=$page&orderby=$sel";
        $paging = getPaging($url, $rowNumber);
             
//** PAGING *************/

  
  $result = "";
  $sql = "SELECT * FROM newsletter_popup_subscribers ORDER BY ".$orderby." LIMIT ".$page.", ".ADMIN_ITEM_PER_PAGE;
  $datas = array();
  $datas = $wpdb->get_results($sql);
  foreach($datas as $data) {
    $result .= '<tr>';
    $result .= '<td><input type="checkbox" class="chk" name="chk[]" value="'.$data->subscriber_id.'" /></td>';
    $result .= '<td>'.date("Y.m.d. H:i", $data->subscriber_time).'</td>';
    $result .= '<td>'.$data->subscriber_name.'</td>';
    $result .= '<td>'.$data->subscriber_email.'</td>';
    $result .= '</tr>';
  }
  if(empty($result)) $result = '<tr><td colspan="4" style="text-align: center;">No records available!</tr></td>';
  $result = '<tbody>'.$result.'</tbody>';

  
  $op1 = ($sel == "0")?'selected="selected"':"";
  $op2 = ($sel == "1")?'selected="selected"':"";
  $op3 = ($sel == "2")?'selected="selected"':"";


?>
<h3>Subscriber's List</h3>
<form id="subscribers_form" name="subscribers_form" method="post" action="<?php echo "".$_SERVER['PHP_SELF']."?page=simple-popup-newsletter&p=$page"; ?>">
<input id="submit1" name="submit1" class="button button-primary" type="submit" value="Export .csv" />&nbsp;&nbsp;&nbsp;
<input id="submit2" name="submit2" class="button button-primary" type="submit" value="Export Only Emails" /><br />
<br />
<div style="float: left;">
  <select id="table_order" name="table_order" style="padding: 0px 10px 0px 10px;">
    <option value="0" <?php echo $op1; ?>>Time Descending</option>
    <option value="1" <?php echo $op2; ?>>Email Ascending</option>
    <option value="2" <?php echo $op3; ?>>Name Ascending</option>
  </select>
    - Order 
</div>
<div style="float: left; margin: 5px 0px 0px 20px;"><input id="all_checked" name="all_checked" type="checkbox" value="1" style="margin-top: 0px;" /> - Check All Boxes</div>  
<div style="float: right;"><input id="submit3" name="submit3" class="button button-primary" type="submit" value="Delete Checked Emails" /></div>
<div style="clear: both;"></div>

<table class="wp-list-table widefat pages">
  <thead>
    <tr>
      <th style="width: 20px;"></th>
      <th>Date</th>
      <th>Name</th>
      <th>Email</th>
    </tr>
  </thead>
  <?php echo $result; ?>
  <tfoot>
    <tr>
      <th></th>
      <th>Date</th>
      <th>Name</th>
      <th>Email</th>
    </tr>
  </tfoot>
</table>
<div style="text-align: center;"><?php echo $paging; ?></div>
</form>  
<?php  
echo "".$jav_src;
}

//*************** PAGIN ROUTINES *************************//
define('ADMIN_ITEM_PER_PAGE', 30);
define('ADMIN_ITEM_LINKS_PER_PAGE', 10);

function rowNumber($sql) {
  global $wpdb;

  $datas = array();
  $datas = $wpdb->get_results($sql);
  foreach($datas as $data) {
    break;
  }
  $rowNumber = isset($data->cnt)?$data->cnt:-1;
  
return $rowNumber;
} 

function links($url, $p_, $s_, $rowNumber = -1) {

  $page = isset($_GET['p'])?$_GET['p']:0;
  $q = '?';
  if (strpos($url, "?") > -1) { $q = '&'; }

  if ($p_<0) $p_=0;
  if ($p_ == $page || $p_ >= $rowNumber) {
    return $s_.'&nbsp;&nbsp;'; 
  }
  $ret = '<a href="'.$url.$q.'p='.$p_.'">'.$s_.'</a>&nbsp;&nbsp;';
  
return $ret; 
}

function getPaging($url, $rowNumber) {

  $page = isset($_GET['p'])?$_GET['p']:0;
  $rowNum  = intval((double)$rowNumber / (double)ADMIN_ITEM_PER_PAGE)*ADMIN_ITEM_PER_PAGE;
  $rowNum += (($rowNumber - $rowNum) > 0)?ADMIN_ITEM_PER_PAGE:0;
 
  $jump = ADMIN_ITEM_LINKS_PER_PAGE * ADMIN_ITEM_PER_PAGE;
  $paging = '<div>'.(links($url, 0, '&laquo;First', $rowNumber));
  $paging.= links($url, $page - ADMIN_ITEM_PER_PAGE, '&laquo;Previous', $rowNumber);

  for ($i = $page; $i < $rowNum && $i < ($page + ADMIN_ITEM_LINKS_PER_PAGE * ADMIN_ITEM_PER_PAGE); $i += ADMIN_ITEM_PER_PAGE) {
    $paging.= links($url, $i, intval($i / ADMIN_ITEM_PER_PAGE) + 1, $rowNumber);
  }

  $paging.= links($url, $page + ADMIN_ITEM_PER_PAGE, 'Next&raquo;', $rowNumber); 
  $paging.= links($url, $rowNum - ADMIN_ITEM_PER_PAGE, 'Last&raquo;', $rowNumber)."</div>";

return $paging;
}

function writeFile1($file_name1, $txt1) {
    @fwrite($file_id = @fopen("$file_name1", 'w'), $txt1);
    @fclose($file_id);
}

function strrep($str) {
  return str_replace(array("'", '"', "<", ">", "&"), array("", "", "", "", ""), $str);
}


?>