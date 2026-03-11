import * as React from "react"
import { Card, CardHeader, CardTitle, CardDescription, CardContent } from "@/components/ui/card"
import { Button } from "@/components/ui/button"
import { Input } from "@/components/ui/input"
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from "@/components/ui/select"
import { Alert, AlertDescription, AlertTitle } from "@/components/ui/alert"
import { getConfig } from "@/lib/utils"
import { SwitchCamera, Loader2, CheckCircle2, AlertCircle } from "lucide-react"

interface Props {
    code_from?: string[];
}

export default function UserSponsorActivate({ code_from = [] }: Props) {
    const sources = React.useMemo(() => (Array.isArray(code_from) ? code_from : []), [code_from]);
    const [orderBy, setOrderBy] = React.useState(sources[0] || '');
    const [order, setOrder] = React.useState('');
    const [loading, setLoading] = React.useState(false);
    const [message, setMessage] = React.useState<{ type: 'success' | 'error', text: string, description?: string } | null>(null);

    React.useEffect(() => {
        if (!orderBy && sources.length > 0) {
            setOrderBy(sources[0] || "");
        }
    }, [orderBy, sources]);

    const handleSubmit = async (e: React.FormEvent) => {
        e.preventDefault();
        setLoading(true);
        setMessage(null);

        const config = getConfig();
        const apiUrl = config.apiUrl;
        const nonce = config.apiNonce;

        try {
            const response = await fetch(`${apiUrl}/aiya/v1/sponsor_activate`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-WP-Nonce': nonce || '',
                },
                body: JSON.stringify({
                    order_by: orderBy,
                    order: order,
                }),
            });

            const data = await response.json();

            if (response.ok) {
                setMessage({
                    type: 'success',
                    text: data.message || '激活成功',
                    description: data.description
                });
                setOrder('');
            } else {
                setMessage({
                    type: 'error',
                    text: data.message || '激活失败',
                    description: data.detail || data.data?.detail
                });
            }
        } catch {
            setMessage({ type: 'error', text: '请求失败', description: '请检查网络连接后重试' });
        } finally {
            setLoading(false);
        }
    };

    const getSourceLabel = (source: string) => {
        const map: Record<string, string> = {
            'code': '兑换码',
            'afdian': '爱发电',
            'patreon': 'Patreon'
        };
        return map[source] || source;
    };

    if (sources.length === 0) {
        return null;
    }

    return (
        <Card>
            <CardHeader>
                <CardTitle className="flex items-center gap-2">
                    <SwitchCamera className="w-6 h-6" />
                    <h4 className=" font-bold tracking-tight">自助激活</h4>
                </CardTitle>
                <CardDescription>使用您获得的订单号或激活码激活赞助者身份</CardDescription>
            </CardHeader>
            <CardContent className="space-y-4">
                {message && (
                    <Alert variant={message.type === 'error' ? 'destructive' : 'default'} className={message.type === 'success' ? 'border-green-500 text-green-500' : ''}>
                        {message.type === 'success' ? <CheckCircle2 className="h-4 w-4" /> : <AlertCircle className="h-4 w-4" />}
                        <AlertTitle>{message.text}</AlertTitle>
                        <AlertDescription>{message.description}</AlertDescription>
                    </Alert>
                )}
                <form onSubmit={handleSubmit} className="flex items-end gap-2">
                    <Select value={orderBy} onValueChange={setOrderBy}>
                        <SelectTrigger className="w-[140px]">
                            <SelectValue placeholder="选择方式" />
                        </SelectTrigger>
                        <SelectContent>
                            {sources.map((method) => (
                                <SelectItem key={method} value={method}>
                                    {getSourceLabel(method)}
                                </SelectItem>
                            ))}
                        </SelectContent>
                    </Select>
                    <Input
                        className="w-full flex-1"
                        placeholder="请输入您的订单号 / 激活码"
                        value={order}
                        onChange={(e) => setOrder(e.target.value)}
                        required
                    />
                    <Button type="submit" disabled={loading}>
                        {loading && <Loader2 className="mr-2 h-4 w-4 animate-spin" />}
                        立即激活
                    </Button>
                </form>
            </CardContent>
        </Card>
    );
}
