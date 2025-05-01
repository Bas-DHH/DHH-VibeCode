import React, { useState } from 'react';
import { useForm } from '@inertiajs/react';
import { Button } from '@/Components/ui/button';
import { Card } from '@/Components/ui/card';
import { Input } from '@/Components/ui/input';
import { Textarea } from '@/Components/ui/textarea';
import { Alert } from '@/Components/ui/alert';
import { Label } from '@/Components/ui/label';

interface CookingTaskFormProps {
    task: {
        id: number;
        name: string;
        instructions: string;
        temperature_norm: number;
        cooking_time_required: boolean;
        visual_checks_required: boolean;
    };
    onSuccess?: () => void;
}

export default function CookingTaskForm({ task, onSuccess }: CookingTaskFormProps) {
    const [showCorrectiveAction, setShowCorrectiveAction] = useState(false);
    
    const { data, setData, post, processing, errors } = useForm({
        task_id: task.id,
        product_name: '',
        temperature: '',
        cooking_time: '',
        visual_check_passed: true,
        corrective_action: '',
        notes: '',
    });

    const handleSubmit = (e: React.FormEvent) => {
        e.preventDefault();
        post(route('tasks.cooking.complete'), {
            onSuccess: () => {
                onSuccess?.();
            },
        });
    };

    const handleTemperatureChange = (value: string) => {
        setData('temperature', value);
        const temp = parseFloat(value);
        if (!isNaN(temp) && temp < task.temperature_norm) {
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
                </div>

                <div className="space-y-4">
                    <div className="space-y-2">
                        <Label htmlFor="product_name">Product Name</Label>
                        <Input
                            id="product_name"
                            value={data.product_name}
                            onChange={e => setData('product_name', e.target.value)}
                            placeholder="Enter product name"
                            required
                        />
                        {errors.product_name && (
                            <Alert variant="destructive">{errors.product_name}</Alert>
                        )}
                    </div>

                    <div className="space-y-2">
                        <Label htmlFor="temperature">
                            Temperature (°C) - Required: ≥ {task.temperature_norm}°C
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

                    {task.cooking_time_required && (
                        <div className="space-y-2">
                            <Label htmlFor="cooking_time">Cooking Time (minutes)</Label>
                            <Input
                                id="cooking_time"
                                type="number"
                                value={data.cooking_time}
                                onChange={e => setData('cooking_time', e.target.value)}
                                placeholder="Enter cooking time"
                                required
                            />
                            {errors.cooking_time && (
                                <Alert variant="destructive">{errors.cooking_time}</Alert>
                            )}
                        </div>
                    )}

                    {task.visual_checks_required && (
                        <div className="space-y-2">
                            <Label htmlFor="visual_check_passed">Visual Check</Label>
                            <select
                                id="visual_check_passed"
                                value={data.visual_check_passed ? 'true' : 'false'}
                                onChange={e => {
                                    const passed = e.target.value === 'true';
                                    setData('visual_check_passed', passed);
                                    setShowCorrectiveAction(!passed);
                                }}
                                className="w-full rounded-md border border-gray-300 px-3 py-2"
                                required
                            >
                                <option value="true">Passed</option>
                                <option value="false">Failed</option>
                            </select>
                            {errors.visual_check_passed && (
                                <Alert variant="destructive">{errors.visual_check_passed}</Alert>
                            )}
                        </div>
                    )}

                    {showCorrectiveAction && (
                        <div className="space-y-2">
                            <Label htmlFor="corrective_action">Corrective Action Required</Label>
                            <Textarea
                                id="corrective_action"
                                value={data.corrective_action}
                                onChange={e => setData('corrective_action', e.target.value)}
                                placeholder="Explain why the task failed and what actions will be taken"
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
                            placeholder="Any additional notes about the cooking task"
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