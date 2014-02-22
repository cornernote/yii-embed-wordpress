<?php
global $table_prefix;
return array(
    'basePath' => YII_EMBED_PATH,
    'components' => array(
        'assetManager' => array(
            'basePath' => YII_EMBED_PATH . 'assets',
            'baseUrl' => YII_EMBED_URL . 'assets',
        ),
        'urlManager' => array(
            'showScriptName' => false,
            'baseUrl' => trailingslashit(get_option('home')) . 'yii',
        ),
        'db' => array(
            'connectionString' => 'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME,
            'emulatePrepare' => true,
            'username' => DB_USER,
            'password' => DB_PASSWORD,
            'charset' => DB_CHARSET,
            'schemaCachingDuration' => 3600,
            'tablePrefix' => $table_prefix,
            //'enableProfiling' => true,
            //'enableParamLogging' => true,
        ),
    ),
    'modules' => array(
        'gii' => array(
            'class' => 'system.gii.GiiModule',
            'generatorPaths' => array(
                'vendor.cornernote.gii-tasty-templates.tasty',
                'vendor.cornernote.gii-modeldoc-generator.gii',
            ),
            'password' => YII_DEBUG ? false : null,
            'layout' => 'dssdfd',
        ),
    ),
);