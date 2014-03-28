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
 * YiiEmbedDownload
 *
 * Download and unzip the Yii Framework.
 */
class YiiEmbedDownload
{

    /**
     * Creates a static instance of the class.
     *
     * @return YiiEmbedDownload
     */
    public static function init()
    {
        return new YiiEmbedDownload();
    }

    /**
     * Download and unzip the Yii Framework.
     *
     * @throws Exception
     */
    public function download()
    {
        // header
        $this->output('<style>body{ font-family: Arial, "Helvetica Neue", Helvetica, sans-serif; }</style>');
        $this->output('<h1>' . __('Yii Framework Downloader') . '</h1>');

        // do the download
        if (!empty($_GET['force']) || !YiiEmbed::yiiVersion()) {

            // setup variables
            $yiiPath = YiiEmbed::yiiPath();
            $yiiDownloadUrl = YiiEmbed::yiiDownloadUrl();
            $pathinfo = pathinfo($yiiDownloadUrl);
            $yiiFrameworkPath = $yiiPath . '/framework/';
            $yiiFrameworkUnzipPath = $yiiPath . '/' . $pathinfo['filename'];
            $yiiZipFile = $yiiPath . '/' . $pathinfo['basename'];
            $yiiZipPath = $pathinfo['filename'] . '/framework/';

            // pre cleanup
            $this->delete($yiiFrameworkPath);
            $this->delete($yiiFrameworkUnzipPath);
            $this->delete($yiiZipFile);

            // download
            $this->output('<h2>' . __('Downloading') . '</h2><b>' . $yiiDownloadUrl . '</b> - ');
            $downloaded = $this->downloadChunked($yiiDownloadUrl, $yiiZipFile);
            if (!$downloaded) {
                throw new Exception(__('Failed to download.'));
            }

            // unzip
            $this->output('<h2>' . __('Unzipping') . '</h2><b>' . $yiiZipFile . '</b> - ');
            $files = $this->unzipEntryPath($yiiZipFile, $yiiPath, $yiiZipPath);
            if (!$files) {
                throw new Exception(__('Failed to unzip.'));
            }
            $this->output(count($files) . ' ' . __('files'), true);

            // post cleanup
            rename($yiiFrameworkUnzipPath . '/framework/', $yiiFrameworkPath);
            $this->delete($yiiFrameworkUnzipPath);
            $this->delete($yiiZipFile);

            // output yii version
            $this->output('<h2>' . __('Success!') . '</h2>');
            $this->output(strtr(__('Yii Framework version :version is installed, Yii-Haw!'), array(':version' => YiiEmbed::yiiVersion(true))));
        }
        // already downloaded
        else {
            $this->output('<h2>' . strtr(__('Yii Framework :version is already installed'), array(':version' => YiiEmbed::yiiVersion())) . '</h2>');
            $this->output('<a href="download.php?force=true">' . __('Force Download') . '</a> | ');
        }
        // link back to admin
        $this->output('<a href="' . get_admin_url() . 'options-general.php?page=yii_embed_settings">' . __('Return to WordPress') . '</a>');
    }

    /**
     * Outputs a message and flushes buffers.
     *
     * @param $message
     * @param bool $newLine
     */
    public function output($message, $newLine = false)
    {
        echo $message;
        if ($newLine)
            echo '<br/>';
        echo str_pad('', 4096);
        ob_flush();
        flush();
    }

    /**
     * Unzips a single folder from a zip archive
     *
     * @param string $zipFile The path to the zip archive file.
     * @param string $extractPath The path to extract the zip files to.
     * @param string $entryPath The path within the zip archive to extract.
     * @param bool $outputFiles Set to true to output the extracted files.
     * @return array|bool List of files that were extracted, or false on error.
     */
    public function unzipEntryPath($zipFile, $extractPath, $entryPath, $outputFiles = false)
    {
        $files = array();
        $zip = new ZipArchive;
        if ($zip->open($zipFile)) {
            for ($i = 0; $i < $zip->numFiles; $i++) {
                $file = $zip->getNameIndex($i);
                if (strpos($file, $entryPath) !== false) {
                    $files[] = $file;
                    if ($outputFiles) {
                        $this->output($file, true);
                    }
                }
            }
            if (!$zip->extractTo($extractPath, $files)) {
                $zip->close();
                return false;
            }
            $zip->close();
            return $files;
        }
        return false;
    }

    /**
     * Remove a directory and all its contents.
     *
     * @param $path
     */
    public function delete($path)
    {
        if (is_dir($path)) {
            foreach (scandir($path) as $object) {
                if ($object != '.' && $object != '..') {
                    if (is_dir($path . '/' . $object))
                        $this->delete($path . '/' . $object);
                    else
                        unlink($path . '/' . $object);
                }
            }
            rmdir($path);
        }
        elseif (file_exists($path)) {
            unlink($path);
        }
    }

    /**
     * Copy remote file over HTTP one small chunk at a time.
     *
     * @param string $inFile The full URL to the remote file.
     * @param string $outFile The path where to save the file.
     * @param int $chunkSize The size of the chunks in bytes.
     * @param string $outputMarker A string that will be output on each chunk, or "%" to output the percentage.
     * @return bool|int
     */
    public function downloadChunked($inFile, $outFile, $chunkSize = 1024000, $outputMarker = '%')
    {
        // parse_url breaks a part a URL into it's parts, i.e. host, path, query string, etc.
        $parts = parse_url($inFile);
        $i_handle = fsockopen(($parts['scheme'] == 'https' ? 'tls://' : '') . $parts['host'], $parts['scheme'] == 'https' ? 443 : 80, $errstr, $errcode, 5);
        $o_handle = fopen($outFile, 'wb');
        if ($i_handle == false || $o_handle == false) {
            return false;
        }
        if (!empty($parts['query'])) {
            $parts['path'] .= '?' . $parts['query'];
        }

        // send the request to the server for the file
        $request = "GET {$parts['path']} HTTP/1.1\r\n";
        $request .= "Host: {$parts['host']}\r\n";
        $request .= "User-Agent: Mozilla/5.0\r\n";
        $request .= "Keep-Alive: 115\r\n";
        $request .= "Connection: keep-alive\r\n\r\n";
        fwrite($i_handle, $request);

        // read the headers from the remote server
        $headers = array();
        while (!feof($i_handle)) {
            $line = fgets($i_handle);
            if ($line == "\r\n") break;
            $headers[] = $line;
        }

        // look for Location header and download the new location
        foreach ($headers as $header) {
            if (stripos($header, 'Location:') !== false) {
                return $this->downloadChunked(trim(substr($header, 9)), $outFile, $chunkSize, $outputMarker);
            }
        }

        // look for the Content-Length header, and get the size of the remote file
        $length = 0;
        foreach ($headers as $header) {
            if (stripos($header, 'Content-Length:') === 0) {
                $length = (int)str_replace('Content-Length: ', '', $header);
                break;
            }
        }

        // output the percentage placeholder
        if ($outputMarker == '%')
            $this->output('<span id="download-percent">0.00</span>% - <span id="download-bytes">0</span> of ' . number_format($length, 0) . ' ' . __('bytes') . '<br/>');

        // start reading in the remote file, and writing it to the local file one chunk at a time
        $cnt = 0;
        while (!feof($i_handle)) {
            // Download a chunk
            $buf = fread($i_handle, $chunkSize);
            $bytes = fwrite($o_handle, $buf);
            if ($bytes == false) {
                return false;
            }
            $cnt += $bytes;
            // reached the content length, done reading
            if ($cnt >= $length) break;
            // output a marker to show progress
            if ($outputMarker == '%')
                $this->output('<script>document.getElementById("download-bytes").innerHTML="' . number_format($cnt) . '";document.getElementById("download-percent").innerHTML="' . number_format($cnt / $length * 100, 2) . '";</script>');
            elseif ($outputMarker)
                $this->output($outputMarker);
        }

        // set the final progress
        if ($outputMarker == '%')
            $this->output('<script>document.getElementById("download-bytes").innerHTML="' . number_format($cnt) . '";document.getElementById("download-percent").innerHTML="' . number_format($cnt / $length * 100, 2) . '";</script>');

        fclose($i_handle);
        fclose($o_handle);
        return $cnt;
    }

}