<?php
/**
 * This is the template for generating a controller class file.
 * The following variables are available in this template:
 * @var $this ControllerCode
 *
 * @author Brett O'Donnell <cornernote@gmail.com>
 * @author Zain Ul abidin <zainengineer@gmail.com>
 * @copyright 2013 Mr PHP
 * @link https://github.com/cornernote/yii-dressing
 * @license BSD-3-Clause https://raw.github.com/cornernote/yii-dressing/master/license.txt
 */

echo "<?php\n";
echo "/**\n";
echo " *\n";
echo " */\n";
echo "class " . $this->getControllerClass() . " extends " . $this->baseClass . "\n";
echo "{\n";
echo "\n";

// access control
$actions = array();
foreach ($this->getActionIDs() as $action) {
    $actions[] = "'" . $action . "'";
}
echo "    /**\n";
echo "     * Access Control\n";
echo "     * @return array\n";
echo "     */\n";
echo "    public function accessRules()\n";
echo "    {\n";
echo "        return array(\n";
echo "            array('allow',\n";
echo "                'actions' => array(" . implode(', ', $actions) . "),\n";
echo "                'roles' => array('admin'),\n";
echo "                //'users' => array('*','@','?'), // all, user, guest\n";
echo "            ),\n";
echo "            array('deny', 'users' => array('*')),\n";
echo "        );\n";
echo "    }\n";
echo "\n";

// filters
echo "    /**\n";
echo "     * Filters\n";
echo "     */\n";
echo "    //public function filters()\n";
echo "    //{\n";
echo "    //    return array(\n";
echo "    //        'inlineFilterName',\n";
echo "    //        array(\n";
echo "    //            'class'=>'path.to.FilterClass',\n";
echo "    //            'propertyName'=>'propertyValue',\n";
echo "    //        ),\n";
echo "    //    );\n";
echo "    //}\n";
echo "\n";

// action classes
echo "    /**\n";
echo "     * Actions\n";
echo "     */\n";
echo "    //public function actions()\n";
echo "    //{\n";
echo "    //    return array(\n";
echo "    //        'action1' => 'path.to.ActionClass',\n";
echo "    //        'action2' => array(\n";
echo "    //            'class' => 'path.to.AnotherActionClass',\n";
echo "    //            'propertyName' => 'propertyValue',\n";
echo "    //        ),\n";
echo "    //    );\n";
echo "    //}\n";
echo "\n";

// action methods
foreach ($this->getActionIDs() as $action) {
    echo "    /**\n";
    echo "     * " . ucfirst($action) . "\n";
    echo "     */\n";
    echo "    public function action" . ucfirst($action) . "()\n";
    echo "    {\n";
    echo "        \$this->render('" . $action . "');\n";
    echo "    }\n";
    echo "\n";
}

echo "\n";
echo "}\n";