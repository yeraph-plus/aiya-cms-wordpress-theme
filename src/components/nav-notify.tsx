"use client"

import * as React from "react"
import { AlertTriangle, Bell, CheckCircle2, CircleEllipsis, Info, XCircle } from "lucide-react"

import { Button } from "@/components/ui/button"
import { Popover, PopoverContent, PopoverTrigger } from "@/components/ui/popover"

type NotifyLevel = "success" | "info" | "warning" | "error" | "message"

type NotifyNote = {
  level: NotifyLevel
  scope: "guest" | "user" | "author" | "administrator" | string
  title?: string
  content?: string
  time?: string
}

function isNotifyNote(value: unknown): value is NotifyNote {
  if (!value || typeof value !== "object") return false
  const v = value as any
  return typeof v.level === "string" && typeof v.scope === "string"
}

function normalizeNotes(props: any): NotifyNote[] {
  if (!props) return []
  if (Array.isArray(props.notes)) {
    return props.notes.filter(isNotifyNote)
  }

  const values = Object.values(props)
  const notes = values.filter(isNotifyNote) as NotifyNote[]
  return notes
}

const levelMeta: Record<
  NotifyLevel,
  { label: string; Icon: React.ComponentType<{ className?: string }>; iconClassName: string }
> = {
  success: { label: "成功", Icon: CheckCircle2, iconClassName: "text-emerald-500" },
  info: { label: "提示", Icon: Info, iconClassName: "text-sky-500" },
  warning: { label: "警告", Icon: AlertTriangle, iconClassName: "text-amber-500" },
  error: { label: "错误", Icon: XCircle, iconClassName: "text-rose-500" },
  message: { label: "消息", Icon: CircleEllipsis, iconClassName: "text-muted-foreground" },
}

export default function NavNotify(props: any) {
  const notes = React.useMemo(() => normalizeNotes(props), [props])
  const count = notes.length
  const [open, setOpen] = React.useState(false)
  const interactedRef = React.useRef(false)

  React.useEffect(() => {
    interactedRef.current = false
  }, [])

  React.useEffect(() => {
    if (count === 0) return
    const key = "aiya_nav_notify_auto_opened"
    try {
      if (sessionStorage.getItem(key)) return
    } catch {
      return
    }

    const onPointerDown = () => {
      interactedRef.current = true
    }
    window.addEventListener("pointerdown", onPointerDown, true)

    const timer = window.setTimeout(() => {
      if (interactedRef.current) return
      setOpen(true)
      try {
        sessionStorage.setItem(key, "1")
      } catch { }
    }, 600)

    return () => {
      window.removeEventListener("pointerdown", onPointerDown, true)
      window.clearTimeout(timer)
    }
  }, [count])

  if (count === 0) return null

  return (
    <Popover open={open} onOpenChange={setOpen}>
      <PopoverTrigger asChild>
        <Button variant="outline" size="icon" className="relative h-9 w-9">
          <Bell className="h-4 w-4" />
          {count > 0 && (
            <span className="absolute -top-1 -right-1 min-w-5 h-5 px-1.5 rounded-full text-[10px] leading-5 text-background bg-primary text-center">
              {count > 99 ? "99+" : count}
            </span>
          )}
        </Button>
      </PopoverTrigger>
      <PopoverContent align="end" sideOffset={8} className="w-[340px] p-0">
        <div className="flex items-center justify-between px-4 py-3">
          <div className="text-sm font-medium">站点通知</div>
          <div className="text-xs text-muted-foreground">{count} 条</div>
        </div>
        <div className="max-h-[420px] overflow-auto">
          <div className="p-2 space-y-2">
            {notes.map((note, idx) => {
              const meta = levelMeta[note.level] ?? levelMeta.message
              const Icon = meta.Icon
              const title = (note.title ?? "").trim()
              const content = (note.content ?? "").trim()
              const time = (note.time ?? "").trim()

              return (
                <div key={`${note.level}-${idx}`} className="rounded-md border bg-background p-3">
                  <div className="flex items-start gap-3">
                    <Icon className={`h-5 w-5 shrink-0 ${meta.iconClassName}`} />
                    <div className="min-w-0 flex-1 pt-0.5">
                      <div className="flex items-center justify-between gap-2">
                        <div className="flex items-center gap-2 min-w-0">
                          <div className="text-sm font-medium truncate">{title || meta.label}</div>
                        </div>
                        {time && <div className="text-[11px] text-muted-foreground shrink-0">{time}</div>}
                      </div>
                      {content && (
                        <div
                          className="mt-2 text-sm text-muted-foreground leading-relaxed"
                          dangerouslySetInnerHTML={{ __html: content }}
                        />
                      )}
                    </div>
                  </div>
                </div>
              )
            })}
          </div>
        </div>
      </PopoverContent>
    </Popover>
  )
}
