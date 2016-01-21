<?php

/**
 * @package patternlab
 */
class PatternLab extends Controller {

	private static $allowed_actions = array(
		'index'
	);

	protected function templateArray() {
		global $project;
		$config = SiteConfig::current_site_config();
		if ($config->Theme) {
			Config::inst()->update('SSViewer', 'theme_enabled', true);
			Config::inst()->update('SSViewer', 'theme', $config->Theme);
		}
		$theme = $config->Theme;

		$manifest = SS_TemplateLoader::instance()->getManifest();
		$templateList = array();

		foreach($manifest->getTemplates() as $template_name => $templateInfo) {
			$projectexists = isset($templateInfo[$project]) && isset($templateInfo[$project]['Patterns']);
			$themeexists = $theme && isset($templateInfo['themes'][$theme]) && isset($templateInfo['themes'][$theme]['Patterns']);
			//always use project template files, and grab template files if not already used

			if ($projectexists || ($themeexists && !isset($templateList[$template_name]))) {
				$templateList[$template_name] = array(
					'Link' => Controller::join_links(
						Director::absoluteBaseUrl(),'patterns','index',$template_name
					),
					'Name' => $this->stripeTemplateName($template_name),
					'Template' => $template_name
				);
			}


		}

		ksort($templateList);

		return $templateList;
	}

	protected function stripeTemplateName($name) {
		return ucfirst(str_replace(array('Pattern_', 'pattern_', '_pattern', '_Pattern'), '', $name));
	}

	public function index(SS_HTTPRequest $request) {
		if(!Director::isDev() && !Permission::check('CMS_ACCESS_CMSMain')) {
			return Security::permissionFailure($this);
		}

		if($request->latestParam('ID')) {
			$templates = $this->templateArray();

			if (isset($templates[$request->latestParam('ID')])) {
				$next = false;
				$previous = false;
				$useNext = false;

				foreach($templates as $k => $v) {
					if($useNext) {
						$next = new ArrayData(array(
							'Name' => $v['Name'],
							'Link' => 'patterns/index/'. $k
						));

						break;
					}

					if($k == $request->latestParam('ID')) {
						// mat
						$useNext = true;
					} else {
						$previous = new ArrayData(array(
							'Name' => $v['Name'],
							'Link' => 'patterns/index/'. $k
						));
					}
				}

				return $this->customise(new ArrayData(array(
					'ClassName' => 'Pattern',
					'IsPatternLab' => true,
					'PreviousPattern' => $previous,
					'NextPattern' => $next,
					'PatternName' =>$templates[$request->latestParam('ID')]['Name'],
					'Patterns' => $this->renderWith(array(
						$templates[$request->latestParam('ID')]['Template']
					))
				)))->renderWith(
					$templates[$request->latestParam('ID')]['Template']
				);
			}
		}

		return $this->renderWith(array(
			__CLASS__,
			'Page'
		));
	}

	public function getSiteConfig() {
		return SiteConfig::current_site_config();
	}

	public function getTitle() {
		$request = $this->getRequest();

		if ($request->latestParam('ID')) {
			$templates = $this->templateArray();
			if (isset($templates[$request->latestParam('ID')])) {
				return $templates[$request->latestParam('ID')]['Name'];
			}
		}

		return 'Pattern Lab';
	}

	public function getPatterns() {
		return new ArrayList($this->templateArray());
	}
}