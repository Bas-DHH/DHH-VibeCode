import { Head } from '@inertiajs/react';
import { router } from '@inertiajs/react';
import { Button } from '@/Components/ui/button';
import { Card, CardContent, CardDescription, CardFooter, CardHeader, CardTitle } from '@/Components/ui/card';
import { Badge } from '@/Components/ui/badge';
import { Alert, AlertDescription } from '@/Components/ui/alert';
import { formatCurrency } from '@/lib/utils';
import AppLayout from '@/Layouts/AppLayout';

interface Plan {
  id: string;
  name: string;
  price: number;
  interval: string;
}

interface Props {
  business: {
    name: string;
    trial_ends_at: string | null;
  };
  subscription: {
    mollie_status: string;
    mollie_plan: string;
    ends_at: string | null;
  } | null;
  plans: Record<string, Plan>;
  isOnTrial: boolean;
  trialEndsAt: string | null;
}

export default function Index({ business, subscription, plans, isOnTrial, trialEndsAt }: Props) {
  const handleSubscribe = (planId: string) => {
    router.post(`/subscriptions/${planId}`);
  };

  const handleCancel = () => {
    router.post('/subscriptions/cancel');
  };

  const handleResume = () => {
    router.post('/subscriptions/resume');
  };

  const handleUpdate = (planId: string) => {
    router.put(`/subscriptions/${planId}`);
  };

  return (
    <AppLayout>
      <Head title="Subscription Management" />

      <div className="py-12">
        <div className="max-w-7xl mx-auto sm:px-6 lg:px-8">
          {/* Subscription Status */}
          <div className="mb-8">
            <h2 className="text-2xl font-bold mb-4">Subscription Status</h2>
            {isOnTrial && (
              <Alert>
                <AlertDescription>
                  Your trial ends on {new Date(trialEndsAt!).toLocaleDateString()}. 
                  Choose a plan below to continue using the service.
                </AlertDescription>
              </Alert>
            )}
            {subscription && (
              <Alert>
                <AlertDescription>
                  Current Plan: {plans[subscription.mollie_plan]?.name}
                  <Badge className="ml-2" variant={subscription.mollie_status === 'active' ? 'default' : 'destructive'}>
                    {subscription.mollie_status}
                  </Badge>
                </AlertDescription>
              </Alert>
            )}
          </div>

          {/* Subscription Plans */}
          <div className="grid md:grid-cols-2 gap-6">
            {Object.entries(plans).map(([id, plan]) => (
              <Card key={id} className={subscription?.mollie_plan === id ? 'border-primary' : ''}>
                <CardHeader>
                  <CardTitle>{plan.name}</CardTitle>
                  <CardDescription>
                    {formatCurrency(plan.price)} / {plan.interval}
                  </CardDescription>
                </CardHeader>
                <CardContent>
                  <ul className="list-disc list-inside space-y-2">
                    <li>All features included</li>
                    <li>Single location support</li>
                    <li>Unlimited tasks and users</li>
                    <li>Email support</li>
                  </ul>
                </CardContent>
                <CardFooter>
                  {!subscription && (
                    <Button 
                      className="w-full" 
                      onClick={() => handleSubscribe(id)}
                    >
                      Subscribe
                    </Button>
                  )}
                  {subscription?.mollie_plan === id ? (
                    <Button 
                      className="w-full" 
                      variant="destructive"
                      onClick={handleCancel}
                    >
                      Cancel Subscription
                    </Button>
                  ) : subscription && (
                    <Button 
                      className="w-full" 
                      onClick={() => handleUpdate(id)}
                    >
                      Switch to this Plan
                    </Button>
                  )}
                </CardFooter>
              </Card>
            ))}
          </div>

          {/* Cancelled Subscription */}
          {subscription?.ends_at && (
            <div className="mt-8">
              <Alert>
                <AlertDescription>
                  Your subscription will end on {new Date(subscription.ends_at).toLocaleDateString()}.
                  <Button 
                    variant="link" 
                    className="ml-2 underline" 
                    onClick={handleResume}
                  >
                    Resume Subscription
                  </Button>
                </AlertDescription>
              </Alert>
            </div>
          )}
        </div>
      </div>
    </AppLayout>
  );
} 