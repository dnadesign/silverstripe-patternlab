<?php
class PatternLab extends Controller {

	private static $allowed_actions = array(
		'index'
	);

	protected function templateArray() {
		global $project;
		$manifest = SS_TemplateLoader::instance()->getManifest();
		$templateList = array();
		foreach($manifest->getTemplates() as $template_name => $templateInfo) {
			if (isset($templateInfo[$project]) && isset($templateInfo[$project]['Patterns'])) {
				$templateList[$template_name] = array(
					'Link' => '/patterns/index/' . $template_name,
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
		if ($request->latestParam('ID')) {
			$templates = $this->templateArray();
			if (isset($templates[$request->latestParam('ID')])) {
				return $this->renderWith(array($request->latestParam('ID')));
			}
		}

		return $this->renderWith(array('Pattern_Index'));
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