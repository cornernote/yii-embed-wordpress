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
echo "Yii::app()->user->setState('index." . lcfirst($this->modelClass) . "', Yii::app()->request->requestUri);\n";
echo "\n";
echo "\$this->renderPartial('_menu');\n";
echo "\n";
echo "// page menu\n";
echo "\$menu = array();\n";
echo "\$menu[] = array(\n";
echo "    'label' => Yii::t('app', 'Create') . ' " . $this->modelClass . "',\n";
echo "    'url' => array('/" . lcfirst($this->modelClass) . "/create'),\n";
echo "    'active' => true,\n";
echo "    'linkOptions' => array('data-toggle' => 'modal-remote'),\n";
echo ");\n";
echo "\$menu[] = array(\n";
echo "    'label' => Yii::t('app', 'Search'),\n";
echo "    'linkOptions' => array('class' => '" . lcfirst($this->modelClass) . "-grid-search'),\n";
echo "    'url' => 'javascript:void(0);',\n";
echo ");\n";
echo "if (Yii::app()->user->getState('index." . lcfirst($this->modelClass) . "') != Yii::app()->createUrl('/" . lcfirst($this->modelClass) . "/index')) {\n";
echo "    \$menu[] = array(\n";
echo "        'label' => Yii::t('app', 'Reset Filters'),\n";
echo "        'url' => array('/" . lcfirst($this->modelClass) . "/index'),\n";
echo "    );\n";
echo "}\n";
echo "echo TbHtml::pills(\$menu);\n";
echo "\n";
echo "// search\n";
echo "\$this->renderPartial('/" . lcfirst($this->modelClass) . "/_search', array(\n";
echo "    '" . lcfirst($this->modelClass) . "' => \$" . lcfirst($this->modelClass) . ",\n";
echo "));\n";
echo "\n";
echo "// grid\n";
echo "\$this->renderPartial('/" . lcfirst($this->modelClass) . "/_grid', array(\n";
echo "    '" . lcfirst($this->modelClass) . "' => \$" . lcfirst($this->modelClass) . ",\n";
echo "    'dataProvider' => isset(\$dataProvider) ? \$dataProvider : null,\n";
echo "));\n";
