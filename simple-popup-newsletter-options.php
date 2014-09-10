<?php

function simple_popup_newsletter_options() {
  global $wpdb, $box_lang, $box_options;

  if(isset($_POST['submit1'])) {
  
    $active = ($_POST['option_active'] == 1)?"1":"0";
    $delay = (intval($_POST['option_delay']) > 0)?"".intval($_POST['option_delay']):"0";
    $html = strrep($_POST['option_html']);

    $sql = "UPDATE newsletter_popup_options SET ".
           "option_active = '".$active."', ".
           "option_delay = '".$delay."', ".
           "option_html = '".$html."' ".
           "WHERE option_id =  '1' ";

    $wpdb->query($sql);
  }

  get_langwords();
    

  echo '<h3>Setting Parameter Options</h3>'.
  '<form id="popup_box_options" name="popup_box_options" method="post" action="#"><table class="tbl_wrapper">';
  
    $option = (isset($box_options->option_active) && $box_options->option_active == 1)?' checked="checked" ':'';
    echo '<tr><td colspan="3"><br /></td></tr>';
    echo '<tr><td style="text-align: right; width: 400px;">Subscribtion and the unsubscribtion popup are active</td><td> : </td><td>'.
    '<input id="option_active" name="option_active" type="checkbox" '.$option.' value="1" /></td></tr>';

    $option = "".$box_options->option_delay;
    echo '<tr><td colspan="3"><br /></td></tr>';
    echo '<tr><td style="text-align: right; width: 400px;">Showing subscribtion popup window after this delay in milliseconds</td><td> : </td><td>'.
    '<input id="option_delay" name="option_delay" type="text" maxlength="5" style="width: 60px;" value="'.$option.'" /></td></tr>';
    
    $option = "".$box_options->option_html;
    echo '<tr><td colspan="3"><br /></td></tr>';
    echo '<tr><td style="text-align: right; width: 400px;">Significant part of the unsubscribtion URI in order to show popup window on this page.'.
         ' For example: /unsubscribe-page/ </td><td> : </td><td>'.
    '<input id="option_html" name="option_html" type="text" maxlength="50" style="width: 250px;" value="'.$option.'" /></td></tr>';
    
    $option = '[subscribtion caption=&quot;Subscribtion&quot;]';
    echo '<tr><td colspan="3"><br /></td></tr>';
    echo '<tr><td style="text-align: right; width: 400px;">Insert subscribtion button in the text editor, or copy this code into the widget text.'.
         '</td><td> : </td><td>'.
    '<input type="text" readonly="readonly" style="width: 300px;" value="'.$option.'" /></td></tr>';
    
    $option = '[unsubscribtion caption=&quot;Unsubscribtion&quot;]';
    echo '<tr><td colspan="3"><br /></td></tr>';
    echo '<tr><td style="text-align: right; width: 400px;">Insert unsubscribtion button in the text editor, or copy this code into the widget text.'.
         '</td><td> : </td><td>'.
    '<input type="text" readonly="readonly" style="width: 300px;" value="'.$option.'" /></td></tr>';
    
  echo '</table><br /><br />';
  echo '<input id="submit1" name="submit1" class="button button-primary" type="submit" value="Save Changes" /></form>';
}


?>