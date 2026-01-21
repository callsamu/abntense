import { ReferenceRecord } from '@/lib/references';
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
    location: string;
    description: string;
    institution: string;
    year: number;
}

export interface DocumentData {
    id: number;
    title: string;
    updated_at: string;
    metadata: AbntMetadata;
    references: {
        [key: string]: ReferenceRecord
    };
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
