import React from 'react';
import { Link } from '@inertiajs/react';
import { Card, CardContent, CardHeader, CardTitle } from './ui/card';
import { Badge } from './ui/badge';
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from './ui/table';

export default function TaskList({ tasks, canManage = false }) {
    const getStatusBadge = (status, isOverdue) => {
        if (isOverdue) {
            return <Badge variant="destructive">Overdue</Badge>;
        }
        switch (status) {
            case 'completed':
                return <Badge variant="success">Completed</Badge>;
            case 'pending':
                return <Badge variant="secondary">Pending</Badge>;
            default:
                return <Badge>{status}</Badge>;
        }
    };

    return (
        <Card>
            <CardHeader>
                <CardTitle>Tasks</CardTitle>
            </CardHeader>
            <CardContent>
                <Table>
                    <TableHeader>
                        <TableRow>
                            <TableHead>Title</TableHead>
                            <TableHead>Category</TableHead>
                            <TableHead>Due Date</TableHead>
                            <TableHead>Status</TableHead>
                            <TableHead>Assigned To</TableHead>
                            {canManage && <TableHead>Actions</TableHead>}
                        </TableRow>
                    </TableHeader>
                    <TableBody>
                        {tasks.map((task) => (
                            <TableRow key={task.id}>
                                <TableCell>
                                    <Link href={route('tasks.show', task.id)} className="hover:underline">
                                        {task.title}
                                    </Link>
                                </TableCell>
                                <TableCell>{task.category?.name}</TableCell>
                                <TableCell>{new Date(task.due_date).toLocaleDateString()}</TableCell>
                                <TableCell>{getStatusBadge(task.status, task.is_overdue)}</TableCell>
                                <TableCell>{task.assigned_user?.name || 'Unassigned'}</TableCell>
                                {canManage && (
                                    <TableCell>
                                        <div className="flex space-x-2">
                                            <Link
                                                href={route('tasks.edit', task.id)}
                                                className="text-blue-600 hover:text-blue-800"
                                            >
                                                Edit
                                            </Link>
                                            <Link
                                                href={route('tasks.complete', task.id)}
                                                className="text-green-600 hover:text-green-800"
                                            >
                                                Complete
                                            </Link>
                                        </div>
                                    </TableCell>
                                )}
                            </TableRow>
                        ))}
                    </TableBody>
                </Table>
            </CardContent>
        </Card>
    );
} 