<script>
(() => {
    const DEFAULT_THEME = "light";
    const root = document.documentElement;

    if (!root) return;

    const getStoredTheme = () => {
        try {
            return localStorage.getItem("data-bs-theme");
        } catch {
            return null;
        }
    };

    const getSystemTheme = () =>
        window.matchMedia("(prefers-color-scheme: dark)").matches ? "dark" : "light";

    const resolveTheme = () => {
        const attrTheme = root.getAttribute("data-bs-theme-mode");
        if (attrTheme) return attrTheme;

        const storedTheme = getStoredTheme();
        if (storedTheme) return storedTheme;

        return DEFAULT_THEME;
    };

    let theme = resolveTheme();

    if (theme === "system") {
        theme = getSystemTheme();
    }

    root.setAttribute("data-bs-theme", theme);
})();
</script>