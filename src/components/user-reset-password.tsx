import { __ } from '@wordpress/i18n';

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
    return resetToken ? "" : __('重置链接缺少验证令牌，请重新申请找回密码。', 'aiya-cms')
  })
  const [isSubmitting, setIsSubmitting] = React.useState(false)
  const [password, setPassword] = React.useState("")
  const [passwordConfirm, setPasswordConfirm] = React.useState("")

  React.useEffect(() => {
    if (!resetToken) {
      setViewState("invalid")
      setError(__('重置链接缺少验证令牌，请重新申请找回密码。', 'aiya-cms'))
      return
    }

    const { apiUrl, apiNonce } = getConfig()
    if (!apiUrl) {
      setViewState("invalid")
      setError(__('接口配置缺失，无法校验重置链接。', 'aiya-cms'))
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
          throw new Error(getResponseError(data, __('重置链接无效或已过期', 'aiya-cms')))
        }

        if (!isCancelled) {
          setError("")
          setViewState("ready")
        }
      } catch (err) {
        if (!isCancelled) {
          setError(err instanceof Error ? err.message : __('重置链接无效或已过期', 'aiya-cms'))
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
      toast.error(__('请输入新密码并确认', 'aiya-cms'))
      return
    }

    if (password !== passwordConfirm) {
      toast.error(__('两次输入的密码不一致', 'aiya-cms'))
      return
    }

    const { apiUrl, apiNonce } = getConfig()
    if (!apiUrl) {
      toast.error(__('接口配置缺失，无法提交重置请求', 'aiya-cms'))
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
        throw new Error(getResponseError(data, __('密码重置失败', 'aiya-cms')))
      }

      toast.success(data.message || __('密码已重置，请使用新密码登录'))
      setPassword("")
      setPasswordConfirm("")
      setViewState("success")
    } catch (err) {
      const message = err instanceof Error ? err.message : __('密码重置失败', 'aiya-cms')
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
          {__('重置密码', 'aiya-cms')}
        </CardTitle>
        <CardDescription>
          {__('请输入新的登录密码。密码至少需要 8 位，并同时包含字母和数字。', 'aiya-cms')}
        </CardDescription>
      </CardHeader>
      <CardContent className="space-y-4">
        {viewState === "checking" && (
          <Alert>
            <Loader2 className="h-4 w-4 animate-spin" />
            <AlertTitle>{__('正在验证链接', 'aiya-cms')}</AlertTitle>
            <AlertDescription>{__('请稍候，我们正在检查密码重置链接是否有效。', 'aiya-cms')}</AlertDescription>
          </Alert>
        )}

        {viewState === "invalid" && (
          <Alert variant="destructive">
            <AlertCircle className="h-4 w-4" />
            <AlertTitle>{__('链接不可用', 'aiya-cms')}</AlertTitle>
            <AlertDescription>{error || __('重置链接无效或已过期，请重新申请找回密码。', 'aiya-cms')}</AlertDescription>
          </Alert>
        )}

        {viewState === "success" && (
          <Alert className="border-green-500 text-green-600">
            <CheckCircle2 className="h-4 w-4" />
            <AlertTitle>{__('密码已更新', 'aiya-cms')}</AlertTitle>
            <AlertDescription>{__('您现在可以返回首页，通过新的密码重新登录。', 'aiya-cms')}</AlertDescription>
          </Alert>
        )}

        {viewState === "ready" && (
          <form onSubmit={handleSubmit} className="space-y-4">
            {error && (
              <Alert variant="destructive">
                <AlertCircle className="h-4 w-4" />
                <AlertTitle>
                  {__('提交失败', 'aiya-cms')}
                </AlertTitle>
                <AlertDescription>{error}</AlertDescription>
              </Alert>
            )}

            <div className="space-y-2">
              <Label htmlFor="reset-password">{__('请输入新密码', 'aiya-cms')}</Label>
              <Input
                id="reset-password"
                type="password"
                value={password}
                onChange={(e) => setPassword(e.target.value)}
                placeholder={__('请输入新密码', 'aiya-cms')}
                minLength={8}
                autoComplete="new-password"
                disabled={isSubmitting}
                required
              />
            </div>

            <div className="space-y-2">
              <Label htmlFor="reset-password-confirm">{__('请确认密码', 'aiya-cms')}</Label>
              <Input
                id="reset-password-confirm"
                type="password"
                value={passwordConfirm}
                onChange={(e) => setPasswordConfirm(e.target.value)}
                placeholder={__('请再次输入新密码', 'aiya-cms')}
                minLength={8}
                autoComplete="new-password"
                disabled={isSubmitting}
                required
              />
            </div>

            <Button type="submit" disabled={isSubmitting} className="w-full">
              {isSubmitting && <Loader2 className="mr-2 h-4 w-4 animate-spin" />}
              {__('保存新密码', 'aiya-cms')}
            </Button>
          </form>
        )}
      </CardContent>
      <CardFooter className="justify-end">
        <Button variant={viewState === "success" ? "default" : "outline"} asChild>
          <a href={fallbackHomeUrl}>{__('返回首页', 'aiya-cms')}</a>
        </Button>
      </CardFooter>
    </Card>
  )
}
