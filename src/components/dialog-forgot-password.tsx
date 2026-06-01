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
import { joinTranslations } from '@/lib/i18n';

const { t } = joinTranslations();

function getResponseError(data: any, fallback: string) {
  return data?.message || data?.detail || data?.data?.detail || fallback
}

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

    const trimmedEmail = email.trim()

    const { apiUrl, apiNonce } = getConfig()
    if (!apiNonce) {
      setError(t('page_expired'))
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
          email: trimmedEmail,
        }),
      })

      const data = await response.json()

      if (!response.ok) {
        throw new Error(getResponseError(data, t('request_failed')))
      }

      setIsSuccess(true)
      toast.success(data?.data?.message || data?.message || t('reset_link_sent'))

    } catch (err: any) {
      console.error(err)
      setError(err.message || t('error_retry_later'))
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
          <DialogTitle>{t('forgot_password_title')}</DialogTitle>
          <DialogDescription>
            {isSuccess
              ? t('email_sent_success')
              : t('forgot_password_description')}
          </DialogDescription>
        </DialogHeader>

        {isSuccess ? (
          <div className="py-6 text-center space-y-4">
            <div className="text-green-600 bg-green-50 p-4 rounded-md">
              <p>{t('reset_link_sent_to')}</p>
              <p className="font-medium mt-1">{email}</p>
            </div>
            <p className="text-sm text-muted-foreground">
              {t('check_email_instruction')}
            </p>
            <div className="pt-4">
              <Button variant="outline" onClick={onBackToLogin} className="w-full">
                {t('back_to_login')}
              </Button>
            </div>
          </div>
        ) : (
          <form onSubmit={handleSubmit} className="grid gap-4 py-4">
            <div className="grid gap-2">
              <Label htmlFor="forgot-email">{t('enter_email')}</Label>
              <Input
                id="forgot-email"
                type="email"
                placeholder={t('enter_email')}
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
                  {t('back_to_login')}
                </Button>
                <Button type="submit" disabled={isLoading}>
                  {isLoading && <Spinner className="mr-2 h-4 w-4" />}
                  {t('send_reset_link')}
                </Button>
              </div>
            </DialogFooter>
          </form>
        )}
      </DialogContent>
    </Dialog>
  )
}
