"use client"

import * as React from "react"
import { Search, X } from "lucide-react"

import { cn, useIsMobile } from "@/lib/utils"
import { Button } from "@/components/ui/button"
import { Input } from "@/components/ui/input"
import { InputGroup, InputGroupAddon } from "@/components/ui/input-group"
import { Kbd, KbdGroup } from "@/components/ui/kbd"
import { useSearchHistoryStore } from "@/stores/search-history"

const SEARCH_DATALIST_ID = "aiya-nav-search-history"

export default function NavSearch() {
  const isMobile = useIsMobile()
  const [query, setQuery] = React.useState("")
  const inputRef = React.useRef<HTMLInputElement>(null)
  const recentSearches = useSearchHistoryStore((state) => state.recentSearches)
  const addRecentSearch = useSearchHistoryStore((state) => state.addRecentSearch)

  const hasQuery = query.trim().length > 0

  const handleClear = () => {
    setQuery("")
    inputRef.current?.focus()
  }

  React.useEffect(() => {
    const currentQuery = new URLSearchParams(window.location.search).get("s")
    if (currentQuery) {
      setQuery(currentQuery)
    }
  }, [])

  React.useEffect(() => {
    const down = (e: KeyboardEvent) => {
      if (e.key === "k" && (e.metaKey || e.ctrlKey)) {
        e.preventDefault()
        inputRef.current?.focus()
      }
      if (e.key === "Escape") {
        if (document.activeElement === inputRef.current) {
          if (hasQuery) {
            e.preventDefault()
            handleClear()
          } else {
            inputRef.current?.blur()
          }
        }
      }
    }
    document.addEventListener("keydown", down)
    return () => document.removeEventListener("keydown", down)
  }, [hasQuery])

  if (isMobile) {
    return null
  }

  const handleSubmit = () => {
    addRecentSearch(query)
  }

  return (
    <div className="w-full max-w-sm">
      <form action="/" method="get" onSubmit={handleSubmit}>
        <InputGroup>
          <InputGroupAddon align="inline-start" className="pointer-events-none">
            <Search className="h-4 w-4 text-muted-foreground" />
          </InputGroupAddon>
          <Input
            ref={inputRef}
            type="search"
            name="s"
            data-slot="input-group-control"
            placeholder="Search..."
            value={query}
            onChange={(e) => setQuery(e.target.value)}
            list={recentSearches.length > 0 ? SEARCH_DATALIST_ID : undefined}
            className={cn(
              "shadow-none border-0 focus-visible:ring-0",
              // Hide browser default clear button
              "[&::-webkit-search-cancel-button]:hidden"
            )}
          />
          <InputGroupAddon align="inline-end">
            {hasQuery ? (
              <Button
                type="button"
                variant="ghost"
                size="icon"
                className="h-6 w-6 text-muted-foreground hover:text-foreground"
                onClick={handleClear}
              >
                <X className="h-4 w-4" />
                <span className="sr-only">Clear</span>
              </Button>
            ) : (
              <KbdGroup className="pointer-events-none">
                <Kbd>Ctrl</Kbd>
                <span className="text-muted-foreground font-medium">+</span>
                <Kbd>K</Kbd>
              </KbdGroup>
            )}
          </InputGroupAddon>
        </InputGroup>
        {recentSearches.length > 0 && (
          <datalist id={SEARCH_DATALIST_ID}>
            {recentSearches.map((term) => (
              <option key={term} value={term} />
            ))}
          </datalist>
        )}
      </form>
    </div>
  )
}
