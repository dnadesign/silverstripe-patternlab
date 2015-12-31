<?php

/**
 * @package patternlab
 */
class PatternLab extends Page_Controller
{
    public function init()
    {
        parent::init();
    }

    public function index()
    {
        Requirements::css(PATTERN_DIR . '/css/patternlab.css');
        if (!Director::isDev() && !Permission::check('CMS_ACCESS_CMSMain')) {
            return Security::permissionFailure($this);
        }

        return $this->renderWith(array(
            __CLASS__,
            'Page'
        ));
    }

    public function getPatterns()
    {
        $config = SiteConfig::current_site_config();
        if ($config->Theme) {
            Config::inst()->update('SSViewer', 'theme_enabled', true);
            Config::inst()->update('SSViewer', 'theme', $config->Theme);
        }
        $theme = $config->Theme;

        $manifest = SS_TemplateLoader::instance()->getManifest();

        $templates = array();

        foreach ($manifest->getTemplates() as $templateName => $templateInfo) {
            $themeexists = $theme && isset($templateInfo['themes'][$theme]) && isset($templateInfo['themes'][$theme]['Patterns']);

            if ((isset($templateInfo['Patterns']) || $themeexists) && !isset($templates[$templateName])) {
                $templates[$templateName] = array(
                    'Name' => trim(preg_replace('/([A-Z])/', ' $1', preg_replace('/([0-9])/', '', $templateName))),
                    'Layout' => $this->renderWith(array($templateName))
                );
            }
        }

        // ksort($templates);

        return new ArrayList($templates);
    }
}
