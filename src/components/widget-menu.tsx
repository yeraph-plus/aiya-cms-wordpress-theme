import { __ } from '@wordpress/i18n';

import { ChevronRight, MoreHorizontal, EllipsisVertical } from "lucide-react"

import { cn } from "@/lib/utils"
import { Button } from "@/components/ui/button"
import {
  Card,
  CardHeader,
  CardTitle,
  CardContent,
} from "@/components/ui/card"
import {
  Collapsible,
  CollapsibleContent,
  CollapsibleTrigger,
} from "@/components/ui/collapsible"
import {
  DropdownMenu,
  DropdownMenuContent,
  DropdownMenuItem,
  DropdownMenuTrigger,
  DropdownMenuSub,
  DropdownMenuSubTrigger,
  DropdownMenuSubContent,
} from "@/components/ui/dropdown-menu"

type MenuNode = {
  label?: string
  url?: string
  target?: string
  is_active?: boolean
  child?: Record<string, MenuNode> | MenuNode[]
}

function normalizeMenuNodes(nodes: any): MenuNode[] {
  if (!nodes) {
    return []
  }

  if (Array.isArray(nodes)) {
    return nodes
  }

  return Object.values(nodes)
}

interface WidgetMenuProps {
  menu: MenuNode[] | Record<string, MenuNode>
  widgetTitle?: string
  className?: string
}

function RecursiveDropdownMenu({ items }: { items: MenuNode[] }) {
  if (!items || items.length === 0) return null

  return (
    <>
      {items.map((item, idx) => {
        const children = normalizeMenuNodes(item.child)
        if (children.length > 0) {
          return (
            <DropdownMenuSub key={idx}>
              <DropdownMenuSubTrigger>
                <span>{item.label}</span>
              </DropdownMenuSubTrigger>
              <DropdownMenuSubContent>
                <RecursiveDropdownMenu items={children} />
              </DropdownMenuSubContent>
            </DropdownMenuSub>
          )
        }

        return (
          <DropdownMenuItem key={idx} asChild>
            <a href={item.url} target={item.target}>
              {item.label}
            </a>
          </DropdownMenuItem>
        )
      })}
    </>
  )
}

export default function WidgetMenu({ menu, widgetTitle, className }: WidgetMenuProps) {
  const items = normalizeMenuNodes(menu)

  if (!items.length) {
    return null
  }

  return (
    <Card className={cn("border-0 shadow-none bg-transparent", className || "")}>
      {widgetTitle && (
        <CardHeader className="px-0 pb-0">
          <CardTitle className="text-lg font-bold flex items-center gap-2">
            <EllipsisVertical className="w-5 h-5 text-primary" />
            {widgetTitle}
          </CardTitle>
        </CardHeader>
      )}
      <CardContent className="p-2 grid gap-1">
        {items.map((item, idx) => {
          const children = normalizeMenuNodes(item.child)

          if (children.length > 0) {
            return (
              <Collapsible
                key={idx}
                defaultOpen={true}
                className="group/collapsible"
              >
                <CollapsibleTrigger asChild>
                  <Button
                    variant="ghost"
                    className="w-full justify-between font-normal"
                  >
                    <span>{item.label}</span>
                    <ChevronRight className="ml-auto size-4 transition-transform duration-200 group-data-[state=open]/collapsible:rotate-90" />
                  </Button>
                </CollapsibleTrigger>
                <CollapsibleContent className="pl-4 mt-1 space-y-1">
                  {children.map((subItem, sIdx) => {
                    const subChildren = normalizeMenuNodes(subItem.child)

                    if (subChildren.length > 0) {
                      return (
                        <DropdownMenu key={sIdx}>
                          <DropdownMenuTrigger asChild>
                            <Button
                              variant="ghost"
                              size="sm"
                              className="w-full justify-between h-8 font-normal"
                            >
                              <span>{subItem.label}</span>
                              <MoreHorizontal className="ml-auto size-4 opacity-50" />
                            </Button>
                          </DropdownMenuTrigger>
                          <DropdownMenuContent side="right" align="start">
                            <RecursiveDropdownMenu items={subChildren} />
                          </DropdownMenuContent>
                        </DropdownMenu>
                      )
                    }

                    return (
                      <Button
                        key={sIdx}
                        variant="ghost"
                        size="sm"
                        asChild
                        className={cn(
                          "w-full justify-start h-8 font-normal",
                          subItem.is_active && "bg-accent text-accent-foreground font-medium"
                        )}
                      >
                        <a href={subItem.url} target={subItem.target}>
                          <span>{subItem.label}</span>
                        </a>
                      </Button>
                    )
                  })}
                </CollapsibleContent>
              </Collapsible>
            )
          }

          return (
            <Button
              key={idx}
              variant="ghost"
              asChild
              className={cn(
                "w-full justify-start font-normal",
                item.is_active && "bg-accent text-accent-foreground font-medium"
              )}
            >
              <a href={item.url} target={item.target}>
                <span>{item.label}</span>
              </a>
            </Button>
          )
        })}
      </CardContent>
    </Card>
  )
}
