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
import { Field } from "@/components/ui/field"
import { toast } from "sonner"
import { getConfig } from "@/lib/utils"

interface RegisterDialogProps {
  open: boolean
  onOpenChange: (open: boolean) => void
}

export function RegisterDialog({ open, onOpenChange }: RegisterDialogProps) {
  const [isLoading, setIsLoading] = React.useState(false)
  const [error, setError] = React.useState<string | null>(null)
  const [username, setUsername] = React.useState("")
  const [email, setEmail] = React.useState("")
  const [password, setPassword] = React.useState("")
  const [confirmPassword, setConfirmPassword] = React.useState("")

  const handleSubmit = async (e: React.FormEvent) => {
    e.preventDefault()
    
    if (password !== confirmPassword) {
      setError(__('两次输入的密码不一致', 'aiya-cms'))
      return
    }

    const { apiUrl, apiNonce } = getConfig()
    if (!apiNonce) {
      setError(__('页面已过期，请刷新页面重试', 'aiya-cms'))
      return
    }

    setIsLoading(true)
    setError(null)

    try {
      const response = await fetch(`${apiUrl}/aiya/v1/register`, {
        method: "POST",
        headers: {
          "Content-Type": "application/json",
          "X-WP-Nonce": apiNonce,
        },
        body: JSON.stringify({
          username,
          email,
          password,
          password_confirm: confirmPassword,
        }),
      })

      const data = await response.json()

      if (!response.ok) {
        throw new Error(data.message || __('注册失败', 'aiya-cms'))
      }

      // Registration successful, show toast and reload page (auto-login handled by API)
      toast.success(__('注册成功', 'aiya-cms'))
      setTimeout(() => {
        window.location.reload()
      }, 1000)
    } catch (err) {
      setError(err instanceof Error ? err.message : __('注册发生错误', 'aiya-cms'))
    } finally {
      setIsLoading(false)
    }
  }

  return (
    <Dialog open={open} onOpenChange={onOpenChange}>
      <DialogContent className="sm:max-w-[425px]">
        <DialogHeader>
          <DialogTitle>{__('注册', 'aiya-cms')}</DialogTitle>
          <DialogDescription>
            {__('请输入您的信息以创建新账户。', 'aiya-cms')}
          </DialogDescription>
        </DialogHeader>
        <form onSubmit={handleSubmit} className="grid gap-4 py-4">
          <Field>
            <Label htmlFor="username">{__('用户名', 'aiya-cms')}</Label>
            <Input
              id="username"
              type="text"
              placeholder={__('您的用户名', 'aiya-cms')}
              value={username}
              onChange={(e) => setUsername(e.target.value)}
              disabled={isLoading}
              required
            />
          </Field>
          <Field>
            <Label htmlFor="email">{__('邮箱', 'aiya-cms')}</Label>
            <Input
              id="email"
              type="email"
              placeholder="name@example.com"
              value={email}
              onChange={(e) => setEmail(e.target.value)}
              disabled={isLoading}
              required
            />
          </Field>
          <Field>
            <Label htmlFor="password">{__('密码', 'aiya-cms')}</Label>
            <Input
              id="password"
              type="password"
              value={password}
              onChange={(e) => setPassword(e.target.value)}
              disabled={isLoading}
              required
            />
          </Field>
          <Field>
            <Label htmlFor="confirmPassword">{__('确认密码', 'aiya-cms')}</Label>
            <Input
              id="confirmPassword"
              placeholder={__('确认您的密码', 'aiya-cms')}
              type="password"
              value={confirmPassword}
              onChange={(e) => setConfirmPassword(e.target.value)}
              disabled={isLoading}
              required
            />
          </Field>
          {error && (
            <div className="text-sm text-destructive">
              {error}
            </div>
          )}
          <DialogFooter>
            <Button type="submit" disabled={isLoading}>
              {isLoading && <Spinner className="mr-2" />}
              {__('注册', 'aiya-cms')}
            </Button>
          </DialogFooter>
        </form>
      </DialogContent>
    </Dialog>
  )
}
