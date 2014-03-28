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
echo "    'id' => '" . lcfirst($this->modelClass) . "-form',\n";
echo "    //'layout' => TbHtml::FORM_LAYOUT_HORIZONTAL,\n";
echo "    //'enableAjaxValidation' => true,\n";
echo "));\n";
echo "echo \$form->beginModalWrap();\n";
echo "echo \$form->errorSummary(\$" . lcfirst($this->modelClass) . ");\n";
echo "\n";
foreach ($this->tableSchema->columns as $column) {
    if ($column->autoIncrement)
        continue;
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
echo "\n";
echo "echo \$form->endModalWrap();\n";
echo "echo \$form->getSubmitButtonRow(\$" . lcfirst($this->modelClass) . "->isNewRecord ? Yii::t('app', 'Create') : Yii::t('app', 'Save'));\n";
echo "\$this->endWidget();\n";