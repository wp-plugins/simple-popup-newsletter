<?php

function delete_newsletter_plugin() {
  global $wpdb;

  $sql = "DROP TABLE IF EXISTS newsletter_popup_options";
  $wpdb->query($sql);

  $sql = "DROP TABLE IF EXISTS newsletter_popup_subscribers";
  $wpdb->query($sql);

}

delete_newsletter_plugin();

?>