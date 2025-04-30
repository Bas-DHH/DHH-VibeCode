import { useEffect, useState } from 'react';
import { Alert, AlertDescription, AlertTitle } from "@/Components/ui/alert";
import { AlertCircle } from "lucide-react";
import { Link } from '@inertiajs/react';
import { Badge } from "@/Components/ui/badge";

export default function OverdueTasksAlert({ initialOverdueTasks = [] }) {
    const [overdueTasks, setOverdueTasks] = useState(initialOverdueTasks);
    const [isVisible, setIsVisible] = useState(initialOverdueTasks.length > 0);

    useEffect(() => {
        // Set up real-time updates using Echo
        window.Echo.private(`tasks.${window.auth.user.id}`)
            .listen('TaskOverdue', (e) => {
                setOverdueTasks(prev => [...prev, e.task]);
                setIsVisible(true);
            })
            .listen('TaskCompleted', (e) => {
                setOverdueTasks(prev => prev.filter(task => task.id !== e.task.id));
                if (overdueTasks.length === 1) {
                    setIsVisible(false);
                }
            });
    }, []);

    if (!isVisible || overdueTasks.length === 0) {
        return null;
    }

    return (
        <Alert variant="destructive" className="mb-4">
            <AlertCircle className="h-4 w-4" />
            <AlertTitle>Overdue Tasks</AlertTitle>
            <AlertDescription>
                <div className="space-y-2">
                    <p>You have {overdueTasks.length} overdue task{overdueTasks.length > 1 ? 's' : ''} that need attention:</p>
                    <ul className="list-disc list-inside">
                        {overdueTasks.map(task => (
                            <li key={task.id} className="flex items-center gap-2">
                                <Link 
                                    href={route('tasks.show', task.id)}
                                    className="text-sm font-medium hover:underline"
                                >
                                    {task.title}
                                </Link>
                                <Badge variant="secondary">
                                    {task.category.name}
                                </Badge>
                            </li>
                        ))}
                    </ul>
                </div>
            </AlertDescription>
        </Alert>
    );
} 