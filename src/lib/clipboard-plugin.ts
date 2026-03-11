import { toast } from 'sonner';

/**
 * Initialize clipboard functionality for elements with .aya-clipboard-btn class
 */
export function initClipboard() {
    // Use event delegation for better performance and handling dynamic content
    document.addEventListener('click', async (e) => {
        const target = (e.target as Element).closest('.aya-clipboard-btn');
        if (!target) return;
        
        const btn = target as HTMLButtonElement;
        const text = btn.getAttribute('data-clipboard-text');
        
        if (!text) return;
        
        try {
            // Try modern API first
            if (navigator.clipboard && navigator.clipboard.writeText) {
                await navigator.clipboard.writeText(text);
                handleSuccess(btn);
            } else {
                // Fallback for older browsers or non-secure contexts
                fallbackCopyTextToClipboard(text, btn);
            }
        } catch (err) {
            console.error('Clipboard API failed: ', err);
            // Try fallback
            fallbackCopyTextToClipboard(text, btn);
        }
    });
}

function fallbackCopyTextToClipboard(text: string, btn: HTMLButtonElement) {
    const textArea = document.createElement("textarea");
    textArea.value = text;
    
    // Avoid scrolling to bottom
    textArea.style.top = "0";
    textArea.style.left = "0";
    textArea.style.position = "fixed";
    textArea.style.opacity = "0"; // Invisible but selectable
    
    document.body.appendChild(textArea);
    textArea.focus();
    textArea.select();

    try {
        const successful = document.execCommand('copy');
        if (successful) {
            handleSuccess(btn);
        } else {
            toast.error('复制失败，请手动复制');
        }
    } catch (err) {
        console.error('Fallback: Oops, unable to copy', err);
        toast.error('复制失败');
    }

    document.body.removeChild(textArea);
}

function handleSuccess(btn: HTMLButtonElement) {
    toast.success('已复制到剪贴板');
    
    // Visual feedback
    const originalContent = btn.innerHTML;
    
    // Change icon to checkmark
    btn.innerHTML = `<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-check text-green-500"><path d="M20 6 9 17l-5-5"/></svg>`;
    
    // Disable button temporarily
    btn.disabled = true;
    
    setTimeout(() => {
        btn.innerHTML = originalContent;
        btn.disabled = false;
    }, 2000);
}
