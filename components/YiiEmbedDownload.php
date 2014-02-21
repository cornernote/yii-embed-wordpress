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
        $this->output(__('Downloading') . ': ' . $yiiDownloadUrl, true);
        $downloaded = $this->downloadChunked($yiiDownloadUrl, $yiiZipFile, 1024, '.');
        if (!$downloaded) {
            throw new Exception(__('Failed to download.'));
        }
        $this->output(' ' . strtr(__('downloaded :size bytes.'), array(':size' => number_format($downloaded, 0, '', ','))), true);

        // unzip
        $this->output(__('Unzipping') . ': ' . $yiiZipFile);
        if (!$this->unzipEntryPath($yiiZipFile, $yiiPath, $yiiZipPath, true)) {
            throw new Exception(__('Failed to unzip.'));
        }
        $this->output(strtr(__('Downloaded Yii :version, Yii-Haw!'), array(':version' => YiiEmbed::yiiVersion(true))), true);

        // post cleanup
        rename($yiiFrameworkUnzipPath . '/framework/', $yiiFrameworkPath);
        $this->delete($yiiFrameworkUnzipPath);
        $this->delete($yiiZipFile);
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
     * @param string $outputMarker A string that will be output on each chunk.
     * @return bool|int
     */
    public function downloadChunked($inFile, $outFile, $chunkSize = 1024, $outputMarker = '')
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

        // Send the request to the server for the file
        $request = "GET {$parts['path']} HTTP/1.1\r\n";
        $request .= "Host: {$parts['host']}\r\n";
        $request .= "User-Agent: Mozilla/5.0\r\n";
        $request .= "Keep-Alive: 115\r\n";
        $request .= "Connection: keep-alive\r\n\r\n";
        fwrite($i_handle, $request);

        // Now read the headers from the remote server. We'll need to get the content length.
        $headers = array();
        while (!feof($i_handle)) {
            $line = fgets($i_handle);
            if ($line == "\r\n") break;
            $headers[] = $line;
        }

        // Look for location header and download the new location
        foreach ($headers as $header) {
            if (strpos(strtolower($header), 'location:') !== false) {
                return $this->downloadChunked(trim(substr($header, 9)), $outFile, $chunkSize, $outputMarker);
            }
        }

        // Look for the Content-Length header, and get the size of the remote file.
        $length = 0;
        foreach ($headers as $header) {
            if (stripos($header, 'Content-Length:') === 0) {
                $length = (int)str_replace('Content-Length: ', '', $header);
                break;
            }
        }

        // Start reading in the remote file, and writing it to the local file one chunk at a time.
        $cnt = 0;
        while (!feof($i_handle)) {
            // Download a chunk
            $buf = fread($i_handle, $chunkSize);
            $bytes = fwrite($o_handle, $buf);
            if ($bytes == false) {
                return false;
            }
            $cnt += $bytes;
            // We're done reading when we've reached the content length
            if ($cnt >= $length) break;
            // Output a marker to show progress
            $this->output($outputMarker);
        }

        fclose($i_handle);
        fclose($o_handle);
        return $cnt;
    }

}