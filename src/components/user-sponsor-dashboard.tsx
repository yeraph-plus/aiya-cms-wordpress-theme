import * as React from "react"
import { Card, CardContent, CardDescription, CardHeader, CardTitle, CardAction } from '@/components/ui/card';
import { Badge } from '@/components/ui/badge';
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/components/ui/table';
import { Alert, AlertDescription, AlertTitle } from '@/components/ui/alert';
import { Collapsible, CollapsibleContent, CollapsibleTrigger } from '@/components/ui/collapsible';
import { Button } from '@/components/ui/button';
import { Calendar, History, LockOpen, Clock, CreditCard, AlertTriangle, CirclePause, CircleCheckBig, XCircle, ChevronsUpDown } from 'lucide-react';

interface Order {
    id: number;
    user_id: number;
    order_id: string;
    start_time: number;
    duration_days: number;
    source: string;
    status: string;
    created_at: string;
}

interface SponsorData {
    force_cancel: boolean;
    expiration: string;
    left_days: number;
    is_valid: boolean;
    total_days: number;
    used_count: number;
    orders: Order[];
}

export default function SponsorDashboard({ data }: { data: SponsorData }) {
    const [isOpen, setIsOpen] = React.useState(false);

    if (!data) return null;

    const { orders, force_cancel, expiration, left_days, is_valid, total_days, used_count } = data;

    const getStatus = () => {
        if (force_cancel) {
            return <span className="inline-flex items-center gap-2 text-destructive">
                <XCircle className="w-6 h-6" />
                被强制取消
            </span>;
        }
        if (is_valid) {
            return <span className="inline-flex items-center gap-2 text-green-600 dark:text-green-400">
                <CircleCheckBig className="w-6 h-6" />
                生效中
            </span>;
        }
        return <span className="inline-flex items-center gap-2 text-muted-foreground">
            <CirclePause className="w-6 h-6" />
            未激活
        </span>;
    };

    const getSourceLabel = (source: string) => {
        const map: Record<string, string> = {
            'payment': '在线支付',
            'afdian': '爱发电',
            'code': '兑换码',
            'admin': '赠送',
            'manual': '其他',
        };
        return map[source] || source;
    };

    return (
        <>
            {force_cancel && (
                <Alert variant="destructive">
                    <AlertTriangle className="h-4 w-4" />
                    <AlertTitle>注意</AlertTitle>
                    <AlertDescription>
                        您的赞助权限已被管理员强制取消，如有疑问请联系客服。
                    </AlertDescription>
                </Alert>
            )}
            <div className="grid gap-4 md:grid-cols-2 lg:grid-cols-4">
                <Card>
                    <CardHeader className="flex flex-row items-center justify-between space-y-0 pb-2">
                        <CardTitle className="text-md font-medium">当前订阅</CardTitle>
                        <CreditCard className="h-6 w-6 text-muted-foreground" />
                    </CardHeader>
                    <CardContent>
                        <div className="text-xl font-bold pt-2">{getStatus()}</div>
                    </CardContent>
                </Card>
                <Card>
                    <CardHeader className="flex flex-row items-center justify-between space-y-0 pb-2">
                        <CardTitle className="text-md font-medium">已使用访问</CardTitle>
                        <LockOpen className="h-6 w-6 text-muted-foreground" />
                    </CardHeader>
                    <CardContent>
                        <div className="text-2xl font-bold">{used_count}</div>
                        <p className="text-xs text-muted-foreground pt-1">已使用赞助权限访问 {used_count} 次</p>
                    </CardContent>
                </Card>
                <Card>
                    <CardHeader className="flex flex-row items-center justify-between space-y-0 pb-2">
                        <CardTitle className="text-md font-medium">剩余天数</CardTitle>
                        <Clock className="h-6 w-6 text-muted-foreground" />
                    </CardHeader>
                    <CardContent>
                        <div className="text-2xl font-bold">{left_days} <span className="text-sm font-normal text-muted-foreground">天</span></div>
                        <p className="text-xs text-muted-foreground pt-1">有效期至 {expiration}</p>
                    </CardContent>
                </Card>
                <Card>
                    <CardHeader className="flex flex-row items-center justify-between space-y-0 pb-2">
                        <CardTitle className="text-md font-medium">累计赞助</CardTitle>
                        <Calendar className="h-6 w-6 text-muted-foreground" />
                    </CardHeader>
                    <CardContent>
                        <div className="text-2xl font-bold">{total_days} <span className="text-sm font-normal text-muted-foreground">天</span></div>
                        <p className="text-xs text-muted-foreground pt-1">感谢您的支持</p>
                    </CardContent>
                </Card>
            </div>

            <Card className="mt-4">
                <Collapsible
                    open={isOpen}
                    onOpenChange={setIsOpen}
                >
                    <CardHeader>
                        <CardTitle className="flex items-center gap-2">
                            <History className="w-6 h-6" />
                            历史订单记录
                        </CardTitle>
                        <CardDescription>查看您的所有赞助与兑换历史记录</CardDescription>
                        <CardAction>
                            <CollapsibleTrigger asChild>
                                <Button variant="ghost" size="sm" className="w-9 p-0">
                                    <ChevronsUpDown className="h-4 w-4" />
                                    <span className="sr-only">Toggle</span>
                                </Button>
                            </CollapsibleTrigger>
                        </CardAction>
                    </CardHeader>
                    <CollapsibleContent>
                        <CardContent>
                            <Table>
                                <TableHeader>
                                    <TableRow>
                                        <TableHead>订单号</TableHead>
                                        <TableHead>来源</TableHead>
                                        <TableHead>时长</TableHead>
                                        <TableHead>状态</TableHead>
                                        <TableHead className="text-right">创建时间</TableHead>
                                    </TableRow>
                                </TableHeader>
                                <TableBody>
                                    {orders && orders.length > 0 ? (
                                        orders.map((order) => (
                                            <TableRow key={order.id}>
                                                <TableCell className="font-medium font-mono text-xs">{order.order_id}</TableCell>
                                                <TableCell>{getSourceLabel(order.source)}</TableCell>
                                                <TableCell>{order.duration_days} 天</TableCell>
                                                <TableCell>
                                                    <Badge variant="outline" className="capitalize">
                                                        {order.status === 'paid' ? '已支付' : order.status}
                                                    </Badge>
                                                </TableCell>
                                                <TableCell className="text-right text-muted-foreground text-sm">{order.created_at}</TableCell>
                                            </TableRow>
                                        ))
                                    ) : (
                                        <TableRow>
                                            <TableCell colSpan={5} className="h-24 text-center">
                                                暂无订单记录
                                            </TableCell>
                                        </TableRow>
                                    )}
                                </TableBody>
                            </Table>
                        </CardContent>
                    </CollapsibleContent>
                </Collapsible>
            </Card>
        </>
    );
}
