import { __ } from '@wordpress/i18n';

import * as React from "react"
import { Card, CardDescription, CardHeader, CardTitle, CardFooter } from '@/components/ui/card';
import { Button } from '@/components/ui/button';
import { Dialog, DialogContent, DialogDescription, DialogHeader, DialogTitle, DialogFooter } from '@/components/ui/dialog';
import { Empty, EmptyHeader, EmptyTitle, EmptyDescription, EmptyMedia } from "@/components/ui/empty";
import { Receipt, ExternalLink, SendToBack, PackageOpen } from 'lucide-react';

interface Plan {
    title: string;
    desc?: string;
    price?: string;
    color: string;
    href: string;
    href_title: string;
    triggered_msg: string;
    refresh: boolean | string | number;
}

interface UserSponsorSubscribeProps {
    plans: Record<string, Plan>;
}

export default function UserSponsorSubscribe({ plans }: UserSponsorSubscribeProps) {
    const [selectedPlan, setSelectedPlan] = React.useState<Plan | null>(null);
    const [open, setOpen] = React.useState(false);

    const hasPlans = plans && Object.keys(plans).length > 0;

    const handlePlanClick = (plan: Plan) => {
        if (plan.href) {
            window.open(plan.href, '_blank');
            setSelectedPlan(plan);
            setOpen(true);
        }
    };


    return (
        <>
            <div className="flex items-center gap-2 pl-2 my-4">
                <Receipt className="w-6 h-6 text-primary" />
                <h2 className="text-xl font-bold tracking-tight">{__('用户赞助计划', 'aiya-cms')}</h2>
            </div>

            {!hasPlans ? (
                <Empty>
                    <EmptyMedia>
                        <PackageOpen className="text-muted-foreground size-10" />
                    </EmptyMedia>
                    <EmptyHeader>
                        <EmptyTitle>{__('暂无用户赞助计划', 'aiya-cms')}</EmptyTitle>
                        <EmptyDescription>
                            {__('当前没有可用的赞助订阅方案，请稍后再来看看吧。', 'aiya-cms')}
                        </EmptyDescription>
                    </EmptyHeader>
                </Empty>
            ) : (
                <div className="grid grid-cols-2 md:grid-cols-5 gap-4">
                    {Object.entries(plans).map(([key, plan]) => (
                        <Card
                            key={key}
                            className="flex flex-col h-full hover:shadow-md transition-shadow cursor-pointer border-t-4 border-l-4"
                            style={{ borderTopColor: plan.color || undefined, borderLeftColor: plan.color || undefined }}
                            onClick={() => handlePlanClick(plan)}
                        >
                            <CardHeader>
                                <CardTitle className="truncate w-full text-lg flex flex-col items-start gap-1">
                                    {plan.title}
                                </CardTitle>
                                <CardDescription>
                                    <span className="text-2xl font-bold text-primary">{plan.price}</span>
                                    <p className="text-sm text-muted-foreground mt-2">{plan.desc}</p>
                                </CardDescription>
                            </CardHeader>
                            <CardFooter className="mt-auto ">
                                <Button className="w-full" variant="outline" style={{ color: plan.color, borderColor: plan.color }} title={__('跳转到第三方支付接口', 'aiya-cms')}>
                                    <ExternalLink className="mr-2 h-4 w-4" />
                                    {plan.href_title || __('跳转', 'aiya-cms')}
                                </Button>
                            </CardFooter>
                        </Card>
                    ))}
                </div>
            )}

            <Dialog open={open} onOpenChange={setOpen}>
                <DialogContent>
                    <DialogHeader>
                        <DialogTitle className="flex items-center">
                            <SendToBack className="w-5 h-5 mr-2" />
                            {__('正在处理', 'aiya-cms')}
                        </DialogTitle>
                        <DialogDescription className="pt-4 text-base">
                            {selectedPlan?.triggered_msg || __('请在新打开的页面中完成操作...', 'aiya-cms')}
                        </DialogDescription>
                    </DialogHeader>
                    <DialogFooter>
                        <Button variant="secondary" onClick={() => setOpen(false)}>{__('关闭', 'aiya-cms')}</Button>
                        <Button onClick={() => window.location.reload()}>{__('刷新页面', 'aiya-cms')}</Button>
                    </DialogFooter>
                </DialogContent>
            </Dialog>
        </>
    );
}
