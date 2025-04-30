import { Head } from '@inertiajs/react';
import { Button } from "@/Components/ui/button";
import { Card, CardContent, CardHeader, CardTitle } from "@/Components/ui/card";
import { Input } from "@/Components/ui/input";
import { Label } from "@/Components/ui/label";
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from "@/Components/ui/select";
import { Checkbox } from "@/Components/ui/checkbox";
import { useForm } from '@inertiajs/react';
import { format } from 'date-fns';
import { useState } from 'react';

const COLUMN_OPTIONS = [
    { id: 'Task Name', label: 'Task Name' },
    { id: 'Category', label: 'Category' },
    { id: 'Product Name', label: 'Product Name' },
    { id: 'Measured Value', label: 'Measured Value' },
    { id: 'Validation Norm', label: 'Validation Norm' },
    { id: 'Extra Questions', label: 'Extra Questions' },
    { id: 'Corrective Actions', label: 'Corrective Actions' },
    { id: 'Notes', label: 'Notes' },
    { id: 'Status', label: 'Status' },
    { id: 'Timestamp', label: 'Timestamp' },
    { id: 'Completed By', label: 'Completed By' },
];

export default function Export({ categories, defaultStartDate, defaultEndDate }) {
    const [selectedColumns, setSelectedColumns] = useState(COLUMN_OPTIONS.map(opt => opt.id));
    const { data, setData, post, processing } = useForm({
        start_date: defaultStartDate,
        end_date: defaultEndDate,
        category_id: '',
        format: 'csv',
        columns: selectedColumns,
    });

    const handleSubmit = (e) => {
        e.preventDefault();
        const route = data.format === 'csv' ? 'tasks.export' : 'tasks.export.batch';
        post(route(route), {
            onSuccess: () => {
                // The browser will handle the file download or show success message
            },
        });
    };

    const toggleColumn = (columnId) => {
        const newColumns = selectedColumns.includes(columnId)
            ? selectedColumns.filter(id => id !== columnId)
            : [...selectedColumns, columnId];
        
        setSelectedColumns(newColumns);
        setData('columns', newColumns);
    };

    return (
        <>
            <Head title="Export Tasks" />

            <div className="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
                <Card>
                    <CardHeader>
                        <CardTitle>Export Tasks</CardTitle>
                    </CardHeader>
                    <CardContent>
                        <form onSubmit={handleSubmit} className="space-y-6">
                            <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div className="space-y-2">
                                    <Label htmlFor="start_date">Start Date</Label>
                                    <Input
                                        id="start_date"
                                        type="date"
                                        value={data.start_date}
                                        onChange={(e) => setData('start_date', e.target.value)}
                                        required
                                    />
                                </div>

                                <div className="space-y-2">
                                    <Label htmlFor="end_date">End Date</Label>
                                    <Input
                                        id="end_date"
                                        type="date"
                                        value={data.end_date}
                                        onChange={(e) => setData('end_date', e.target.value)}
                                        required
                                    />
                                </div>

                                <div className="space-y-2">
                                    <Label htmlFor="category">Task Category (Optional)</Label>
                                    <Select
                                        value={data.category_id}
                                        onValueChange={(value) => setData('category_id', value)}
                                    >
                                        <SelectTrigger>
                                            <SelectValue placeholder="Select a category" />
                                        </SelectTrigger>
                                        <SelectContent>
                                            <SelectItem value="">All Categories</SelectItem>
                                            {categories.map((category) => (
                                                <SelectItem key={category.id} value={category.id}>
                                                    {category.name}
                                                </SelectItem>
                                            ))}
                                        </SelectContent>
                                    </Select>
                                </div>

                                <div className="space-y-2">
                                    <Label htmlFor="format">Export Format</Label>
                                    <Select
                                        value={data.format}
                                        onValueChange={(value) => setData('format', value)}
                                    >
                                        <SelectTrigger>
                                            <SelectValue placeholder="Select format" />
                                        </SelectTrigger>
                                        <SelectContent>
                                            <SelectItem value="csv">CSV</SelectItem>
                                            <SelectItem value="pdf">PDF</SelectItem>
                                        </SelectContent>
                                    </Select>
                                </div>
                            </div>

                            <div className="space-y-4">
                                <Label>Columns to Include</Label>
                                <div className="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
                                    {COLUMN_OPTIONS.map((column) => (
                                        <div key={column.id} className="flex items-center space-x-2">
                                            <Checkbox
                                                id={column.id}
                                                checked={selectedColumns.includes(column.id)}
                                                onCheckedChange={() => toggleColumn(column.id)}
                                            />
                                            <Label htmlFor={column.id}>{column.label}</Label>
                                        </div>
                                    ))}
                                </div>
                            </div>

                            <div className="flex justify-end">
                                <Button type="submit" disabled={processing}>
                                    {processing ? 'Exporting...' : 'Export'}
                                </Button>
                            </div>
                        </form>
                    </CardContent>
                </Card>
            </div>
        </>
    );
} 