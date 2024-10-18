import { JSONContent } from '@tiptap/core';
import { Config } from 'ziggy-js';

export interface User {
    id: number;
    name: string;
    email: string;
    email_verified_at?: string;
    role: string;
}

export interface AbntMetadata {
}

export interface DocumentData {
    id: number;
    title: string;
    updated_at: string;
    metadata: AbntMetadata;
    content: JSONContent;
    users: User[];
}

export type PageProps<
    T extends Record<string, unknown> = Record<string, unknown>,
> = T & {
    auth: {
        user: User;
    };
    ziggy: Config & { location: string };
};
