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
 * YiiEmbedController
 *
 * @property string $pageHeading
 * @property array $breadcrumbs
 *
 * @package yii-embed-wordpress
 */
class YiiEmbedController extends CController
{

    /**
     * @var array context menu items. This property will be assigned to {@link CMenu::items}.
     */
    public $menu = array();

    /**
     * @var array breadcrumbs links to current page. This property will be assigned to {@link CBreadcrumbs::links}.
     */
    protected $_breadcrumbs = array();

    /**
     * @var
     */
    protected $_pageHeading;

    /**
     * @var
     */
    protected $_loadModel;

    /**
     *
     */
    public function init()
    {
        parent::init();
        // set the layout
        $this->layout = is_admin() ? 'application.views.layouts.admin' : 'application.views.layouts.front';
    }

    /**
     * @return string Defaults to the controllers pageTitle.
     */
    public function getPageHeading()
    {
        if ($this->_pageHeading === null)
            $this->_pageHeading = $this->pageTitle;
        return $this->_pageHeading;
    }

    /**
     * @param $pageHeading string
     */
    public function setPageHeading($pageHeading)
    {
        $this->_pageHeading = $pageHeading;
    }

    /**
     * @return string
     */
    public function getBreadcrumbs()
    {
        return $this->_breadcrumbs;
    }

    /**
     * @param string $breadcrumbs
     */
    public function setBreadcrumbs($breadcrumbs)
    {
        $this->_breadcrumbs = $breadcrumbs;
    }

    /**
     * @param string $name
     * @param array|string $link
     */
    public function addBreadcrumb($name, $link = null)
    {
        if ($link)
            $this->_breadcrumbs[$name] = $link;
        else
            $this->_breadcrumbs[] = $name;
    }

    /**
     *
     */
    public function getPageBreadcrumbs()
    {
        $breadcrumbs = $this->_breadcrumbs;
        $breadcrumbs[] = $this->pageTitle;
        return $breadcrumbs;
    }

    /**
     * Loads a CActiveRecord or throw a CHTTPException
     *
     * @param $id
     * @param bool|string $model
     * @return CActiveRecord
     * @throws CHttpException
     */
    public function loadModel($id, $model = false)
    {
        if (!$model)
            $model = str_replace('Controller', '', get_class($this));
        if ($this->_loadModel === null) {
            $this->_loadModel = CActiveRecord::model($model)->findByPk($id);
            if ($this->_loadModel === null)
                throw new CHttpException(404, Yii::t('dressing', 'The requested page does not exist.'));
        }
        return $this->_loadModel;
    }

    /**
     * Gets a submitted field
     * used to be named getSubmittedField()
     *
     * @param $field
     * @param null $model
     * @return mixed
     */
    public function getSubmittedField($field, $model = null)
    {
        $return = null;
        if ($model && isset($_GET[$model][$field])) {
            $return = $_GET[$model][$field];
        }
        elseif ($model && isset($_POST[$model][$field])) {
            $return = $_POST[$model][$field];
        }
        elseif (isset($_GET[$field])) {
            $return = $_GET[$field];
        }
        elseif (isset($_POST[$field])) {
            $return = $_POST[$field];
        }
        return $return;
    }

    /**
     * @param $ids
     * @return array
     */
    public function getGridIds($ids = null)
    {
        if (!$ids)
            $ids = array();
        if (!is_array($ids))
            $ids = explode(',', $ids);
        foreach ($_REQUEST as $k => $v) {
            if (strpos($k, '-grid_c0') === false || !is_array($v))
                continue;
            foreach ($v as $vv) {
                $ids[$vv] = $vv;
            }
        }
        return $ids;
    }


}
