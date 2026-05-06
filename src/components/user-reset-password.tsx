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
import { joinTranslations } from "@/lib/i18n"

const { t } = joinTranslations()

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
    return resetToken ? "" : t("reset_token_missing")
  })
  const [isSubmitting, setIsSubmitting] = React.useState(false)
  const [password, setPassword] = React.useState("")
  const [passwordConfirm, setPasswordConfirm] = React.useState("")

  React.useEffect(() => {
    if (!resetToken) {
      setViewState("invalid")
      setError(t("reset_token_missing"))
      return
    }

    const { apiUrl, apiNonce } = getConfig()
    if (!apiUrl) {
      setViewState("invalid")
      setError(t("api_config_missing_reset_link_validation"))
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
          throw new Error(getResponseError(data, t("reset_link_invalid_or_expired")))
        }

        if (!isCancelled) {
          setError("")
          setViewState("ready")
        }
      } catch (err) {
        if (!isCancelled) {
          setError(err instanceof Error ? err.message : t("reset_link_invalid_or_expired"))
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
      toast.error(t("enter_and_confirm_new_password"))
      return
    }

    if (password !== passwordConfirm) {
      toast.error(t("password_mismatch"))
      return
    }

    const { apiUrl, apiNonce } = getConfig()
    if (!apiUrl) {
      toast.error(t("api_config_missing_reset_submit"))
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
        throw new Error(getResponseError(data, t("password_reset_failed")))
      }

      toast.success(data.message || t("password_reset_success_login_new_password"))
      setPassword("")
      setPasswordConfirm("")
      setViewState("success")
    } catch (err) {
      const message = err instanceof Error ? err.message : t("password_reset_failed")
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
          {t("reset_password")}
        </CardTitle>
        <CardDescription>
          {t("reset_password_description_security_requirements")}
        </CardDescription>
      </CardHeader>
      <CardContent className="space-y-4">
        {viewState === "checking" && (
          <Alert>
            <Loader2 className="h-4 w-4 animate-spin" />
            <AlertTitle>{t("validating_link")}</AlertTitle>
            <AlertDescription>{t("checking_reset_link_validity")}</AlertDescription>
          </Alert>
        )}

        {viewState === "invalid" && (
          <Alert variant="destructive">
            <AlertCircle className="h-4 w-4" />
            <AlertTitle>{t("link_unavailable")}</AlertTitle>
            <AlertDescription>{error || t("reset_link_invalid_or_expired_request_again")}</AlertDescription>
          </Alert>
        )}

        {viewState === "success" && (
          <Alert className="border-green-500 text-green-600">
            <CheckCircle2 className="h-4 w-4" />
            <AlertTitle>{t("password_updated")}</AlertTitle>
            <AlertDescription>{t("return_home_login_with_new_password")}</AlertDescription>
          </Alert>
        )}

        {viewState === "ready" && (
          <form onSubmit={handleSubmit} className="space-y-4">
            {error && (
              <Alert variant="destructive">
                <AlertCircle className="h-4 w-4" />
                <AlertTitle>
                  {t("submit_failed")}
                </AlertTitle>
                <AlertDescription>{error}</AlertDescription>
              </Alert>
            )}

            <div className="space-y-2">
              <Label htmlFor="reset-password">{t("enter_new_password")}</Label>
              <Input
                id="reset-password"
                type="password"
                value={password}
                onChange={(e) => setPassword(e.target.value)}
                placeholder={t("enter_new_password")}
                minLength={8}
                autoComplete="new-password"
                disabled={isSubmitting}
                required
              />
            </div>

            <div className="space-y-2">
              <Label htmlFor="reset-password-confirm">{t("please_confirm_password")}</Label>
              <Input
                id="reset-password-confirm"
                type="password"
                value={passwordConfirm}
                onChange={(e) => setPasswordConfirm(e.target.value)}
                placeholder={t("please_enter_new_password_again")}
                minLength={8}
                autoComplete="new-password"
                disabled={isSubmitting}
                required
              />
            </div>

            <Button type="submit" disabled={isSubmitting} className="w-full">
              {isSubmitting && <Loader2 className="mr-2 h-4 w-4 animate-spin" />}
              {t("save_new_password")}
            </Button>
          </form>
        )}
      </CardContent>
      <CardFooter className="justify-end">
        <Button variant={viewState === "success" ? "default" : "outline"} asChild>
          <a href={fallbackHomeUrl}>{t("back_to_home")}</a>
        </Button>
      </CardFooter>
    </Card>
  )
}
