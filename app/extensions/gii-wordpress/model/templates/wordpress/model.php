<?php
/**
 * This is the template for generating the model class of a specified table.
 * @var $this ModelCode
 * @var $tableName
 * @var $modelClass
 * @var $columns
 * @var $labels
 * @var $rules
 * @var $relations
 * @var $connectionId
 *
 * @author Brett O'Donnell <cornernote@gmail.com>
 * @author Zain Ul abidin <zainengineer@gmail.com>
 * @copyright 2013 Mr PHP
 * @link https://github.com/cornernote/yii-dressing
 * @license BSD-3-Clause https://raw.github.com/cornernote/yii-dressing/master/license.txt
 */

echo "<?php\n";
echo "/**\n";
echo " * --- BEGIN ModelDoc ---\n";
echo " *\n";
echo " * @see https://github.com/cornernote/gii-modeldoc-generator#gii-modeldoc-generator-for-yii\n";
echo " *\n";
echo " * --- END ModelDoc ---\n";
echo " */\n";
echo "\n";
echo "class " . $modelClass . " extends " . $this->baseClass . "\n";
echo "{\n";
echo "\n";
echo "    /**\n";
echo "     * Returns the static model of the specified AR class.\n";
echo "     * @param string \$className active record class name.\n";
echo "     * @return " . $modelClass . " the static model class\n";
echo "     */\n";
echo "    public static function model(\$className=__CLASS__)\n";
echo "    {\n";
echo "        return parent::model(\$className);\n";
echo "    }\n";
echo "\n";
if ($connectionId != 'db') {
    echo "    /**\n";
    echo "     * @return CDbConnection database connection\n";
    echo "     */\n";
    echo "    public function getDbConnection()\n";
    echo "    {\n";
    echo "        return Yii::app()->" . $connectionId . ";\n";
    echo "    }\n";
    echo "\n";
}
echo "    /**\n";
echo "     * @return string the associated database table name\n";
echo "     */\n";
echo "    public function tableName()\n";
echo "    {\n";
echo "        return '" . $tableName . "';\n";
echo "    }\n";
echo "\n";
echo "    /**\n";
echo "     * @return array validation rules for model attributes.\n";
echo "     */\n";
echo "    public function rules()\n";
echo "    {\n";
echo "        \$rules = array();\n";
echo "        if (\$this->scenario == 'search') {\n";
echo "            \$rules[] = array('" . implode(', ', array_keys($columns)) . "', 'safe');\n";
echo "        }\n";
echo "        if (in_array(\$this->scenario, array('create', 'update'))) {\n";
foreach ($rules as $rule) {
    echo "            //\$rules[] = " . $rule . ";\n";
}
echo "        }\n";
echo "        return \$rules;\n";
echo "    }\n";
echo "\n";
echo "    /**\n";
echo "     * @return array containing model behaviors\n";
echo "     */\n";
echo "    public function behaviors()\n";
echo "    {\n";
echo "        return array(\n";
echo "            //'AuditFieldBehavior' => 'audit.components.AuditFieldBehavior',\n";
echo "            //'CacheBehavior' => 'dressing.behaviors.YdCacheBehavior',\n";
echo "            //'DefaultAttributesBehavior' => 'dressing.behaviors.YdDefaultAttributesBehavior',\n";
echo "            //'LinkBehavior' => 'dressing.behaviors.YdLinkBehavior',\n";

$useTimestampBehavior = false;
$timestampFields = array('created', 'create_time', 'created_at', 'updated', 'update_time', 'updated_at');
$tableFields = CHtml::listData($columns, 'name', 'name');
foreach ($timestampFields as $timestampField)
    if (in_array($timestampField, $tableFields))
        $useTimestampBehavior = true;
if ($useTimestampBehavior)
    echo "            //'TimestampBehavior' => 'dressing.behaviors.YdTimestampBehavior',\n";
if (in_array('deleted', CHtml::listData($columns, 'name', 'name')))
    echo "            //'SoftDeleteBehavior' => 'dressing.behaviors.YdSoftDeleteBehavior',\n";

echo "        );\n";
echo "    }\n";
echo "\n";
echo "    /**\n";
echo "     * @return array relational rules.\n";
echo "     */\n";
echo "    public function relations()\n";
echo "    {\n";
echo "        return array(\n";
foreach ($relations as $name => $relation) {
    echo "            '$name' => $relation,\n";
}
echo "        );\n";
echo "    }\n";
echo "\n";
echo "    /**\n";
echo "     * @return array customized attribute labels (name=>label)\n";
echo "     */\n";
echo "    public function attributeLabels()\n";
echo "    {\n";
echo "        return array(\n";
foreach ($labels as $name => $label) {
    echo "            '$name' => Yii::t('app', '$label'),\n";
}
echo "        );\n";
echo "    }\n";
echo "\n";
echo "    /**\n";
echo "     * Retrieves a list of models based on the current search/filter conditions.\n";
echo "     * @return CActiveDataProvider the data provider that can return the models based on the search/filter conditions.\n";
echo "     */\n";
echo "    public function search()\n";
echo "    {\n";
echo "        // Warning: Please modify the following code to remove attributes that\n";
echo "        // should not be searched.\n";
echo "\n";
echo "        \$criteria=new CDbCriteria;\n";
echo "\n";
foreach ($columns as $name => $column) {
    if ($column->type === 'string') {
        echo "        \$criteria->compare('t.$name',\$this->$name,true);\n";
    }
    else {
        echo "        \$criteria->compare('t.$name',\$this->$name);\n";
    }
}
echo "\n";
echo "        return new CActiveDataProvider(\$this, array(\n";
echo "            'criteria' => \$criteria,\n";
echo "        ));\n";
echo "    }\n";
echo "\n";
echo "    /**\n";
echo "     * Retrieves a list of links to be used in menus.\n";
echo "     * @param bool \$extra\n";
echo "     * @return array\n";
echo "     */\n";
echo "    public function getMenuLinks(\$extra = false)\n";
echo "    {\n";
echo "        \$links = array();\n";
echo "        \$links[] = array('label' => Yii::t('app', 'Update'), 'url' => \$this->getUrl('update'));\n";
echo "        if (\$extra) {\n";
echo "            \$more = array();\n";
if (in_array('deleted', CHtml::listData($columns, 'name', 'name')))
    echo "            if (!\$this->deleted)\n    ";
echo "            \$more[] = array('label' => Yii::t('app', 'Delete'), 'url' => \$this->getUrl('delete', array('returnUrl' => Yii::app()->returnUrl->getLinkValue(true))), 'linkOptions' => array('data-toggle' => 'modal-remote'));\n";
if (in_array('deleted', CHtml::listData($columns, 'name', 'name'))) {
    echo "            else\n";
    echo "                \$more[] = array('label' => Yii::t('app', 'Undelete'), 'url' => \$this->getUrl('delete', array('task' => 'undelete', 'returnUrl' => Yii::app()->returnUrl->getLinkValue(true))), 'linkOptions' => array('data-toggle' => 'modal-remote'));\n";
}
echo "            \$links[] = array(\n";
echo "                'label' => Yii::t('app', 'More'),\n";
echo "                'items' => \$more,\n";
echo "            );\n";
echo "        }\n";
echo "        return \$links;\n";
echo "    }\n";
echo "\n";
echo "}\n";
echo "\n";
