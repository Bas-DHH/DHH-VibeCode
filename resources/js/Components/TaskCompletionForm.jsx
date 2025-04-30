import React from 'react';
import { useForm } from '@inertiajs/react';
import { Button } from '@/Components/ui/button';
import { Label } from '@/Components/ui/label';
import { Textarea } from '@/Components/ui/textarea';
import { Input } from '@/Components/ui/input';
import { Switch } from '@/Components/ui/switch';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/Components/ui/select';
import { Alert, AlertDescription } from '@/Components/ui/alert';
import { AlertCircle } from 'lucide-react';

export default function TaskCompletionForm({ task, onClose }) {
    const { data, setData, post, processing, errors } = useForm({
        notes: '',
        input_data: {},
        corrective_action: '',
    });

    const handleSubmit = (e) => {
        e.preventDefault();
        post(route('tasks.instances.complete', task.id), {
            preserveScroll: true,
            onSuccess: () => onClose(),
        });
    };

    const renderFormFields = () => {
        switch (task.category.name) {
            case 'Cleaning':
                return (
                    <div className="space-y-4">
                        <div className="space-y-2">
                            <Label>Cleaned</Label>
                            <div className="flex items-center space-x-2">
                                <Switch
                                    id="cleaned"
                                    checked={data.input_data.cleaned}
                                    onCheckedChange={(checked) =>
                                        setData('input_data', {
                                            ...data.input_data,
                                            cleaned: checked,
                                        })
                                    }
                                />
                                <Label htmlFor="cleaned">
                                    {data.input_data.cleaned ? 'Yes' : 'No'}
                                </Label>
                            </div>
                        </div>

                        {task.input_data?.requires_disinfection && (
                            <div className="space-y-2">
                                <Label>Disinfected</Label>
                                <div className="flex items-center space-x-2">
                                    <Switch
                                        id="disinfected"
                                        checked={data.input_data.disinfected}
                                        onCheckedChange={(checked) =>
                                            setData('input_data', {
                                                ...data.input_data,
                                                disinfected: checked,
                                            })
                                        }
                                    />
                                    <Label htmlFor="disinfected">
                                        {data.input_data.disinfected ? 'Yes' : 'No'}
                                    </Label>
                                </div>
                            </div>
                        )}

                        {(data.input_data.cleaned === false ||
                            data.input_data.disinfected === false) && (
                            <div className="space-y-2">
                                <Label htmlFor="corrective_action">Corrective Action</Label>
                                <Textarea
                                    id="corrective_action"
                                    value={data.corrective_action}
                                    onChange={(e) => setData('corrective_action', e.target.value)}
                                    required
                                />
                            </div>
                        )}
                    </div>
                );

            case 'Temperature Control':
                return (
                    <div className="space-y-4">
                        <div className="space-y-2">
                            <Label htmlFor="temperature">Measured Temperature (°C)</Label>
                            <Input
                                id="temperature"
                                type="number"
                                step="0.1"
                                value={data.input_data.temperature}
                                onChange={(e) =>
                                    setData('input_data', {
                                        ...data.input_data,
                                        temperature: parseFloat(e.target.value),
                                    })
                                }
                                required
                            />
                        </div>

                        {task.input_data?.extra_questions?.map((question) => (
                            <div key={question.id} className="space-y-2">
                                <Label>{question.text}</Label>
                                <div className="flex items-center space-x-2">
                                    <Switch
                                        id={question.id}
                                        checked={data.input_data[question.id]}
                                        onCheckedChange={(checked) =>
                                            setData('input_data', {
                                                ...data.input_data,
                                                [question.id]: checked,
                                            })
                                        }
                                    />
                                    <Label htmlFor={question.id}>
                                        {data.input_data[question.id] ? 'Yes' : 'No'}
                                    </Label>
                                </div>
                            </div>
                        ))}

                        {(data.input_data.temperature > task.input_data.norm ||
                            Object.values(data.input_data).some(
                                (value) => value === false
                            )) && (
                            <div className="space-y-2">
                                <Label htmlFor="corrective_action">Corrective Action</Label>
                                <Textarea
                                    id="corrective_action"
                                    value={data.corrective_action}
                                    onChange={(e) => setData('corrective_action', e.target.value)}
                                    required
                                />
                            </div>
                        )}
                    </div>
                );

            case 'Critical Cooking':
                return (
                    <div className="space-y-4">
                        <div className="space-y-2">
                            <Label htmlFor="product_name">Product Name</Label>
                            <Input
                                id="product_name"
                                value={data.input_data.product_name}
                                onChange={(e) =>
                                    setData('input_data', {
                                        ...data.input_data,
                                        product_name: e.target.value,
                                    })
                                }
                                required
                            />
                        </div>

                        <div className="space-y-2">
                            <Label htmlFor="temperature">Measured Temperature (°C)</Label>
                            <Input
                                id="temperature"
                                type="number"
                                step="0.1"
                                value={data.input_data.temperature}
                                onChange={(e) =>
                                    setData('input_data', {
                                        ...data.input_data,
                                        temperature: parseFloat(e.target.value),
                                    })
                                }
                                required
                            />
                        </div>

                        {data.input_data.temperature < task.input_data.norm && (
                            <div className="space-y-2">
                                <Label htmlFor="corrective_action">Corrective Action</Label>
                                <Textarea
                                    id="corrective_action"
                                    value={data.corrective_action}
                                    onChange={(e) => setData('corrective_action', e.target.value)}
                                    required
                                />
                            </div>
                        )}
                    </div>
                );

            case 'Goods Receiving':
                return (
                    <div className="space-y-4">
                        <div className="space-y-2">
                            <Label>Supplier Did Not Visit</Label>
                            <div className="flex items-center space-x-2">
                                <Switch
                                    id="no_delivery"
                                    checked={data.input_data.no_delivery}
                                    onCheckedChange={(checked) =>
                                        setData('input_data', {
                                            ...data.input_data,
                                            no_delivery: checked,
                                        })
                                    }
                                />
                                <Label htmlFor="no_delivery">
                                    {data.input_data.no_delivery ? 'Yes' : 'No'}
                                </Label>
                            </div>
                        </div>

                        {!data.input_data.no_delivery && (
                            <>
                                <div className="space-y-2">
                                    <Label htmlFor="product_group">Product Group</Label>
                                    <Select
                                        value={data.input_data.product_group}
                                        onValueChange={(value) =>
                                            setData('input_data', {
                                                ...data.input_data,
                                                product_group: value,
                                            })
                                        }
                                    >
                                        <SelectTrigger>
                                            <SelectValue placeholder="Select product group" />
                                        </SelectTrigger>
                                        <SelectContent>
                                            <SelectItem value="fresh">Fresh (≤ 7°C)</SelectItem>
                                            <SelectItem value="canned">Canned/Dry (15-25°C)</SelectItem>
                                            <SelectItem value="frozen">Frozen (≤ -18°C)</SelectItem>
                                        </SelectContent>
                                    </Select>
                                </div>

                                <div className="space-y-2">
                                    <Label htmlFor="product_name">Product Name</Label>
                                    <Input
                                        id="product_name"
                                        value={data.input_data.product_name}
                                        onChange={(e) =>
                                            setData('input_data', {
                                                ...data.input_data,
                                                product_name: e.target.value,
                                            })
                                        }
                                        required
                                    />
                                </div>

                                <div className="space-y-2">
                                    <Label htmlFor="temperature">Measured Temperature (°C)</Label>
                                    <Input
                                        id="temperature"
                                        type="number"
                                        step="0.1"
                                        value={data.input_data.temperature}
                                        onChange={(e) =>
                                            setData('input_data', {
                                                ...data.input_data,
                                                temperature: parseFloat(e.target.value),
                                            })
                                        }
                                        required
                                    />
                                </div>

                                <div className="space-y-2">
                                    <Label>Packaging</Label>
                                    <div className="flex items-center space-x-2">
                                        <Switch
                                            id="packaging"
                                            checked={data.input_data.packaging}
                                            onCheckedChange={(checked) =>
                                                setData('input_data', {
                                                    ...data.input_data,
                                                    packaging: checked,
                                                })
                                            }
                                        />
                                        <Label htmlFor="packaging">
                                            {data.input_data.packaging ? 'Yes' : 'No'}
                                        </Label>
                                    </div>
                                </div>

                                <div className="space-y-2">
                                    <Label>Correct</Label>
                                    <div className="flex items-center space-x-2">
                                        <Switch
                                            id="correct"
                                            checked={data.input_data.correct}
                                            onCheckedChange={(checked) =>
                                                setData('input_data', {
                                                    ...data.input_data,
                                                    correct: checked,
                                                })
                                            }
                                        />
                                        <Label htmlFor="correct">
                                            {data.input_data.correct ? 'Yes' : 'No'}
                                        </Label>
                                    </div>
                                </div>

                                <div className="space-y-2">
                                    <Label>BBE</Label>
                                    <div className="flex items-center space-x-2">
                                        <Switch
                                            id="bbe"
                                            checked={data.input_data.bbe}
                                            onCheckedChange={(checked) =>
                                                setData('input_data', {
                                                    ...data.input_data,
                                                    bbe: checked,
                                                })
                                            }
                                        />
                                        <Label htmlFor="bbe">
                                            {data.input_data.bbe ? 'Yes' : 'No'}
                                        </Label>
                                    </div>
                                </div>

                                {(data.input_data.temperature > task.input_data.norm ||
                                    !data.input_data.packaging ||
                                    !data.input_data.correct ||
                                    !data.input_data.bbe) && (
                                    <div className="space-y-2">
                                        <Label htmlFor="corrective_action">Corrective Action</Label>
                                        <Textarea
                                            id="corrective_action"
                                            value={data.corrective_action}
                                            onChange={(e) => setData('corrective_action', e.target.value)}
                                            required
                                        />
                                    </div>
                                )}
                            </>
                        )}

                        {data.input_data.no_delivery && (
                            <div className="space-y-2">
                                <Label htmlFor="notes">Notes (Required)</Label>
                                <Textarea
                                    id="notes"
                                    value={data.notes}
                                    onChange={(e) => setData('notes', e.target.value)}
                                    required
                                />
                            </div>
                        )}
                    </div>
                );

            case 'Verification of Measurement Devices':
                return (
                    <div className="space-y-4">
                        <div className="space-y-2">
                            <Label htmlFor="device_name">Device Name / ID</Label>
                            <Input
                                id="device_name"
                                value={data.input_data.device_name}
                                onChange={(e) =>
                                    setData('input_data', {
                                        ...data.input_data,
                                        device_name: e.target.value,
                                    })
                                }
                                required
                            />
                        </div>

                        <div className="space-y-2">
                            <Label htmlFor="boiling_water">Boiling Water Temperature (°C)</Label>
                            <Input
                                id="boiling_water"
                                type="number"
                                step="0.1"
                                value={data.input_data.boiling_water}
                                onChange={(e) =>
                                    setData('input_data', {
                                        ...data.input_data,
                                        boiling_water: parseFloat(e.target.value),
                                    })
                                }
                                required
                            />
                            <p className="text-sm text-muted-foreground">
                                Expected range: 99.0°C - 101.0°C
                            </p>
                        </div>

                        <div className="space-y-2">
                            <Label htmlFor="melting_ice">Melting Ice Temperature (°C)</Label>
                            <Input
                                id="melting_ice"
                                type="number"
                                step="0.1"
                                value={data.input_data.melting_ice}
                                onChange={(e) =>
                                    setData('input_data', {
                                        ...data.input_data,
                                        melting_ice: parseFloat(e.target.value),
                                    })
                                }
                                required
                            />
                            <p className="text-sm text-muted-foreground">
                                Expected range: -1.0°C - +1.0°C
                            </p>
                        </div>

                        {(data.input_data.boiling_water < 99.0 ||
                            data.input_data.boiling_water > 101.0 ||
                            data.input_data.melting_ice < -1.0 ||
                            data.input_data.melting_ice > 1.0) && (
                            <div className="space-y-2">
                                <Label htmlFor="corrective_action">Corrective Action</Label>
                                <Textarea
                                    id="corrective_action"
                                    value={data.corrective_action}
                                    onChange={(e) => setData('corrective_action', e.target.value)}
                                    required
                                />
                            </div>
                        )}
                    </div>
                );

            default:
                return (
                    <div className="space-y-4">
                        <div className="space-y-2">
                            <Label htmlFor="notes">Notes</Label>
                            <Textarea
                                id="notes"
                                value={data.notes}
                                onChange={(e) => setData('notes', e.target.value)}
                            />
                        </div>
                    </div>
                );
        }
    };

    return (
        <form onSubmit={handleSubmit} className="space-y-4">
            {renderFormFields()}

            {errors.input_data && (
                <Alert variant="destructive">
                    <AlertCircle className="h-4 w-4" />
                    <AlertDescription>{errors.input_data}</AlertDescription>
                </Alert>
            )}

            <div className="flex justify-end space-x-2">
                <Button type="button" variant="outline" onClick={onClose}>
                    Cancel
                </Button>
                <Button type="submit" disabled={processing}>
                    Complete Task
                </Button>
            </div>
        </form>
    );
} 