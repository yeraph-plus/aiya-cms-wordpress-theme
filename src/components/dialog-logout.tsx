import { __ } from '@wordpress/i18n';

import * as React from "react"
import { toast } from "sonner"
import {Spinner} from '@/components/ui/spinner';
import { Button } from "@/components/ui/button"
import { getConfig } from "@/lib/utils"
import {
  Dialog,
  DialogContent,
  DialogDescription,
  DialogFooter,
  DialogHeader,
  DialogTitle,
} from "@/components/ui/dialog"

interface LogoutDialogProps {
  open: boolean
  onOpenChange: (open: boolean) => void
}

export function LogoutDialog({ open, onOpenChange }: LogoutDialogProps) {
  const [isLoading, setIsLoading] = React.useState(false)

  const handleLogout = async () => {
    const { apiUrl, apiNonce } = getConfig()
    if (!apiNonce) {
      toast.error(__('页面已过期，请刷新页面重试', 'aiya-cms'))
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

      toast.success(__('退出登录成功', 'aiya-cms'))
      setTimeout(() => {
        window.location.reload()
      }, 1000)
    } catch (error) {
      console.error('Logout error:', error)
      toast.error(__('退出登录失败', 'aiya-cms'))
      setIsLoading(false)
    }
  }

  return (
    <Dialog open={open} onOpenChange={onOpenChange}>
      <DialogContent className="sm:max-w-[425px]">
        <DialogHeader>
          <DialogTitle>{__('确认退出登录', 'aiya-cms')}</DialogTitle>
          <DialogDescription>
            {__('您确定要退出登录吗？', 'aiya-cms')}
          </DialogDescription>
        </DialogHeader>
        <DialogFooter>
          <Button variant="outline" onClick={() => onOpenChange(false)} disabled={isLoading}>
            {__('取消', 'aiya-cms')}
          </Button>
          <Button variant="destructive" onClick={handleLogout} disabled={isLoading}>
            {isLoading && <Spinner className="mr-2" />}
            {__('确认退出登录', 'aiya-cms')}
          </Button>
        </DialogFooter>
      </DialogContent>
    </Dialog>
  )
}
