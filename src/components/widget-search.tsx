"use client"

import * as React from "react"
import { Search, X } from "lucide-react"

import { Button } from "@/components/ui/button"
import { Input } from "@/components/ui/input"
import { Card, CardContent } from "@/components/ui/card"

export default function WidgetSearch() {
    const [query, setQuery] = React.useState("")
    const inputRef = React.useRef<HTMLInputElement>(null)

    const hasQuery = query.length > 0

    const handleClear = () => {
        setQuery("")
        inputRef.current?.focus()
    }

    return (
        <Card className="border-0 shadow-none bg-transparent">
            <CardContent className="p-0">
                <form action="/" method="get" className="relative">
                    <div className="relative flex items-center w-full bg-background rounded-lg border shadow-sm">
                        <Search className="absolute left-3 h-4 w-4 text-muted-foreground pointer-events-none" />

                        <Input
                            ref={inputRef}
                            type="search"
                            name="s"
                            placeholder="搜索..."
                            value={query}
                            onChange={(e) => setQuery(e.target.value)}
                            className="pl-9 pr-10 h-10 w-full border-0 focus-visible:ring-0 shadow-none bg-transparent [&::-webkit-search-cancel-button]:hidden"
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
                                    <span className="sr-only">清除</span>
                                </Button>
                            )}
                        </div>
                    </div>
                </form>
            </CardContent>
        </Card>
    )
}
