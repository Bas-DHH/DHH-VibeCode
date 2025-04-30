import React from 'react';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { Head, router } from '@inertiajs/react';
import { Card, CardContent, CardHeader, CardTitle } from '@/Components/ui/card';
import { Badge } from '@/Components/ui/badge';
import { Button } from '@/Components/ui/button';
import { Input } from '@/Components/ui/input';
import { Label } from '@/Components/ui/label';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/Components/ui/select';
import { format } from 'date-fns';

export default function History({ auth, instances, categories, filters }) {
    const handleFilter = (key, value) => {
        router.get(route('task-instances.index'), {
            ...filters,
            [key]: value,
        }, {
            preserveState: true,
            preserveScroll: true,
        });
    };

    const handleExport = () => {
        window.location.href = route('task-instances.export', filters);
    };

    return (
        <AuthenticatedLayout
            user={auth.user}
            header={<h2 className="font-semibold text-xl text-gray-800 leading-tight">Task History</h2>}
        >
            <Head title="Task History" />

            <div className="py-12">
                <div className="max-w-7xl mx-auto sm:px-6 lg:px-8">
                    <Card>
                        <CardHeader>
                            <div className="flex justify-between items-center">
                                <CardTitle>Task History</CardTitle>
                                <Button onClick={handleExport}>Export to CSV</Button>
                            </div>
                        </CardHeader>
                        <CardContent>
                            <div className="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
                                <div className="space-y-2">
                                    <Label htmlFor="start_date">Start Date</Label>
                                    <Input
                                        id="start_date"
                                        type="date"
                                        value={filters.start_date || ''}
                                        onChange={(e) => handleFilter('start_date', e.target.value)}
                                    />
                                </div>
                                <div className="space-y-2">
                                    <Label htmlFor="end_date">End Date</Label>
                                    <Input
                                        id="end_date"
                                        type="date"
                                        value={filters.end_date || ''}
                                        onChange={(e) => handleFilter('end_date', e.target.value)}
                                    />
                                </div>
                                <div className="space-y-2">
                                    <Label htmlFor="category">Category</Label>
                                    <Select
                                        value={filters.category || ''}
                                        onValueChange={(value) => handleFilter('category', value)}
                                    >
                                        <SelectTrigger>
                                            <SelectValue placeholder="All Categories" />
                                        </SelectTrigger>
                                        <SelectContent>
                                            <SelectItem value="">All Categories</SelectItem>
                                            {categories.map((category) => (
                                                <SelectItem key={category.id} value={category.id}>
                                                    {category.name}
                                                </SelectItem>
                                            ))}
                                        </SelectContent>
                                    </Select>
                                </div>
                            </div>

                            <div className="space-y-4">
                                {instances.data.map((instance) => (
                                    <div
                                        key={instance.id}
                                        className="p-4 border rounded-lg space-y-2"
                                    >
                                        <div className="flex items-center justify-between">
                                            <div>
                                                <h3 className="font-medium">{instance.task.title}</h3>
                                                <p className="text-sm text-gray-500">
                                                    {instance.task.category?.name} •{' '}
                                                    {format(new Date(instance.scheduled_for), 'PPp')}
                                                </p>
                                            </div>
                                            <Badge variant={instance.status === 'completed' ? 'success' : 'secondary'}>
                                                {instance.status}
                                            </Badge>
                                        </div>

                                        {instance.completed_by && (
                                            <p className="text-sm text-gray-500">
                                                Completed by: {instance.completed_by.name} on{' '}
                                                {format(new Date(instance.completed_at), 'PPp')}
                                            </p>
                                        )}

                                        {instance.input_data && (
                                            <div className="mt-2">
                                                <h4 className="text-sm font-medium">Input Data:</h4>
                                                <pre className="text-sm bg-gray-50 p-2 rounded">
                                                    {JSON.stringify(instance.input_data, null, 2)}
                                                </pre>
                                            </div>
                                        )}

                                        {instance.notes && (
                                            <div className="mt-2">
                                                <h4 className="text-sm font-medium">Notes:</h4>
                                                <p className="text-sm">{instance.notes}</p>
                                            </div>
                                        )}
                                    </div>
                                ))}

                                {instances.data.length === 0 && (
                                    <p className="text-center text-gray-500">No tasks found for the selected filters.</p>
                                )}
                            </div>

                            {instances.last_page > 1 && (
                                <div className="mt-6 flex justify-center">
                                    <div className="flex space-x-2">
                                        {Array.from({ length: instances.last_page }, (_, i) => i + 1).map((page) => (
                                            <Button
                                                key={page}
                                                variant={page === instances.current_page ? 'default' : 'outline'}
                                                onClick={() => handleFilter('page', page)}
                                            >
                                                {page}
                                            </Button>
                                        ))}
                                    </div>
                                </div>
                            )}
                        </CardContent>
                    </Card>
                </div>
            </div>
        </AuthenticatedLayout>
    );
} 