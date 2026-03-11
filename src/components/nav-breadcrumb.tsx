import React from "react"
import { Navigation } from "lucide-react"
import {
  Breadcrumb,
  BreadcrumbItem,
  BreadcrumbLink,
  BreadcrumbList,
  BreadcrumbPage,
  BreadcrumbSeparator,
} from "@/components/ui/breadcrumb"

interface BreadcrumbItemType {
  label: string
  url: string
}

interface LoopBreadcrumbProps {
  items: BreadcrumbItemType[]
}

export default function NavBreadcrumb({ items }: LoopBreadcrumbProps) {
  if (!items || items.length === 0) return null

  return (
    <Breadcrumb className="my-4">
      <BreadcrumbList className="flex-nowrap overflow-hidden">
        <BreadcrumbItem className="shrink-0">
          <Navigation  className="h-4 w-4" />
        </BreadcrumbItem>
        {items.map((item, index) => {
          const isLast = index === items.length - 1

          return (
            <React.Fragment key={`${item.url}-${index}`}>
              <BreadcrumbItem className="whitespace-nowrap min-w-0">
                {isLast ? (
                  <BreadcrumbPage className="truncate max-w-[240px] sm:max-w-[400px] md:max-w-[400px] block">{item.label}</BreadcrumbPage>
                ) : (
                  <BreadcrumbLink href={item.url} className="truncate max-w-[160px] sm:max-w-[240px] md:max-w-[400px] block">{item.label}</BreadcrumbLink>
                )}
              </BreadcrumbItem>
              {!isLast && <BreadcrumbSeparator className="shrink-0" />}
            </React.Fragment>
          )
        })}
      </BreadcrumbList>
    </Breadcrumb>
  )
}
