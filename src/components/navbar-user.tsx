"use client"

import * as React from "react"
import {
  BadgeCheck,
  ChevronDown,
  LogOut,
  User,
  LogIn,
  UserPlus,
  Inbox,
  Gauge,
  Ticket,
  Bolt,
} from "lucide-react"

import { Badge } from "@/components/ui/badge"
import {
  Avatar,
  AvatarFallback,
  AvatarImage,
} from "@/components/ui/avatar"
import {
  DropdownMenu,
  DropdownMenuContent,
  DropdownMenuGroup,
  DropdownMenuItem,
  DropdownMenuLabel,
  DropdownMenuSeparator,
  DropdownMenuTrigger,
} from "@/components/ui/dropdown-menu"
import { Button } from "@/components/ui/button"
import { cn } from "@/lib/utils"
import { LoginDialog } from "@/components/dialog-login"
import { RegisterDialog } from "@/components/dialog-register"
import { LogoutDialog } from "@/components/dialog-logout"

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
  compact?: boolean
}

// Icon mapping based on PHP return values
const IconMap: Record<string, React.ElementType> = {
  dashboard: Gauge,
  profile: User,
  sponsor: Ticket,
  inbox: Inbox,
  settings: Bolt,
}

const roleMap: Record<string, string> = {
  administrator: "管理员",
  editor: "编辑",
  author: "作者",
  contributor: "贡献者",
  subscriber: "用户",
  guest: "访客",
  sponsor: "会员",
}

export default function NavUser(props: UserLoginData) {
  const [showLogin, setShowLogin] = React.useState(false)
  const [showRegister, setShowRegister] = React.useState(false)
  const [showLogout, setShowLogout] = React.useState(false)

  const { data, menus, enable_register, compact = false } = props

  // Not logged in state
  if (!data) {
    return (
      <>
        <div className="flex items-center gap-2">
          <Button
            variant={compact ? "ghost" : "default"}
            size={compact ? "icon" : "default"}
            onClick={() => setShowLogin(true)}
            className={cn(compact ? "h-9 w-9 rounded-full" : "h-8 px-3")}
            aria-label="登录"
          >
            <LogIn className={cn("w-4 h-4", compact ? "" : "mr-2")} />
            {compact ? <span className="sr-only">登录</span> : "登录"}
          </Button>
          {!compact && enable_register && (
            <Button
              variant="outline"
              onClick={() => setShowRegister(true)}
              className="h-8 px-3"
            >
              <UserPlus className="w-4 h-4 mr-2" />
              注册
            </Button>
          )}
        </div>

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
      <DropdownMenu>
        <DropdownMenuTrigger asChild>
          <Button
            variant="ghost"
            size="lg"
            className={cn(
              "data-[state=open]:bg-sidebar-accent data-[state=open]:text-sidebar-accent-foreground",
              compact
                ? "h-9 w-9 px-0 flex items-center justify-center rounded-full"
                : "h-9 w-9 md:w-auto px-0 md:px-3 flex items-center gap-2 justify-center md:justify-start"
            )}
            aria-label="用户菜单"
          >
            <Avatar className={cn(compact ? "h-8 w-8" : "h-7 w-7 md:h-8 md:w-8")}>
              <AvatarImage src={data.avatar} alt={data.name} />
              <AvatarFallback className="rounded-lg">CN</AvatarFallback>
            </Avatar>
            <div className={cn("flex-1 text-left leading-tight max-w-[120px]", compact ? "hidden" : "hidden md:grid")}>
              {data.name}
            </div>
            <ChevronDown className={cn("ml-auto size-4 text-muted-foreground", compact ? "hidden" : "hidden md:block")} />
          </Button>
        </DropdownMenuTrigger>
        <DropdownMenuContent
          className="w-[--radix-dropdown-menu-trigger-width] min-w-56 rounded-lg"
          align="end"
          sideOffset={4}
        >
          <DropdownMenuLabel className="p-0 font-normal">
            <div className="flex items-center gap-2 px-1 py-1.5 text-left">
              <Avatar className="h-8 w-8">
                <AvatarImage src={data.avatar} alt={data.name} />
                <AvatarFallback className="rounded-lg">User</AvatarFallback>
              </Avatar>
              <div className="grid flex-1 text-left leading-tight">
                <span className="truncate font-medium">{data.name}</span>
                <div className="flex items-center gap-2">
                  <Badge variant="secondary" className="text-[10px] px-1 py-0 h-4 leading-none shrink-0 font-normal">
                    {roleMap[data.role] || data.role}
                  </Badge>
                  <span className="truncate text-xs text-muted-foreground">{data.email}</span>
                </div>
              </div>
            </div>
          </DropdownMenuLabel>
          <DropdownMenuSeparator />

          {menus && menus.length > 0 && (
            <>
              <DropdownMenuGroup>
                {menus.map((item, index) => {
                  const IconComponent = IconMap[item.icon] || BadgeCheck
                  return (
                    <DropdownMenuItem key={index} asChild>
                      <a
                        href={item.url}
                        target={item.targe_blank ? "_blank" : undefined}
                        rel={item.targe_blank ? "noopener noreferrer" : undefined}
                        className="cursor-pointer"
                      >
                        <IconComponent className="mr-2 h-4 w-4" />
                        {item.label}
                      </a>
                    </DropdownMenuItem>
                  )
                })}
              </DropdownMenuGroup>
              <DropdownMenuSeparator />
            </>
          )}

          <DropdownMenuItem onClick={() => setShowLogout(true)} className="text-destructive focus:text-destructive cursor-pointer">
            <LogOut className="mr-2 h-4 w-4 text-destructive" />
            退出登录
          </DropdownMenuItem>
        </DropdownMenuContent>
      </DropdownMenu>

      <LogoutDialog
        open={showLogout}
        onOpenChange={setShowLogout}
      />
    </>
  )
}
