import { Head } from '@inertiajs/react';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { Card, CardContent, CardHeader, CardTitle } from '@/Components/ui/card';
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/Components/ui/table';

export default function SuperAdminDashboard({ businesses, admins }) {
    return (
        <AuthenticatedLayout>
            <Head title="Super Admin Dashboard" />

            <div className="py-12">
                <div className="max-w-7xl mx-auto sm:px-6 lg:px-8">
                    <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
                        {/* Businesses Overview */}
                        <Card>
                            <CardHeader>
                                <CardTitle>Businesses Overview</CardTitle>
                            </CardHeader>
                            <CardContent>
                                <Table>
                                    <TableHeader>
                                        <TableRow>
                                            <TableHead>Business Name</TableHead>
                                            <TableHead>Subscription Status</TableHead>
                                            <TableHead>Admins</TableHead>
                                        </TableRow>
                                    </TableHeader>
                                    <TableBody>
                                        {businesses.map((business) => (
                                            <TableRow key={business.id}>
                                                <TableCell>{business.name}</TableCell>
                                                <TableCell>
                                                    <span className={`px-2 py-1 rounded-full text-xs ${
                                                        business.subscription_status === 'active'
                                                            ? 'bg-green-100 text-green-800'
                                                            : 'bg-red-100 text-red-800'
                                                    }`}>
                                                        {business.subscription_status}
                                                    </span>
                                                </TableCell>
                                                <TableCell>
                                                    {business.admins_count} Admin{business.admins_count !== 1 ? 's' : ''}
                                                </TableCell>
                                            </TableRow>
                                        ))}
                                    </TableBody>
                                </Table>
                            </CardContent>
                        </Card>

                        {/* System Admins */}
                        <Card>
                            <CardHeader>
                                <CardTitle>System Administrators</CardTitle>
                            </CardHeader>
                            <CardContent>
                                <Table>
                                    <TableHeader>
                                        <TableRow>
                                            <TableHead>Name</TableHead>
                                            <TableHead>Email</TableHead>
                                            <TableHead>Business</TableHead>
                                        </TableRow>
                                    </TableHeader>
                                    <TableBody>
                                        {admins.map((admin) => (
                                            <TableRow key={admin.id}>
                                                <TableCell>{admin.name}</TableCell>
                                                <TableCell>{admin.email}</TableCell>
                                                <TableCell>{admin.business?.name || 'N/A'}</TableCell>
                                            </TableRow>
                                        ))}
                                    </TableBody>
                                </Table>
                            </CardContent>
                        </Card>
                    </div>
                </div>
            </div>
        </AuthenticatedLayout>
    );
} 