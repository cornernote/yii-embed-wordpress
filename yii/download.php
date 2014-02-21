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


/** no theme */
define('WP_USE_THEMES', false);

// load wp-load
$path = realpath(__DIR__ . '/../../../../');
while ($path) {
    if (file_exists($path . '/wp-load.php')) {
        require_once($path . '/wp-load.php');
        break;
    }
    if ($path == dirname($path))
        break;
    $path = dirname($path);
}

// load wordpress
if (!function_exists('wp')) {
    throw new Exception('Could not load wordpress.');
}

// check access
if (!current_user_can('manage_options')) {
    throw new Exception(__('You do not have sufficient permissions to access this page.'));
}

// increase execution time
set_time_limit(60 * 10);

// disable gzip to ensure buffers can be flushed
@apache_setenv('no-gzip', 1);
@ini_set('zlib.output_compression', 0);

// load classes
require_once(dirname(__DIR__) . '/components/YiiEmbed.php');
require_once(dirname(__DIR__) . '/components/YiiEmbedDownload.php');

// do the download
YiiEmbedDownload::init()->download();