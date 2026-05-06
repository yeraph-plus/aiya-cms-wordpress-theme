import * as React from "react"
import { toast } from "sonner"
import {Spinner} from '@/components/ui/spinner';
import { Button } from "@/components/ui/button"
import { getConfig } from "@/lib/utils"
import { joinTranslations } from '@/lib/i18n';
import {
  Dialog,
  DialogContent,
  DialogDescription,
  DialogFooter,
  DialogHeader,
  DialogTitle,
} from "@/components/ui/dialog"

const { t } = joinTranslations();

interface LogoutDialogProps {
  open: boolean
  onOpenChange: (open: boolean) => void
}

export function LogoutDialog({ open, onOpenChange }: LogoutDialogProps) {
  const [isLoading, setIsLoading] = React.useState(false)

  const handleLogout = async () => {
    const { apiUrl, apiNonce } = getConfig()
    if (!apiNonce) {
      toast.error(t('page_expired'))
      return
    }

    setIsLoading(true)

    try {
      const response = await fetch(`${apiUrl}/aiya/v1/logout`, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'X-WP-Nonce': apiNonce,
        },
      })

      if (!response.ok) {
        throw new Error('Logout failed')
      }

      toast.success(t('logout_success'))
      setTimeout(() => {
        window.location.reload()
      }, 1000)
    } catch (error) {
      console.error('Logout error:', error)
      toast.error(t('logout_failed'))
      setIsLoading(false)
    }
  }

  return (
    <Dialog open={open} onOpenChange={onOpenChange}>
      <DialogContent className="sm:max-w-[425px]">
        <DialogHeader>
          <DialogTitle>{t('confirm_logout')}</DialogTitle>
          <DialogDescription>
            {t('logout_confirm_text')}
          </DialogDescription>
        </DialogHeader>
        <DialogFooter>
          <Button variant="outline" onClick={() => onOpenChange(false)} disabled={isLoading}>
            {t('cancel')}
          </Button>
          <Button variant="destructive" onClick={handleLogout} disabled={isLoading}>
            {isLoading && <Spinner className="mr-2" />}
            {t('confirm_logout')}
          </Button>
        </DialogFooter>
      </DialogContent>
    </Dialog>
  )
}
