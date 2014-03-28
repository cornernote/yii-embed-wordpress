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
echo "/** @var YiiEmbedActiveForm \$form */\n";
echo "\$form = \$this->beginWidget('YiiEmbedActiveForm', array(\n";
echo "    //'action' => Yii::app()->createUrl(\$this->route),\n";
echo "    //'layout' => TbHtml::FORM_LAYOUT_HORIZONTAL,\n";
echo "    'method' => 'get',\n";
echo "    'htmlOptions' => array('class' => 'hide'),\n";
echo "));\n";
echo "\$form->searchToggle('" . lcfirst($this->modelClass) . "-grid-search', '" . lcfirst($this->modelClass) . "-grid');\n";
echo "\n";
echo "echo '<fieldset>';\n";
echo "echo '<legend>" . $this->modelClass . " ' . Yii::t('app', 'Search') . '</legend>';\n";
foreach ($this->tableSchema->columns as $column) {
    if ($column->type === 'boolean')
        $inputField = 'checkBoxControlGroup';
    elseif (stripos($column->dbType, 'text') !== false)
        $inputField = 'textAreaControlGroup';
    elseif (preg_match('/^(password|pass|passwd|passcode)$/i', $column->name))
        $inputField = 'passwordFieldControlGroup';
    else
        $inputField = 'textFieldControlGroup';

    echo "echo \$form->" . $inputField . "(\$" . lcfirst($this->modelClass) . ", '" . $column->name . "');\n";
}
echo "echo '</fieldset>';\n";
echo "\n";
echo "echo \$form->getSubmitButtonRow(Yii::t('app', 'Search'), array('icon' => 'search white'));\n";
echo "\n";
echo "\$this->endWidget();\n";
