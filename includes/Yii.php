<?php

/**
 * Yii
 *
 * @method static YiiEmbedApplication app()
 */
class Yii extends YiiBase
{
    /**
     * @var array
     */
    public static $templateRedirectRoutes = array(
        'gii',
    );

    /**
     * Initialize the callbacks to allow Yii controllers to be called via short tags.
     *
     * @return YiiEmbedApplication
     */
    public static function init()
    {
        // create application
        Yii::createApplication('YiiEmbedApplication', YII_EMBED_PATH . 'app/config/main.php');

        // add short tag callback
        add_shortcode('yii_embed_yii', 'Yii::returnController');

        // add template redirects
        foreach (self::$templateRedirectRoutes as $route)
            if (!empty($_GET['r']) && strpos($_GET['r'], $route) === 0)
                add_filter('template_redirect', 'Yii::renderController');

        // add output buffer for clientScript
        ob_start('Yii::renderClientScript');

        // add YiiStrap
        Yii::app()->bootstrap->register();
    }

    /**
     * Runs the controller then exits.
     */
    public static function renderController()
    {
        self::runController();
        Yii::app()->end();
    }

    /**
     * Runs the controller then returns the output.
     *
     * @return string
     */
    public static function returnController()
    {
        ob_start();
        self::runController();
        return ob_get_clean();
    }

    /**
     * Runs a Yii controller.
     */
    public static function runController($route = null)
    {
        if (!$route)
            $route = !empty($_GET['r']) ? $_GET['r'] : null;
        Yii::app()->runController($route);
    }

    /**
     * Output buffer callback to add CSS/JS to the page.
     * @param $output
     * @return mixed
     */
    public static function renderClientScript($output)
    {
        Yii::app()->getClientScript()->render($output);
        return $output;
    }

}
