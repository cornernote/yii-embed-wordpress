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
echo " * @var \$ids int[]\n";
echo " * @var \$task string\n";
echo " */\n";
echo "\n";
echo "/** @var YiiEmbedActiveForm \$form */\n";
echo "\$form = \$this->beginWidget('YiiEmbedActiveForm', array(\n";
echo "    'id' => '" . lcfirst($this->modelClass) . "-' . \$task . '-form',\n";
echo "    'type' => 'horizontal',\n";
echo "    'action' => array('/" . lcfirst($this->modelClass) . "/delete', 'task' => \$task, 'confirm' => 1),\n";
echo "));\n";
echo "echo \$form->getGridIdHiddenFields(\$ids);\n";
echo "echo \$form->beginModalWrap();\n";
echo "\n";
echo "//echo '<fieldset><legend>' . Yii::t('app', 'Selected Records') . '</legend>';\n";
echo "\$" . lcfirst($this->modelClass) . "s = " . $this->modelClass . "::model()->findAll('t." . CActiveRecord::model($this->modelClass)->tableSchema->primaryKey . " IN (' . implode(',', \$ids) . ')');\n";
echo "if (\$" . lcfirst($this->modelClass) . "s) {\n";
echo "	echo '<ul>';\n";
echo "	foreach (\$" . lcfirst($this->modelClass) . "s as \$" . lcfirst($this->modelClass) . ") {\n";
echo "		echo '<li>';\n";
echo "		echo \$" . lcfirst($this->modelClass) . "->getName();\n";
echo "		echo '</li>';\n";
echo "	}\n";
echo "	echo '</ul>';\n";
echo "}\n";
echo "//echo '</fieldset>';\n";
echo "\n";
echo "echo \$form->endModalWrap();\n";
echo "echo \$form->getSubmitButtonRow(Yii::t('app', 'Confirm ' . ucfirst(\$task)));\n";
echo "\$this->endWidget();\n";