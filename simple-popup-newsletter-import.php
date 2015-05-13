<?php

function simple_popup_newsletter_import() {
  global $wpdb;

  if(isset($_POST['submit1'])) {
      
      $error = -1;
      if($_FILES["file1"]["error"] > 0) $error = 1; 
      
      $_file = $_FILES["file1"];
      $file_type = $_file['type'];
      $file_name = $_file['name'];
      $file_tmp  = $_file['tmp_name'];
      $file_ext = explode(".", $file_name);
      
      if(count($file_ext) < 1) $error = 1;
      if($error < 0) if($file_ext[(count($file_ext)-1)] != "csv") $error = 1;
            
      $datas = @explode("\r\n", @file_get_contents($file_tmp));
      if(!isset($datas) || count($datas) < 1) $error = 1;
      
      if($error < 0) for($i = 0; $i < (count($datas)-1); $i++) if(count(explode(";", $datas[$i])) != 3) $error = 1;
      
      if($error < 0) {
        
        foreach(array_reverse($datas) as $data) {
          
          $value = @explode(";", $data);
          if(!isset($value) || (count($value) != 3) || $value[0] == "NAME") continue;
          $name = htmlentities($value[0]); $email = $value[1];
          $sql = "INSERT INTO ".$wpdb->prefix."newsletter_popup_subscribers (subscriber_name, subscriber_email, subscriber_time)".
          " VALUES ('".$name."', '".$email."', '".time()."');";

          $wpdb->query($sql);
        }
        
      }

  }

  echo '<h3>Import and append from .CSV file</h3>'.
  '<form id="popup_box_import" name="popup_box_import" method="post" enctype="multipart/form-data" action="#">'.
  '<table class="tbl_wrapper"><tr><td>';
  echo 'If you want, first delete the contact data which will be duplicated. Because the plugin will append the CSV list.<br />';
  echo '<br />Select file : <input type="file" id="file1" name="file1" accept=".csv" /><br />';
  echo 'You should use the CSV file which was exported from the Simple Popup Newsletter Plugin.<br />';
  echo '<br /></td></tr></table>';
  echo '<input id="submit1" name="submit1" class="button button-primary" type="submit" value="Import file" /></form>';
}


?>