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
Yii::import('zii.widgets.grid.CDataColumn');

/**
 * YiiEmbedDropdownColumn
 *
 * Your model must define getUrl(), or behave as YdLinkBehavior:
 * <pre>
 * public function getUrl($action = 'view', $params = array())
 * {
 *     return array_merge(array('/controllerName/' . $action, 'id' => $this->id), (array)$params);
 * }
 * </pre>
 *
 * If you would like a dropdown menu, your model should also define getMenuLinks():
 * <pre>
 * public function getMenuLinks()
 * {
 *     return array(
 *         array('label' => Yii::t('app', 'Update'), 'url' => $this->getUrl('update')),
 *     );
 * }
 * </pre>
 *
 * @package yii-embed-wordpress
 */
class YiiEmbedDropdownColumn extends TbDataColumn
{

    /**
     *
     */
    function init()
    {
        $this->type = 'raw';
        $this->htmlOptions['nowrap'] = 'nowrap';
        $this->htmlOptions['class'] = isset($this->htmlOptions['class']) ? $this->htmlOptions['class'] . ' dropdown-column' : 'dropdown-column';
    }

    /**
     * Renders the data cell content.
     *
     * @param integer $row the row number (zero-based)
     * @param CActiveRecord $data the data associated with the row
     */
    protected function renderDataCellContent($row, $data)
    {
        ob_start();
        parent::renderDataCellContent($row, $data);
        $parentContents = ob_get_clean();

        $links = array();
        if ($data instanceof YiiEmbedActiveRecord) {
            $links[] = array(
                'label' => $parentContents,
                'url' => $data->getUrl(),
            );
            $items = method_exists($data, 'getMenuLinks') ? call_user_func(array($data, 'getMenuLinks')) : array();
            if ($items) {
                $links[] = array('items' => $items);
            }
        }
        else {
            $links[] = $parentContents;
        }
        echo '<div class="filter-container">';
        echo TbHtml::buttonGroup($links);
        echo '</div>';
    }

}
