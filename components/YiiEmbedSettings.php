<?php
/**
 * Copyright (c) 2014, Mr PHP <info@mrphp.com.au>
 * All rights reserved.
 *  _____     _____ _____ _____
 * |     |___|  _  |  |  |  _  |
 * | | | |  _|   __|     |   __|
 * |_|_|_|_| |__|  |__|__|__|
 *
 *
 * Redistribution and use in source and binary forms, with or without modification,
 * are permitted provided that the following conditions are met:
 *
 * * Redistributions of source code must retain the above copyright notice, this
 *   list of conditions and the following disclaimer.
 *
 * * Redistributions in binary form must reproduce the above copyright notice, this
 *   list of conditions and the following disclaimer in the documentation and/or
 *   other materials provided with the distribution.
 *
 * * Neither the name of the organization nor the names of its
 *   contributors may be used to endorse or promote products derived from
 *   this software without specific prior written permission.
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS" AND
 * ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED
 * WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE
 * DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT HOLDER OR CONTRIBUTORS BE LIABLE FOR
 * ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES
 * (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES;
 * LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON
 * ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT
 * (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS
 * SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
 */

/**
 * YiiEmbedSettings
 *
 * Manages the setup and callbacks for the administration options page.
 *
 * @package yii-embed-wordpress
 */
class YiiEmbedSettings
{

    /**
     * @var array values to be used in the fields callbacks.
     */
    private $options;

    /**
     * Add callbacks to add admin menu and setup options form.
     */
    public function __construct()
    {
        // add admin menu
        add_action('admin_menu', array($this, 'admin_menu'));

        // setup options form
        add_action('admin_init', array($this, 'admin_init'));
    }

    /**
     * Callback to add the admin menu page under "WPAdmin > Settings".
     */
    public function admin_menu()
    {
        // add options page to the menu
        add_options_page('Yii Embed Settings', 'Yii Embed', 'manage_options', 'yii-embed-settings', array($this, 'options_page'));
    }

    /**
     * Callback to register and add sections and settings
     */
    public function admin_init()
    {
        // show version if yii was loaded
        if (YiiEmbed::yiiVersion()) {
            do_action('yii_embed_admin_notice', strtr(__('<p>Detected Yii Framework :version, Yii-Haw!</p>'), array(':version' => YiiEmbed::yiiVersion())));
        }

        // register the setting and validation callback
        register_setting('yii_embed', 'yii_embed', array($this, 'validate'));

        // add the settings section
        add_settings_section('yii_embed', '', array($this, 'settings_section_yii_embed'), 'yii-embed-settings');

        // add the yii_path field
        add_settings_field('yii_embed_yii_path', __('Yii Path'), array($this, 'settings_field_yii_path'), 'yii-embed-settings', 'yii_embed');
    }

    /**
     * Validate and sanitize each setting that is entered into the options page.
     * @param array $input Contains all settings fields as array keys
     * @return array
     */
    public function validate($input)
    {
        // validate yii_path
        if (!empty($input['yii_path']))
            $input['yii_path'] = $this->validate_yii_path($input['yii_path']);

        // return sanitized input
        return $input;
    }

    /**
     * Validate the yii_path that is entered into the options page.
     * @param string $path
     * @return string
     */
    public function validate_yii_path($path)
    {
        // trim whitespace
        $path = untrailingslashit(trim($path));

        // check if the path exists
        if (!file_exists($path)) {
            add_settings_error('yii_embed_yii_path', 'yii_path_not_file_exists', __('The Yii Path entered does not exist.'));
            return $path;
        }

        // check if the path is a directory
        if (!is_dir($path)) {
            add_settings_error('yii_embed_yii_path', 'yii_path_not_is_dir', __('The Yii Path entered is not a directory.'));
            return $path;
        }

        // check if framework/yii.php exists in the path
        if (!file_exists($path . '/framework/yii.php')) {
            add_settings_error('yii_embed_yii_path', 'yii_path_not_yii_file_exists', __('The Yii Path entered does not contain framework/yii.php.'));
            return $path;
        }

        // check the Yii version
        require_once($path . '/framework/yii.php');
        if (!method_exists('Yii', 'getVersion')) {
            add_settings_error('yii_embed_yii_path', 'yii_path_not_yii_version', __('The framework/yii.php does not appear to be a valid Yii.'));
            return $path;
        }

        // yii_path is valid
        return $path;
    }

    /**
     * Callback to display the options page.
     */
    public function options_page()
    {
        // load the options
        $this->options = get_option('yii_embed');

        // begin the options form
        echo '<div class="wrap">';
        echo '<h2>Yii Embed Settings</h2>';
        echo '<form method="post" action="options.php">';

        // render settings fields
        settings_fields('yii_embed');
        do_settings_sections('yii-embed-settings');

        // render submit button
        submit_button(__('Save Changes'), 'primary', 'submit', false);

        // end the options form
        echo '</form>';
        echo '</div>';
    }

    /**
     * Callback to add the section settings
     */
    public static function settings_section_yii_embed()
    {
        //echo __('Yii Embed Settings:');
    }

    /**
     * Callback to add the yii_path setting field
     */
    public function settings_field_yii_path()
    {
        echo strtr('<input type="text" id="yii_embed_yii_path" name="yii_embed[yii_path]" class="regular-text" value=":value" /><p class="description">:description :default_path</p>', array(
            ':value' => isset($this->options['yii_path']) ? esc_attr($this->options['yii_path']) : '',
            ':description' => __('Full path the the folder that contains Yii\'s "framework" folder.'),
            ':default_path' => empty($this->options['yii_path']) ? '<br/>' . __('Default:') . ' ' . YiiEmbed::yiiPath() : '',
        ));
    }

}
