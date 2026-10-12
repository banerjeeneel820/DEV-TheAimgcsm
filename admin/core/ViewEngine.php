<?php
defined('ROOTPATH') or exit('No direct script access allowed');

class ViewEngine extends BaseController
{
	private $route;
	private $data;
	private $assets;
	private $sidebarMenus;
	private $viewRoutes;
	private $viewLayouts;
	private $permissionService;

	public function __construct($container, $route, $data)
	{
		parent::__construct($container);

		$this->route = $route;
		$this->data = $data;
		$this->assets = $data['assetData'] ?? [];

		$this->viewRoutes = require ROOTPATH . '/config/view_routes.php';
		$this->viewLayouts = require ROOTPATH . '/config/view_layouts.php';

		$this->permissionService = $container->get('permissionService');
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

		extract($data, EXTR_SKIP);
		extract($data['pageData'] ?? [], EXTR_SKIP);

		$viewConfig = $this->data['viewConfig'] ?? [];

		$viewPath = $viewConfig['view'] ?? $this->resolveViewPath();
		$layoutName = $this->getLayoutName();
		$layoutConfig = $this->viewLayouts[$layoutName] ?? null;

		$fullViewPath = $this->resolveFilePath(
			$layoutConfig['view_root'] ?? '',
			$viewPath ?? ''
		);

		if (!$fullViewPath || !is_file($fullViewPath)) {
			$this->renderFallbackView(404);
			return;
		}

		$permissionService = $this->permissionService;
		$currentRoute = $this->route;

		// This is where shouldLoadLayout() is used.
		if (!$this->shouldLoadLayout()) {
			include $fullViewPath;
			return;
		}

		$sidebarMenus = null;
		$cssPluginArr = $this->assets['css'] ?? [];
		$jsPluginArr = $this->assets['js'] ?? [];

		if (!empty($layoutConfig['sidebar'])) {
			$sidebarPath = $layoutConfig['sidebar'];

			if (is_file($sidebarPath)) {
				$sidebarMenus = require $sidebarPath;
			}
		}

		$headerPath = $this->resolveFilePath(
			$layoutConfig['layout_root'] ?? '',
			$layoutConfig['header'] ?? ''
		);

		$footerPath = $this->resolveFilePath(
			$layoutConfig['layout_root'] ?? '',
			$layoutConfig['footer'] ?? ''
		);

		if (!$headerPath || !is_file($headerPath)) {
			throw new RuntimeException('Layout header not found.');
		}

		if (!$footerPath || !is_file($footerPath)) {
			throw new RuntimeException('Layout footer not found.');
		}

		include $headerPath;
		include $fullViewPath;
		include $footerPath;
	}

	private function resolveFilePath($root, $relativePath)
	{
		if (
			!is_string($root) ||
			!is_string($relativePath) ||
			$root === '' ||
			$relativePath === '' ||
			str_contains($relativePath, "\0") ||
			str_contains($relativePath, '..') ||
			str_starts_with($relativePath, '/') ||
			str_starts_with($relativePath, '\\')
		) {
			return null;
		}

		return rtrim($root, '/\\')
			. DIRECTORY_SEPARATOR
			. str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $relativePath);
	}

	private function renderFallbackView($status = 404)
	{
		http_response_code($status);

		$layoutName = $this->getLayoutName();
		$layoutConfig = $this->viewLayouts[$layoutName] ?? null;

		$fallbacks = $layoutConfig['fallbacks'] ?? [];

		// Use the requested error page, or fall back to 404.
		$fallbackView = $fallbacks[$status]
			?? $fallbacks[404]
			?? null;

		$viewRoot = $layoutConfig['view_root'] ?? null;

		$fallbackPath = $viewRoot && $fallbackView
			? $this->resolveFilePath($viewRoot, $fallbackView)
			: null;

		if ($fallbackPath && is_file($fallbackPath)) {
			include $fallbackPath;
			return;
		}

		// Safe generic fallback if the configured template is missing.
		echo $status === 403 ? 'Access denied.' : 'Page not found.';
	}

	private function getLayoutName()
	{
		$routeLayouts = $this->viewLayouts['layout_routes'] ?? [];

		return $routeLayouts[$this->route]
			?? ($this->data['viewConfig']['layout'] ?? 'admin');
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
		$routeLayouts = $this->viewLayouts['layout_routes'] ?? [];

		$layoutName = $routeLayouts[$this->route]
			?? ($this->data['viewConfig']['layout'] ?? 'admin');

		$layoutConfig = $this->viewLayouts[$layoutName] ?? null;

		return $layoutConfig !== null
			&& !empty($layoutConfig['enabled']);
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
