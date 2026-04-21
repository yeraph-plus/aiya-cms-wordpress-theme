import * as React from "react"
import { AlertCircle, CheckCircle2, KeyRound, Loader2 } from "lucide-react"
import { toast } from "sonner"

import { Alert, AlertDescription, AlertTitle } from "@/components/ui/alert"
import { Button } from "@/components/ui/button"
import {
  Card,
  CardContent,
  CardDescription,
  CardFooter,
  CardHeader,
  CardTitle,
} from "@/components/ui/card"
import { Input } from "@/components/ui/input"
import { Label } from "@/components/ui/label"
import { getConfig } from "@/lib/utils"

interface ResetPasswordFormProps {
  resetToken?: string
}

type ViewState = "checking" | "ready" | "invalid" | "success"

function getResponseError(data: any, fallback: string) {
  return data?.message || data?.detail || data?.data?.detail || fallback
}

export default function ResetPasswordForm({
  resetToken = "",
}: ResetPasswordFormProps) {
  const fallbackHomeUrl = getConfig().homeUrl || "/"
  const [viewState, setViewState] = React.useState<ViewState>(() => {
    return resetToken ? "checking" : "invalid"
  })
  const [error, setError] = React.useState<string>(() => {
    return resetToken ? "" : "重置链接缺少必要参数，请重新申请找回密码。"
  })
  const [isSubmitting, setIsSubmitting] = React.useState(false)
  const [password, setPassword] = React.useState("")
  const [passwordConfirm, setPasswordConfirm] = React.useState("")

  React.useEffect(() => {
    if (!resetToken) {
      setViewState("invalid")
      setError("重置链接缺少必要参数，请重新申请找回密码。")
      return
    }

    const { apiUrl, apiNonce } = getConfig()
    if (!apiUrl) {
      setViewState("invalid")
      setError("接口配置缺失，无法校验重置链接。")
      return
    }

    let isCancelled = false

    async function validateResetLink() {
      try {
        const response = await fetch(`${apiUrl}/aiya/v1/validate_password_reset`, {
          method: "POST",
          headers: {
            "Content-Type": "application/json",
            "X-WP-Nonce": apiNonce || "",
          },
          body: JSON.stringify({
            token: resetToken,
          }),
        })

        const data = await response.json()

        if (!response.ok) {
          throw new Error(getResponseError(data, "重置链接无效或已过期"))
        }

        if (!isCancelled) {
          setError("")
          setViewState("ready")
        }
      } catch (err) {
        if (!isCancelled) {
          setError(err instanceof Error ? err.message : "重置链接无效或已过期")
          setViewState("invalid")
        }
      }
    }

    validateResetLink()

    return () => {
      isCancelled = true
    }
  }, [resetToken])

  const handleSubmit = async (e: React.FormEvent<HTMLFormElement>) => {
    e.preventDefault()

    if (!password || !passwordConfirm) {
      toast.error("请输入新密码并确认")
      return
    }

    if (password !== passwordConfirm) {
      toast.error("两次输入的密码不一致")
      return
    }

    const { apiUrl, apiNonce } = getConfig()
    if (!apiUrl) {
      toast.error("接口配置缺失，无法提交重置请求")
      return
    }

    setIsSubmitting(true)
    setError("")

    try {
      const response = await fetch(`${apiUrl}/aiya/v1/reset_password`, {
        method: "POST",
        headers: {
          "Content-Type": "application/json",
          "X-WP-Nonce": apiNonce || "",
        },
        body: JSON.stringify({
          token: resetToken,
          password,
          password_confirm: passwordConfirm,
        }),
      })

      const data = await response.json()

      if (!response.ok) {
        throw new Error(getResponseError(data, "密码重置失败"))
      }

      toast.success(data.message || "密码已重置，请使用新密码登录")
      setPassword("")
      setPasswordConfirm("")
      setViewState("success")
    } catch (err) {
      const message = err instanceof Error ? err.message : "密码重置失败"
      setError(message)
      toast.error(message)
    } finally {
      setIsSubmitting(false)
    }
  }

  return (
    <Card className="mx-auto w-full max-w-lg">
      <CardHeader>
        <CardTitle className="flex items-center gap-2">
          <KeyRound className="h-6 w-6" />
          <span>重置密码</span>
        </CardTitle>
        <CardDescription>
          请输入新的登录密码。密码至少需要 8 位，并同时包含字母和数字。
        </CardDescription>
      </CardHeader>
      <CardContent className="space-y-4">
        {viewState === "checking" && (
          <Alert>
            <Loader2 className="h-4 w-4 animate-spin" />
            <AlertTitle>正在验证链接</AlertTitle>
            <AlertDescription>请稍候，我们正在检查密码重置链接是否有效。</AlertDescription>
          </Alert>
        )}

        {viewState === "invalid" && (
          <Alert variant="destructive">
            <AlertCircle className="h-4 w-4" />
            <AlertTitle>链接不可用</AlertTitle>
            <AlertDescription>{error || "重置链接无效或已过期，请重新申请找回密码。"}</AlertDescription>
          </Alert>
        )}

        {viewState === "success" && (
          <Alert className="border-green-500 text-green-600">
            <CheckCircle2 className="h-4 w-4" />
            <AlertTitle>密码已更新</AlertTitle>
            <AlertDescription>您现在可以返回首页，通过新的密码重新登录。</AlertDescription>
          </Alert>
        )}

        {viewState === "ready" && (
          <form onSubmit={handleSubmit} className="space-y-4">
            {error && (
              <Alert variant="destructive">
                <AlertCircle className="h-4 w-4" />
                <AlertTitle>提交失败</AlertTitle>
                <AlertDescription>{error}</AlertDescription>
              </Alert>
            )}

            <div className="space-y-2">
              <Label htmlFor="reset-password">新密码</Label>
              <Input
                id="reset-password"
                type="password"
                value={password}
                onChange={(e) => setPassword(e.target.value)}
                placeholder="请输入新密码"
                minLength={8}
                autoComplete="new-password"
                disabled={isSubmitting}
                required
              />
            </div>

            <div className="space-y-2">
              <Label htmlFor="reset-password-confirm">确认密码</Label>
              <Input
                id="reset-password-confirm"
                type="password"
                value={passwordConfirm}
                onChange={(e) => setPasswordConfirm(e.target.value)}
                placeholder="请再次输入新密码"
                minLength={8}
                autoComplete="new-password"
                disabled={isSubmitting}
                required
              />
            </div>

            <Button type="submit" disabled={isSubmitting} className="w-full">
              {isSubmitting && <Loader2 className="mr-2 h-4 w-4 animate-spin" />}
              保存新密码
            </Button>
          </form>
        )}
      </CardContent>
      <CardFooter className="justify-end">
        <Button variant={viewState === "success" ? "default" : "outline"} asChild>
          <a href={fallbackHomeUrl}>返回首页</a>
        </Button>
      </CardFooter>
    </Card>
  )
}
