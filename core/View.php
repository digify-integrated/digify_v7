<?php
namespace Core;

class View {
    public static string $layout = 'main';

    /**
     * Renders a view file, supporting layouts and basic blade syntax.
     */
    public static function render(string $view, array $params = []): void {
        $layoutContent = self::layoutContent();
        $viewContent = self::renderOnlyView($view, $params);
        
        // Inject view into layout
        echo str_replace('{{content}}', $viewContent, $layoutContent);
    }

    protected static function layoutContent(): string {
        ob_start();
        include_once "../app/Views/layouts/" . self::$layout . ".php";
        return ob_get_clean();
    }

    protected static function renderOnlyView(string $view, array $params): string {
        extract($params); // Transforms array keys into variables
        ob_start();
        
        $path = "../app/Views/$view.php";
        if (file_exists($path)) {
            $content = file_get_contents($path);
            
            // Blade-like syntax compilation
            $content = self::compileBlade($content);
            
            // Execute the compiled PHP
            eval("?>$content");
        } else {
            echo "View [$view] not found.";
        }
        
        return ob_get_clean();
    }

    /**
     * Translates custom syntax ({{ $var }}, @csrf) into standard PHP.
     */
    private static function compileBlade(string $content): string {
        // Compile {{ $var }} to <?= $var ? >
        $content = preg_replace('/\{\{\s*(.+?)\s*\}\}/', '<?= $1 ?>', $content);
        
        // Compile @csrf to a hidden input field
        $csrfInput = '<input type="hidden" name="csrf_token" value="<?= (new \Core\Session())->getCsrfToken() ?>">';
        $content = str_replace('@csrf', $csrfInput, $content);

        return $content;
    }
}