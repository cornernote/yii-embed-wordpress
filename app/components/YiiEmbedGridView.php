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
 * YiiEmbedGridView
 *
 * @package yii-embed-wordpress
 */
class YiiEmbedGridView extends TbGridView
{

    /**
     * @var string
     */
    public $template = "{header}{summary}{pager}{gridButtons}{pageSelect}{gridActions}{multiActions}{items}{footer}";

    /**
     * @var string
     */
    public $templateLong = "{header}{summary}{pager}{gridButtons}{pageSelect}{gridActions}{multiActions}{items}{summary}{pager}{gridButtons}{pageSelect}{gridActions}{multiActions}{footer}";

    /**
     * @var string
     */
    public $type = 'striped condensed bordered';

    /**
     * @var array
     */
    public $multiActions = array();

    /**
     * @var array
     */
    public $gridActions = array();

    /**
     * @var array
     */
    public $gridButtons = array();

    /**
     * @var int
     */
    public $selectableRows = 1000;

    /**
     * @var int
     */
    public $defaultPageSize = 10;

    /**
     * @var array
     */
    public $pageSizeOptions = array(10, 100, 1000);

    /**
     * @var bool
     */
    public $saveUserPageSize = false;

    /**
     * @var array
     */
    public $pager = array('class' => 'YiiEmbedPager');

    /**
     * @var string
     */
    public $header;

    /**
     * @var string
     */
    public $footer;

    /**
     *
     */
    public function init()
    {
        // pager labels
        if (!isset($this->pager['firstPageLabel']))
            $this->pager['firstPageLabel'] = '<i class="icon-fast-backward"></i>';
        if (!isset($this->pager['lastPageLabel']))
            $this->pager['lastPageLabel'] = '<i class="icon-fast-forward"></i>';
        if (!isset($this->pager['nextPageLabel']))
            $this->pager['nextPageLabel'] = '<i class="icon-forward"></i>';
        if (!isset($this->pager['prevPageLabel']))
            $this->pager['prevPageLabel'] = '<i class="icon-backward"></i>';
        if (!isset($this->pager['maxButtonCount']))
            $this->pager['maxButtonCount'] = 5;
        if (!isset($this->pager['displayFirstAndLast']))
            $this->pager['displayFirstAndLast'] = true;

        // userPageSize drop down changed
        $this->setUserPageSize();

        // set pagination
        $this->dataProvider->pagination->pageSize = $this->getUserPageSize();

        // add checkbox when we have multiactions
        if ($this->multiActions) {
            $this->columns = CMap::mergeArray(array(array(
                'class' => 'CCheckBoxColumn',
            )), $this->columns);
        }

        parent::init();
    }

    /**
     *
     */
    public function registerClientScript()
    {
        parent::registerClientScript();

        if ($this->multiActions || $this->gridActions || $this->gridButtons) {
            //Yii::app()->clientScript->registerScriptFile(Yii::app()->dressing->getAssetsUrl() . '/js/jquery.form.js');
            // put the url from the button into the form action
            // handle submit form to capture the response into a modal
            Yii::app()->controller->beginWidget('YiiEmbedJavaScriptWidget', array('position' => CClientScript::POS_END));
            ?>
            <script type="text/javascript">
                var modalRemote = $('#modal-remote');

                // handle multiActions
                $('#<?php echo $this->id; ?>-form').on('change', '.multi-actions', function () {
                    var checked = false;
                    var action = $('#<?php echo $this->id; ?>-form').attr('action');
                    var url = $(this).val();
                    $(this).val('');
                    if (url) {
                        $('.select-on-check').each(function () {
                            if ($(this).is(':checked'))
                                checked = true;
                        });
                        if (checked) {
                            setupGridViewAjaxForm();
                            $('#<?php echo $this->id; ?>-form').attr('action', url).submit();
                        }
                        else {
                            alert('<?php echo Yii::t('dressing', 'No rows selected.'); ?>');
                        }
                    }
                });

                // handle gridActions
                $('#<?php echo $this->id; ?>-form').on('change', '.grid-actions', function () {
                    var action = $('#<?php echo $this->id; ?>-form').attr('action');
                    var url = $(this).val();
                    $(this).val('');
                    if (url) {
                        setupGridViewAjaxForm();
                        $('#<?php echo $this->id; ?>-form').attr('action', url).submit();
                    }
                });

                // handle gridButtons
                $('#<?php echo $this->id; ?>-form').on('click', '.gridButton', function () {
                    var action = $('#<?php echo $this->id; ?>-form').attr('action');
                    var url = $(this).val();
                    $(this).val('');
                    if (url) {
                        $('#<?php echo $this->id; ?>-form').attr('action', url).submit();
                    }
                });

                // handle form submission
                function setupGridViewAjaxForm() {
                    $('#<?php echo $this->id; ?>-form').ajaxForm({
                        beforeSubmit: function (response) {
                            if (!modalRemote.length) modalRemote = $('<div class="modal hide fade" id="modal-remote"></div>');
                            modalRemote.modalResponsiveFix();
                            modalRemote.touchScroll();
                            modalRemote.html('<div class="modal-header"><h3><?php echo Yii::t('dressing', 'Loading...'); ?></h3></div><div class="modal-body"><div class="modal-remote-indicator"></div>').modal();
                        },
                        success: function (response) {
                            modalRemote.html(response);
                            $(window).resize();
                            $('#modal-remote input:text:visible:first').focus();
                        },
                        error: function (response) {
                            modalRemote.children('.modal-header').html('<button type="button" class="close" data-dismiss="modal"><i class="icon-remove"></i></button><h3><?php echo Yii::t('dressing', 'Error!'); ?></h3>');
                            modalRemote.children('.modal-body').html(response);
                        }
                    });
                }
            </script>
            <?php
            Yii::app()->controller->endWidget();
        }
    }

    /**
     *
     */
    public function run()
    {
        if ($this->multiActions || $this->gridActions || $this->gridButtons) {
            echo CHtml::openTag('div', array(
                    'id' => $this->id . '-multi-checkbox',
                    'class' => 'multi-checkbox-table',
                )) . "\n";
            echo CHtml::beginForm('', 'POST', array(
                'id' => $this->id . '-form',
            ));
            echo CHtml::hiddenField('returnUrl', Yii::app()->returnUrl->getFormValue(true));
        }

        parent::run();

        if ($this->multiActions || $this->gridActions || $this->gridButtons) {
            echo CHtml::endForm();
            echo CHtml::closeTag('div');
        }
    }

    /**
     *
     */
    public function renderToggleFilters()
    {
        $js = "jQuery(document).on('click','.toggle-filters',function(){ jQuery(this).closest('.grid-view').find('.filters').toggle(); });";
        Yii::app()->clientScript->registerScript(__CLASS__ . '_toggle-filters', $js);
        echo '<i class="icon-search toggle-filters" title="' . Yii::t('dressing', 'Show Filters') . '"></i>';
    }

    /**
     *
     */
    public function renderPageSelect()
    {
        if (!$this->dataProvider->getItemCount())
            return;

        $label = Yii::t('dressing', 'per page');
        $options = array();
        foreach ($this->pageSizeOptions as $option) {
            $options[$option] = $option . ' ' . $label;
        }
        echo CHtml::dropDownList("userPageSize[{$this->id}]", $this->dataProvider->pagination->pageSize, $options, array(
            'onchange' => "$.fn.yiiGridView.update('{$this->id}',{data:{userPageSize:{" . str_replace('-', '_', $this->id) . ":$(this).val()}}})",
            'class' => 'page-size',
        ));
    }

    /**
     *
     */
    public function renderMultiActions()
    {
        if (!$this->dataProvider->getItemCount())
            return;
        if (!$this->multiActions)
            return;

        echo '<div class="form-multi-actions">';
        echo CHtml::dropDownList("multiAction[{$this->id}]", '', CHtml::listData($this->multiActions, 'url', 'name'), array(
            'empty' => Yii::t('dressing', 'with selected...'),
            'class' => 'multi-actions',
        ));
        echo '</div>';
    }

    /**
     *
     */
    public function renderGridActions()
    {
        if (!$this->dataProvider->getItemCount())
            return;
        if (!$this->gridActions)
            return;

        echo '<div class="form-grid-actions">';
        echo CHtml::dropDownList("gridAction[{$this->id}]", '', CHtml::listData($this->gridActions, 'url', 'name'), array(
            'empty' => Yii::t('dressing', 'with all matching rows...'),
            'class' => 'grid-actions',
        ));
        echo '</div>';
    }

    /**
     *
     */
    public function renderGridButtons()
    {
        if (!$this->gridButtons)
            return;
        echo '<div class="form-grid-buttons">';
        foreach ($this->gridButtons as $gridButton) {
            $class = isset($gridButton['class']) ? $gridButton['class'] . ' ' : '';
            $class .= 'btn gridButton';
            echo '<button class="' . $class . '" value="' . $gridButton['url'] . '">' . $gridButton['name'] . '</button> ';
        }
        echo '</div>';
    }

    /**
     * @return bool
     */
    public function getUserPageSize()
    {
        $key = 'YdGridView_userPageSize_' . str_replace('-', '_', $this->id);
        $user = Yii::app()->getUser();
        $size = 0;
        if (!$size && $this->saveUserPageSize && $user->user && $user->user->asa('EavBehavior'))
            $size = $user->user->getEavAttribute($key);
        if (!$size && $this->saveUserPageSize)
            $size = Yii::app()->user->getState($key, $this->defaultPageSize);
        if (!$size && isset($_GET['userPageSize'][str_replace('-', '_', $this->id)]))
            $size = $_GET['userPageSize'][str_replace('-', '_', $this->id)];
        if (!$size)
            $size = $this->defaultPageSize;
        return $size;
    }

    /**
     *
     */
    private function setUserPageSize()
    {
        if (!$this->saveUserPageSize || !isset($_GET['userPageSize']))
            return;

        $user = Yii::app()->getUser();
        foreach ($_GET['userPageSize'] as $type => $size) {
            $key = 'YdGridView_userPageSize_' . $type;
            $user->setState($key, (int)$size);
            if ($user->user && $user->user->asa('EavBehavior'))
                $user->user->setEavAttribute($key, (int)$size, true);
        }
        //unset($_GET['userPageSize']);
    }


    /**
     * Renders the pager.
     */
    public function renderPager()
    {
        if (!$this->enablePagination)
            return;

        $pager = array();
        $class = 'CLinkPager';
        if (is_string($this->pager))
            $class = $this->pager;
        elseif (is_array($this->pager)) {
            $pager = $this->pager;
            if (isset($pager['class'])) {
                $class = $pager['class'];
                unset($pager['class']);
            }
        }
        $pager['pages'] = $this->dataProvider->getPagination();

        if ($pager['pages']->getPageCount() > 0) {
            echo '<div class="' . $this->pagerCssClass . '">';
            $this->widget($class, $pager);
            echo '</div>';
        }
        else
            $this->widget($class, $pager);
    }

    /**
     *
     */
    public function renderHeader()
    {
        echo $this->header;
    }

    /**
     *
     */
    public function renderFooter()
    {
        echo $this->footer;
    }


}