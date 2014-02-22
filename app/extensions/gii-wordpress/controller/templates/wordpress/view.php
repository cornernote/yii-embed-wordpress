<?php
/**
 * This is the template for generating an action view file.
 * The following variables are available in this template:
 * @var $this ControllerCode
 * @var $action string
 *
 * @author Brett O'Donnell <cornernote@gmail.com>
 * @author Zain Ul abidin <zainengineer@gmail.com>
 * @copyright 2013 Mr PHP
 * @link https://github.com/cornernote/yii-dressing
 * @license BSD-3-Clause https://raw.github.com/cornernote/yii-dressing/master/license.txt
 */
echo "<?php\n";
echo "/**\n";
echo " * @var \$this " . $this->getControllerClass() . "\n";
echo " * @var \$" . $this->getUniqueControllerID() . " " . $this->controller . "\n";
echo " */\n";
echo "\n";

if ($action === 'index') {
    echo "Yii::app()->user->setState('index." . $this->getUniqueControllerID() . "', Yii::app()->request->requestUri);\n";
    echo "\$this->pageTitle = \$this->getName() . ' ' . Yii::t('app', 'List');\n";
}
else {
    $action = ucfirst($action);
    echo "\$this->pageTitle = \$" . $this->getUniqueControllerID() . "->getName() . ' - ' . \$this->getName() . ' ' . Yii::t('app', '" . $action . "');\n";
}
echo "\n";

echo "echo '<p>You may change the content of this page by modifying the file <code>' . __FILE__ . '</code>.</p>';";
