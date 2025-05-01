import React, { useState } from 'react';
import { useForm } from '@inertiajs/react';
import { Button } from '@/Components/ui/button';
import { Card } from '@/Components/ui/card';
import { Input } from '@/Components/ui/input';
import { Textarea } from '@/Components/ui/textarea';
import { Alert } from '@/Components/ui/alert';
import { Label } from '@/Components/ui/label';

interface TemperatureTaskFormProps {
    task: {
        id: number;
        name: string;
        instructions: string;
        min_temperature: number;
        max_temperature: number;
        location: string;
    };
    onSuccess?: () => void;
}

export default function TemperatureTaskForm({ task, onSuccess }: TemperatureTaskFormProps) {
    const [showCorrectiveAction, setShowCorrectiveAction] = useState(false);
    
    const { data, setData, post, processing, errors } = useForm({
        task_id: task.id,
        temperature: '',
        corrective_action: '',
        notes: '',
    });

    const handleSubmit = (e: React.FormEvent) => {
        e.preventDefault();
        post(route('tasks.temperature.complete'), {
            onSuccess: () => {
                onSuccess?.();
            },
        });
    };

    const handleTemperatureChange = (value: string) => {
        setData('temperature', value);
        const temp = parseFloat(value);
        if (!isNaN(temp) && (temp < task.min_temperature || temp > task.max_temperature)) {
            setShowCorrectiveAction(true);
        } else {
            setShowCorrectiveAction(false);
        }
    };

    return (
        <Card className="p-6">
            <form onSubmit={handleSubmit} className="space-y-6">
                <div>
                    <h3 className="text-lg font-medium">{task.name}</h3>
                    <p className="text-sm text-gray-500 mt-1">{task.instructions}</p>
                    <p className="text-sm text-gray-500">Location: {task.location}</p>
                </div>

                <div className="space-y-4">
                    <div className="space-y-2">
                        <Label htmlFor="temperature">
                            Temperature (°C) - Required: {task.min_temperature}°C - {task.max_temperature}°C
                        </Label>
                        <Input
                            id="temperature"
                            type="number"
                            step="0.1"
                            value={data.temperature}
                            onChange={e => handleTemperatureChange(e.target.value)}
                            placeholder="Enter temperature"
                            required
                        />
                        {errors.temperature && (
                            <Alert variant="destructive">{errors.temperature}</Alert>
                        )}
                    </div>

                    {showCorrectiveAction && (
                        <div className="space-y-2">
                            <Label htmlFor="corrective_action">Corrective Action Required</Label>
                            <Textarea
                                id="corrective_action"
                                value={data.corrective_action}
                                onChange={e => setData('corrective_action', e.target.value)}
                                placeholder="Explain why the temperature is out of range and what actions will be taken"
                                rows={3}
                                required
                            />
                            {errors.corrective_action && (
                                <Alert variant="destructive">{errors.corrective_action}</Alert>
                            )}
                        </div>
                    )}

                    <div className="space-y-2">
                        <Label htmlFor="notes">Notes (Optional)</Label>
                        <Textarea
                            id="notes"
                            value={data.notes}
                            onChange={e => setData('notes', e.target.value)}
                            placeholder="Any additional notes about the temperature check"
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