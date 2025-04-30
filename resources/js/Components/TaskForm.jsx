import React from 'react';
import { useForm } from '@inertiajs/react';
import { Card, CardContent, CardHeader, CardTitle } from './ui/card';
import { Label } from './ui/label';
import { Input } from './ui/input';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from './ui/select';
import PrimaryButton from './PrimaryButton';
import InputError from './InputError';

export default function TaskForm({ task = null, categories, users, business }) {
    const { data, setData, post, put, processing, errors, reset } = useForm({
        title: task?.title || '',
        description: task?.description || '',
        task_category_id: task?.task_category_id || '',
        assigned_user_id: task?.assigned_user_id || '',
        frequency: task?.frequency || 'daily',
        scheduled_time: task?.scheduled_time || '',
        day_of_week: task?.day_of_week || '',
        day_of_month: task?.day_of_month || '',
        business_id: business.id,
    });

    const submit = (e) => {
        e.preventDefault();
        if (task) {
            put(route('tasks.update', task.id));
        } else {
            post(route('tasks.store'));
        }
    };

    return (
        <Card>
            <CardHeader>
                <CardTitle>{task ? 'Edit Task' : 'Create Task'}</CardTitle>
            </CardHeader>
            <CardContent>
                <form onSubmit={submit} className="space-y-4">
                    <div>
                        <Label htmlFor="title">Title</Label>
                        <Input
                            id="title"
                            value={data.title}
                            onChange={(e) => setData('title', e.target.value)}
                            required
                        />
                        <InputError message={errors.title} />
                    </div>

                    <div>
                        <Label htmlFor="description">Description</Label>
                        <Input
                            id="description"
                            value={data.description}
                            onChange={(e) => setData('description', e.target.value)}
                        />
                        <InputError message={errors.description} />
                    </div>

                    <div>
                        <Label htmlFor="task_category_id">Category</Label>
                        <Select
                            value={data.task_category_id}
                            onValueChange={(value) => setData('task_category_id', value)}
                        >
                            <SelectTrigger>
                                <SelectValue placeholder="Select a category" />
                            </SelectTrigger>
                            <SelectContent>
                                {categories.map((category) => (
                                    <SelectItem key={category.id} value={category.id}>
                                        {category.name}
                                    </SelectItem>
                                ))}
                            </SelectContent>
                        </Select>
                        <InputError message={errors.task_category_id} />
                    </div>

                    <div>
                        <Label htmlFor="assigned_user_id">Assign To</Label>
                        <Select
                            value={data.assigned_user_id}
                            onValueChange={(value) => setData('assigned_user_id', value)}
                        >
                            <SelectTrigger>
                                <SelectValue placeholder="Select a user" />
                            </SelectTrigger>
                            <SelectContent>
                                {users.map((user) => (
                                    <SelectItem key={user.id} value={user.id}>
                                        {user.name}
                                    </SelectItem>
                                ))}
                            </SelectContent>
                        </Select>
                        <InputError message={errors.assigned_user_id} />
                    </div>

                    <div>
                        <Label htmlFor="frequency">Frequency</Label>
                        <Select
                            value={data.frequency}
                            onValueChange={(value) => setData('frequency', value)}
                        >
                            <SelectTrigger>
                                <SelectValue placeholder="Select frequency" />
                            </SelectTrigger>
                            <SelectContent>
                                <SelectItem value="daily">Daily</SelectItem>
                                <SelectItem value="weekly">Weekly</SelectItem>
                                <SelectItem value="monthly">Monthly</SelectItem>
                            </SelectContent>
                        </Select>
                        <InputError message={errors.frequency} />
                    </div>

                    {data.frequency === 'daily' && (
                        <div>
                            <Label htmlFor="scheduled_time">Scheduled Time</Label>
                            <Input
                                id="scheduled_time"
                                type="time"
                                value={data.scheduled_time}
                                onChange={(e) => setData('scheduled_time', e.target.value)}
                            />
                            <InputError message={errors.scheduled_time} />
                        </div>
                    )}

                    {data.frequency === 'weekly' && (
                        <div>
                            <Label htmlFor="day_of_week">Day of Week</Label>
                            <Select
                                value={data.day_of_week}
                                onValueChange={(value) => setData('day_of_week', value)}
                            >
                                <SelectTrigger>
                                    <SelectValue placeholder="Select day" />
                                </SelectTrigger>
                                <SelectContent>
                                    <SelectItem value="1">Monday</SelectItem>
                                    <SelectItem value="2">Tuesday</SelectItem>
                                    <SelectItem value="3">Wednesday</SelectItem>
                                    <SelectItem value="4">Thursday</SelectItem>
                                    <SelectItem value="5">Friday</SelectItem>
                                    <SelectItem value="6">Saturday</SelectItem>
                                    <SelectItem value="7">Sunday</SelectItem>
                                </SelectContent>
                            </Select>
                            <InputError message={errors.day_of_week} />
                        </div>
                    )}

                    {data.frequency === 'monthly' && (
                        <div>
                            <Label htmlFor="day_of_month">Day of Month</Label>
                            <Input
                                id="day_of_month"
                                type="number"
                                min="1"
                                max="31"
                                value={data.day_of_month}
                                onChange={(e) => setData('day_of_month', e.target.value)}
                            />
                            <InputError message={errors.day_of_month} />
                        </div>
                    )}

                    <div className="flex justify-end">
                        <PrimaryButton disabled={processing}>
                            {task ? 'Update Task' : 'Create Task'}
                        </PrimaryButton>
                    </div>
                </form>
            </CardContent>
        </Card>
    );
} 