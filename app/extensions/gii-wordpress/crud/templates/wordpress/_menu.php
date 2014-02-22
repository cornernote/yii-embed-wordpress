<?php
/**
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
echo " * @var \$this " . $this->controllerClass . "\n";
echo " * @var \$" . lcfirst($this->modelClass) . " " . $this->modelClass . "\n";
echo " */\n";
echo "\n";
echo "// index\n";
echo "if (!isset(\$" . lcfirst($this->modelClass) . ")) {\n";
echo "    //\$this->menu = MenuItem::getItemsFromMenu(1);\n";
echo "    return; // no more links\n";
echo "}\n";
echo "\n";
echo "\$menu = array();\n";
echo "\n";
echo "// create\n";
echo "if (\$" . lcfirst($this->modelClass) . "->isNewRecord) {\n";
echo "    //\$menu[] = array(\n";
echo "    //    'label' => Yii::t('app', 'Create'),\n";
echo "    //    'url' => array('/" . lcfirst($this->modelClass) . "/create'),\n";
echo "    //);\n";
echo "    return; // no more links\n";
echo "}\n";
echo "\n";
echo "// view\n";
echo "\$menu[] = array(\n";
echo "    'label' => Yii::t('app', 'View'),\n";
echo "    'url' => \$" . lcfirst($this->modelClass) . "->getUrl(),\n";
echo ");\n";
echo "\n";
echo "// others\n";
echo "foreach (\$" . lcfirst($this->modelClass) . "->getMenuLinks(true) as \$linkItem) {\n";
echo "    \$menu[] = \$linkItem;\n";
echo "}\n";
echo "\n";
echo "if (empty(\$render) || Yii::app()->getRequest()->getIsAjaxRequest())\n";
echo "    \$this->menu = \$menu;\n";
echo "else\n";
echo "    \$this->widget('bootstrap.widgets.TbNav', array(\n";
echo "        'type' => TbHtml::NAV_TYPE_TABS,\n";
echo "        'items' => \$menu,\n";
echo "    ));\n";