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
 * YiiEmbedApplication
 *
 * YiiEmbedAskToSaveWork will ask for a confirmation to user before leaving the page after they make a change to the form.
 *
 * USAGE:
 * <pre>
 * $this->widget('widgets.AskToSaveWork', array(
 *     'watchElement' => '#my-form :input',
 *     'message' => Yii::t('app', 'Please save before leaving the page')
 * ));
 * </pre>
 *
 * @package yii-embed-wordpress
 */
class YiiEmbedAskToSaveWork extends CWidget
{

    /**
     * @var String Message to show to user preventing exit the page
     */
    public $message;

    /**
     * @var String input element (ex. #Page_title, .Page_description) to watch,
     * allowing to us know if user is already editing or not (avoiding unnecesary messages)
     */
    public $watchElement;

    /**
     *
     */
    public function init()
    {
        parent::init();
        $this->message = isset($this->message) ? $this->message : Yii::t('messages', "Please save before leaving the page");
    }

    /**
     *
     */
    public function run()
    {
        parent::run();
        $js = '';

        if (isset($this->watchElement)) {
            $js .= "$('$this->watchElement').one('change', function() {";
        }

        $js .= "$(window).bind('beforeunload', function() {
                return \"" . $this->message . "\" ;
            });";

        $js .= "$(':submit').bind('click', function(){
                window.onbeforeunload = function(){};
                $(window).unbind('beforeunload');
            });";

        if (isset($this->watchElement)) {
            $js .= "});";
        }

        Yii::app()->clientScript->registerCoreScript('jquery');
        Yii::app()->clientScript->registerScript($this->getId(), $js, CClientScript::POS_READY);
    }
}

