<?php
/**
 * This is the template for generating a controller class file for CRUD feature.
 * The following variables are available in this template:
 * @var $this CrudCode
 *
 * @author Brett O'Donnell <cornernote@gmail.com>
 * @author Zain Ul abidin <zainengineer@gmail.com>
 * @copyright 2013 Mr PHP
 * @link https://github.com/cornernote/yii-dressing
 * @license BSD-3-Clause https://raw.github.com/cornernote/yii-dressing/master/license.txt
 */

echo "<?php\n";
echo "/**\n";
echo " * " . $this->controllerClass . "\n";
echo " *\n";
echo " * @method " . $this->modelClass . " loadModel() loadModel(\$id, \$model = null)\n";
echo " */\n";
echo "class " . $this->controllerClass . " extends " . $this->baseControllerClass . "\n";
echo "{\n";
echo "\n";

// access control
echo "    /**\n";
echo "     * Access Control\n";
echo "     * @return array\n";
echo "     */\n";
echo "    public function accessRules()\n";
echo "    {\n";
echo "        return array(\n";
echo "            array('allow',\n";
echo "                'actions' => array('index', 'view', 'log', 'create', 'update', 'delete'),\n";
echo "                'roles' => array('admin'),\n";
echo "                //'users' => array('*','@','?'), // all, user, guest\n";
echo "            ),\n";
echo "            array('deny', 'users' => array('*')),\n";
echo "        );\n";
echo "    }\n";
echo "\n";

// beforeRender
echo "    /**\n";
echo "     * @param string \$view the view to be rendered\n";
echo "     * @return bool\n";
echo "     */\n";
echo "    public function beforeRender(\$view)\n";
echo "    {\n";
echo "        if (\$view != 'index') {\n";
echo "            \$this->addBreadcrumb(Yii::t('app', '" . $this->modelClass . "'), Yii::app()->user->getState('index." . lcfirst($this->modelClass) . "', array('/" . lcfirst($this->modelClass) . "/index')));\n";
echo "        }\n";
echo "        return parent::beforeRender(\$view);\n";
echo "    }\n";
echo "\n";

// index
echo "    /**\n";
echo "     * Index\n";
echo "     */\n";
echo "    public function actionIndex()\n";
echo "    {\n";
echo "        \$" . lcfirst($this->modelClass) . " = new " . $this->modelClass . "('search');\n";
echo "        if (!empty(\$_GET['" . $this->modelClass . "']))\n";
echo "            \$" . lcfirst($this->modelClass) . "->attributes = \$_GET['" . $this->modelClass . "'];\n";
echo "\n";
echo "        // redirect to view page if only one result\n";
echo "        \$dataProvider = \$" . lcfirst($this->modelClass) . "->search();\n";
echo "        \$this->createWidget('YiiEmbedGridView', array('id' => '" . lcfirst($this->modelClass) . "-grid', 'dataProvider' => \$dataProvider));\n";
echo "        if (!Yii::app()->request->isAjaxRequest && \$dataProvider->itemCount == 1)\n";
echo "            \$this->redirect(\$dataProvider->data[0]->url);\n";
echo "\n";
echo "        \$this->render('index', array(\n";
echo "            '" . lcfirst($this->modelClass) . "' => \$" . lcfirst($this->modelClass) . ",\n";
echo "        ));\n";
echo "    }\n";
echo "\n";

// view
echo "    /**\n";
echo "     * View\n";
echo "     * @param \$id\n";
echo "     */\n";
echo "    public function actionView(\$id)\n";
echo "    {\n";
echo "        \$" . lcfirst($this->modelClass) . " = \$this->loadModel(\$id);\n";
echo "\n";
if (in_array('deleted', CHtml::listData($this->tableSchema->columns, 'name', 'name'))) {
    echo "        // check for deleted " . $this->modelClass . "\n";
    echo "        if (\$" . lcfirst($this->modelClass) . "->deleted) {\n";
    echo "            Yii::app()->user->addFlash(Yii::t('app', '" . $this->modelClass . " is deleted!'), 'warning');\n";
    echo "        }\n";
    echo "\n";
}
echo "        \$this->render('view', array(\n";
echo "            '" . lcfirst($this->modelClass) . "' => \$" . lcfirst($this->modelClass) . ",\n";
echo "        ));\n";
echo "    }\n";
echo "\n";

// create
echo "    /**\n";
echo "     * Create\n";
echo "     */\n";
echo "    public function actionCreate()\n";
echo "    {\n";
echo "        \$" . lcfirst($this->modelClass) . " = new " . $this->modelClass . "('create');\n";
echo "\n";
echo "        //\$this->performAjaxValidation(\$" . lcfirst($this->modelClass) . ", '" . lcfirst($this->modelClass) . "-form');\n";
echo "        if (isset(\$_POST['" . $this->modelClass . "'])) {\n";
echo "            \$" . lcfirst($this->modelClass) . "->attributes = \$_POST['" . $this->modelClass . "'];\n";
echo "            if (\$" . lcfirst($this->modelClass) . "->save()) {\n";
echo "                Yii::app()->user->addFlash(strtr('" . $this->modelClass . " :name has been created.', array(':name' => \$" . lcfirst($this->modelClass) . "->getName())), 'success');\n";
echo "                \$this->redirect(Yii::app()->returnUrl->getUrl(\$" . lcfirst($this->modelClass) . "->getUrl()));\n";
echo "            }\n";
echo "            //Yii::app()->user->addFlash(Yii::t('app', '" . $this->modelClass . " could not be created.'), 'warning');\n";
echo "        }\n";
echo "        else {\n";
echo "            if (isset(\$_GET['" . $this->modelClass . "'])) {\n";
echo "                \$" . lcfirst($this->modelClass) . "->attributes = \$_GET['" . $this->modelClass . "'];\n";
echo "            }\n";
echo "        }\n";
echo "\n";
echo "        \$this->render('create', array(\n";
echo "            '" . lcfirst($this->modelClass) . "' => \$" . lcfirst($this->modelClass) . ",\n";
echo "        ));\n";
echo "    }\n";
echo "\n";

// update
echo "    /**\n";
echo "     * Update\n";
echo "     * @param \$id\n";
echo "     */\n";
echo "    public function actionUpdate(\$id)\n";
echo "    {\n";
echo "        \$" . lcfirst($this->modelClass) . " = \$this->loadModel(\$id);\n";
echo "\n";
echo "        //\$this->performAjaxValidation(\$" . lcfirst($this->modelClass) . ", '" . lcfirst($this->modelClass) . "-form');\n";
echo "        if (isset(\$_POST['" . $this->modelClass . "'])) {\n";
echo "            \$" . lcfirst($this->modelClass) . "->attributes = \$_POST['" . $this->modelClass . "'];\n";
echo "            if (\$" . lcfirst($this->modelClass) . "->save()) {\n";
echo "                Yii::app()->user->addFlash(strtr('" . $this->modelClass . " :name has been updated.', array(':name' => \$" . lcfirst($this->modelClass) . "->getName())), 'success');\n";
echo "                \$this->redirect(Yii::app()->returnUrl->getUrl(\$" . lcfirst($this->modelClass) . "->getUrl()));\n";
echo "            }\n";
echo "            //Yii::app()->user->addFlash(Yii::t('app', '" . $this->modelClass . " could not be updated.'), 'warning');\n";
echo "        }\n";
echo "\n";
echo "        \$this->render('update', array(\n";
echo "            '" . lcfirst($this->modelClass) . "' => \$" . lcfirst($this->modelClass) . ",\n";
echo "        ));\n";
echo "    }\n";
echo "\n";

// delete
echo "    /**\n";
echo "     * Delete and Undelete\n";
echo "     * @param \$id\n";
echo "     */\n";
echo "    public function actionDelete(\$id = null)\n";
echo "    {\n";
echo "        \$task = \$this->getSubmittedField('task', '" . $this->modelClass . "')=='undelete' ? 'undelete' : 'delete';\n";
echo "        \$ids = \$this->getGridIds(\$id);\n";
echo "        if (\$this->getSubmittedField('confirm', '" . $this->modelClass . "')) {\n";
echo "            foreach (\$ids as \$_id) {\n";
echo "                \$" . lcfirst($this->modelClass) . " = " . $this->modelClass . "::model()->findByPk(\$_id);\n";
echo "                if (!\$" . lcfirst($this->modelClass) . ") {\n";
echo "                    continue;\n";
echo "                }\n";
echo "                call_user_func(array(\$" . lcfirst($this->modelClass) . ", \$task));\n";
echo "                Yii::app()->user->addFlash(strtr('" . $this->modelClass . " :name has been :tasked.', array(\n";
echo "                    ':name' => \$" . lcfirst($this->modelClass) . "->getName(),\n";
echo "                    ':tasked' => \$task . 'd',\n";
echo "                )), 'success');\n";
echo "            }\n";
echo "            \$this->redirect(Yii::app()->returnUrl->getUrl(Yii::app()->user->getState('index." . lcfirst($this->modelClass) . "', array('/" . lcfirst($this->modelClass) . "/index'))));\n";
echo "        }\n";
echo "\n";
echo "        \$this->render('delete', array(\n";
echo "            'ids' => \$ids,\n";
echo "            'task' => \$task,\n";
echo "        ));\n";
echo "    }\n";
echo "\n";

// sort
//if (isset($this->tableSchema->columns['sort_order'])) {
//    echo "    /**\n";
//    echo "     * Handles the ordering of lookups.\n";
//    echo "     */\n";
//    echo "    public function actionSortOrder()\n";
//    echo "    {\n";
//    echo "        if (isset(\$_POST['Order'])) {\n";
//    echo "            foreach (explode(',', \$_POST['Order']) as \$k => \$id) {\n";
//    echo "                if ($" . lcfirst($this->modelClass) . " = " . $this->modelClass . "::model()->findbyPk(\$id)) {\n";
//    echo "                    $" . lcfirst($this->modelClass) . "->sort_order = \$k;\n";
//    echo "                    $" . lcfirst($this->modelClass) . "->save(false);\n";
//    echo "                }\n";
//    echo "            }\n";
//    echo "        }\n";
//    echo "    }\n";
//    echo "\n";
//}

// end class
echo "}\n";
