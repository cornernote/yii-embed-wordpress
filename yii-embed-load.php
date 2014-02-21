<?php
/**
 * Plugin Name: Yii Embed
 * Plugin URI: https://github.com/cornernote/yii-embed-wordpress/
 * Description: Yii embedded into WordPress.
 * Version: 1.0.0
 * Author: Mr PHP
 * Author URI: http://mrphp.com.au
 * License: BSD-3-Clause
 */

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

// do not allow direct entry here
if (!function_exists('wp')) {
    echo 'Yii Embed cannot be called directly.';
    exit;
}

// define constants
define('YII_EMBED_VERSION', '1.0.0');
define('YII_EMBED_URL', plugin_dir_url(__FILE__));
define('YII_EMBED_PATH', __DIR__ . '/');

// load YiiEmbed
require_once(YII_EMBED_PATH . 'components/YiiEmbed.php');

// add default options
add_option('yii_embed', array(
    'yii_path' => '',
));

// load language
load_plugin_textdomain('yii-embed', false, basename(YII_EMBED_PATH) . '/languages');

// setup admin pages
if (is_admin()) {

    // add action for notices
    add_action('yii_embed_admin_notice', 'YiiEmbed::admin_notice', 10, 2);

    // verify yii path
    if (!YiiEmbed::yiiVersion()) {
        $message = strtr(__('<p><b>Could not find Yii Framework.</b><br/>Visit <a href=":settings_href"><strong>Settings &gt; Yii Embed</strong></a> to configure the path or download the framework using one of the following methods:</p><p class="submit">:automatic_download :manual_download</p>'), array(
            ':settings_href' => get_admin_url() . 'admin.php?page=yii-embed-settings',
            ':automatic_download' => '<a href="' . WP_CONTENT_URL . '/plugins/yii-embed-wordpress/yii/download.php" class="button-primary" onclick="return confirm(\'Do you want to download and install Yii Framework to:\n' . YiiEmbed::yiiPath() . '\n\nIf you proceed please allow upto 10 minutes for the request to complete.\')">' . __('Automatic Download') . '</a>',
            ':manual_download' => '<a href="' . YiiEmbed::yiiDownloadUrl() . '" onclick="return confirm(\'After downloading, please unzip the Yii &quot;framework/&quot; folder into:\n' . YiiEmbed::yiiPath() . '\');" class="button">' . __('Manual Download') . '</a>',
        ));
        do_action('yii_embed_admin_notice', $message, 'error');
    }

    // register settings page
    require_once(YII_EMBED_PATH . 'components/YiiEmbedSettings.php');
    new YiiEmbedSettings();

    // register download page
    require_once(YII_EMBED_PATH . 'components/YiiEmbedDownload.php');
    new YiiEmbedDownload();
}
