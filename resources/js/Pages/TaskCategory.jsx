import React from 'react';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { Head, Link } from '@inertiajs/react';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { ArrowLeft } from 'lucide-react';

const CATEGORY_CONFIG = {
    goods_receiving: {
        emoji: '📦',
        label: 'Acceptance of goods'
    },
    temperature: {
        emoji: '🌡️',
        label: 'Temperature control'
    },
    cleaning: {
        emoji: '🧹',
        label: 'Cleaning records'
    },
    cooking: {
        emoji: '🍳',
        label: 'Critical cooking'
    },
    verification: {
        emoji: '📱',
        label: 'Verification devices'
    }
};

const TaskCategory = ({ category, tasks }) => {
    const config = CATEGORY_CONFIG[category] || {
        emoji: '📋',
        label: category
    };

    return (
        <AuthenticatedLayout>
            <Head title={`${config.label} Tasks`} />

            <div className="py-12">
                <div className="max-w-7xl mx-auto sm:px-6 lg:px-8">
                    <div className="space-y-4">
                        <div className="flex items-center gap-4">
                            <Link href="/dashboard">
                                <Button variant="outline" size="icon">
                                    <ArrowLeft className="h-4 w-4" />
                                </Button>
                            </Link>
                            <h1 className="text-2xl font-bold">
                                <span className="mr-2">{config.emoji}</span>
                                {config.label}
                            </h1>
                        </div>

                        <div className="grid gap-4">
                            {tasks.map((task) => (
                                <Card key={task.id} className="w-full">
                                    <CardHeader className="flex flex-row items-center justify-between space-y-0 pb-2">
                                        <CardTitle className="text-lg font-medium">
                                            {task.title}
                                        </CardTitle>
                                        <Badge variant={task.status === 'completed' ? 'default' : 'secondary'}>
                                            {task.status}
                                        </Badge>
                                    </CardHeader>
                                    <CardContent>
                                        <p className="text-sm text-muted-foreground">
                                            Due: {new Date(task.due_date).toLocaleDateString()}
                                        </p>
                                    </CardContent>
                                </Card>
                            ))}

                            {tasks.length === 0 && (
                                <Card className="w-full">
                                    <CardContent className="pt-6">
                                        <p className="text-center text-muted-foreground">
                                            No tasks found in this category.
                                        </p>
                                    </CardContent>
                                </Card>
                            )}
                        </div>
                    </div>
                </div>
            </div>
        </AuthenticatedLayout>
    );
};

export default TaskCategory; 