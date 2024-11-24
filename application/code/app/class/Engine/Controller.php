<?php
namespace App\Engine;

class Controller {

	private $twig;
	private $vars = [];
	private $roles = [];
	private $response;

	function __construct() {

		$loader = new \Twig\Loader\FilesystemLoader(PATH_VIEWS);
		$this->twig = new \Twig\Environment($loader, array(
			'cache' => CFG_VIEWS_CACHE,
			'debug' => CFG_VIEWS_DEBUG
		));

		$this->twig->addExtension(new \Twig\Extension\DebugExtension());

		$this->twig->addFilter(new \Twig\TwigFilter('hasRole', function ($string) {
			return HelperUtil::isUserAllowed($string);
		}));

		$this->twig->addFilter(new \Twig\TwigFilter('quantityUntilFinish', function ($string) {
			$datediff = strtotime($string) - time();
			return round($datediff / (60 * 60 * 24));
		}));

		$this->roles = require(PATH_APP . "/resources/roles.php");
		
		$this->vars['datetime'] = date('Y-m-d H:i:s');
		$this->vars['url_site'] = URL_SITE;
		$this->vars['url_assets'] = URL_ASSETS;
		$this->vars['lang'] = require(PATH_APP . "/resources/translation/pt-br.php");

		if(property_exists($this, 'route'))  {
			$this->vars['route'] = URL_SITE . $this->route;
		}

		if(isset($_SESSION['user_account']))  {
			$this->vars['user'] = $_SESSION['user_account'];
		}

		if(isset($_SESSION['user_view']))  {
			$this->vars['institution'] = $_SESSION['user_view'];
		}

		if(isset($_SESSION['flash']))  {
			$this->vars['flash'] = $_SESSION['flash'];
			if(!empty($this->vars['flash']['isRecord']) && $this->vars['flash']['isRecord']) {
				$this->vars['record'] = $this->vars['flash']['data'];
			}
		}

	}

    protected function getAllRoles() {
		return $this->roles;
    }

    protected function checkRole(string $role) {
		if(!HelperUtil::isUserAllowed($role)) {
			$this->flash(URL_SITE, $this->getVar('lang')['user_no_permission'], 'error');
			exit;
		}
    }

	protected function flashDataPost($message, $type = 'error') {
		$this->flash($_SERVER['REQUEST_URI'], $message, $type, $_POST, true);
	}

	protected function flash($redirect, $message, $type = 'info', $data = false, $isRecord = false) {
		$flash = [];
		$flash['message'] = $message;
		$flash['type'] = $type;
		if($data) {
			$flash['data'] = $data;
			$flash['isRecord'] = $isRecord;
		}

		$_SESSION['flash'] = $flash;

		$this->redirect($redirect);
	}

	protected function setResponse($page) {
		$this->response = $this->twig->render($page, $this->vars);
	}

	public function getResponse() {

		if(isset($_SESSION['flash']))  {
			unset($_SESSION['flash']);
		}
		
		return $this->response;
	}

	protected function setVar($var, $val) {
		$this->vars[$var] = $val;
	}

	protected function getVar($var) {
		return $this->vars[$var];
	}

	protected function redirect($redirect) {
		header("location: " . $redirect);
		exit;
	}

}
