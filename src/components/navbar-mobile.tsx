import { __ } from '@wordpress/i18n';

import * as React from "react"
import {
  ChevronRight,
  CircleEllipsis,
  Info,
  Menu,
  MoreHorizontal,
  Search,
  X,
  AlertTriangle,
  CheckCircle2,
  XCircle,
} from "lucide-react"

import { cn } from "@/lib/utils"
import { Button } from "@/components/ui/button"
import {
  Collapsible,
  CollapsibleContent,
  CollapsibleTrigger,
} from "@/components/ui/collapsible"
import {
  DropdownMenu,
  DropdownMenuContent,
  DropdownMenuItem,
  DropdownMenuSub,
  DropdownMenuSubContent,
  DropdownMenuSubTrigger,
  DropdownMenuTrigger,
} from "@/components/ui/dropdown-menu"
import { Input } from "@/components/ui/input"
import {
  Sheet,
  SheetContent,
  SheetDescription,
  SheetTitle,
  SheetTrigger,
} from "@/components/ui/sheet"
import ModeToggle from "@/components/ui-mode-toggle"
import { useSearchHistoryStore } from "@/stores/search-history"
import { Badge } from "@/components/ui/badge"

type MenuNode = {
  label?: string
  url?: string
  target?: string
  is_active?: boolean
  child?: Record<string, MenuNode> | MenuNode[]
  description?: string
}

type NotifyLevel = "success" | "info" | "warning" | "error" | "message"

type NotifyNote = {
  level: NotifyLevel
  scope: string
  title?: string
  content?: string
  time?: string
}

interface NavbarMobileProps {
  menu?: MenuNode[] | Record<string, MenuNode>
  notes?: NotifyNote[] | Record<string, NotifyNote>
}

const SEARCH_DATALIST_ID = "aiya-mobile-nav-search-history"

const levelMeta: Record<
  NotifyLevel,
  { label: string; Icon: React.ComponentType<{ className?: string }>; iconClassName: string }
> = {
  success: { label: __('成功', 'aiya-cms'), Icon: CheckCircle2, iconClassName: "text-emerald-500" },
  info: { label: __('提示', 'aiya-cms'), Icon: Info, iconClassName: "text-sky-500" },
  warning: { label: __('警告', 'aiya-cms'), Icon: AlertTriangle, iconClassName: "text-amber-500" },
  error: { label: __('错误', 'aiya-cms'), Icon: XCircle, iconClassName: "text-rose-500" },
  message: { label: __('消息', 'aiya-cms'), Icon: CircleEllipsis, iconClassName: "text-muted-foreground" },
}

function normalizeMenuNodes(nodes: NavbarMobileProps["menu"]): MenuNode[] {
  if (!nodes) {
    return []
  }

  if (Array.isArray(nodes)) {
    return nodes
  }

  return Object.values(nodes)
}

function isNotifyNote(value: unknown): value is NotifyNote {
  if (!value || typeof value !== "object") return false
  const v = value as Record<string, unknown>
  return typeof v.level === "string" && typeof v.scope === "string"
}

function normalizeNotes(notes: NavbarMobileProps["notes"]): NotifyNote[] {
  if (!notes) {
    return []
  }

  if (Array.isArray(notes)) {
    return notes.filter(isNotifyNote)
  }

  return Object.values(notes).filter(isNotifyNote)
}

function RecursiveDropdownMenu({
  items,
  onNavigate,
}: {
  items: MenuNode[]
  onNavigate: () => void
}) {
  if (!items.length) {
    return null
  }

  return (
    <>
      {items.map((item, index) => {
        const children = normalizeMenuNodes(item.child)
        const itemKey = `${item.label ?? "menu"}-${index}`

        if (children.length > 0) {
          return (
            <DropdownMenuSub key={itemKey}>
              <DropdownMenuSubTrigger>{item.label}</DropdownMenuSubTrigger>
              <DropdownMenuSubContent>
                {item.url && (
                  <DropdownMenuItem asChild>
                    <a
                      href={item.url}
                      target={item.target}
                      rel={item.target === "_blank" ? "noopener noreferrer" : undefined}
                      onClick={onNavigate}
                      aria-current={item.is_active ? "page" : undefined}
                    >{item.label}</a>
                  </DropdownMenuItem>
                )}
                <RecursiveDropdownMenu items={children} onNavigate={onNavigate} />
              </DropdownMenuSubContent>
            </DropdownMenuSub>
          )
        }

        return (
          <DropdownMenuItem key={itemKey} asChild>
            <a
              href={item.url || "#"}
              target={item.target}
              rel={item.target === "_blank" ? "noopener noreferrer" : undefined}
              onClick={onNavigate}
              aria-current={item.is_active ? "page" : undefined}
            >{item.label}</a>
          </DropdownMenuItem>
        )
      })}
    </>
  )
}

function InlineMenuLevel({
  items,
  level,
  onNavigate,
}: {
  items: MenuNode[]
  level: number
  onNavigate: () => void
}) {
  if (!items.length) {
    return null
  }

  const buttonSize = level === 1 ? "default" : "sm"
  const buttonHeightClass = level === 1 ? "" : "h-8"

  return (
    <div className={cn("grid gap-2", level > 1 && "pl-4 mt-1")}>
      {items.map((item, index) => {
        const children = normalizeMenuNodes(item.child)
        const itemKey = `${level}-${item.label ?? "menu"}-${index}`

        if (!children.length) {
          return (
            <Button
              key={itemKey}
              variant="ghost"
              size={buttonSize}
              asChild
              className={cn(
                "justify-start font-normal",
                buttonHeightClass,
                item.is_active && "bg-accent text-accent-foreground font-medium"
              )}
            >
              <a
                href={item.url || "#"}
                target={item.target}
                rel={item.target === "_blank" ? "noopener noreferrer" : undefined}
                onClick={onNavigate}
                aria-current={item.is_active ? "page" : undefined}
              >
                <span>{item.label}</span>
              </a>
            </Button>
          )
        }

        if (level >= 3) {
          return (
            <DropdownMenu key={itemKey}>
              <DropdownMenuTrigger asChild>
                <Button
                  variant="ghost"
                  size="sm"
                  className="w-full justify-between h-8 font-normal"
                >
                  <span>{item.label}</span>
                  <MoreHorizontal className="ml-auto size-4 opacity-50" />
                </Button>
              </DropdownMenuTrigger>
              <DropdownMenuContent side="right" align="start">
                {item.url && (
                  <DropdownMenuItem asChild>
                    <a
                      href={item.url}
                      target={item.target}
                      rel={item.target === "_blank" ? "noopener noreferrer" : undefined}
                      onClick={onNavigate}
                      aria-current={item.is_active ? "page" : undefined}
                    >
                      {item.label}
                    </a>
                  </DropdownMenuItem>
                )}
                <RecursiveDropdownMenu items={children} onNavigate={onNavigate} />
              </DropdownMenuContent>
            </DropdownMenu>
          )
        }

        return (
          <Collapsible
            key={itemKey}
            defaultOpen={true}
            className="group/collapsible"
          >
            <CollapsibleTrigger asChild>
              <Button
                variant="ghost"
                size={buttonSize}
                className={cn(
                  "w-full justify-between font-normal",
                  buttonHeightClass,
                  item.is_active && "bg-accent text-accent-foreground font-medium"
                )}
              >
                <span>{item.label}</span>
                <ChevronRight className="ml-auto size-4 transition-transform duration-200 group-data-[state=open]/collapsible:rotate-90" />
              </Button>
            </CollapsibleTrigger>
            <CollapsibleContent>
              <div className="space-y-1 pt-1">
                {item.url && (
                  <Button
                    variant="ghost"
                    size="sm"
                    asChild
                    className="w-full justify-start h-8 font-normal"
                  >
                    <a
                      href={item.url}
                      target={item.target}
                      rel={item.target === "_blank" ? "noopener noreferrer" : undefined}
                      onClick={onNavigate}
                      aria-current={item.is_active ? "page" : undefined}
                    >
                      {__('进入', 'aiya-cms')} {' '} {item.label}
                    </a>
                  </Button>
                )}
                <InlineMenuLevel items={children} level={level + 1} onNavigate={onNavigate} />
              </div>
            </CollapsibleContent>
          </Collapsible>
        )
      })}
    </div>
  )
}

function MobileSearch({ onSubmitted }: { onSubmitted: () => void }) {
  const [query, setQuery] = React.useState("")
  const inputRef = React.useRef<HTMLInputElement>(null)
  const recentSearches = useSearchHistoryStore((state) => state.recentSearches)
  const addRecentSearch = useSearchHistoryStore((state) => state.addRecentSearch)
  const hasQuery = query.length > 0

  React.useEffect(() => {
    const currentQuery = new URLSearchParams(window.location.search).get("s")
    if (currentQuery) {
      setQuery(currentQuery)
    }
  }, [])

  const handleClear = () => {
    setQuery("")
    inputRef.current?.focus()
  }

  const handleSubmit = () => {
    addRecentSearch(query)
    onSubmitted()
  }

  return (
    <form action="/" method="get" className="relative" onSubmit={handleSubmit}>
      <div className="relative flex items-center w-full rounded-lg border bg-background shadow-sm">
        <Search className="pointer-events-none absolute left-3 h-4 w-4 text-muted-foreground" />
        <Input
          ref={inputRef}
          type="search"
          name="s"
          placeholder="搜索内容..."
          value={query}
          onChange={(event) => setQuery(event.target.value)}
          list={recentSearches.length > 0 ? SEARCH_DATALIST_ID : undefined}
          className="h-10 w-full border-0 bg-transparent pl-9 pr-10 shadow-none focus-visible:ring-0 [&::-webkit-search-cancel-button]:hidden"
        />
        <div className="absolute right-3 flex items-center gap-2">
          {hasQuery && (
            <Button
              type="button"
              variant="ghost"
              size="icon"
              className="h-5 w-5 hover:bg-muted"
              onClick={handleClear}
            >
              <X className="h-3 w-3 text-muted-foreground" />
              <span className="sr-only">{__('清除', 'aiya-cms')}</span>
            </Button>
          )}
        </div>
      </div>
      {recentSearches.length > 0 && (
        <datalist id={SEARCH_DATALIST_ID}>
          {recentSearches.map((term) => (
            <option key={term} value={term} />
          ))}
        </datalist>
      )}
    </form>
  )
}

export default function NavbarMobile({ menu, notes }: NavbarMobileProps) {
  const [open, setOpen] = React.useState(false)
  const items = React.useMemo(() => normalizeMenuNodes(menu), [menu])
  const notifyNotes = React.useMemo(() => normalizeNotes(notes), [notes])

  return (
    <Sheet open={open} onOpenChange={setOpen}>
      <SheetTrigger asChild>
        <Button
          type="button"
          variant="outline"
          size="icon"
          className="md:hidden"
          aria-label="Open navigation drawer menu"
        >
          <Menu className="h-4 w-4" />
        </Button>
      </SheetTrigger>
      <SheetContent side="left" className="flex h-full w-[88vw] max-w-sm flex-col p-0">
        <SheetTitle className="sr-only">{__('移动导航', 'aiya-cms')}</SheetTitle>
        <SheetDescription className="sr-only">
          {__('移动端导航抽屉，包含搜索、菜单、站点通知和主题切换。', 'aiya-cms')}
        </SheetDescription>
        <div className="shrink-0 border-t bg-background px-4 py-4"></div>
        <div className="flex-1 overflow-y-auto px-4 scrollbar-hide">
          <section className="py-4">
            <MobileSearch onSubmitted={() => setOpen(false)} />
          </section>

          <section className="border-t py-4">
            <div className="mb-3 flex items-center justify-between">
              <div className="text-sm font-medium">{__('菜单', 'aiya-cms')}</div>
            </div>

            {items.length > 0 ? (
              <InlineMenuLevel items={items} level={1} onNavigate={() => setOpen(false)} />
            ) : (
              <div className="rounded-md border border-dashed px-3 py-4 text-sm text-muted-foreground">
                {__('暂无可用导航菜单', 'aiya-cms')}
              </div>
            )}
          </section>

          <section className="border-t py-4">
            <div className="mb-3 flex items-center justify-between">
              <div className="text-sm font-medium">{__('站点通知', 'aiya-cms')}</div>
              <Badge className="">{notifyNotes.length}</Badge>
            </div>
            {notifyNotes.length > 0 ? (
              <div className="space-y-2">
                {notifyNotes.map((note, index) => {
                  const meta = levelMeta[note.level] ?? levelMeta.message
                  const Icon = meta.Icon
                  const title = (note.title ?? "").trim() || meta.label
                  const content = (note.content ?? "").trim()
                  const time = (note.time ?? "").trim()

                  return (
                    <div key={`${note.level}-${index}`} className="rounded-md border bg-background p-3">
                      <div className="flex items-start gap-3">
                        <Icon className={`mt-0.5 h-4 w-4 shrink-0 ${meta.iconClassName}`} />
                        <div className="min-w-0 flex-1">
                          <div className="flex items-center justify-between gap-2">
                            <div className="text-sm font-medium">{title}</div>
                            {time && (
                              <div className="shrink-0 text-[11px] text-muted-foreground">{time}</div>
                            )}
                          </div>
                          {content && (
                            <div
                              className="mt-1 text-sm leading-6 text-muted-foreground"
                              dangerouslySetInnerHTML={{ __html: content }}
                            />
                          )}
                        </div>
                      </div>
                    </div>
                  )
                })}
              </div>
            ) : (
              <div className="rounded-md border border-dashed px-3 py-4 text-sm text-muted-foreground">
                {__('当前没有新的站点通知', 'aiya-cms')}
              </div>
            )}
          </section>
        </div>
        <div className="shrink-0 border-t bg-background px-4 py-4">
          <div className="flex items-center justify-between gap-3">
            <div></div>
            <ModeToggle />
          </div>
        </div>
      </SheetContent>
    </Sheet>
  )
}
