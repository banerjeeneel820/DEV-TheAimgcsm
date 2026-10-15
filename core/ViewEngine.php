<?php
namespace App\Core;

use RuntimeException;
use App\Core\BaseController;

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

	/**
	 * Apply authentication, permission, maintenance and logout guards.
	 */
	private function applyGuards()
	{
		// Login fallback
		if (empty($this->route) && empty($_SESSION['user_id'])) {
			$this->route = 'login';
		}

		// Permission check
		if (!($this->data['pageData']['page_permission'] ?? true)) {
			$status = $this->data['pageData']['status'] ?? 403;

			$this->route = ($status == 403)
				? 'no_access'
				: 'not_found';
		}

		// Maintenance check
		if (
			!in_array(
				$_SESSION['user_type'] ?? '',
				['admin', 'developer'],
				true
			)
		) {
			if (!($this->data['check_site_maintenance'] ?? true)) {
				$this->route = 'under_maintenance';
			}
		}

		// Logout
		if ($this->route === 'logout') {
			$application = $this->getApplicationName();

			// Clear session data.
			$_SESSION = [];

			if (session_status() === PHP_SESSION_ACTIVE) {
				session_destroy();
			}

			// Redirect according to the current application.
			$redirectUrl = match ($application) {
				'frontend' => FRONT_SITE_URL,
				'admin'    => SITE_URL,
				default    => throw new RuntimeException(
					'Logout redirect is not configured for this application.'
				),
			};

			header('Location: ' . $redirectUrl);
			exit;
		}
	}

	/**
	 * Resolve the current application from explicit view configuration.
	 *
	 * No application is silently assumed.
	 */

	private function getApplicationName()
	{
		$viewConfig = $this->data['viewConfig'] ?? [];

		// 1. Use the application explicitly provided by the controller.
		$application = $viewConfig['application'] ?? null;

		if (
			is_string($application) &&
			isset($this->viewLayouts[$application]) &&
			!empty($this->viewLayouts[$application]['enabled']) &&
			!empty($this->viewLayouts[$application]['view_root'])
		) {
			return $application;
		}

		// 2. Infer the application from the configured layout.
		$layout = $viewConfig['layout'] ?? null;

		if (
			is_string($layout) &&
			isset($this->viewLayouts[$layout]) &&
			!empty($this->viewLayouts[$layout]['enabled']) &&
			!empty($this->viewLayouts[$layout]['view_root'])
		) {
			return $layout;
		}

		// 3. Infer from the authenticated user's role.
		$userType = $_SESSION['user_type'] ?? '';

		if (
			is_string($userType) &&
			isset($this->viewLayouts[$userType]) &&
			!empty($this->viewLayouts[$userType]['enabled']) &&
			!empty($this->viewLayouts[$userType]['view_root'])
		) {
			return $userType;
		}

		// 4. Fall back to a configured default application.
		$defaultApplication = $this->viewLayouts['default_application'] ?? null;

		if (
			is_string($defaultApplication) &&
			isset($this->viewLayouts[$defaultApplication]) &&
			!empty($this->viewLayouts[$defaultApplication]['enabled']) &&
			!empty($this->viewLayouts[$defaultApplication]['view_root'])
		) {
			return $defaultApplication;
		}

		throw new RuntimeException(
			'Unable to determine the application context for this view.'
		);
	}

	/**
	 * Determine which layout should be used.
	 *
	 * Route-specific layout takes priority, followed by
	 * the explicitly configured layout and application default.
	 */
	private function getLayoutName()
	{
		$application = $this->getApplicationName();
		$applicationConfig = $this->viewLayouts[$application];

		$routeLayouts = $this->viewLayouts['layout_routes'] ?? [];

		if (array_key_exists($this->route, $routeLayouts)) {
			return $routeLayouts[$this->route];
		}

		$requestedLayout = $this->data['viewConfig']['layout'] ?? null;

		if ($requestedLayout !== null) {
			return $requestedLayout;
		}

		return $applicationConfig['default_layout'] ?? $application;
	}

	/**
	 * Determine whether the selected layout should be rendered.
	 */
	private function shouldLoadLayout()
	{
		$application = $this->getApplicationName();
		$applicationConfig = $this->viewLayouts[$application];

		$layoutName = $this->getLayoutName();

		// Layout-free views use the application's nested "none" config.
		if ($layoutName === 'none') {
			return false;
		}

		// Only allow the application's configured layout.
		if ($layoutName !== $application) {
			return false;
		}

		return !empty($applicationConfig['enabled'])
			&& !empty($applicationConfig['layout_root'])
			&& !empty($applicationConfig['header'])
			&& !empty($applicationConfig['footer']);
	}

	/**
	 * Render the requested view with or without its layout.
	 */
	private function renderViewWithLayout()
	{
		$data = $this->prepareViewData();

		extract($data, EXTR_SKIP);
		extract($data['pageData'] ?? [], EXTR_SKIP);

		$viewConfig = $this->data['viewConfig'] ?? [];

		$application = $this->getApplicationName();
		$applicationConfig = $this->viewLayouts[$application];

		$viewPath = $viewConfig['view'] ?? $this->resolveViewPath();

		$layoutName = $this->getLayoutName();

		// Select the configuration for the requested layout.
		if ($layoutName === 'none') {
			$layoutConfig = $applicationConfig['none'] ?? null;
		} elseif ($layoutName === $application) {
			$layoutConfig = $applicationConfig;
		} else {
			$layoutConfig = null;
		}

		if ($layoutConfig === null) {
			$this->renderFallbackView(404);
			return;
		}

		// The selected layout may override the application's view root.
		$viewRoot = $layoutConfig['view_root']
			?? $applicationConfig['view_root']
			?? '';

		$fullViewPath = $this->resolveFilePath(
			$viewRoot,
			$viewPath ?? ''
		);

		if (!$fullViewPath || !is_file($fullViewPath)) {
			$this->renderFallbackView(404);
			return;
		}

		$permissionService = $this->permissionService;
		$currentRoute = $this->route;

		// Render a view without header/footer when layout is disabled.
		if (!$this->shouldLoadLayout()) {
			include $fullViewPath;
			return;
		}

		$sidebarMenus = null;

		$cssPluginArr = $this->assets['css'] ?? [];
		$jsPluginArr = $this->assets['js'] ?? [];

		// Load sidebar configuration when available.
		if (!empty($layoutConfig['sidebar'])) {
			$sidebarPath = $layoutConfig['sidebar'];

			if (is_file($sidebarPath)) {
				$sidebarMenus = require $sidebarPath;
			}
		}

		// Resolve header and footer.
		$headerPath = $this->resolveFilePath(
			$layoutConfig['layout_root'] ?? '',
			$layoutConfig['header'] ?? ''
		);

		$footerPath = $this->resolveFilePath(
			$layoutConfig['layout_root'] ?? '',
			$layoutConfig['footer'] ?? ''
		);

		if (!$headerPath || !is_file($headerPath)) {
			throw new RuntimeException(
				'Layout header not found for application: ' . $application
			);
		}

		if (!$footerPath || !is_file($footerPath)) {
			throw new RuntimeException(
				'Layout footer not found for application: ' . $application
			);
		}

		include $headerPath;
		include $fullViewPath;
		include $footerPath;
	}

	/**
	 * Safely resolve a relative path against its configured root.
	 */
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
			. str_replace(
				['/', '\\'],
				DIRECTORY_SEPARATOR,
				$relativePath
			);
	}

	/**
	 * Render an application-specific fallback page.
	 */
	private function renderFallbackView($status = 404)
	{
		http_response_code($status);

		$application = $this->getApplicationName();
		$applicationConfig = $this->viewLayouts[$application];

		$layoutName = $this->getLayoutName();

		// Use nested "none" configuration when applicable.
		if ($layoutName === 'none') {
			$layoutConfig = $applicationConfig['none'] ?? [];
		} elseif ($layoutName === $application) {
			$layoutConfig = $applicationConfig;
		} else {
			$layoutConfig = [];
		}

		// Prefer the selected context's fallbacks.
		// Fall back to the application's general configuration.
		$fallbacks = $layoutConfig['fallbacks']
			?? $applicationConfig['fallbacks']
			?? [];

		$fallbackView = $fallbacks[$status]
			?? $fallbacks[404]
			?? null;

		$viewRoot = $layoutConfig['view_root']
			?? $applicationConfig['view_root']
			?? null;

		$fallbackPath = $viewRoot && $fallbackView
			? $this->resolveFilePath($viewRoot, $fallbackView)
			: null;

		if ($fallbackPath && is_file($fallbackPath)) {
			$permissionService = $this->permissionService;
			$currentRoute = $this->route;

			$cssPluginArr = $this->assets['css'] ?? [];
			$jsPluginArr = $this->assets['js'] ?? [];

			include $fallbackPath;
			return;
		}

		// Safe generic fallback.
		echo $status === 403
			? 'Access denied.'
			: 'Page not found.';
	}

	/**
	 * Prepare shared view variables.
	 */
	private function prepareViewData()
	{
		return [
			'pageContent' => $this->data,
			'page_title' => $this->data['pageData']['page_title'] ?? '',
			'site_setting_data' => $this->data['site_setting_data'] ?? '',
			'isTinyAllowed' => $this->data['pageData']['tiny_allowed'] ?? false,
		];
	}

	/**
	 * Resolve the view path associated with the current route.
	 */
	private function resolveViewPath()
	{
		$route = $this->route;

		if (!isset($this->viewRoutes[$route])) {
			return null;
		}

		$view = $this->viewRoutes[$route];

		// Role-based views.
		if (is_array($view)) {
			$userType = $_SESSION['user_type'] ?? 'student';

			return $view[$userType] ?? null;
		}

		return $view;
	}
}
