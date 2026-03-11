"use client"

import { useState, useEffect } from "react"
import { cn } from "@/lib/utils"

interface MasonryGridProps<T> {
  items: T[]
  render: (item: T, index: number) => React.ReactNode
  columns?: {
    default: number
    sm?: number
    md?: number
    lg?: number
    xl?: number
  }
  gap?: number
  className?: string
}

export function MasonryGrid<T>({ 
  items, 
  render, 
  columns = { default: 1 }, 
  gap = 16, 
  className 
}: MasonryGridProps<T>) {
  const [columnCount, setColumnCount] = useState(columns.default)

  useEffect(() => {
    const handleResize = () => {
      const width = window.innerWidth
      let cols = columns.default
      if (columns.xl && width >= 1280) cols = columns.xl
      else if (columns.lg && width >= 1024) cols = columns.lg
      else if (columns.md && width >= 768) cols = columns.md
      else if (columns.sm && width >= 640) cols = columns.sm
      setColumnCount(cols)
    }

    // Initial check
    handleResize()

    window.addEventListener("resize", handleResize)
    return () => window.removeEventListener("resize", handleResize)
  }, [columns.default, columns.sm, columns.md, columns.lg, columns.xl])

  // Distribute items into columns
  const columnWrapper = Array.from({ length: columnCount }, () => [] as T[])
  
  items.forEach((item, index) => {
    columnWrapper[index % columnCount].push(item)
  })

  return (
    <div
      className={cn("flex w-full items-start", className)} 
      style={{ gap: `${gap}px` }}
    >
      {columnWrapper.map((colItems, colIndex) => (
        <div 
          key={colIndex} 
          className="flex flex-col flex-1 min-w-0" 
          style={{ gap: `${gap}px` }}
        >
          {colItems.map((item) => {
             const originalIndex = items.indexOf(item)
             return (
               <div key={originalIndex} className="w-full">
                 {render(item, originalIndex)}
               </div>
             )
          })}
        </div>
      ))}
    </div>
  )
}
