<?php 

/**
 * Uninstalls the plugin and clears all the data from the database
 * 
 */

 if (! defined('WP_UNINSTALL_PLUGIN')){
    die;
 }

 //Delete from database via query
 global $wpdb;

 $wpdb->query("DELETE FROM {$wpdb->prefix}posts WHERE post_type='movies'");
 $wpdb->query("DELETE FROM {$wpdb->prefix}postmeta WHERE post_id NOT IN (SELECT id FROM {$wpdb->prefix}posts) ");
 $wpdb->query("DELETE FROM {$wpdb->prefix}term_relationships WHERE object_id NOT IN (SELECT id FROM {$wpdb->prefix}posts) ");
