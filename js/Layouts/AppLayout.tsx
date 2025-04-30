import React from 'react';
import { Link } from '@inertiajs/react';
import { NavigationMenu, NavigationMenuItem } from '@/Components/ui/navigation-menu';

interface AppLayoutProps {
  children: React.ReactNode;
}

export default function AppLayout({ children }: AppLayoutProps) {
  return (
    <div className="min-h-screen bg-background">
      <NavigationMenu>
        {/* ... existing navigation items ... */}
        <NavigationMenuItem>
          <Link href={route('subscriptions.index')} className={route().current('subscriptions.*') ? 'active' : ''}>
            Subscription
          </Link>
        </NavigationMenuItem>
      </NavigationMenu>
      <main>{children}</main>
    </div>
  );
} 