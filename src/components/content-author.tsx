import { __ } from '@wordpress/i18n';

import {
    Avatar,
    AvatarFallback,
    AvatarImage,
} from "@/components/ui/avatar"
import { Card, CardContent } from "@/components/ui/card"
import { Badge } from "@/components/ui/badge"

export interface LoopAuthorProps {
    avatar: string
    name: string
    role: 'guest' | 'subscriber' | 'author' | 'sponsor' | 'administrator'
    description: string
    className?: string
}

const roleMap: Record<string, string> = {
    administrator: __('管理员', 'aiya-cms'),
    editor: __('编辑', 'aiya-cms'),
    author: __('作者', 'aiya-cms'),
    contributor: __('贡献者', 'aiya-cms'),
    subscriber: __('用户', 'aiya-cms'),
    guest: __('访客', 'aiya-cms'),
    sponsor: __('会员', 'aiya-cms'),
}

export default function LoopAuthor({
    avatar,
    name,
    role,
    description,
    className,
}: LoopAuthorProps) {

    return (
        <Card className={`mb-8 border-none shadow-none bg-secondary/50 ${className || ''}`}>
            <CardContent className="flex flex-col sm:flex-row items-center sm:items-start gap-6 p-6">
                <Avatar className="w-24 h-24 border-4 border-background shadow-sm">
                    <AvatarImage src={avatar} alt={name} />
                    <AvatarFallback className="text-2xl">
                        {name.slice(0, 2).toUpperCase()}
                    </AvatarFallback>
                </Avatar>

                <div className="flex-1 space-y-2 text-center sm:text-left">
                    <div className="flex flex-col sm:flex-row items-center gap-3">
                        <h1 className="text-2xl font-bold tracking-tight text-foreground">
                            {name}
                        </h1>
                        <Badge variant="secondary" className="text-[10px] px-1 py-0 h-4 leading-none shrink-0 font-normal">
                            {roleMap[role] || role}
                        </Badge>
                    </div>

                    <p className="text-muted-foreground leading-relaxed max-w-2xl">
                        {description}
                    </p>
                </div>
            </CardContent>
        </Card>
    )
}
