<?php
defined('ROOTPATH') or exit('No direct script access allowed');

class ViewEngine extends BaseController
{
	private $route;
	private $data;
	private $assets;
	private $viewRoutes;
	private $permissionService;

	public function __construct($container, $route, $data)
	{
		parent::__construct($container);

		$this->route  = $route;
		$this->data   = $data;
		$this->assets = $data['assetData'] ?? [];
		$this->viewRoutes = require ROOTPATH . '/config/view_routes.php';
		$this->permissionService = $this->permissionService = $container->get('permissionService');
	}

	public function render()
	{
		$this->applyGuards();

		$this->renderViewWithLayout();
	}

	private function applyGuards()
	{
		// login fallback
		if (empty($this->route) && empty($_SESSION['user_id'])) {
			$this->route = 'login';
		}

		// permission check
		if (!($this->data['pageData']['page_permission'] ?? true)) {
			$status = $this->data['pageData']['status'] ?? 403;
			$this->route = ($status == 403) ? 'no_access' : 'not_found';
		}

		// maintenance check
		if (
			($_SESSION['user_type'] ?? '') !== 'admin' &&
			($_SESSION['user_type'] ?? '') !== 'developer'
		) {
			if (!($this->data['check_site_maintenance'] ?? true)) {
				$this->route = 'under_maintenance';
			}
		}

		// logout
		if ($this->route === 'logout') {
			session_destroy();
			header("Location: " . SITE_URL);
			exit;
		}
	}

		private function renderViewWithLayout()
	{
		$data = $this->prepareViewData();

		// Extract ONCE for everything
		extract($data, EXTR_SKIP);
		extract($data['pageData'] ?? [], EXTR_SKIP);

		//$this->dd($pageContent['pageData']['franchise_data']);

		$viewPath = $this->resolveViewPath();

		if (!$viewPath) {
			$viewPath = 'utility/not_found.php';
		}

		// Layout check
		if ($this->shouldLoadLayout()) {
			$cssPluginArr = $this->assets['css'] ?? [];
			include ROOTPATH . "/layout/header.php";
		}

		// Main View
		include ROOTPATH . "/views/" . $viewPath;

		if ($this->shouldLoadLayout()) {
			$jsPluginArr = $this->assets['js'] ?? [];
			include ROOTPATH . "/layout/footer.php";
		}
	}

	private function prepareViewData()
	{
		return [
			'pageContent' => $this->data,
			'page_title'  => $this->data['pageData']['page_title'] ?? '',
			'site_setting_data' => $this->data['site_setting_data'] ?? '',
			'isTinyAllowed' => $this->data['pageData']['tiny_allowed'] ?? false
		];
	}

	private function shouldLoadLayout()
	{
		return !in_array($this->route, ['login', 'logout']);
	}

	private function resolveViewPath()
	{
		$route = $this->route;

		if (!isset($this->viewRoutes[$route])) {
			return null;
		}

		$view = $this->viewRoutes[$route];

		// Role-based views (for home)
		if (is_array($view)) {
			$userType = $_SESSION['user_type'] ?? 'student';
			return $view[$userType] ?? null;
		}

		return $view;
	}

}
