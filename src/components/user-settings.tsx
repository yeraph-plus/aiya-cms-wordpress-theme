import { __ } from '@wordpress/i18n';

import * as React from "react"
import { UserCog, UserRoundPen, UserRoundKey } from "lucide-react"
import { Spinner } from '@/components/ui/spinner';
import { Button } from "@/components/ui/button"
import { Input } from "@/components/ui/input"
import { Label } from "@/components/ui/label"
import {
  Select,
  SelectContent,
  SelectItem,
  SelectTrigger,
  SelectValue,
} from "@/components/ui/select"
import { Textarea } from "@/components/ui/textarea"
import { toast } from "sonner"
import { getConfig } from "@/lib/utils"
import {
  Card,
  CardContent,
  CardDescription,
  CardFooter,
  CardHeader,
  CardTitle,
} from "@/components/ui/card"

interface UserSettingsProps {
  initialUser: {
    email: string
    first_name: string
    last_name: string
    locale: string
    nickname: string
    description: string
    user_url: string
  }
}

const LANGUAGE_OPTIONS = [
  { value: "zh_CN", label: "简体中文" },
  { value: "zh_TW", label: "繁体中文" },
  { value: "zh_HK", label: "香港中文" },
  { value: "en_US", label: "English" },
]

export default function UserSettings({ initialUser }: UserSettingsProps) {
  const [isProfileLoading, setIsProfileLoading] = React.useState(false)
  const [isPasswordLoading, setIsPasswordLoading] = React.useState(false)

  const [profileData, setProfileData] = React.useState({
    email: initialUser.email,
    first_name: initialUser.first_name,
    last_name: initialUser.last_name,
    locale: initialUser.locale,
    nickname: initialUser.nickname,
    description: initialUser.description,
    user_url: initialUser.user_url,
  })

  const [passwordData, setPasswordData] = React.useState({
    pass: "",
    pass_again: "",
  })

  const handleProfileChange = (e: React.ChangeEvent<HTMLInputElement | HTMLTextAreaElement>) => {
    const { id, value } = e.target
    setProfileData(prev => ({ ...prev, [id]: value }))
  }

  const handlePasswordChange = (e: React.ChangeEvent<HTMLInputElement>) => {
    const { id, value } = e.target
    setPasswordData(prev => ({ ...prev, [id]: value }))
  }

  const handleProfileSubmit = async (e: React.FormEvent) => {
    e.preventDefault()
    const { apiUrl, apiNonce } = getConfig()
    if (!apiNonce) {
      toast.error(__('页面已过期，请刷新页面重试', 'aiya-cms'))
      return
    }

    setIsProfileLoading(true)

    try {
      const response = await fetch(`${apiUrl}/aiya/v1/update_profile`, {
        method: "POST",
        headers: {
          "Content-Type": "application/json",
          "X-WP-Nonce": apiNonce,
        },
        body: JSON.stringify(profileData),
      })

      const data = await response.json()

      if (data.code && data.code !== 'success' && data.code !== 200) {
        throw new Error(data.message || data.detail || __('更新失败', 'aiya-cms'))
      }

      toast.success(data.message || __('资料已更新', 'aiya-cms'))
    } catch (err: any) {
      toast.error(err.message || __('更新发生错误', 'aiya-cms'))
    } finally {
      setIsProfileLoading(false)
    }
  }

  const handlePasswordSubmit = async (e: React.FormEvent) => {
    e.preventDefault()

    if (!passwordData.pass || !passwordData.pass_again) {
      toast.error(__('请输入新密码', 'aiya-cms'))
      return
    }

    if (passwordData.pass !== passwordData.pass_again) {
      toast.error(__('两次输入的密码不一致', 'aiya-cms'))
      return
    }

    const { apiUrl, apiNonce } = getConfig()
    if (!apiNonce) {
      toast.error(__('页面已过期，请刷新页面重试', 'aiya-cms'))
      return
    }

    setIsPasswordLoading(true)

    try {
      const response = await fetch(`${apiUrl}/aiya/v1/update_password`, {
        method: "POST",
        headers: {
          "Content-Type": "application/json",
          "X-WP-Nonce": apiNonce,
        },
        body: JSON.stringify(passwordData),
      })

      const data = await response.json()

      if (data.code && data.code !== 'success' && data.code !== 200) {
        throw new Error(data.message || data.detail || __('更新失败', 'aiya-cms'))
      }

      toast.success(data.message || __('密码已更新，请重新登录', 'aiya-cms'))
      setPasswordData({ pass: "", pass_again: "" })

      // Optionally reload after a delay to force re-login if needed
      setTimeout(() => {
        window.location.reload()
      }, 1500)

    } catch (err: any) {
      toast.error(err.message || __('更新发生错误', 'aiya-cms'))
    } finally {
      setIsPasswordLoading(false)
    }
  }

  return (
    <>
      <div className="flex items-center gap-2 pl-2 mb-4">
        <UserCog className="w-6 h-6 text-primary" />
        <h2 className="text-xl font-bold tracking-tight">{__('用户设置', 'aiya-cms')}</h2>
      </div>
      <Card>
        <CardHeader>
          <CardTitle className="flex items-center gap-2">
            <UserRoundPen className="w-6 h-6" />
            <h4 className="font-bold tracking-tight">{__('修改个人资料', 'aiya-cms')}</h4>
          </CardTitle>
          <CardDescription>
            {__('更新您的个人信息和账户设置', 'aiya-cms')}
          </CardDescription>
        </CardHeader>
        <form onSubmit={handleProfileSubmit}>
          <CardContent className="space-y-6">
            <div className="grid grid-cols-2 gap-4">
              <div className="space-y-2">
                <Label htmlFor="last_name">{__('姓氏', 'aiya-cms')}</Label>
                <Input
                  id="last_name"
                  value={profileData.last_name}
                  onChange={handleProfileChange}
                  placeholder={__('姓', 'aiya-cms')}
                />
              </div>
              <div className="space-y-2">
                <Label htmlFor="first_name">{__('名字', 'aiya-cms')}</Label>
                <Input
                  id="first_name"
                  value={profileData.first_name}
                  onChange={handleProfileChange}
                  placeholder={__('名', 'aiya-cms')}
                />
              </div>
            </div>

            <div className="space-y-2">
              <Label htmlFor="nickname">{__('昵称', 'aiya-cms')}</Label>
              <Input
                id="nickname"
                value={profileData.nickname}
                onChange={handleProfileChange}
                placeholder={__('显示的昵称', 'aiya-cms')}
              />
            </div>

            <div className="space-y-2">
              <Label htmlFor="locale">{__('界面语言', 'aiya-cms')}</Label>
              <Select
                value={profileData.locale}
                onValueChange={(value) => setProfileData((prev) => ({ ...prev, locale: value }))}
              >
                <SelectTrigger id="locale" className="w-full">
                  <SelectValue placeholder={__('选择语言', 'aiya-cms')} />
                </SelectTrigger>
                <SelectContent>
                  {LANGUAGE_OPTIONS.map((option) => (
                    <SelectItem key={option.value} value={option.value}>
                      {option.label}
                    </SelectItem>
                  ))}
                </SelectContent>
              </Select>
            </div>

            <div className="space-y-2">
              <Label htmlFor="email">{__('邮箱地址', 'aiya-cms')}</Label>
              <Input
                id="email"
                type="email"
                value={profileData.email}
                onChange={handleProfileChange}
                placeholder="name@example.com"
                required
              />
            </div>

            <div className="space-y-2">
              <Label htmlFor="user_url">{__('个人网站', 'aiya-cms')}</Label>
              <Input
                id="user_url"
                type="url"
                value={profileData.user_url}
                onChange={handleProfileChange}
                placeholder="https://example.com"
              />
            </div>

            <div className="space-y-2">
              <Label htmlFor="description">{__('个人说明', 'aiya-cms')}</Label>
              <Textarea
                id="description"
                value={profileData.description}
                onChange={handleProfileChange}
                placeholder={__('请介绍一下自己...', 'aiya-cms')}
                className="min-h-[100px]"
              />
            </div>
          </CardContent>
          <CardFooter className="flex justify-end mt-4">
            <Button type="submit" disabled={isProfileLoading}>
              {isProfileLoading && <Spinner className="mr-2" />}
              {__('保存资料', 'aiya-cms')}
            </Button>
          </CardFooter>
        </form>
      </Card>

      <Card className="mt-6">
        <CardHeader>
          <CardTitle className="flex items-center gap-2">
            <UserRoundKey className="w-6 h-6" />
            <h4 className=" font-bold tracking-tight">{__('修改密码', 'aiya-cms')}</h4>
          </CardTitle>
          <CardDescription>
            {__('为了您的账户安全，建议定期更换密码', 'aiya-cms')}
          </CardDescription>
        </CardHeader>
        <form onSubmit={handlePasswordSubmit}>
          <CardContent className="space-y-4">
            <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
              <div className="space-y-2">
                <Label htmlFor="pass">{__('新密码', 'aiya-cms')}</Label>
                <Input
                  id="pass"
                  type="password"
                  value={passwordData.pass}
                  onChange={handlePasswordChange}
                  placeholder={__('请输入新密码', 'aiya-cms')}
                />
              </div>
              <div className="space-y-2">
                <Label htmlFor="pass_again">{__('确认新密码', 'aiya-cms')}</Label>
                <Input
                  id="pass_again"
                  type="password"
                  value={passwordData.pass_again}
                  onChange={handlePasswordChange}
                  placeholder={__('再次输入新密码', 'aiya-cms')}
                />
              </div>
            </div>
          </CardContent>
          <CardFooter className="flex justify-end mt-4">
            <Button type="submit" variant="outline" disabled={isPasswordLoading}>
              {isPasswordLoading && <Spinner className="mr-2" />}
              {__('修改密码', 'aiya-cms')}
            </Button>
          </CardFooter>
        </form>
      </Card>
    </>
  )
}
