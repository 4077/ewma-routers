<?php namespace ewma\routers;

class Svc extends \ewma\service\Service
{
    /**
     * @var self
     */
    public static $instance;

    /**
     * @return \ewma\routers\Svc
     */
    public static function getInstance()
    {
        if (null === static::$instance) {
            static::$instance = new self;
            static::$instance->__register__();
        }

        return static::$instance;
    }

    protected $services = ['cats', 'routes'];

    /**
     * @var $cats \ewma\routers\Svc\Cats
     */
    public $cats = \ewma\routers\Svc\Cats::class;

    /**
     * @var $cats \ewma\routers\Svc\Routes
     */
    public $routes = \ewma\routers\Svc\Routes::class;

    //
    //
    //

    public function compileAll()
    {
        clean_dir(abs_path('cache/routers'));

        $routers = \ewma\routers\models\Router::where('enabled', true)->get();

        foreach ($routers as $router) {
            $this->compile($router);
        }

        $this->compileEnabledRouters();

        return 'routers compiled: ' . count($routers);
    }

    public function compile(\ewma\routers\models\Router $router)
    {
        $compiler = new \ewma\routers\Svc\Compiler;

        awrite(abs_path('cache/routers/id/' . $router->id . '.php'), $compiler->compile($router));
    }

    private function compileEnabledRouters()
    {
        $routers = \ewma\routers\models\Router::where('enabled', true)->orderBy('position')->get();

        awrite(abs_path('cache/routers/enabled_routers.php'), table_column($routers, 'id'));
    }

    public function render($routeString = null)
    {
        if (null === $routeString) {
            $routeString = app()->route;
        }

        $renderer = new \ewma\routers\Svc\Renderer;

        return $renderer->render($routeString);
    }

    public function create()
    {
        $router = \ewma\routers\models\Router::create([]);

        $this->getRootRoute($router);

        return $router;
    }

    public function duplicate(\ewma\routers\models\Router $router)
    {
        $newRouter = \ewma\routers\models\Router::create($router->toArray());

        $routerRootRoute = $this->getRootRoute($router);
        $newRouterRootRoute = $this->getRootRoute($newRouter);

        $this->routes->import($newRouterRootRoute, $this->routes->export($routerRootRoute), true);

        return $newRouter;
    }

    public function delete(\ewma\routers\models\Router $router)
    {
        $router->routes()->delete();
        $router->delete();

        routers()->compileAll();
    }

    public function createRoute(\ewma\routers\models\Router $router)
    {
        return $this->routes->create($this->getRootRoute($router));
    }

    /**
     * @param models\Router $router
     *
     * @return \ewma\routers\models\Route
     */
    public function getRootRoute(\ewma\routers\models\Router $router)
    {
        if (!$node = $router->routes()->where('parent_id', 0)->first()) {
            $node = $router->routes()->create([
                                                  'parent_id' => 0,
                                                  'enabled'   => true
                                              ]);
        }

        return $node;
    }
}
