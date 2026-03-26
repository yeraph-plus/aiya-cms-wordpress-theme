import * as React from "react"
import { Spinner } from '@/components/ui/spinner';
import { Button } from "@/components/ui/button"
import {
  Dialog,
  DialogContent,
  DialogDescription,
  DialogFooter,
  DialogHeader,
  DialogTitle,
} from "@/components/ui/dialog"
import { Input } from "@/components/ui/input"
import { Label } from "@/components/ui/label"
import { toast } from "sonner"
import { getConfig } from "@/lib/utils"

interface ForgotPasswordDialogProps {
  open: boolean
  onOpenChange: (open: boolean) => void
  onBackToLogin: () => void
}

export function ForgotPasswordDialog({ open, onOpenChange, onBackToLogin }: ForgotPasswordDialogProps) {
  const [isLoading, setIsLoading] = React.useState(false)
  const [error, setError] = React.useState<string | null>(null)
  const [email, setEmail] = React.useState("")
  const [isSuccess, setIsSuccess] = React.useState(false)

  const handleSubmit = async (e: React.FormEvent) => {
    e.preventDefault()
    
    const { apiUrl, apiNonce } = getConfig()
    if (!apiNonce) {
      setError("Missing security nonce")
      return
    }

    setIsLoading(true)
    setError(null)

    try {
      const response = await fetch(`${apiUrl}/aiya/v1/forgot_password`, {
        method: "POST",
        headers: {
          "Content-Type": "application/json",
          "X-WP-Nonce": apiNonce,
        },
        body: JSON.stringify({
          email,
        }),
      })

      const data = await response.json()

      if (data.code && data.code !== 'success' && data.code !== 200) {
        throw new Error(data.message || data.detail || "请求失败")
      }

      // Success
      setIsSuccess(true)
      toast.success(data.message || "密码重置链接已发送到您的邮箱")
      
    } catch (err: any) {
      console.error(err)
      setError(err.message || "发生错误，请稍后重试")
    } finally {
      setIsLoading(false)
    }
  }

  const handleOpenChange = (newOpen: boolean) => {
    if (!newOpen) {
      // Reset state when closing
      setTimeout(() => {
        setIsSuccess(false)
        setEmail("")
        setError(null)
      }, 300)
    }
    onOpenChange(newOpen)
  }

  return (
    <Dialog open={open} onOpenChange={handleOpenChange}>
      <DialogContent className="sm:max-w-[425px]">
        <DialogHeader>
          <DialogTitle>找回密码</DialogTitle>
          <DialogDescription>
            {isSuccess 
              ? "邮件发送成功" 
              : "请输入您的注册邮箱，我们将向您发送重置密码的链接。"}
          </DialogDescription>
        </DialogHeader>

        {isSuccess ? (
          <div className="py-6 text-center space-y-4">
            <div className="text-green-600 bg-green-50 p-4 rounded-md">
              <p>重置密码链接已发送至：</p>
              <p className="font-medium mt-1">{email}</p>
            </div>
            <p className="text-sm text-muted-foreground">
              请查收邮件并按照提示重置密码。如果没有收到，请检查垃圾邮件箱。
            </p>
            <div className="pt-4">
              <Button variant="outline" onClick={onBackToLogin} className="w-full">
                返回登录
              </Button>
            </div>
          </div>
        ) : (
          <form onSubmit={handleSubmit} className="grid gap-4 py-4">
            <div className="grid gap-2">
              <Label htmlFor="forgot-email">邮箱</Label>
              <Input
                id="forgot-email"
                type="email"
                placeholder="name@example.com"
                value={email}
                onChange={(e) => setEmail(e.target.value)}
                disabled={isLoading}
                required
              />
            </div>
            
            {error && (
              <div className="text-sm text-destructive bg-destructive/10 p-2 rounded">
                {error}
              </div>
            )}
            
            <DialogFooter className="flex-col sm:justify-between gap-2">
              <div className="flex justify-between w-full items-center mt-4">
                <Button 
                  type="button" 
                  variant="ghost" 
                  onClick={onBackToLogin}
                  disabled={isLoading}
                >
                  返回登录
                </Button>
                <Button type="submit" disabled={isLoading}>
                  {isLoading && <Spinner className="mr-2 h-4 w-4" />}
                  发送重置链接
                </Button>
              </div>
            </DialogFooter>
          </form>
        )}
      </DialogContent>
    </Dialog>
  )
}
