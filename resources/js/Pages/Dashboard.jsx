import React, { useEffect } from 'react';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { Head, Link } from '@inertiajs/react';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Badge } from '@/components/ui/badge';

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

const Dashboard = ({ tasks }) => {
    // Group tasks by category
    const groupedTasks = tasks?.reduce((acc, task) => {
        const category = task.category;
        if (!acc[category]) {
            acc[category] = [];
        }
        acc[category].push(task);
        return acc;
    }, {}) || {};

    return (
        <AuthenticatedLayout>
            <Head title="Dashboard" />

            <div className="py-12">
                <div className="max-w-7xl mx-auto sm:px-6 lg:px-8">
                    <div className="space-y-4">
                        <h1 className="text-2xl font-bold mb-4">Welkom bij De Horeca Helper</h1>
                        
                        <div className="grid gap-4">
                            {Object.entries(groupedTasks).map(([category, categoryTasks]) => {
                                const config = CATEGORY_CONFIG[category] || {
                                    emoji: '📋',
                                    label: category
                                };

                                return (
                                    <Link href={`/tasks/category/${category}`} key={category}>
                                        <Card className="w-full hover:bg-accent transition-colors">
                                            <CardHeader className="flex flex-row items-center justify-between space-y-0 pb-2">
                                                <CardTitle className="text-lg font-medium">
                                                    <span className="mr-2">{config.emoji}</span>
                                                    {config.label}
                                                </CardTitle>
                                                <Badge variant="secondary">{categoryTasks.length}</Badge>
                                            </CardHeader>
                                            <CardContent>
                                                <p className="text-sm text-muted-foreground">
                                                    {categoryTasks.length} task{categoryTasks.length !== 1 ? 's' : ''} to complete
                                                </p>
                                            </CardContent>
                                        </Card>
                                    </Link>
                                );
                            })}
                        </div>
                    </div>
                </div>
            </div>
        </AuthenticatedLayout>
    );
};

export default Dashboard;
