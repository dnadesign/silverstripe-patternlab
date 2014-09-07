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
		$theme = Config::inst()->get('SSViewer', 'theme');
		
		$manifest = SS_TemplateLoader::instance()->getManifest();
		$templateList = array();

		foreach($manifest->getTemplates() as $template_name => $templateInfo) {
			$projectexists = isset($templateInfo[$project]) && isset($templateInfo[$project]['Patterns']);
			$themeexists = $theme && isset($templateInfo['themes'][$theme]) && isset($templateInfo['themes'][$theme]['Patterns']);
			//always use project template files, and grab template files if not already used
			if ($projectexists || ($themeexists && !isset($templateList[$template_name]))) {
				$templateList[$template_name] = array(
					'Link' => Director::absoluteBaseUrl() . 'patterns/index/' . $template_name,
					'Name' => $this->stripeTemplateName($template_name)
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
		if (!$this->canView()) {
			throw new PermissionFailureException();
		}

		if ($request->latestParam('ID')) {
			$templates = $this->templateArray();
			if (isset($templates[$request->latestParam('ID')])) {
				return $this->renderWith(array($request->latestParam('ID')));
			}
		}

		return $this->customise(new ArrayData(array(
			'Content' => $this->renderWith(array('Includes/Pattern_Index'))
		)));
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

	public function canView($member = null) {
		if (Director::isLive()) {
			return Permission::check('CMS_ACCESS_CMSMain', 'any', $member);
		} else {
			return true;
		}
	}

}