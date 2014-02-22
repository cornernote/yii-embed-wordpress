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
 * YiiEmbedReturnUrl
 *
 * Wrapper to maintain state of a Return Url
 * Allows the user to have multiple tabs open, each tab will handle its own Return Url passed in via the GET or POST params.
 *
 * @package yii-embed-wordpress
 */
class YiiEmbedReturnUrl extends CApplicationComponent
{

    /**
     * @var string The key used in GET and POST requests for the Return Url.
     */
    public $requestKey = 'returnUrl';

    /**
     * Get url from submitted data or the current page url
     * for usage in a hidden form element
     *
     * @usage
     * in views/your_page.php
     * <pre>
     * CHtml::hiddenField('returnUrl', Yii::app()->returnUrl->getFormValue());
     * </pre>
     *
     * @param bool $currentPage
     * @param bool $encode
     * @return null|string
     */
    public function getFormValue($currentPage = false, $encode = false)
    {
        if ($currentPage)
            $url = $encode ? $this->urlEncode(Yii::app()->getRequest()->getUrl()) : Yii::app()->getRequest()->getUrl();
        else
            $url = $this->getUrlFromSubmitFields();
        return $url;
    }

    /**
     * Get url from submitted data or the current page url
     * for usage in a link
     *
     * @usage
     * in views/your_page.php
     * <pre>
     * CHtml::link('my link', array('test/form', 'returnUrl' => Yii::app()->returnUrl->getLinkValue(true)));
     * </pre>
     *
     * @param bool $currentPage
     * @return string
     */
    public function getLinkValue($currentPage = false)
    {
        return $this->encodeLinkValue($currentPage ? Yii::app()->request->getUrl() : $this->getUrlFromSubmitFields());
    }

    /**
     * Get url from submitted data or the current page url
     * for usage in a link
     *
     * @usage
     * in views/your_page.php
     * <pre>
     * CHtml::link('my link', array('test/form', 'returnUrl' => Yii::app()->returnUrl->encodeLinkValue($item->getUrl())));
     * </pre>
     *
     * @param $url
     * @return string
     */
    public function encodeLinkValue($url)
    {
        return $this->urlEncode($url);
    }

    /**
     * Get url from submitted data or session
     *
     * @usage
     * in YourController::actionYourAction()
     * <pre>
     * $this->redirect(Yii::app()->returnUrl->getUrl());
     * </pre>
     *
     * @param bool|mixed $altUrl
     * @return mixed|null
     */
    public function getUrl($altUrl = false)
    {
        $url = $this->getUrlFromSubmitFields();
        // alt url or current page
        if (!$url && $altUrl)
            $url = $altUrl;
        return $url ? $url : Yii::app()->homeUrl;
    }

    /**
     * Get the url from the request, decodes if needed
     *
     * @return null|string
     */
    private function getUrlFromSubmitFields()
    {
        $requestKey = $this->requestKey;
        $url = isset($_GET[$requestKey]) ? $_GET[$requestKey] : (isset($_POST[$requestKey]) ? $_POST[$requestKey] : false);
        return isset($_GET[$requestKey]) ? $this->urlDecode($url) : $url;
    }

    /**
     * @param $input
     * @return string
     */
    private function urlEncode($input)
    {
        $key = uniqid();
        Yii::app()->cache->set($this->requestKey . '.' . $key, $input);
        return $key;
    }

    /**
     * @param $key
     * @return string
     */
    private function urlDecode($key)
    {
        return Yii::app()->cache->get($this->requestKey . '.' . $key);
    }

}
