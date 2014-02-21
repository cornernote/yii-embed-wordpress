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
     * YiiEmbedApplication
     *
     * @return YiiEmbedApplication
     */
    public static function app()
    {
        static $app;
        if ($app !== null)
            return $app;

        return $app = Yii::createApplication('YiiEmbedApplication', YII_EMBED_PATH . 'config/main.php');
    }

    /**
     * Yii Version
     *
     * @param bool $refresh
     * @return bool|string Yii version or false if Yii is not found
     */
    public static function yiiVersion($refresh = false)
    {
        static $yiiVersion;
        if ($yiiVersion !== null && !$refresh)
            return $yiiVersion;

        $yii_file = self::yiiPath($refresh) . '/framework/yii.php';
        if (!file_exists($yii_file))
            return $yiiVersion = false;

        require_once($yii_file);
        Yii::setPathOfAlias('yii-embed', dirname(__DIR__));
        Yii::import('yii-embed.components.*');
        Yii::$enableIncludePath = false;
        return $yiiVersion = Yii::getVersion();
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

}