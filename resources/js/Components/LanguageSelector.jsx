import React from 'react';
import { useForm } from '@inertiajs/react';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/Components/ui/select';
import { Label } from '@/Components/ui/label';

export default function LanguageSelector({ currentLanguage }) {
    const { post } = useForm();

    const handleLanguageChange = (language) => {
        post(route('language.update'), {
            language,
            preserveScroll: true,
        });
    };

    return (
        <div className="space-y-2">
            <Label htmlFor="language">Language / Taal</Label>
            <Select
                value={currentLanguage}
                onValueChange={handleLanguageChange}
            >
                <SelectTrigger id="language">
                    <SelectValue />
                </SelectTrigger>
                <SelectContent>
                    <SelectItem value="nl">Nederlands</SelectItem>
                    <SelectItem value="en">English</SelectItem>
                </SelectContent>
            </Select>
        </div>
    );
} 