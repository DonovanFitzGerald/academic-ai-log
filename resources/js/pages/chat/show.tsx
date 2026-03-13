import { router } from '@inertiajs/react';
import React, { useState, useRef, useEffect } from 'react';
import { route } from 'ziggy-js';
import ChatMessage from '@/components/ui/chat-message';
import AppLayout from '@/layouts/app-layout';

export default function Show({ chat, messages }) {
    const breadcrumbs = [
        { title: chat.title ?? `Chat #${chat.id}`, href: `/chat/${chat.id}` },
    ];

    const [sending, setSending] = useState(false);
    const conversationDiv = useRef<HTMLDivElement | null>(null);
    const [inputText, setinputText] = useState('');

    const handleInputChange = (e: React.ChangeEvent<HTMLInputElement>) => {
        setinputText(e.target.value);
    };

    const handleKeyDown = (e: React.KeyboardEvent<HTMLInputElement>) => {
        if (e.key !== 'Enter') return;
        e.preventDefault();
        handleInputSubmit(inputText);
    };

    const handleInputSubmit = (content: string) => {
        if (!content.trim() || sending) return;

        router.post(
            route('chat.messages.store', { chat: chat.id }),
            { content },
            {
                preserveScroll: true,
                onStart: () => setSending(true),
                onFinish: () => setSending(false),
                onSuccess: () => {
                    setinputText('');
                },
            },
        );
    };

    useEffect(() => {
        const el = conversationDiv.current;
        if (!el) return;

        el.scrollTo({
            top: el.scrollHeight,
            behavior: 'smooth',
        });
    }, [messages]);

    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <div className="max-h-[90vh] overflow-auto" ref={conversationDiv}>
                <div className="mx-auto flex max-w-3xl flex-col gap-4 p-4">
                    {messages.map((m) => (
                        <ChatMessage key={m.id} message={m} />
                    ))}
                </div>

                <div className="sticky bottom-0 mt-auto flex w-full justify-center bg-background p-8">
                    <input
                        type="text"
                        className="w-full max-w-lg rounded-3xl border px-6 py-3 shadow-lg"
                        id="message-input"
                        placeholder="Ask anything..."
                        onChange={handleInputChange}
                        onKeyDown={handleKeyDown}
                        value={inputText}
                        disabled={sending}
                    />
                </div>
            </div>
        </AppLayout>
    );
}
