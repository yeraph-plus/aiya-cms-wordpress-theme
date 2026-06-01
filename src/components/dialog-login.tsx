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
import { joinTranslations } from '@/lib/i18n';
import { ForgotPasswordDialog } from "./dialog-forgot-password"

const { t } = joinTranslations();

function getResponseError(data: any, fallback: string) {
  return data?.message || data?.detail || data?.data?.detail || fallback
}

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
      setError(t('page_expired'))
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
        throw new Error(getResponseError(data, t('request_failed')))
      }

      // Login successful, show toast and reload page
      toast.success(t('login_success'))
      setTimeout(() => {
        window.location.reload()
      }, 1000)
    } catch (err) {
      setError(err instanceof Error ? err.message : t('login_error'))
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
          <DialogTitle>{t('login')}</DialogTitle>
          <DialogDescription>
            {t('login_description')}
          </DialogDescription>
        </DialogHeader>
        <form onSubmit={handleSubmit} className="grid gap-4 py-4">
          <div className="grid gap-2">
            <Label htmlFor="email">{t('email')}</Label>
            <Input
              id="email"
              type="text"
              placeholder={t('your_email')}
              value={email}
              onChange={(e) => setEmail(e.target.value)}
              disabled={isLoading}
              required
            />
          </div>
          <div className="grid gap-2">
            <div className="flex items-center justify-between">
              <Label htmlFor="password">{t('password')}</Label>
              <Button
                variant="link"
                className="px-0 h-auto text-xs text-muted-foreground"
                type="button"
                onClick={() => setShowForgot(true)}
              >
                {t('forgot_password_question')}
              </Button>
            </div>
            <Input
              id="password"
              type="password"
              placeholder={t('login_password')}
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
              {t('remember_me')}
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
              {t('login')}
            </Button>
          </DialogFooter>
        </form>
      </DialogContent>
    </Dialog>
  )   
}
