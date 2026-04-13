<?php
namespace Core;

class Application {
    public Router $router;
    public Session $session;
    public Request $request;

    public function __construct() {
        $this->request = new Request();
        $this->router = new Router();
        $this->session = new Session();

        // Set global exception handler
        set_exception_handler([$this, 'handleException']);
    }

    public function run(): void {
        try {
            echo $this->router->resolve($this->request);
        } catch (\Exception $e) {
            $this->handleException($e);
        }
    }

    public function handleException(\Throwable $e): void {
        $code = $e->getCode() !== 0 ? $e->getCode() : 500;
        http_response_code($code);

        // In a real app, you would render a specific view like 'errors/_404.php'
        if ($_ENV['APP_ENV'] === 'development') {
            echo "<h1>Error {$code}</h1>";
            echo "<p><strong>Message:</strong> " . $e->getMessage() . "</p>";
            echo "<pre>" . $e->getTraceAsString() . "</pre>";
        } else {
            // Production friendly message
            echo "<h1>Something went wrong.</h1>";
            echo "<p>Please try again later.</p>";
        }
    }
}