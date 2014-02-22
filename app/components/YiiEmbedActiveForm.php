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
 * YiiEmbedActiveForm
 *
 * @package yii-embed-wordpress
 */
class YiiEmbedActiveForm extends TbActiveForm
{

    /**
     * @var string
     */
    public $layout = TbHtml::FORM_LAYOUT_HORIZONTAL;

    /**
     * @var YiiEmbedActiveFormModel
     */
    public $model;

    /**
     * @var
     */
    public $returnUrl;

    /**
     * @var
     */
    public $askToSaveWork;

    /**
     * Initializes the widget.
     * This renders the form open tag.
     */
    public function init()
    {
        // modal-form for popups
        if (Yii::app()->request->isAjaxRequest) {
            if (!isset($this->htmlOptions['class'])) {
                $this->htmlOptions['class'] = '';
            }
            $this->htmlOptions['class'] .= ' modal-form';
        }

        // get a model we can use for this form
        Yii::import('YiiEmbedActiveFormModel');
        $this->model = $this->model ? $this->model : new YiiEmbedActiveFormModel();

        // init the parent (output <form> tag)
        parent::init();

        // output the return url
        if ($this->returnUrl !== false)
            echo CHtml::hiddenField('returnUrl', $this->returnUrl ? $this->returnUrl : Yii::app()->returnUrl->getFormValue());

        // ask to save work
        if ($this->askToSaveWork)
            Yii::app()->controller->widget('YiiEmbedAskToSaveWork', array('watchElement' => '#setting-form :input', 'message' => Yii::t('dressing', 'Please save before leaving the page.')));

    }

    /**
     * @return string
     */
    public function beginModalWrap()
    {
        // more modal stuff
        if (Yii::app()->getRequest()->isAjaxRequest) {
            return '<div class="modal-body">';
        }
        return '';
    }

    /**
     * @return string
     */
    public function endModalWrap()
    {
        // more modal stuff
        if (Yii::app()->getRequest()->isAjaxRequest) {
            return '</div>';
        }
        return '';
    }

    /**
     * @return string
     */
    public function getSubmitRowClass()
    {
        return Yii::app()->getRequest()->isAjaxRequest ? 'modal-footer' : 'form-actions';
    }

    /**
     * @param $buttonClass
     * @param null $gridId
     */
    public function searchToggle($buttonClass, $gridId = null)
    {
        $script = "$('." . $buttonClass . "').click(function(){ $('#" . $this->id . "').toggle(); });";
        if ($gridId) {
            $script .= "
                $('#" . $this->id . "').submit(function(){
                    $.fn.yiiGridView.update('" . $gridId . "', {url: $(this).attr('action'),data: $(this).serialize()});
                    return false;
                });
            ";
        }
        Yii::app()->clientScript->registerScript($this->id . '-searchToggle', $script, CClientScript::POS_READY);
    }


    /**
     * @param $ids
     * @internal param null $id
     * @return string
     */
    public function getGridIdHiddenFields($ids)
    {
        $inputs = array();
        foreach ($ids as $id) {
            $inputs[] = CHtml::hiddenField('hidden-sf-grid_c0[]', $id);
        }
        return implode("\n", $inputs);
    }

    /**
     * @param null $label
     * @param array $options
     * @return string
     */
    public function getSubmitButtonRow($label = null, $options = array())
    {
        if (!isset($options['color']))
            $options['color'] = TbHtml::BUTTON_COLOR_PRIMARY;
        return CHtml::tag('div', array('class' => $this->getSubmitRowClass()), TbHtml::submitButton($label, $options));
    }

}