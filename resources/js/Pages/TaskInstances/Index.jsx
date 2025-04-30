import React, { useState } from 'react';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { Head, router } from '@inertiajs/react';
import { Card, CardContent, CardHeader, CardTitle } from '@/Components/ui/card';
import { Badge } from '@/Components/ui/badge';
import { Button } from '@/Components/ui/button';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/Components/ui/select';
import { format } from 'date-fns';
import TaskCompletionForm from '@/Components/TaskCompletionForm';
import { Dialog, DialogContent, DialogHeader, DialogTitle } from '@/Components/ui/dialog';

export default function Index({ auth, instances, categories, filters }) {
    const [selectedInstance, setSelectedInstance] = useState(null);
    const [showCompletionForm, setShowCompletionForm] = useState(false);

    const handleFilter = (key, value) => {
        router.get(route('task-instances.index'), {
            ...filters,
            [key]: value,
        }, {
            preserveState: true,
            preserveScroll: true,
        });
    };

    const handleComplete = (instance) => {
        setSelectedInstance(instance);
        setShowCompletionForm(true);
    };

    const handleCompletionSuccess = () => {
        setShowCompletionForm(false);
        setSelectedInstance(null);
        router.reload();
    };

    return (
        <AuthenticatedLayout
            user={auth.user}
            header={<h2 className="font-semibold text-xl text-gray-800 leading-tight">Tasks</h2>}
        >
            <Head title="Tasks" />

            <div className="py-12">
                <div className="max-w-7xl mx-auto sm:px-6 lg:px-8">
                    <Card>
                        <CardHeader>
                            <div className="flex justify-between items-center">
                                <CardTitle>Today's Tasks</CardTitle>
                                <Select
                                    value={filters.category || ''}
                                    onValueChange={(value) => handleFilter('category', value)}
                                >
                                    <SelectTrigger className="w-[180px]">
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
                        </CardHeader>
                        <CardContent>
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
                                            <div className="flex items-center space-x-2">
                                                <Badge variant={instance.status === 'completed' ? 'success' : 'secondary'}>
                                                    {instance.status}
                                                </Badge>
                                                {instance.status === 'pending' && (
                                                    <Button
                                                        size="sm"
                                                        onClick={() => handleComplete(instance)}
                                                    >
                                                        Complete
                                                    </Button>
                                                )}
                                            </div>
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
                                    <p className="text-center text-gray-500">No tasks found for today.</p>
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

            <Dialog open={showCompletionForm} onOpenChange={setShowCompletionForm}>
                <DialogContent>
                    <DialogHeader>
                        <DialogTitle>Complete Task</DialogTitle>
                    </DialogHeader>
                    {selectedInstance && (
                        <TaskCompletionForm
                            instance={selectedInstance}
                            onSuccess={handleCompletionSuccess}
                        />
                    )}
                </DialogContent>
            </Dialog>
        </AuthenticatedLayout>
    );
} 