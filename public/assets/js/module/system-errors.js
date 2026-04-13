import { showErrorDialog } from './notifications.js';

/**
 * Utility to check if we are in a development environment
 */
const isDev = window.location.hostname === 'localhost' || window.location.hostname.includes('127.0.0.1');

/**
 * Safely formats data for HTML display
 */
const formatDetail = (label, value) => 
    value ? `<div><strong>${label}:</strong> ${String(value).replace(/</g, '&lt;')}</div>` : '';

/**
 * Main Error Handler
 */
export const handleSystemError = async (errorSource, statusText, errorThrown) => {
    let details = '';
    let summary = errorThrown || statusText || 'System Error';

    // 1. Handle Native JavaScript Errors (try/catch)
    if (errorSource instanceof Error) {
        summary = errorSource.message;
        details += formatDetail('Type', errorSource.name);
        if (isDev) {
            details += `<div class="mt-2"><strong>Stack Trace:</strong><pre class="p-2 bg-light small">${errorSource.stack}</pre></div>`;
        }
    } 
    
    // 2. Handle Fetch API Response Objects
    else if (errorSource instanceof Response) {
        summary = `Server Error: ${errorSource.status} ${errorSource.statusText}`;
        details += formatDetail('URL', errorSource.url);
        
        try {
            const contentType = errorSource.headers.get('content-type');
            if (contentType && contentType.includes('application/json')) {
                const json = await errorSource.json();
                details += `<pre class="p-2 bg-light small">${JSON.stringify(json, null, 2)}</pre>`;
            } else {
                const text = await errorSource.text();
                details += `<pre class="p-2 bg-light small">${text.substring(0, 500)}</pre>`;
            }
        } catch {
            details += '<div><em>(Could not read response body)</em></div>';
        }
    } 
    
    // 3. Handle jQuery XHR Objects
    else if (errorSource?.responseText !== undefined) {
        summary = `Request Failed: ${errorSource.status}`;
        details += formatDetail('Status', statusText);
        details += `<pre class="p-2 bg-light small">${errorSource.responseText.substring(0, 500)}</pre>`;
    }

    // Combine and Display
    const finalMessage = `
        <div class="system-error-container">
            <h5 class="text-danger mb-3">${summary}</h5>
            ${details}
            ${!isDev ? '<p class="mt-3 small text-muted">Please contact support if this issue persists.</p>' : ''}
        </div>
    `;

    showErrorDialog(finalMessage);
};