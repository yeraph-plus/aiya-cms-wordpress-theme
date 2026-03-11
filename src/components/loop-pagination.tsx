"use client"

import { cn } from "@/lib/utils"
import {
  Pagination,
  PaginationContent,
  PaginationEllipsis,
  PaginationItem,
  PaginationLink,
  PaginationNext,
  PaginationPrevious,
} from "@/components/ui/pagination"

export interface PaginationLink {
  type: 'prev' | 'next' | 'page' | 'current'
  url: string
  text: string
  label: string
  active?: boolean | number
}

interface LoopPaginationProps {
  current: number
  total: number
  perPage: number
  links: PaginationLink[]
  className?: string
}

export default function LoopPagination({
  current,
  total,
  links,
  className,
}: LoopPaginationProps) {
  if (!links || links.length === 0) return null

  // Separate links into prev, next, and pages
  const prevLink = links.find(link => link.type === 'prev')
  const nextLink = links.find(link => link.type === 'next')
  const pageLinks = links.filter(link => link.type === 'page' || link.type === 'current')

  return (
    <div className={cn("my-4 flex flex-col items-center gap-4", className)}>
      <Pagination>
        <PaginationContent>
          <PaginationItem>
            {prevLink ? (
              <PaginationPrevious href={prevLink.url} />
            ) : (
              <PaginationPrevious href="#" className="pointer-events-none opacity-50" />
            )}
          </PaginationItem>

          {pageLinks.map((link, index) => {
            const isActive = link.type === 'current' || link.active === true || link.active === 1
            
            // Check if we need to render an ellipsis
            // This is a simplified logic, assuming the backend or parent component provides the links correctly including dots if needed
            // If the link text is dots, render PaginationEllipsis
            if (link.text === '…' || link.text === '...') {
               return (
                <PaginationItem key={`${link.type}-${link.text}-${index}`}>
                  <PaginationEllipsis />
                </PaginationItem>
               )
            }

            return (
              <PaginationItem key={`${link.type}-${link.text}-${index}`}>
                <PaginationLink 
                  href={link.url} 
                  isActive={isActive}
                >
                  {link.text}
                </PaginationLink>
              </PaginationItem>
            )
          })}

          <PaginationItem>
            {nextLink ? (
              <PaginationNext href={nextLink.url} />
            ) : (
              <PaginationNext href="#" className="pointer-events-none opacity-50" />
            )}
          </PaginationItem>
        </PaginationContent>
      </Pagination>

      {/* Pagination Info Text */}
      <div className="text-sm text-muted-foreground">
        第 {current} 页，共 {total} 页
      </div>
    </div>
  )
}
