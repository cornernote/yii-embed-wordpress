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
 * YiiEmbed
 *
 * Core static helper class for yii-embed-wordpress.
 *
 * @package yii-embed-wordpress
 */
class YiiEmbed
{

    /**
     * Initialize the Plugin Manager callbacks
     */
    public static function init()
    {
        // load language
        load_plugin_textdomain('yii-embed', false, basename(YII_EMBED_PATH) . '/languages');
        // runs when plugin is activated
        register_activation_hook(YII_EMBED_PATH . 'yii-embed.php', 'YiiEmbed::activation');
        // runs on plugin deactivation
        register_deactivation_hook(YII_EMBED_PATH . 'yii-embed.php', 'YiiEmbed::deactivation');
        // setup admin pages
        if (is_admin()) {
            require_once(YII_EMBED_PATH . 'includes/YiiEmbedAdmin.php');
            YiiEmbedAdmin::init();
        }
        // create Yii app
        if (YII_EMBED_YII_VERSION) {
            require_once(YII_EMBED_PATH . 'includes/Yii.php');
            Yii::init();
        }
    }

    /**
     * Callback when the plugin is activated
     */
    public static function activation()
    {
        // create or activate page
        $page = get_page_by_path('yii');
        if (!$page) {
            wp_insert_post(array(
                'post_title' => 'Yii',
                'post_content' => '[yii_embed_yii]',
                'post_type' => 'page',
                'post_status' => 'publish',
                'post_category' => array(1),
                'comment_status' => 'closed',
                'ping_status' => 'closed',
            ));
        }
        else {
            $page->post_status = 'publish';
            wp_update_post($page);
        }
        // add options
        delete_option('yii_embed');
        add_option('yii_embed', array('yii_path' => ''));
    }

    /**
     * Callback when the plugin is deactivated
     */
    public static function deactivation()
    {
        // trash the page
        $page = get_page_by_path('yii');
        if ($page)
            wp_delete_post($page->ID);
        // delete options
        delete_option('yii_embed');
    }

    /**
     * Initialize Yii and return Yii version
     *
     * @param bool $refresh
     * @return bool|string Yii version or false if Yii is not found
     */
    public static function yiiVersion($refresh = false)
    {
        static $yiiVersion;
        if ($yiiVersion !== null && !$refresh)
            return $yiiVersion;

        $yii_file = self::yiiPath($refresh) . '/framework/YiiBase.php';
        if (!file_exists($yii_file))
            return $yiiVersion = false;

        require_once($yii_file);
        YiiBase::setPathOfAlias('yii-embed', YII_EMBED_PATH . 'app');
        YiiBase::import('yii-embed.components.*');
        YiiBase::$enableIncludePath = false;
        return $yiiVersion = YiiBase::getVersion();
    }

    /**
     * Yii Path
     *
     * @param bool $refresh
     * @return string Yii path from the settings or default path if it is empty
     */
    public static function yiiPath($refresh = false)
    {
        static $yiiPath;
        if ($yiiPath !== null && !$refresh)
            return $yiiPath;

        $yiiEmbed = get_option('yii_embed');
        return $yiiPath = !empty($yiiEmbed['yii_path']) ? $yiiEmbed['yii_path'] : str_replace('\\', '/', YII_EMBED_PATH . 'yii');
    }

    /**
     * Yii Download URL
     *
     * @return string
     */
    public static function yiiDownloadUrl()
    {
        return 'https://github.com/yiisoft/yii/releases/download/1.1.14/yii-1.1.14.f0fee9.zip';
    }

    /**
     * Registers the css and js scripts
     * @return string
     */
    public static function registerScripts()
    {
        // only run if Yii was found
        if (!YII_EMBED_YII_VERSION)
            return;
        // register css/js files
        $assetsUrl = self::assetsUrl();
        Yii::app()->clientScript->registerCssFile($assetsUrl . '/css/yii-embed.css');
        Yii::app()->clientScript->registerCssFile($assetsUrl . '/js/yii-embed.js');
    }

    /**
     * Returns the assets url
     * @return string
     */
    public static function assetsUrl()
    {
        static $assetsUrl;
        if ($assetsUrl !== null)
            return $assetsUrl;
        // only run if Yii was found
        if (!YII_EMBED_YII_VERSION)
            return $assetsUrl = false;
        // publish the assets
        return $assetsUrl = Yii::app()->assetManager->publish(Yii::getPathOfAlias('yii-embed.assets'), true, -1, YII_DEBUG);
    }

}