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
echo "// list\n";
echo "\$this->widget('ListView', array(\n";
echo "    'id' => '" . lcfirst($this->modelClass) . "-list',\n";
echo "    'dataProvider' => \$" . lcfirst($this->modelClass) . "->search(),\n";
echo "    'itemView' => '_list_view',\n";
echo "));\n";
