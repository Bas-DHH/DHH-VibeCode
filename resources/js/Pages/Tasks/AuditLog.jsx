import { Head } from '@inertiajs/react';
import { Card, CardContent, CardHeader, CardTitle } from "@/Components/ui/card";
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from "@/Components/ui/table";
import { format } from 'date-fns';
import { Badge } from "@/Components/ui/badge";
import { Pagination } from "@/Components/ui/pagination";

export default function AuditLog({ taskInstance, auditLogs }) {
    const formatChanges = (oldValues, newValues) => {
        const changes = [];
        for (const key in newValues) {
            if (oldValues[key] !== newValues[key]) {
                changes.push(
                    <div key={key} className="mb-1">
                        <span className="font-medium">{key}:</span>{' '}
                        <span className="text-red-500 line-through">{oldValues[key]}</span>{' '}
                        <span className="text-green-500">{newValues[key]}</span>
                    </div>
                );
            }
        }
        return changes;
    };

    return (
        <>
            <Head title={`Audit Log - ${taskInstance.task.title}`} />

            <div className="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
                <Card>
                    <CardHeader>
                        <CardTitle>
                            Audit Log for {taskInstance.task.title}
                        </CardTitle>
                    </CardHeader>
                    <CardContent>
                        <Table>
                            <TableHeader>
                                <TableRow>
                                    <TableHead>Date</TableHead>
                                    <TableHead>User</TableHead>
                                    <TableHead>Action</TableHead>
                                    <TableHead>Changes</TableHead>
                                    <TableHead>Notes</TableHead>
                                </TableRow>
                            </TableHeader>
                            <TableBody>
                                {auditLogs.data.map(log => (
                                    <TableRow key={log.id}>
                                        <TableCell>
                                            {format(new Date(log.created_at), 'PPpp')}
                                        </TableCell>
                                        <TableCell>{log.user.name}</TableCell>
                                        <TableCell>
                                            <Badge variant={log.action === 'completed' ? 'success' : 'default'}>
                                                {log.action}
                                            </Badge>
                                        </TableCell>
                                        <TableCell>
                                            {formatChanges(log.old_values, log.new_values)}
                                        </TableCell>
                                        <TableCell>{log.notes}</TableCell>
                                    </TableRow>
                                ))}
                            </TableBody>
                        </Table>

                        <div className="mt-4">
                            <Pagination links={auditLogs.links} />
                        </div>
                    </CardContent>
                </Card>
            </div>
        </>
    );
} 