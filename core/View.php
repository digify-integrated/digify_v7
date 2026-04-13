<?php
namespace Core;

class View {
    public static string $layout = 'main'; // Default layout

    /**
     * Renders a view file, supporting layouts and basic blade syntax.
     * @param string $view The view to load
     * @param array $params Variables to pass to the view
     * @param string|null $layout Override the default layout
     */
    public static function render(string $view, array $params = [], string $layout = null): void {
        $currentLayout = $layout ?? self::$layout;

        $viewRaw = self::getViewRawContent($view);
        $layoutRaw = self::getLayoutRawContent($currentLayout);
        
        // Use preg_replace to be flexible with spaces around "content"
        $fullRawContent = preg_replace('/\{\{\s*content\s*\}\}/', $viewRaw, $layoutRaw);

        $compiledContent = self::compileBlade($fullRawContent);

        // Add $params to the eval scope so @include can see them
        extract($params);
        eval("?>$compiledContent");
    }

    protected static function layoutContent(string $layoutName): string {
        ob_start();
        
        // FIX: Support dot notation for layouts too (e.g., 'admin.dashboard')
        $layoutPath = str_replace('.', '/', $layoutName);
        $fullPath = "../app/Views/layouts/{$layoutPath}.php";
        
        if (file_exists($fullPath)) {
            include_once $fullPath;
        } else {
            // Graceful error handling for missing layouts
            echo "<div style='color:red; font-weight:bold;'>Layout Error: [$layoutName] not found. Looked in: $fullPath</div>";
        }
        
        return ob_get_clean();
    }

    protected static function renderOnlyView(string $view, array $params): string {
        extract($params); // Transforms array keys into variables
        ob_start();
        
        // --- FIX: Convert dot-notation to directory slashes ---
        $viewPath = str_replace('.', '/', $view);
        
        $path = "../app/Views/$viewPath.php";
        
        if (file_exists($path)) {
            $content = file_get_contents($path);
            
            // Blade-like syntax compilation
            $content = self::compileBlade($content);
            
            // Execute the compiled PHP
            eval("?>$content");
        } else {
            // Updated error to show EXACTLY where it tried to look
            echo "<div style='color:red; font-weight:bold;'>View Error: [$view] not found. Looked in: $path</div>";
        }
        
        return ob_get_clean();
    }

    /**
     * Translates custom syntax ({{ $var }}, @csrf) into standard PHP.
     */
    private static function compileBlade(string $content): string {
        // 1. Improved Echo: Supports newlines and adds htmlspecialchars for security
        // Use the 's' flag at the end for multiline support
        $content = preg_replace_callback('/\{\{\s*(.+?)\s*\}\}/s', function($matches) {
            // Return a secure PHP echo statement
            return "<?php echo htmlspecialchars((string)({$matches[1]}), ENT_QUOTES); ?>";
        }, $content);

        // 2. Raw Echo: For when you WANT to output HTML (Optional syntax: {!! $var !!})
        $content = preg_replace('/\{!!\s*(.+?)\s*!!\}/s', '<?php echo $1; ?>', $content);

        // 3. CSRF replacement
        $csrfInput = '<input type="hidden" name="csrf_token" value="<?= (new \Core\Session())->getCsrfToken() ?>">';
        $content = str_replace('@csrf', $csrfInput, $content);

        // 4. @include support (as we discussed before)
        $content = preg_replace(
            '/@include\(\s*\'(.*?)\'\s*\)/', 
            '<?= self::renderOnlyView("$1", $params) ?>', 
            $content
        );

        return $content;
    }

    protected static function getViewRawContent(string $view): string {
        $viewPath = str_replace('.', '/', $view);
        $path = "../app/Views/$viewPath.php";
        return file_exists($path) ? file_get_contents($path) : "View [$view] not found.";
    }

    protected static function getLayoutRawContent(string $layoutName): string {
        $layoutPath = str_replace('.', '/', $layoutName);
        $path = "../app/Views/layouts/$layoutPath.php";
        return file_exists($path) ? file_get_contents($path) : "Layout [$layoutName] not found.";
    }
}