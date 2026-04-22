import { __ } from '@wordpress/i18n';

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
import { Checkbox } from "@/components/ui/checkbox"
import { toast } from "sonner"
import { getConfig } from "@/lib/utils"
import { ForgotPasswordDialog } from "./dialog-forgot-password"

interface LoginDialogProps {
  open: boolean
  onOpenChange: (open: boolean) => void
}

export function LoginDialog({ open, onOpenChange }: LoginDialogProps) {
  const [isLoading, setIsLoading] = React.useState(false)
  const [error, setError] = React.useState<string | null>(null)
  const [email, setEmail] = React.useState("")
  const [password, setPassword] = React.useState("")
  const [remember, setRemember] = React.useState(false)
  const [showForgot, setShowForgot] = React.useState(false)

  const handleSubmit = async (e: React.FormEvent) => {
    e.preventDefault()
    const { apiUrl, apiNonce } = getConfig()
    if (!apiNonce) {
      setError(__('页面已过期，请刷新页面重试', 'aiya-cms'))
      return
    }

    setIsLoading(true)
    setError(null)

    try {
      const response = await fetch(`${apiUrl}/aiya/v1/login`, {
        method: "POST",
        headers: {
          "Content-Type": "application/json",
          "X-WP-Nonce": apiNonce,
        },
        body: JSON.stringify({
          email,
          password,
          remember,
        }),
      })

      const data = await response.json()

      if (!response.ok) {
        throw new Error(data.message || __('请求失败', 'aiya-cms'))
      }

      // Login successful, show toast and reload page
      toast.success(__('登录成功', 'aiya-cms'))
      setTimeout(() => {
        window.location.reload()
      }, 1000)
    } catch (err) {
      setError(err instanceof Error ? err.message : __('登录发生错误', 'aiya-cms'))
    } finally {
      setIsLoading(false)
    }
  }

  if (showForgot) {
    return (
      <ForgotPasswordDialog
        open={open}
        onOpenChange={(isOpen) => {
          if (!isOpen) {
            setShowForgot(false)
            onOpenChange(false)
          }
        }}
        onBackToLogin={() => setShowForgot(false)}
      />
    )
  }

  return (
    <Dialog open={open} onOpenChange={onOpenChange}>
      <DialogContent className="sm:max-w-[425px]">
        <DialogHeader>
          <DialogTitle>{__('登录', 'aiya-cms')}</DialogTitle>
          <DialogDescription>
            {__('请输入您的邮箱和密码登录。', 'aiya-cms')}
          </DialogDescription>
        </DialogHeader>
        <form onSubmit={handleSubmit} className="grid gap-4 py-4">
          <div className="grid gap-2">
            <Label htmlFor="email">{__('邮箱', 'aiya-cms')}</Label>
            <Input
              id="email"
              type="text"
              placeholder={__('登录邮箱', 'aiya-cms')}
              value={email}
              onChange={(e) => setEmail(e.target.value)}
              disabled={isLoading}
              required
            />
          </div>
          <div className="grid gap-2">
            <div className="flex items-center justify-between">
              <Label htmlFor="password">{__('密码', 'aiya-cms')}</Label>
              <Button
                variant="link"
                className="px-0 h-auto text-xs text-muted-foreground"
                type="button"
                onClick={() => setShowForgot(true)}
              >
                {__('忘记密码？', 'aiya-cms')}
              </Button>
            </div>
            <Input
              id="password"
              type="password"
              placeholder={__('登录密码', 'aiya-cms')}
              value={password}
              onChange={(e) => setPassword(e.target.value)}
              disabled={isLoading}
              required
            />
          </div>

          <div className="flex items-center space-x-2">
            <Checkbox
              id="remember"
              checked={remember}
              onCheckedChange={(checked) => setRemember(checked as boolean)}
              disabled={isLoading}
            />
            <label
              htmlFor="remember"
              className="text-sm font-medium leading-none peer-disabled:cursor-not-allowed peer-disabled:opacity-70"
            >
              {__('记住我', 'aiya-cms')}
            </label>
          </div>

          {error && (
            <div className="text-sm text-destructive">
              {error}
            </div>
          )}
          <DialogFooter>
            <Button type="submit" disabled={isLoading}>
              {isLoading && <Spinner className="mr-2" />}
              {__('登录', 'aiya-cms')}
            </Button>
          </DialogFooter>
        </form>
      </DialogContent>
    </Dialog>
  )   
}
