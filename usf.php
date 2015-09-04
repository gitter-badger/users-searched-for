<?php

/*
 * Plugin Name:       Users Searched For
 * Plugin URI:        http://github.com/foae/users-searched-for
 * Description:       This tool will record and display every term your users have searched for on your WordPress website. It will also show if they found what they were looking for, by indicating the page they landed on after the search.
 * Version:           1.0.0
 * Author:            Lucian Alexandru
 * Author URI:        https://plainsight.ro
 * Text Domain:       usf-locale
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Domain Path:       /languages
 */

if ( ! defined( 'ABSPATH' ) ) exit;


/**
 * Class USF - Users Searched For
 */
class USF {


    /**
     * Plugin's version
     * @var string
     */
    public $version;


    /**
     * Stores the current logged in user ID
     * @var int
     */
    public $userId;


    /**
     * Holds information about the current logged in user
     * @var object
     */
    public $currentUserInfo;


    /**
     * Holds the $wpdb object
     * @var object
     */
    public $wpdb;


    /**
     * Holds the name of our custom DB entry where we keep our records
     * @var string
     */
    public $DbUsfRecords;


    /**
     * Holds the name of out custom DB entry where we store the plugin's settings
     * @var string
     */
    public $DbUsfSettings;


    /**
     * Class constructor
     */
    public function __construct() {

        global $wpdb;
        $this->wpdb = &$wpdb;

        $this->version = '1.0.0';

        $this->DbUsfRecords = $this->wpdb->prefix . 'usf_records';
        $this->DbUsfSettings = $this->wpdb->prefix .'usf_settings';

        // register_activation_hook, register_deactivation_hook
        $this->controlRunningState();

        // Add Admin Menu Page
        add_action('admin_menu', array($this, 'addMenuPage'));

        // Make the object properties available in the View
        add_action('admin_init', array($this, 'loadPluginView'));

        // Listen for searched terms in frontend
        add_action('wp', array($this, 'listenForSearches'));

        // Enqueue scripts (css and js) in the admin area - only on our plugin page
        add_action('admin_enqueue_scripts', array($this, 'enqueueAdminScripts'));
    }


    /**
     * Sets up all hooks and filters needed for our plugin to work
     */
    public function controlRunningState() {
        register_activation_hook(__FILE__, array($this, 'activate'));
        register_deactivation_hook(__FILE__, array($this, 'deactivate'));
    }


    /**
     * Class name -> string method
     * @return string
     */
    public function __toString() {
        return __CLASS__;
    }


    /**
     * Get the plugin's URL
     * @return string
     */
    public function getPluginUrl() {
        return plugins_url() . '/users-searched-for/';
    }


    /**
     * Return the plugin's path in the OS' filesystem
     * @return string
     */
    public function getPluginPath() {
        return plugin_dir_path(__FILE__) . 'users-searched-for/';
    }


    /**
     * Return the plugin's static folder URL
     * @return string
     */
    public function getPluginViewURL() {
        return $this->getPluginUrl() . 'static/';
    }


    /**
     * Activate and install the needed tables in the WordPress DB
     */
    public function activate() {

        // Records
        $this->wpdb->query("
                            CREATE TABLE IF NOT EXISTS `{$this->DbUsfRecords}`
                            (
                              `record_id` INT(3) NOT NULL AUTO_INCREMENT,
                              `user_id` INT(3) NOT NULL DEFAULT '0',
                              `searched_term` VARCHAR(64) NOT NULL,
                              `page_id` VARCHAR(64) NOT NULL,
                              `user_ip` VARCHAR(64) NOT NULL,
                              `timestamp` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                              PRIMARY KEY (`record_id`)
                            )
                            ");

        // Settings
        $this->wpdb->query("
                            CREATE TABLE IF NOT EXISTS `{$this->DbUsfSettings}`
                            (
                              `general_settings` VARCHAR(3000)
                            )
                            ");

    }


    /**
     * Action taken on plugin deactivation
     */
    public function deactivate() {
        $this->wpdb->query("DROP TABLE `{$this->DbUsfRecords}`");
        $this->wpdb->query("DROP TABLE `{$this->DbUsfSettings}`");
    }


    /**
     * Loads the view
     */
    public function renderView() {
        require_once 'views/usf.php';
    }


    /**
     * Adds the Admin Menu Page under Tools -> Users Searched For
     */
    public function addMenuPage() {

        // $parent_slug, $page_title, $menu_title, $capability, $menu_slug, $function
        add_submenu_page(
            'tools.php',
            'Users Searched For - Statistics',
            'Users Searched For',
            'manage_options',
            'view-statistics',
            array($this, 'renderView')
        );
    }


    /**
     * Loads the scripts, styles and Class' properties into the View
     */
    public function loadPluginView() {

        $this->userId = get_current_user_id();
        $this->currentUserInfo = wp_get_current_user($this->userId);
    }


    /**
     * Listens to searched terms on website and records them in the database
     */
    public function listenForSearches() {

        if (isset($_GET) && !empty($_GET['s'])) {

            $pageID = get_the_ID();
            $this->userId = get_current_user_id();
            $userIP = $_SERVER['REMOTE_ADDR'];
            $searched_term = filter_var($_GET['s'], FILTER_SANITIZE_FULL_SPECIAL_CHARS, FILTER_FLAG_NO_ENCODE_QUOTES);
            $searched_term = trim(rtrim($searched_term));

            $this->wpdb->query("
                                INSERT INTO `{$this->DbUsfRecords}` 
                                (user_id, searched_term, page_id, user_ip)
                                VALUES
                                ('{$this->userId}', '{$searched_term}', '{$pageID}', '{$userIP}')
                                ");

        }
    }


    /**
     * Return the recorded terms from the database
     * @return array > objects $results
     */
    public function getSearchedTerms() {
        $results = $this->wpdb->get_results("SELECT * FROM `{$this->DbUsfRecords}`");

        if (!empty($results)) {
            return $results;
        }

        return false;
    }


    /**
     * Return the username using the userID; otherwise return 'Visitor'
     * @param int $userId
     * @return string
     */
    public function getUserName($userId) {
        if ($userId) {
            return get_userdata($userId)->user_login;
        } else {
            return 'Visitor';
        }
    }


    /**
     * Return the page name based on page ID; otherwise return 'No results'
     * @param int $pageId
     * @return string
     */
    public function getPageName($pageId) {
        if (!empty($pageId)) {
            return get_the_title($pageId);
        } else {
            return 'No results';
        }
    }


    /**
     * Return the page URL based on page ID
     * @param int $pageId
     * @return string
     */
    public function getPageUrl($pageId) {
        if (!empty($pageId)) {
            return get_permalink($pageId);
        } else {
            return '#';
        }
    }


    /**
     * Localize the plugin's scripts. Load only where & when needed
     * @param $hook
     */
    public function enqueueAdminScripts($hook) {

        if ($hook === 'tools_page_view-statistics') {
            wp_enqueue_style(__CLASS__, $this->getPluginViewURL() . 'usf.css', array(), $this->version, 'all' );
            wp_enqueue_script(__CLASS__, $this->getPluginViewURL() . 'list-header.js', array('jquery'), $this->version, FALSE);
        }
    }


}


$usfApp = new USF();