import * as React from "react"
import { Card, CardContent, CardDescription, CardHeader, CardTitle, CardAction } from '@/components/ui/card';
import { Badge } from '@/components/ui/badge';
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/components/ui/table';
import { Alert, AlertDescription, AlertTitle } from '@/components/ui/alert';
import { Collapsible, CollapsibleContent, CollapsibleTrigger } from '@/components/ui/collapsible';
import { Button } from '@/components/ui/button';
import { Calendar, History, LockOpen, Clock, CreditCard, AlertTriangle, CirclePause, CircleCheckBig, XCircle, ChevronsUpDown } from 'lucide-react';
import { joinTranslations } from '@/lib/i18n';

const { t, sprintf } = joinTranslations();

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
                {t('force_canceled')}
            </span>;
        }
        if (is_valid) {
            return <span className="inline-flex items-center gap-2 text-green-600 dark:text-green-400">
                <CircleCheckBig className="w-6 h-6" />
                {t('active')}
            </span>;
        }
        return <span className="inline-flex items-center gap-2 text-muted-foreground">
            <CirclePause className="w-6 h-6" />
            {t('not_activated')}
        </span>;
    };

    const getSourceLabel = (source: string) => {
        const map: Record<string, string> = {
            'payment': t('online_payment'),
            'afdian': t('afdian'),
            'code': t('redeem_code'),
            'admin': t('gifted'),
            'manual': t('other'),
        };
        return map[source] || source;
    };

    return (
        <>
            {force_cancel && (
                <Alert variant="destructive">
                    <AlertTriangle className="h-4 w-4" />
                    <AlertTitle>{t('notice')}</AlertTitle>
                    <AlertDescription>
                        {t('sponsor_access_force_canceled_contact_support')}
                    </AlertDescription>
                </Alert>
            )}
            <div className="grid gap-4 md:grid-cols-2 lg:grid-cols-4">
                <Card>
                    <CardHeader className="flex flex-row items-center justify-between space-y-0 pb-2">
                        <CardTitle className="text-md font-medium">{t('current_subscription')}</CardTitle>
                        <CreditCard className="h-6 w-6 text-muted-foreground" />
                    </CardHeader>
                    <CardContent>
                        <div className="text-xl font-bold pt-2">{getStatus()}</div>
                    </CardContent>
                </Card>
                <Card>
                    <CardHeader className="flex flex-row items-center justify-between space-y-0 pb-2">
                        <CardTitle className="text-md font-medium">{t('used_access')}</CardTitle>
                        <LockOpen className="h-6 w-6 text-muted-foreground" />
                    </CardHeader>
                    <CardContent>
                        <div className="text-2xl font-bold">{used_count}</div>
                        <p className="text-xs text-muted-foreground pt-1">{sprintf(t('used_sponsor_access_count_times'), used_count)}</p>
                    </CardContent>
                </Card>
                <Card>
                    <CardHeader className="flex flex-row items-center justify-between space-y-0 pb-2">
                        <CardTitle className="text-md font-medium">{t('remaining_days')}</CardTitle>
                        <Clock className="h-6 w-6 text-muted-foreground" />
                    </CardHeader>
                    <CardContent>
                        <div className="text-2xl font-bold">{left_days} <span className="text-sm font-normal text-muted-foreground">{t('day_unit')}</span></div>
                        <p className="text-xs text-muted-foreground pt-1">{sprintf(t('valid_until_s'), expiration)}</p>
                    </CardContent>
                </Card>
                <Card>
                    <CardHeader className="flex flex-row items-center justify-between space-y-0 pb-2">
                        <CardTitle className="text-md font-medium">{t('total_sponsorship')}</CardTitle>
                        <Calendar className="h-6 w-6 text-muted-foreground" />
                    </CardHeader>
                    <CardContent>
                        <div className="text-2xl font-bold">{total_days} <span className="text-sm font-normal text-muted-foreground">{t('day_unit')}</span></div>
                        <p className="text-xs text-muted-foreground pt-1">{t('thank_you_for_support')}</p>
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
                            {t('historical_order_records')}
                        </CardTitle>
                        <CardDescription>{t('view_all_sponsor_and_redeem_history')}</CardDescription>
                        <CardAction>
                            <CollapsibleTrigger asChild>
                                <Button variant="ghost" size="sm" className="w-9 p-0">
                                    <ChevronsUpDown className="h-4 w-4" />
                                    <span className="sr-only">{t('toggle')}</span>
                                </Button>
                            </CollapsibleTrigger>
                        </CardAction>
                    </CardHeader>
                    <CollapsibleContent>
                        <CardContent>
                            <Table>
                                <TableHeader>
                                    <TableRow>
                                        <TableHead>{t('order_number')}</TableHead>
                                        <TableHead>{t('order_source')}</TableHead>
                                        <TableHead>{t('duration')}</TableHead>
                                        <TableHead>{t('order_status')}</TableHead>
                                        <TableHead className="text-right">{t('order_created_time')}</TableHead>
                                    </TableRow>
                                </TableHeader>
                                <TableBody>
                                    {orders && orders.length > 0 ? (
                                        orders.map((order) => (
                                            <TableRow key={order.id}>
                                                <TableCell className="font-medium font-mono text-xs">{order.order_id}</TableCell>
                                                <TableCell>{getSourceLabel(order.source)}</TableCell>
                                                <TableCell>{order.duration_days} {t('day_unit')}</TableCell>
                                                <TableCell>
                                                    <Badge variant="outline" className="capitalize">
                                                        {order.status === 'paid' ? t('paid') : order.status}
                                                    </Badge>
                                                </TableCell>
                                                <TableCell className="text-right text-muted-foreground text-sm">{order.created_at}</TableCell>
                                            </TableRow>
                                        ))
                                    ) : (
                                        <TableRow>
                                            <TableCell colSpan={5} className="h-24 text-center">
                                                {t('no_order_records')}
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
