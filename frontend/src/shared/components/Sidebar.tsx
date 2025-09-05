import React from 'react'
import { Link, useLocation } from 'react-router-dom'
import { cn } from '@/utils/cn'
import { 
  Home, 
  Users, 
  ClipboardList, 
  Package, 
  Wrench, 
  Building2, 
  Car,
  MapPin
} from 'lucide-react'

export const Sidebar: React.FC = () => {
  const location = useLocation()

  const navigation = [
    { name: 'Dashboard', href: '/dashboard', icon: Home },
    { name: 'Tasks', href: '/tasks', icon: ClipboardList },
    { name: 'Users', href: '/users', icon: Users },
    { name: 'Materials', href: '/materials', icon: Package },
    { name: 'Tools', href: '/tools', icon: Wrench },
    { name: 'Estates', href: '/estates', icon: Building2 },
    { name: 'Cars', href: '/cars', icon: Car },
    { name: 'Cities', href: '/cities', icon: MapPin },
  ]

  return (
    <aside className="w-64 bg-white shadow-sm border-r border-gray-200 min-h-screen">
      <div className="p-6">
        <div className="flex items-center space-x-2 mb-8">
          <div className="w-8 h-8 bg-blue-600 rounded-lg flex items-center justify-center">
            <span className="text-white font-bold text-sm">S</span>
          </div>
          <span className="text-xl font-bold text-gray-900">SkyTech</span>
        </div>

        <nav className="space-y-2">
          {navigation.map((item) => {
            const Icon = item.icon
            const isActive = location.pathname === item.href
            
            return (
              <Link
                key={item.name}
                to={item.href}
                className={cn(
                  'flex items-center space-x-3 px-3 py-2 rounded-lg text-sm font-medium transition-colors',
                  isActive
                    ? 'bg-blue-50 text-blue-700 border-r-2 border-blue-600'
                    : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900'
                )}
              >
                <Icon className="w-5 h-5" />
                <span>{item.name}</span>
              </Link>
            )
          })}
        </nav>
      </div>
    </aside>
  )
}
