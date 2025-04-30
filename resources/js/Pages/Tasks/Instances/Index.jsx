import React from 'react';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { Head } from '@inertiajs/react';
import { Card, CardContent, CardHeader, CardTitle } from '@/Components/ui/card';
import { Badge } from '@/Components/ui/badge';
import { Button } from '@/Components/ui/button';
import { Check, Skip } from 'lucide-react';
import { useForm } from '@inertiajs/react';
import { Dialog, DialogContent, DialogHeader, DialogTitle, DialogTrigger } from '@/Components/ui/dialog';
import { Label } from '@/Components/ui/label';
import { Textarea } from '@/Components/ui/textarea';
import { Input } from '@/Components/ui/input';

export default function Index({ auth, instances }) {
    const { data, setData, post, processing } = useForm({
        notes: '',
        input_data: {},
    });

    const handleComplete = (instance) => {
        post(route('tasks.instances.complete', instance.id), {
            preserveScroll: true,
        });
    };

    const handleSkip = (instance) => {
        post(route('tasks.instances.skip', instance.id), {
            preserveScroll: true,
        });
    };

    const getStatusBadge = (status, isOverdue) => {
        if (isOverdue) {
            return <Badge variant="destructive">Overdue</Badge>;
        }
        switch (status) {
            case 'completed':
                return <Badge variant="success">Completed</Badge>;
            case 'skipped':
                return <Badge variant="secondary">Skipped</Badge>;
            default:
                return <Badge variant="outline">Pending</Badge>;
        }
    };

    return (
        <AuthenticatedLayout
            user={auth.user}
            header={<h2 className="font-semibold text-xl text-gray-800 leading-tight">Today's Tasks</h2>}
        >
            <Head title="Today's Tasks" />

            <div className="py-12">
                <div className="max-w-7xl mx-auto sm:px-6 lg:px-8">
                    <Card>
                        <CardHeader>
                            <CardTitle>Tasks for {new Date().toLocaleDateString()}</CardTitle>
                        </CardHeader>
                        <CardContent>
                            <div className="space-y-4">
                                {instances.map((instance) => (
                                    <div
                                        key={instance.id}
                                        className="flex items-center justify-between p-4 border rounded-lg"
                                    >
                                        <div className="space-y-1">
                                            <div className="flex items-center gap-2">
                                                <h3 className="font-medium">{instance.task.title}</h3>
                                                {getStatusBadge(instance.status, instance.isOverdue())}
                                            </div>
                                            <p className="text-sm text-gray-500">
                                                {instance.task.category?.name} •{' '}
                                                {new Date(instance.scheduled_for).toLocaleTimeString()}
                                            </p>
                                            {instance.task.assigned_user && (
                                                <p className="text-sm text-gray-500">
                                                    Assigned to: {instance.task.assigned_user.name}
                                                </p>
                                            )}
                                        </div>

                                        {instance.status === 'pending' && (
                                            <div className="flex gap-2">
                                                <Dialog>
                                                    <DialogTrigger asChild>
                                                        <Button variant="default" size="sm">
                                                            <Check className="mr-2 h-4 w-4" />
                                                            Complete
                                                        </Button>
                                                    </DialogTrigger>
                                                    <DialogContent>
                                                        <DialogHeader>
                                                            <DialogTitle>Complete Task</DialogTitle>
                                                        </DialogHeader>
                                                        <div className="space-y-4">
                                                            <div>
                                                                <Label htmlFor="notes">Notes</Label>
                                                                <Textarea
                                                                    id="notes"
                                                                    value={data.notes}
                                                                    onChange={(e) => setData('notes', e.target.value)}
                                                                />
                                                            </div>
                                                            <div>
                                                                <Label htmlFor="input_data">Input Data</Label>
                                                                <Input
                                                                    id="input_data"
                                                                    type="text"
                                                                    value={data.input_data}
                                                                    onChange={(e) => setData('input_data', e.target.value)}
                                                                />
                                                            </div>
                                                            <Button
                                                                onClick={() => handleComplete(instance)}
                                                                disabled={processing}
                                                            >
                                                                Complete Task
                                                            </Button>
                                                        </div>
                                                    </DialogContent>
                                                </Dialog>

                                                <Button
                                                    variant="outline"
                                                    size="sm"
                                                    onClick={() => handleSkip(instance)}
                                                    disabled={processing}
                                                >
                                                    <Skip className="mr-2 h-4 w-4" />
                                                    Skip
                                                </Button>
                                            </div>
                                        )}
                                    </div>
                                ))}
                            </div>
                        </CardContent>
                    </Card>
                </div>
            </div>
        </AuthenticatedLayout>
    );
} 