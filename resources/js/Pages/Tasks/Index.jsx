import React, { useState } from 'react';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { Head } from '@inertiajs/react';
import TaskList from '@/Components/TaskList';
import TaskForm from '@/Components/TaskForm';
import { Button } from '@/Components/ui/button';
import { Plus } from 'lucide-react';

export default function Index({ auth, tasks, categories, users, business }) {
    const [showForm, setShowForm] = useState(false);
    const [selectedTask, setSelectedTask] = useState(null);

    const handleEdit = (task) => {
        setSelectedTask(task);
        setShowForm(true);
    };

    const handleFormClose = () => {
        setShowForm(false);
        setSelectedTask(null);
    };

    return (
        <AuthenticatedLayout
            user={auth.user}
            header={<h2 className="font-semibold text-xl text-gray-800 leading-tight">Tasks</h2>}
        >
            <Head title="Tasks" />

            <div className="py-12">
                <div className="max-w-7xl mx-auto sm:px-6 lg:px-8">
                    <div className="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div className="p-6">
                            <div className="flex justify-between items-center mb-6">
                                <h3 className="text-lg font-medium">Task Management</h3>
                                {auth.user.can.manage_tasks && (
                                    <Button onClick={() => setShowForm(true)}>
                                        <Plus className="mr-2 h-4 w-4" />
                                        Create Task
                                    </Button>
                                )}
                            </div>

                            {showForm ? (
                                <TaskForm
                                    task={selectedTask}
                                    categories={categories}
                                    users={users}
                                    business={business}
                                    onClose={handleFormClose}
                                />
                            ) : (
                                <TaskList
                                    tasks={tasks}
                                    onEdit={handleEdit}
                                    canManageTasks={auth.user.can.manage_tasks}
                                />
                            )}
                        </div>
                    </div>
                </div>
            </div>
        </AuthenticatedLayout>
    );
} 