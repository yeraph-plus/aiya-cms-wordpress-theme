import { __ } from '@wordpress/i18n';

import * as React from "react"
import { cn } from "@/lib/utils"
import {
  NavigationMenu,
  NavigationMenuContent,
  NavigationMenuItem,
  NavigationMenuLink,
  NavigationMenuList,
  NavigationMenuTrigger,
  navigationMenuTriggerStyle,
} from "@/components/ui/navigation-menu"

type MenuNode = {
  label?: string
  url?: string
  target?: string
  is_active?: boolean
  child?: Record<string, MenuNode> | MenuNode[]
  description?: string // Added description support if available, though PHP might not send it
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

interface NavMenuProps {
  menu: MenuNode[] | Record<string, MenuNode>
  className?: string
}

export default function NavMenu({ menu, className }: NavMenuProps) {
  const items = normalizeMenuNodes(menu)

  if (!items.length) {
    return null
  }

  return (
    <NavigationMenu className={className}>
      <NavigationMenuList>
        {items.map((item, index) => {
          const children = normalizeMenuNodes(item.child)
          const hasChildren = children.length > 0

          if (hasChildren) {
            return (
              <NavigationMenuItem key={index}>
                <NavigationMenuTrigger className="bg-transparent">{item.label}</NavigationMenuTrigger>
                <NavigationMenuContent>
                  <ul className="grid w-[400px] gap-3 p-4 md:w-[500px] md:grid-cols-2 lg:w-[600px]">
                    {children.map((child, childIndex) => (
                      <ListItem
                        key={childIndex}
                        title={child.label}
                        href={child.url}
                        target={child.target}
                      >
                        {child.description}
                      </ListItem>
                    ))}
                  </ul>
                </NavigationMenuContent>
              </NavigationMenuItem>
            )
          }

          return (
            <NavigationMenuItem key={index}>
              <NavigationMenuLink
                href={item.url}
                target={item.target}
                className={cn(navigationMenuTriggerStyle(), "bg-transparent")}
              >
                {item.label}
              </NavigationMenuLink>
            </NavigationMenuItem>
          )
        })}
      </NavigationMenuList>
    </NavigationMenu>
  )
}

const ListItem = React.forwardRef<
  React.ElementRef<"a">,
  React.ComponentPropsWithoutRef<"a">
>(({ className, title, children, ...props }, ref) => {
  return (
    <li>
      <NavigationMenuLink asChild>
        <a
          ref={ref}
          className={cn(
            "block select-none space-y-1 rounded-lg p-3 leading-none no-underline outline-none transition-colors hover:bg-accent hover:text-accent-foreground focus:bg-accent focus:text-accent-foreground border border-transparent",
            className
          )}
          {...props}
        >
          <div className="text-sm font-medium leading-none">{title}</div>
          {children && (
            <p className="line-clamp-2 text-sm leading-snug text-muted-foreground">
              {children}
            </p>
          )}
        </a>
      </NavigationMenuLink>
    </li>
  )
})
ListItem.displayName = "ListItem"
