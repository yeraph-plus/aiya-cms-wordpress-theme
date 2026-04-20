import { useState } from "react";
import { Copy, Check } from "lucide-react";
import { toast } from "sonner";
import { Button } from "@/components/ui/button";

interface ClipboardButtonProps {
  text: string;
  label?: string;
}

export default function ClipboardButton({ text, label = "复制" }: ClipboardButtonProps) {
  const [copied, setCopied] = useState(false);

  const handleCopy = async () => {
    try {
      // 尝试使用现代 Clipboard API
      if (navigator.clipboard && navigator.clipboard.writeText) {
        await navigator.clipboard.writeText(text);
        handleSuccess();
      } else {
        // 降级方案
        fallbackCopyTextToClipboard(text);
      }
    } catch (err) {
      console.error("Clipboard API failed: ", err);
      // 尝试降级方案
      const success = fallbackCopyTextToClipboard(text);
      if (success) {
        handleSuccess();
      } else {
        toast.error("复制失败，请手动复制");
      }
    }
  };

  const fallbackCopyTextToClipboard = (text: string): boolean => {
    const textArea = document.createElement("textarea");
    textArea.value = text;
    textArea.style.top = "0";
    textArea.style.left = "0";
    textArea.style.position = "fixed";
    textArea.style.opacity = "0";

    document.body.appendChild(textArea);
    textArea.focus();
    textArea.select();

    try {
      const successful = document.execCommand("copy");
      return successful;
    } catch (err) {
      console.error("Fallback: Oops, unable to copy", err);
      return false;
    } finally {
      document.body.removeChild(textArea);
    }
  };

  const handleSuccess = () => {
    toast.success("已复制到剪贴板");
    setCopied(true);
    setTimeout(() => {
      setCopied(false);
    }, 2000);
  };

  return (
    <Button
      variant="ghost"
      size="icon"
      className="absolute top-2 right-2 h-8 w-8 p-0 rounded-lg hover:bg-muted text-muted-foreground hover:text-foreground transition-colors z-10"
      onClick={handleCopy}
      disabled={copied}
      aria-label={label}
    >
      {copied ? (
        <Check className="h-4 w-4 text-green-500" />
      ) : (
        <Copy className="h-4 w-4" />
      )}
    </Button>
  );
}
