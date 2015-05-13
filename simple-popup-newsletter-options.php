<?php

function simple_popup_newsletter_options() {
  global $wpdb, $box_lang, $box_options;

  if(isset($_POST['submit1'])) {
  
    $active = ($_POST['option_active'] == 1)?"1":"0";
    $delay = (intval($_POST['option_delay']) > 0)?"".intval($_POST['option_delay']):"0";
    $html = strrep($_POST['option_html']);
    $submit = ($_POST['option_submit'] == 1)?"1":"0";

    $sql = "UPDATE ".$wpdb->prefix."newsletter_popup_options SET ".
           "option_active = '".$active."', ".
           "option_delay = '".$delay."', ".
           "option_html = '".$html."', ".
           "option_submit = '".$submit."' ".
           "WHERE option_id =  '1' ";

    $wpdb->query($sql);
  }

  get_langwords();
    

  echo '<h3>Setting Parameter Options</h3>'.
  '<form id="popup_box_options" name="popup_box_options" method="post" action="#"><table class="tbl_wrapper">';
  
    $option = (isset($box_options->option_active) && $box_options->option_active == 1)?' checked="checked" ':'';
    echo '<tr><td colspan="3"><br /></td></tr>';
    echo '<tr><td style="text-align: right; width: 400px;">Subscription and the unsubscription pop-up are active</td><td> : </td><td>'.
    '<input id="option_active" name="option_active" type="checkbox" '.$option.' value="1" /></td></tr>';

    $option = "".$box_options->option_delay;
    echo '<tr><td colspan="3"><br /></td></tr>';
    echo '<tr><td style="text-align: right; width: 400px;">Showing subscription pop-up window after this delay in milliseconds</td><td> : </td><td>'.
    '<input id="option_delay" name="option_delay" type="text" maxlength="5" style="width: 60px;" value="'.$option.'" /></td></tr>';
    
    $option = "".$box_options->option_html;
    echo '<tr><td colspan="3"><br /></td></tr>';
    echo '<tr><td style="text-align: right; width: 400px;">Significant part of the unsubscription URI in order to show pop-up window on this page.'.
         ' For example: /unsubscribe-page/ </td><td> : </td><td>'.
    '<input id="option_html" name="option_html" type="text" maxlength="50" style="width: 250px;" value="'.$option.'" /></td></tr>';
    
    $option = '[subscribtion caption=&quot;Subscription&quot;]';
    echo '<tr><td colspan="3"><br /></td></tr>';
    echo '<tr><td style="text-align: right; width: 400px;">Insert subscription button in the text editor, or copy this code into the widget text.'.
         '</td><td> : </td><td>'.
    '<input type="text" readonly="readonly" style="width: 300px;" value="'.$option.'" /></td></tr>';
    
    $option = '[unsubscribtion caption=&quot;Unsubscription&quot;]';
    echo '<tr><td colspan="3"><br /></td></tr>';
    echo '<tr><td style="text-align: right; width: 400px;">Insert unsubscription button in the text editor, or copy this code into the widget text.'.
         '</td><td> : </td><td>'.
    '<input type="text" readonly="readonly" style="width: 300px;" value="'.$option.'" /></td></tr>';
    
    $code1 = '
    <form id="popup_box_form" name="popup_box_form" method="post" action="#">
      Your name<br />
      <input id="popup_box_name" name="popup_box_name" type="text" maxlength="50" /><br />
      Your email address<br />
      <input id="popup_box_email" name="popup_box_email" type="text" maxlength="50" /><br />
      <input type="hidden" id="subscribe_submit" name="subscribe_submit" value="1" />
      <input type="submit" value="Send" />
    </form>
    ';
    
    $code2 = '
    <form id="popup_box_form_unsub" name="popup_box_form_unsub" method="post" action="#">
      <br />
      <input id="popup_box_email_unsub" name="popup_box_email_unsub" type="text" maxlength="50" /><br />
      <input type="hidden" id="unsubscribe_submit" name="unsubscribe_submit" value="1" />
      <input type="submit" value="Send" />
    </form>
    ';
    
    $option = (isset($box_options->option_submit) && $box_options->option_submit == 1)?' checked="checked" ':'';
    echo '<tr><td colspan="3"><br /></td></tr>';
    echo '<tr><td style="text-align: right; width: 400px;">Hide pop-up windows and allow my own HTML forms like these codes below</td><td> : </td><td>'.
    '<input id="option_submit" name="option_submit" type="checkbox" '.$option.' value="1" /></td></tr>';
    echo '<tr><td colspan="3"><br />Subscription HTML Form. (You can insert these in text widget for example.)<br /><textarea readonly="readonly" style="background-color: #FFFFFF; width: 700px; height: 170px;">'.$code1.'</textarea></td></tr>';
    echo '<tr><td colspan="3"><br />Unsubscription HTML Form<br /><textarea readonly="readonly" style="background-color: #FFFFFF; width: 700px; height: 150px;">'.$code2.'</textarea></td></tr>';

  echo '</table><br /><br />';
  echo '<input id="submit1" name="submit1" class="button button-primary" type="submit" value="Save Changes" /></form>';
}


?>