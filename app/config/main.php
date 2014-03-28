<?php
global $table_prefix;
return array(
    'basePath' => YII_EMBED_PATH . 'app',
    'runtimePath' => YII_EMBED_PATH . 'runtime',
    'aliases' => array(
        'bootstrap' => realpath(YII_EMBED_PATH . 'app/extensions/yiistrap'),
        'audit' => realpath(YII_EMBED_PATH . 'app/extensions/audit'),
    ),
    'import' => array(
        'bootstrap.helpers.*',
        'bootstrap.behaviors.*',
        'bootstrap.widgets.*',
    ),
    'preload' => array(
        'log',
        'errorHandler',
    ),
    'exitRoutes' => array(
        'gii',
    ),
    'components' => array(
        'assetManager' => array(
            'basePath' => YII_EMBED_PATH . 'assets',
            'baseUrl' => YII_EMBED_URL . 'assets',
        ),
        'bootstrap' => array(
            'class' => 'bootstrap.components.TbApi',
        ),
        'cache' => array(
            'class' => 'CFileCache',
        ),
        'clientScript' => array(
            'class' => 'YiiEmbedClientScript',
        ),
        'db' => array(
            'connectionString' => 'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME,
            'emulatePrepare' => true,
            'username' => DB_USER,
            'password' => DB_PASSWORD,
            'charset' => DB_CHARSET,
            'schemaCachingDuration' => 3600,
            'tablePrefix' => $table_prefix,
            'enableProfiling' => YII_DEBUG,
            'enableParamLogging' => YII_DEBUG,
        ),
        'errorHandler' => array(
            'class' => 'audit.components.AuditErrorHandler',
            'errorAction' => 'yiiEmbedSite/error',
        ),
        'returnUrl' => array(
            'class' => 'YiiEmbedReturnUrl',
        ),
        'user' => array(
            'class' => 'YiiEmbedWebUser',
        ),
        'urlManager' => array(
            'class' => 'YiiEmbedUrlManager',
            'urlFormat' => is_admin() ? 'get' : 'path',
            'baseUrl' => is_admin() ? '?page=yii_embed' : null,
            'showScriptName' => false,
        ),
    ),
    'modules' => array(
        'gii' => array(
            'class' => 'system.gii.GiiModule',
            'generatorPaths' => array(
                'application.extensions.gii-wordpress',
                'application.extensions.gii-modeldoc-generator',
            ),
            'password' => YII_DEBUG ? false : null,
        ),
        'audit' => array(
            'class' => 'application.extensions.audit.AuditModule',
        ),
    ),
);