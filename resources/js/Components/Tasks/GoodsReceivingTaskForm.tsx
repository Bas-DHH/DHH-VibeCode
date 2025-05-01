import React, { useState } from 'react';
import { useForm } from '@inertiajs/react';
import { Button } from '@/Components/ui/button';
import { Card } from '@/Components/ui/card';
import { Input } from '@/Components/ui/input';
import { Textarea } from '@/Components/ui/textarea';
import { Alert } from '@/Components/ui/alert';
import { Label } from '@/Components/ui/label';
import { Switch } from '@/Components/ui/switch';

interface GoodsReceivingTaskFormProps {
    task: {
        id: number;
        name: string;
        instructions: string;
        temperature_check_required: boolean;
        min_temperature: number | null;
        max_temperature: number | null;
        visual_check_required: boolean;
    };
    onSuccess?: () => void;
}

export default function GoodsReceivingTaskForm({ task, onSuccess }: GoodsReceivingTaskFormProps) {
    const [showCorrectiveAction, setShowCorrectiveAction] = useState(false);
    
    const { data, setData, post, processing, errors } = useForm({
        task_id: task.id,
        supplier_name: '',
        product_name: '',
        batch_number: '',
        expiry_date: '',
        temperature: task.temperature_check_required ? '' : undefined,
        packaging_intact: true,
        visual_check_passed: task.visual_check_required ? true : undefined,
        corrective_action: '',
        notes: '',
    });

    const handleSubmit = (e: React.FormEvent) => {
        e.preventDefault();
        post(route('tasks.goods-receiving.complete'), {
            onSuccess: () => {
                onSuccess?.();
            },
        });
    };

    const handleTemperatureChange = (value: string) => {
        setData('temperature', value);
        if (task.temperature_check_required) {
            const temp = parseFloat(value);
            if (!isNaN(temp) && 
                ((task.min_temperature !== null && temp < task.min_temperature) || 
                 (task.max_temperature !== null && temp > task.max_temperature))) {
                setShowCorrectiveAction(true);
            } else {
                setShowCorrectiveAction(false);
            }
        }
    };

    const handlePackagingChange = (checked: boolean) => {
        setData('packaging_intact', checked);
        if (!checked) {
            setShowCorrectiveAction(true);
        } else if (!showCorrectiveAction) {
            setShowCorrectiveAction(false);
        }
    };

    const handleVisualCheckChange = (checked: boolean) => {
        setData('visual_check_passed', checked);
        if (!checked) {
            setShowCorrectiveAction(true);
        } else if (!showCorrectiveAction) {
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
                        <Label htmlFor="supplier_name">Supplier Name</Label>
                        <Input
                            id="supplier_name"
                            value={data.supplier_name}
                            onChange={e => setData('supplier_name', e.target.value)}
                            placeholder="Enter supplier name"
                            required
                        />
                        {errors.supplier_name && (
                            <Alert variant="destructive">{errors.supplier_name}</Alert>
                        )}
                    </div>

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
                        <Label htmlFor="batch_number">Batch Number</Label>
                        <Input
                            id="batch_number"
                            value={data.batch_number}
                            onChange={e => setData('batch_number', e.target.value)}
                            placeholder="Enter batch number"
                            required
                        />
                        {errors.batch_number && (
                            <Alert variant="destructive">{errors.batch_number}</Alert>
                        )}
                    </div>

                    <div className="space-y-2">
                        <Label htmlFor="expiry_date">Expiry Date</Label>
                        <Input
                            id="expiry_date"
                            type="date"
                            value={data.expiry_date}
                            onChange={e => setData('expiry_date', e.target.value)}
                            required
                        />
                        {errors.expiry_date && (
                            <Alert variant="destructive">{errors.expiry_date}</Alert>
                        )}
                    </div>

                    {task.temperature_check_required && (
                        <div className="space-y-2">
                            <Label htmlFor="temperature">
                                Temperature (°C)
                                {task.min_temperature !== null && task.max_temperature !== null && (
                                    <span> - Required: {task.min_temperature}°C - {task.max_temperature}°C</span>
                                )}
                                {task.min_temperature !== null && task.max_temperature === null && (
                                    <span> - Required: ≥ {task.min_temperature}°C</span>
                                )}
                                {task.min_temperature === null && task.max_temperature !== null && (
                                    <span> - Required: ≤ {task.max_temperature}°C</span>
                                )}
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
                    )}

                    <div className="flex items-center justify-between">
                        <Label htmlFor="packaging_intact">Packaging Intact</Label>
                        <Switch
                            id="packaging_intact"
                            checked={data.packaging_intact}
                            onCheckedChange={handlePackagingChange}
                        />
                    </div>

                    {task.visual_check_required && (
                        <div className="flex items-center justify-between">
                            <Label htmlFor="visual_check_passed">Visual Check</Label>
                            <Switch
                                id="visual_check_passed"
                                checked={data.visual_check_passed}
                                onCheckedChange={handleVisualCheckChange}
                            />
                        </div>
                    )}

                    {showCorrectiveAction && (
                        <div className="space-y-2">
                            <Label htmlFor="corrective_action">Corrective Action Required</Label>
                            <Textarea
                                id="corrective_action"
                                value={data.corrective_action}
                                onChange={e => setData('corrective_action', e.target.value)}
                                placeholder="Explain why the goods failed inspection and what actions will be taken"
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
                            placeholder="Any additional notes about the goods received"
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