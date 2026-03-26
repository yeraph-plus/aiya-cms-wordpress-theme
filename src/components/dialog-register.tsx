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
      setError("两次输入的密码不一致")
      return
    }

    const { apiUrl, apiNonce } = getConfig()
    if (!apiNonce) {
      setError("Missing security nonce")
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
        throw new Error(data.message || "注册失败")
      }

      // Registration successful, show toast and reload page (auto-login handled by API)
      toast.success("注册成功")
      setTimeout(() => {
        window.location.reload()
      }, 1000)
    } catch (err) {
      setError(err instanceof Error ? err.message : "注册发生错误")
    } finally {
      setIsLoading(false)
    }
  }

  return (
    <Dialog open={open} onOpenChange={onOpenChange}>
      <DialogContent className="sm:max-w-[425px]">
        <DialogHeader>
          <DialogTitle>注册</DialogTitle>
          <DialogDescription>
            请输入您的信息以创建新账户。
          </DialogDescription>
        </DialogHeader>
        <form onSubmit={handleSubmit} className="grid gap-4 py-4">
          <Field>
            <Label htmlFor="username">用户名</Label>
            <Input
              id="username"
              type="text"
              placeholder="您的用户名"
              value={username}
              onChange={(e) => setUsername(e.target.value)}
              disabled={isLoading}
              required
            />
          </Field>
          <Field>
            <Label htmlFor="email">邮箱</Label>
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
            <Label htmlFor="password">密码</Label>
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
            <Label htmlFor="confirmPassword">确认密码</Label>
            <Input
              id="confirmPassword"
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
              注册
            </Button>
          </DialogFooter>
        </form>
      </DialogContent>
    </Dialog>
  )
}
