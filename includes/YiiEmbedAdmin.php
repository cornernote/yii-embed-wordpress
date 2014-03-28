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
 * YiiEmbedAdmin
 *
 * WordPress administration configuration.
 *
 * @package yii-embed-wordpress
 */
class YiiEmbedAdmin
{

    /**
     * Initialize the Admin callbacks and messages.
     */
    public static function init()
    {
        // add action for notices
        add_action('yii_embed_admin_notice', 'YiiEmbedAdmin::notice', 10, 2);

        // message if yii is not found
        if (!YII_EMBED_YII_VERSION) {
            $message = strtr(__('<p><b>Could not find Yii Framework.</b><br/>Visit <a href=":settings_href"><strong>Settings &gt; Yii Embed</strong></a> to configure the path or download the framework using one of the following methods:</p><p class="submit">:automatic_download :manual_download</p>'), array(
                ':settings_href' => get_admin_url() . 'admin.php?page=yii_embed_settings',
                ':automatic_download' => '<a href="' . YII_EMBED_URL . '/yii/download.php" class="button-primary" onclick="return confirm(\'Do you want to download and install Yii Framework to:\n' . YiiEmbed::yiiPath() . '\')">' . __('Automatic Download') . '</a>',
                ':manual_download' => '<a href="' . YiiEmbed::yiiDownloadUrl() . '" onclick="return confirm(\'After downloading, please unzip the Yii &quot;framework/&quot; folder into:\n' . YiiEmbed::yiiPath() . '\');" class="button">' . __('Manual Download') . '</a>',
            ));
            do_action('yii_embed_admin_notice', $message, 'error');
        }

        // register settings page
        require_once(YII_EMBED_PATH . 'includes/YiiEmbedSettings.php');
        new YiiEmbedSettings();
    }

    /**
     * Callback to handle Yii controller on WordPress administration page.
     */
    public static function page()
    {
        // hide the submenu
        Yii::app()->clientScript->registerCss('hide-submenu', '.wp-has-current-submenu .wp-submenu{ display:none; }');
        // get the route
        $page = isset($_GET['page']) ? $_GET['page'] : null;
        $route = str_replace('_', '/', str_replace('yii_embed_', '', $page));
        // try run controller
        try {
            ob_start();
            Yii::app()->runController($route);
            echo ob_get_clean();
        } catch (CHttpException $e) {
            // got an exception, let wordpress handle the page
        }
    }

    /**
     * Callback for yii_embed_admin_notice.
     *
     * Example:
     * do_action('yii_embed_admin_notice', __('<p>Hello world!</p>'));
     * do_action('yii_embed_admin_notice', __('<p>An error occurred.</p>'), 'error');
     *
     * @param string $message HTML message to display on the admin page.
     * @param string $class CSS class to wrap the message in, either "updated" or "error".
     */
    public static function notice($message, $class = 'updated')
    {
        add_action('admin_notices', create_function('', 'echo \'<div class="' . addslashes($class) . '">' . str_replace("'", "\\'", $message) . '</div>\';'));
    }

}
