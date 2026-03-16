import React, { useEffect, useRef, useState } from 'react';
import { route } from 'ziggy-js';
import ChatMessage from '@/components/ui/chat-message';
import AppLayout from '@/layouts/app-layout';

type Message = {
    id: number | string;
    chat_id: number;
    role: string;
    content: string;
    sequence: number;
    model?: string | null;
    created_at?: string | null;
    updated_at?: string | null;
};

export default function Show({ chat, messages: initialMessages }: any) {
    const breadcrumbs = [
        { title: chat.title ?? `Chat #${chat.id}`, href: `/chat/${chat.id}` },
    ];

    const [messages, setMessages] = useState<Message[]>(initialMessages ?? []);
    const [sending, setSending] = useState(false);
    const [inputText, setInputText] = useState('');
    const conversationDiv = useRef<HTMLDivElement | null>(null);

    const csrf =
        (document.querySelector('meta[name="csrf-token"]') as HTMLMetaElement)
            ?.content ?? '';

    const scrollToBottom = () => {
        const el = conversationDiv.current;
        if (!el) return;
        el.scrollTo({ top: el.scrollHeight, behavior: 'smooth' });
    };

    useEffect(() => {
        scrollToBottom();
    }, [messages.length]);

    const requestUseLog = async () => {
        try {
            const response = await fetch(
                route('use-log.store', { chat: chat.id }),
                {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        Accept: 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': csrf,
                    },
                },
            );
            if (!response.ok) return;
            const data = await response.json();
            console.log(data);
        } catch (error) {
            console.error(error);
        }
    };

    const handleInputSubmit = async (content: string) => {
        const trimmed = content.trim();
        if (!trimmed || sending) return;

        setSending(true);
        setInputText('');

        const tempId = `temp-user-${Date.now()}`;
        const tempUserMessage: Message = {
            id: tempId,
            chat_id: chat.id,
            role: 'user',
            content: trimmed,
            sequence: Number.MAX_SAFE_INTEGER,
            model: null,
            created_at: new Date().toISOString(),
            updated_at: new Date().toISOString(),
        };

        setMessages((prev) => [...prev, tempUserMessage]);

        try {
            const response = await fetch(
                route('chat.messages.store', { chat: chat.id }),
                {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        Accept: 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': csrf,
                    },
                    body: JSON.stringify({ content: trimmed }),
                },
            );

            if (!response.ok) throw new Error('Failed to send message');

            const data = await response.json();

            setMessages((prev) => [
                ...prev.filter((m) => m.id !== tempId),
                data.userMessage,
                data.assistantMessage,
            ]);

            requestUseLog();
        } catch (error) {
            setMessages((prev) => prev.filter((m) => m.id !== tempId));
            setInputText(trimmed);
            console.error(error);
        } finally {
            setSending(false);
        }
    };

    const handleKeyDown = (e: React.KeyboardEvent<HTMLInputElement>) => {
        if (e.key !== 'Enter') return;
        e.preventDefault();
        handleInputSubmit(inputText);
    };

    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <div className="flex h-[90vh] flex-col">
                <div className="flex-1 overflow-auto" ref={conversationDiv}>
                    <div className="mx-auto flex max-w-3xl flex-col gap-4 p-4">
                        {messages.map((m) => (
                            <ChatMessage key={m.id} message={m} />
                        ))}
                    </div>
                </div>

                <div className="mx-auto flex w-full max-w-3xl flex-col items-center justify-center gap-2 px-6 py-8">
                    {sending && (
                        <p className="animate-bounce text-neutral-400">
                            Awaiting Response...
                        </p>
                    )}
                    <input
                        type="text"
                        className="w-full rounded-3xl border px-6 py-3 shadow-lg"
                        placeholder="Ask anything..."
                        onChange={(e) => setInputText(e.target.value)}
                        onKeyDown={handleKeyDown}
                        value={inputText}
                        disabled={sending}
                    />
                </div>
            </div>
        </AppLayout>
    );
}
