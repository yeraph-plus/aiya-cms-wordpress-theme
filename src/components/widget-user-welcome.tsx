"use client"

import * as React from "react"
import {
  BadgeCheck,
  User,
  Inbox,
  LogIn,
  UserPlus,
  Gauge,
  Ticket,
  Bolt,
} from "lucide-react"

import {
  Avatar,
  AvatarFallback,
  AvatarImage,
} from "@/components/ui/avatar"
import { Button } from "@/components/ui/button"
import { Card, CardContent } from "@/components/ui/card"

import { LoginDialog } from "@/components/dialog-login"
import { RegisterDialog } from "@/components/dialog-register"

export interface UserData {
  id: number
  avatar: string
  role: 'guest' | 'subscriber' | 'author' | 'sponsor' | 'administrator'
  name: string
  email: string
}

export interface MenuItem {
  label: string
  icon: string
  url: string
  targe_blank?: boolean
}

export interface UserLoginData {
  data?: UserData
  menus?: MenuItem[]
  enable_register?: boolean
  className?: string
}

// Icon mapping based on PHP return values
const IconMap: Record<string, React.ElementType> = {
  dashboard: Gauge,
  profile: User,
  sponsor: Ticket,
  inbox: Inbox,
  settings: Bolt,
}


export default function WidgetUserWelcome(props: UserLoginData) {
  const [showLogin, setShowLogin] = React.useState(false)
  const [showRegister, setShowRegister] = React.useState(false)

  const { data, menus, enable_register, className } = props

  // Not logged in state
  if (!data) {
    return (
      <>
        <Card className={`border-0 shadow-none bg-transparent ${className || ''}`}>
          <CardContent className="px-0 pb-0 space-y-4">
            <div className="space-y-2">
              <div className="flex items-center justify-start gap-3">
                <div className="p-2 rounded-full bg-secondary/50 border border-border">
                  <User className="w-5 h-5 text-muted-foreground/80" />
                </div>
                <h3 className="text-lg font-semibold tracking-wide text-foreground">嗨！新朋友</h3>
              </div>
              <p className="text-sm text-muted-foreground/70 leading-relaxed">登录以解锁更多功能，体验完整服务</p>
            </div>

            <div className="grid grid-cols-2 gap-3">
              <Button
                variant="default"
                className="w-full"
                onClick={() => setShowLogin(true)}
              >
                <LogIn className="w-4 h-4 mr-2" />
                登录
              </Button>
              {enable_register && (
                <Button
                  variant="outline"
                  className="w-full"
                  onClick={() => setShowRegister(true)}
                >
                  <UserPlus className="w-4 h-4 mr-2" />
                  注册
                </Button>
              )}
            </div>
          </CardContent>
        </Card>

        <LoginDialog
          open={showLogin}
          onOpenChange={setShowLogin}
        />

        {enable_register && (
          <RegisterDialog
            open={showRegister}
            onOpenChange={setShowRegister}
          />
        )}
      </>
    )
  }

  // Logged in state
  return (
    <>
      <Card className={`border-0 shadow-none bg-transparent ${className || ''}`}>
        <CardContent className="px-0 pb-0 space-y-4">
          {/* User Profile Info */}
          <div className="flex items-center gap-4 p-4 bg-secondary/50 rounded-lg border">
            <Avatar className="h-12 w-12 border-2 border-background shadow-sm">
              <AvatarImage src={data.avatar} alt={data.name} />
              <AvatarFallback>{data.name.slice(0, 2).toUpperCase()}</AvatarFallback>
            </Avatar>
            <div className="space-y-1 overflow-hidden">
              <h4 className="font-semibold truncate">{data.name}</h4>
              <p className="text-xs text-muted-foreground truncate">{data.email}</p>
            </div>
          </div>

          {/* Menu Buttons Group */}
          {menus && menus.length > 0 && (
            <div className="grid grid-cols-2 gap-2">
              {menus.map((item, index) => {
                const IconComponent = IconMap[item.icon] || BadgeCheck
                return (
                  <Button
                    key={index}
                    variant="outline"
                    className="w-full justify-start h-auto py-3 px-3"
                    asChild
                  >
                    <a
                      href={item.url}
                      target={item.targe_blank ? "_blank" : undefined}
                      rel={item.targe_blank ? "noopener noreferrer" : undefined}
                    >
                      <IconComponent className="mr-2 h-4 w-4 shrink-0 text-primary" />
                      <span className="truncate">{item.label}</span>
                    </a>
                  </Button>
                )
              })}
            </div>
          )}

        </CardContent>
      </Card>
    </>
  )
}
