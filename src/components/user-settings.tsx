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
      toast.error("Missing security nonce")
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
        throw new Error(data.message || data.detail || "更新失败")
      }

      toast.success(data.message || "资料已更新")
    } catch (err: any) {
      toast.error(err.message || "更新发生错误")
    } finally {
      setIsProfileLoading(false)
    }
  }

  const handlePasswordSubmit = async (e: React.FormEvent) => {
    e.preventDefault()

    if (!passwordData.pass || !passwordData.pass_again) {
      toast.error("请输入新密码")
      return
    }

    if (passwordData.pass !== passwordData.pass_again) {
      toast.error("两次输入的密码不一致")
      return
    }

    const { apiUrl, apiNonce } = getConfig()
    if (!apiNonce) {
      toast.error("Missing security nonce")
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
        throw new Error(data.message || data.detail || "更新失败")
      }

      toast.success(data.message || "密码已更新，请重新登录")
      setPasswordData({ pass: "", pass_again: "" })

      // Optionally reload after a delay to force re-login if needed
      setTimeout(() => {
        window.location.reload()
      }, 1500)

    } catch (err: any) {
      toast.error(err.message || "更新发生错误")
    } finally {
      setIsPasswordLoading(false)
    }
  }

  return (
    <>
      <div className="flex items-center gap-2 pl-2 mb-4">
        <UserCog className="w-6 h-6 text-primary" />
        <h2 className="text-xl font-bold tracking-tight">用户设置</h2>
      </div>
      <Card>
        <CardHeader>
          <CardTitle className="flex items-center gap-2">
            <UserRoundPen className="w-6 h-6" />
            <h4 className="font-bold tracking-tight">修改个人资料</h4>
          </CardTitle>
          <CardDescription>
            更新您的个人信息和账户设置
          </CardDescription>
        </CardHeader>
        <form onSubmit={handleProfileSubmit}>
          <CardContent className="space-y-6">
            <div className="grid grid-cols-2 gap-4">
              <div className="space-y-2">
                <Label htmlFor="last_name">姓氏</Label>
                <Input
                  id="last_name"
                  value={profileData.last_name}
                  onChange={handleProfileChange}
                  placeholder="姓"
                />
              </div>
              <div className="space-y-2">
                <Label htmlFor="first_name">名字</Label>
                <Input
                  id="first_name"
                  value={profileData.first_name}
                  onChange={handleProfileChange}
                  placeholder="名"
                />
              </div>
            </div>

            <div className="space-y-2">
              <Label htmlFor="nickname">昵称</Label>
              <Input
                id="nickname"
                value={profileData.nickname}
                onChange={handleProfileChange}
                placeholder="显示的昵称"
              />
            </div>

            <div className="space-y-2">
              <Label htmlFor="locale">界面语言</Label>
              <Select
                value={profileData.locale}
                onValueChange={(value) => setProfileData((prev) => ({ ...prev, locale: value }))}
              >
                <SelectTrigger id="locale" className="w-full">
                  <SelectValue placeholder="选择语言" />
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
              <Label htmlFor="email">邮箱地址</Label>
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
              <Label htmlFor="user_url">个人网站</Label>
              <Input
                id="user_url"
                type="url"
                value={profileData.user_url}
                onChange={handleProfileChange}
                placeholder="https://example.com"
              />
            </div>

            <div className="space-y-2">
              <Label htmlFor="description">个人说明</Label>
              <Textarea
                id="description"
                value={profileData.description}
                onChange={handleProfileChange}
                placeholder="介绍一下自己..."
                className="min-h-[100px]"
              />
            </div>
          </CardContent>
          <CardFooter className="flex justify-end mt-4">
            <Button type="submit" disabled={isProfileLoading}>
              {isProfileLoading && <Spinner className="mr-2" />}
              保存资料
            </Button>
          </CardFooter>
        </form>
      </Card>

      <Card className="mt-6">
        <CardHeader>
          <CardTitle className="flex items-center gap-2">
            <UserRoundKey className="w-6 h-6" />
            <h4 className=" font-bold tracking-tight">修改密码</h4>
          </CardTitle>
          <CardDescription>
            为了您的账户安全，建议定期更换密码
          </CardDescription>
        </CardHeader>
        <form onSubmit={handlePasswordSubmit}>
          <CardContent className="space-y-4">
            <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
              <div className="space-y-2">
                <Label htmlFor="pass">新密码</Label>
                <Input
                  id="pass"
                  type="password"
                  value={passwordData.pass}
                  onChange={handlePasswordChange}
                  placeholder="请输入新密码"
                />
              </div>
              <div className="space-y-2">
                <Label htmlFor="pass_again">确认新密码</Label>
                <Input
                  id="pass_again"
                  type="password"
                  value={passwordData.pass_again}
                  onChange={handlePasswordChange}
                  placeholder="再次输入新密码"
                />
              </div>
            </div>
          </CardContent>
          <CardFooter className="flex justify-end mt-4">
            <Button type="submit" variant="outline" disabled={isPasswordLoading}>
              {isPasswordLoading && <Spinner className="mr-2" />}
              修改密码
            </Button>
          </CardFooter>
        </form>
      </Card>
    </>
  )
}
