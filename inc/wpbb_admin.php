<?php

class WPPHPBBU_AdminPanel
{
    /**
     * Class Constructor
     */
    function WPPHPBBU_AdminPanel()
    {
        add_menu_page(
            'WP phpBB Unicorn',
            'WP phpBB Unicorn',
            'activate_plugins',
            'wpphpbbu',
            array(
                $this,
                'WPPHPBBU_AdminPage'
            ),
            null,
            71
        );

        add_submenu_page(
            'wpphpbbu',
            'WP phpBB Unicorn ' . __('settings', 'wpphpbbu'),
            __('Settings', 'wpphpbbu'),
            'activate_plugins',
            'wpphpbbu_settings',
            array(
                $this,
                'WPPHPBBU_SettingsPage'
            )
        );


    }

    function WPPHPBBU_AdminPage()
    {
        do_action('wpphpbbu_before_admin_dashboard');
        require_once(__DIR__.'/admin/dashboard.php');
        do_action('wpphpbbu_after_admin_dashboard');
    }

    function WPPHPBBU_SettingsPage()
    {
        do_action('wpphpbbu_before_admin_settings');

        if(isset($_POST['action']) && $_POST['action'] == 'update')
        {
            $e = new WP_Error();

            if(!wp_verify_nonce($_POST['_wpnonce'], 'wpphpbbu_settings_page'))
            {
                $e->add('access_denied', __('You submition does not meet the WordPress security level.', 'wpphpbbu'));
            }
            else
            {
              $wpphpbbu_path = stripslashes($_POST['wpphpbbu_path']);
              $wpphpbbu_url = stripslashes($_POST['wpphpbbu_url']);
              $wpphpbbu_post_posts = isset($_POST['wpphpbbu_post_posts']) ? 'yes' : 'no';
              $wpphpbbu_post_locked = isset($_POST['wpphpbbu_post_locked']) ? 'yes' : 'no';

              update_option('wpphpbbu_path', $wpphpbbu_path);
			        $is_path = wpphpbbu\Path::is_path_ok();
              if(!$is_path)
              {
                  $e->add('file_not_exists', __('The file config.php does not exists in the path you have enter', 'wpphpbbu'));
			        }
      				update_option('wpphpbbu_path_ok', $is_path);
              update_option('wpphpbbu_url',$wpphpbbu_url);


      				do_action('wpphpbbu_changed');
              update_option('wpphpbbu_post_posts', $wpphpbbu_post_posts);
              update_option('wpphpbbu_post_locked', $wpphpbbu_post_locked);
           }
        }

        $wpphpbbu_path = trim(get_option('wpphpbbu_path', ABSPATH . 'phpbb3/config.php'));
        $wpphpbbu_url = trim(get_option('wpphpbbu_url', ''));
        $wpphpbbu_post_posts = trim(get_option('wpphpbbu_post_posts', 'yes'));
        $wpphpbbu_post_locked = trim(get_option('wpphpbbu_post_locked', 'yes'));

        require_once(__DIR__.'/admin/settings.php');

        do_action('wpphpbbu_after_admin_settings');
    }


}

$wpphpbbu_admin = null;

function create_admin_menu()
{
    global $wpphpbbu_admin;
    $wpphpbbu_admin = new WPPHPBBU_AdminPanel();
}

add_action('admin_menu', 'create_admin_menu');

?>
