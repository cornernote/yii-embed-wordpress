<?php

/**
 * ModelDocCode
 *
 * @author Brett O'Donnell <cornernote@gmail.com>
 * @author Zain Ul abidin <zainengineer@gmail.com>
 * @copyright 2013 Mr PHP
 * @link https://github.com/cornernote/gii-modeldoc-generator
 * @license BSD-3-Clause https://raw.github.com/cornernote/gii-modeldoc-generator/master/LICENSE
 */
class ModelDocCode extends CCodeModel
{
    /**
     * @var
     */
    public $modelClass;

    /**
     * @var string
     */
    public $modelPath = 'application.models';

    /**
     * @var
     */
    public $addModelMethodDoc;

    /**
     * @var string
     */
    public $beginBlock = ' * --- BEGIN ModelDoc ---';

    /**
     * @var string
     */
    public $endBlock = ' * --- END ModelDoc ---';

    /**
     * @return array
     */
    public function rules()
    {
        return array_merge(parent::rules(), array(
            array('modelClass, modelPath', 'filter', 'filter' => 'trim'),
            array('modelPath', 'required'),
            array('modelPath', 'match', 'pattern' => '/^(\w+[\w\.]*|\*?|\w+\.\*)$/', 'message' => '{attribute} should only contain word characters, dots, and an optional ending asterisk.'),
            array('modelClass', 'match', 'pattern' => '/^[a-zA-Z_]\w*$/', 'message' => '{attribute} should only contain word characters.'),
            array('modelPath', 'validateModelPath', 'skipOnError' => true),
            array('modelPath', 'sticky'),
            array('addModelMethodDoc', 'numerical', 'integerOnly' => true),
        ));
    }

    /**
     * @return array
     */
    public function attributeLabels()
    {
        return array_merge(parent::attributeLabels(), array(
            'modelPath' => 'Model Path',
            'modelClass' => 'Model Class',
        ));
    }

    /**
     * @return array
     */
    public function requiredTemplates()
    {
        return array(
            'model.php',
        );
    }

    /**
     *
     */
    public function validateModelPath()
    {
        if (Yii::getPathOfAlias($this->modelPath) === false)
            $this->addError('modelPath', 'Model Path must be a valid path alias.');
    }

    /**
     *
     */
    public function prepare()
    {
        $this->files = array();
        $templatePath = $this->templatePath;
        foreach ($this->getModels() as $model) {
            $modelClass = get_class($model);
            $params = array(
                'model' => $model,
                'modelClass' => $modelClass,
            );
            //wrap the lines below in a try..catch block so execution
            //continues with next model file if an error occurs
            try {
                $this->files[] = new CCodeFile(
                    Yii::getPathOfAlias($this->modelPath) . '/' . $modelClass . '.php',
                    $this->render($templatePath . '/model.php', $params)
                );
            } catch (Exception $ex) {
                // continue with next model file, we could also do some logging here
                continue;
            }
        }
    }

    /**
     * @return array
     */
    public function getModels()
    {
        $modelClass = $this->modelClass;
        if ($modelClass)
            return array(CActiveRecord::model($modelClass));
        $modelList = array();
        $files = CFileHelper::findFiles(Yii::getPathOfAlias($this->modelPath), array('fileTypes' => array('php'), 'level' => 0));
        foreach ($files as $file) {
            $modelClass = basename($file, '.php');

            // there is dot in modelName [$modelClass] probably a version conflict file
            if (strpos($modelClass, '.') !== false)
                continue;

            //use reflection to check if class is instantiable
            $reflectedClass = new ReflectionClass($modelClass);
            if ($reflectedClass->isInstantiable() === false)
                continue; //continue if this class is not instantiable

            // load the model
            $model = new $modelClass;
            if (!$model || !is_subclass_of($model, 'CActiveRecord'))
                continue;

            // everything passes, add it to the list
            $modelList[] = $model;
        }
        return $modelList;
    }

    /**
     * @param $modelClass string
     * @throws CException
     * @return string
     */
    public function getContent($modelClass)
    {
        $file = Yii::getPathOfAlias($this->modelPath) . '/' . $modelClass . '.php';
        if (!file_exists($file))
            throw new CException(strtr(Yii::t('modelDocGenerator', 'File :file was not found.'), array(':file' => $file)));
        $content = file_get_contents($file);
        $content = explode($this->beginBlock, $content);
        if (!isset($content[1]))
            throw new CException(strtr(Yii::t('modelDocGenerator', 'File :file does not contain the beginBlock :beginBlock.'), array(':file' => $file, ':beginBlock' => $this->beginBlock)));
        $content[1] = explode($this->endBlock, $content[1]);
        if (!isset($content[1][1]))
            throw new CException(strtr(Yii::t('modelDocGenerator', 'File :file does not contain the endBlock :endBlock.'), array(':file' => $file, ':endBlock' => $this->endBlock)));
        $content[1] = $content[1][1];
        return $content;
    }

    /**
     * @param $behavior
     * @return mixed
     */
    public function getBehaviorClass($behavior)
    {
        if (is_array($behavior))
            $behavior = $behavior['class'];
        $behavior = explode('.', $behavior);
        return $behavior[count($behavior) - 1];
    }

    /**
     * @param $modelName
     * @param $behavior
     * @param array $ignoreMethods
     * @return array
     */
    public function getBehaviorProperties($modelClass, $behavior, $ignoreMethods = array())
    {
        $properties = array();

        //// properties
        //foreach (get_class_vars($behavior) as $propertyName => $default) {
        //    if (isset($ignoreProperties[$propertyName]))
        //        continue;
        //    $properties[] = ' * @property ' . gettype($default) . ' $' . $propertyName;
        //}

        // methods
        $ignoreMethods['__destruct'] = '__destruct';
        foreach (get_class_methods($behavior) as $methodName) {
            if (isset($ignoreMethods[$methodName]))
                continue;
            $methodReturn = $this->getTypeFromDocComment($behavior, $methodName, 'return');
            $paramTypes = $this->getDocComment($behavior, $methodName, 'param');
            $methodReturn = $methodReturn ? current($methodReturn) . ' ' : '';
            $property = " * @method $methodReturn$methodName() $methodName(";
            $r = new ReflectionMethod($behavior, $methodName);
            $params = $r->getParameters();
            $separator = '';
            foreach ($params as $param) {
                //$param is an instance of ReflectionParameter
                /* @var $param ReflectionParameter */
                $type = current($paramTypes);
                $filterType = '';
                if ($type && strpos($type, '$')) {
                    $typeString = $this->getBetweenString($type, false, '$');
                    $typeString = trim($typeString);
                    $filterType = $this->filterDocType($typeString);
                    $filterType = $filterType ? trim($filterType) . ' ' : '';
                }
                next($paramTypes);
                $property .= $separator . $filterType . '$' . $param->getName();
                if ($param->isOptional()) {
                    $property .= ' = ';
                    $property .= strtr(str_replace("\n", '', var_export($param->getDefaultValue(), true)), array(
                        'array (' => 'array(',
                    ));
                }
                $separator = ', ';
            }
            $property .= ")";
            $property = str_replace('CActiveRecord', $modelClass, $property);
            $properties[] = $property;

        }

        return $properties;
    }

    /**
     * @param $class
     * @param $method
     * @param string $tag
     * @return array|string
     */
    public function getDocComment($class, $method, $tag = '')
    {
        $reflection = new ReflectionMethod($class, $method);
        $comment = $reflection->getDocComment();
        if (!$tag) {
            return $comment;
        }

        $matches = array();
        preg_match_all("/@" . $tag . " (.*)(\\r\\n|\\r|\\n)/U", $comment, $matches);

        $returns = array();
        foreach ($matches[1] as $match) {
            $match = explode(' ', $match);
            $type = $match[0];
            $name = isset($match[1]) ? $match[1] : '';
            if (strpos($type, '$') === 0) {
                $name_ = $name;
                $name = $type;
                $type = $name_;
            }
            if (strpos($name, '$') !== 0) {
                $name = '';
            }
            $returns[] = trim($type . ' ' . $name);
        }

        return $returns;
    }

    /**
     * @param $class
     * @param $method
     * @param $tag
     * @return array
     */
    public function getTypeFromDocComment($class, $method, $tag)
    {
        $types = $this->getDocComment($class, $method, $tag);
        $returnTypes = array();
        foreach ($types as $k => $type) {
            $filteredType = $this->filterDocType($type);
            if ($filteredType) {
                $returnTypes[$k] = trim($filteredType);
            }
        }
        return $returnTypes;

    }

    /**
     * @param $type
     * @return mixed|string
     */
    public function filterDocType($type)
    {
        $ignoreTypes = array('void', 'mixed', 'null');
        $replace = array(
            'bool' => 'boolean',
            'integer' => 'int',
        );
        $filteredType = '';
        if (strpos($type, '|') !== false) {
            $multiType = explode('|', $type);
            $multiTypeSafe = array();
            foreach ($multiType as $singleType) {
                if (!in_array($singleType, $ignoreTypes)) {
                    if (isset($replace[$singleType])) {
                        $singleType = $replace[$singleType];
                    }
                    $multiTypeSafe[] = $singleType;
                }
            }
            $filteredType = implode('|', $multiTypeSafe);
        }
        else {
            if (!in_array($type, $ignoreTypes)) {
                $filteredType = $type;
                if (isset($replace[$type])) {
                    $filteredType = $replace[$type];
                }
            }
        }
        if ($filteredType) {
            $filteredType = str_replace('-', ' ', $filteredType);
            $filteredType = trim($filteredType);
            if (strpos($type, ' ')) {
                $filteredType = $this->getBetweenString($type, false, ' ');
            }
        }

        return $filteredType;

    }

    /**
     * @param $contents
     * @param $start
     * @param $end
     * @param bool $removeStart
     * @param bool $removeEnd
     * @return string
     */
    static public function getBetweenString($contents, $start, $end, $removeStart = true, $removeEnd = true)
    {
        $startPos = $start ? strpos($contents, $start) : 0;
        if ($startPos === false)
            return false;

        if ($end) {
            $endPos = strpos($contents, $end, $startPos);
            if ($endPos === false) {
                $endPos = $endPos = strlen($contents);
            }
        }
        else {
            $endPos = strlen($contents);
        }

        if ($removeStart) {
            $startPos += strlen($start);
        }
        $len = $endPos - $startPos;
        if (!$removeEnd && $end && $endPos) {
            $len = $len + strlen($end);
        }
        return substr($contents, $startPos, $len);
    }

}
