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
echo "\$this->renderPartial('_menu', array(\n";
echo "    '" . lcfirst($this->modelClass) . "' => \$" . lcfirst($this->modelClass) . ",\n";
echo "));\n";
echo "\n";
echo "\$attributes = array();\n";
foreach ($this->tableSchema->columns as $column) {
    echo "\$attributes[] = array(\n";
    echo "    'name' => '" . $column->name . "',\n";
    echo ");\n";
}
echo "\n";
echo "\$this->widget('YiiEmbedDetailView', array(\n";
echo "    'data' => \$" . lcfirst($this->modelClass) . ",\n";
echo "    'attributes' => \$attributes,\n";
echo "));\n";