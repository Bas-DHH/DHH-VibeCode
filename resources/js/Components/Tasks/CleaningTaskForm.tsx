import React, { useState } from 'react';
import { useForm } from '@inertiajs/react';
import { Button } from '@/Components/ui/button';
import { Card } from '@/Components/ui/card';
import { Switch } from '@/Components/ui/switch';
import { Textarea } from '@/Components/ui/textarea';
import { Alert } from '@/Components/ui/alert';

interface CleaningTaskFormProps {
    task: {
        id: number;
        name: string;
        instructions: string;
        disinfection_required: boolean;
    };
    onSuccess?: () => void;
}

export default function CleaningTaskForm({ task, onSuccess }: CleaningTaskFormProps) {
    const [showCorrectiveAction, setShowCorrectiveAction] = useState(false);
    
    const { data, setData, post, processing, errors } = useForm({
        task_id: task.id,
        cleaned: true,
        disinfected: task.disinfection_required ? true : undefined,
        notes: '',
        corrective_action: '',
    });

    const handleSubmit = (e: React.FormEvent) => {
        e.preventDefault();
        post(route('tasks.cleaning.complete'), {
            onSuccess: () => {
                onSuccess?.();
            },
        });
    };

    const handleCleanedChange = (checked: boolean) => {
        setData('cleaned', checked);
        setShowCorrectiveAction(!checked);
    };

    const handleDisinfectedChange = (checked: boolean) => {
        setData('disinfected', checked);
        if (!checked && task.disinfection_required) {
            setShowCorrectiveAction(true);
        }
    };

    return (
        <Card className="p-6">
            <form onSubmit={handleSubmit} className="space-y-6">
                <div>
                    <h3 className="text-lg font-medium">{task.name}</h3>
                    <p className="text-sm text-gray-500 mt-1">{task.instructions}</p>
                </div>

                <div className="space-y-4">
                    <div className="flex items-center justify-between">
                        <label htmlFor="cleaned" className="font-medium">
                            Cleaned
                        </label>
                        <Switch
                            id="cleaned"
                            checked={data.cleaned}
                            onCheckedChange={handleCleanedChange}
                        />
                    </div>

                    {task.disinfection_required && (
                        <div className="flex items-center justify-between">
                            <label htmlFor="disinfected" className="font-medium">
                                Disinfected
                            </label>
                            <Switch
                                id="disinfected"
                                checked={data.disinfected}
                                onCheckedChange={handleDisinfectedChange}
                            />
                        </div>
                    )}

                    {showCorrectiveAction && (
                        <div className="space-y-2">
                            <label htmlFor="corrective_action" className="block font-medium">
                                Corrective Action Required
                            </label>
                            <Textarea
                                id="corrective_action"
                                value={data.corrective_action}
                                onChange={e => setData('corrective_action', e.target.value)}
                                placeholder="Explain why the task couldn't be completed and what actions will be taken"
                                rows={3}
                            />
                            {errors.corrective_action && (
                                <Alert variant="destructive">{errors.corrective_action}</Alert>
                            )}
                        </div>
                    )}

                    <div className="space-y-2">
                        <label htmlFor="notes" className="block font-medium">
                            Notes (Optional)
                        </label>
                        <Textarea
                            id="notes"
                            value={data.notes}
                            onChange={e => setData('notes', e.target.value)}
                            placeholder="Any additional notes about the cleaning task"
                            rows={2}
                        />
                    </div>
                </div>

                <Button
                    type="submit"
                    className="w-full"
                    disabled={processing}
                >
                    {processing ? 'Submitting...' : 'Complete Task'}
                </Button>
            </form>
        </Card>
    );
} 